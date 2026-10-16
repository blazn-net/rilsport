<?php
namespace Module\Sport\Controller;

use Core\Controller;

class SportMatch extends Controller {
    private $matchModel;
    private $standingModel;

    public function __construct() {
        $this->matchModel    = $this->model('sport/SportMatch');
        $this->standingModel = $this->model('sport/Standing');
    }

    public function index($id = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $matchData = null;
        if ($id !== null && $id !== '') {
            $matchData = $this->matchModel->getMatchById((int)$id);
            if (!$matchData) {
                return $this->redirect('sport/matches');
            }
        }

        $isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');
        $mode   = $id ? ((($action === 'edit' || $isPost) && $isAdmin) ? 'edit' : 'view') : 'add';

        $data = [
            'txt'      => $txt,
            'title'    => ($id ? ($mode === 'edit' ? ($txt['MATCH_TITLE_EDIT'] ?? 'Modifier le match') : ($txt['MATCH_TITLE_VIEW'] ?? 'Fiche match')) : ($txt['MATCH_TITLE_ADD'] ?? 'Nouveau match')) . ' - ' . SITENAME,
            'match'    => $matchData ? (object)$matchData : null,
            'rounds'   => $this->matchModel->getRoundsList(),
            'teams'    => $this->matchModel->getTeamsList(),
            'statuses' => $this->matchModel->getAllStatuses(),
            'mode'     => $mode,
            'isAdmin'  => $isAdmin,
            'error'    => $_SESSION['flash_error'] ?? '',
            'message'  => $_SESSION['flash_message'] ?? '',
        ];

        if ($isPost) {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $roundId    = (int)($_POST['round_id'] ?? 0);
            $homeTeamId = !empty($_POST['home_team_id']) ? (int)$_POST['home_team_id'] : null;
            $awayTeamId = !empty($_POST['away_team_id']) ? (int)$_POST['away_team_id'] : null;
            $matchOrder = !empty($_POST['match_order']) ? (int)$_POST['match_order'] : 1;
            $matchDate  = !empty($_POST['match_date']) ? trim($_POST['match_date']) : null;
            $venueName  = !empty($_POST['venue_name']) ? trim($_POST['venue_name']) : null;
            $scoreHome  = ($_POST['score_home'] !== '' && $_POST['score_home'] !== null) ? (int)$_POST['score_home'] : null;
            $scoreAway  = ($_POST['score_away'] !== '' && $_POST['score_away'] !== null) ? (int)$_POST['score_away'] : null;
            $note       = !empty($_POST['note']) ? trim($_POST['note']) : null;
            $statusId   = !empty($_POST['status_id']) ? (int)$_POST['status_id'] : 1;

            if (empty($roundId)) {
                $data['error'] = $txt['MATCH_ERR_MISSING_FIELDS'] ?? 'Veuillez sélectionner une journée.';
                $data['match'] = (object)$_POST;
            } elseif (!empty($homeTeamId) && !empty($awayTeamId) && $homeTeamId === $awayTeamId) {
                $data['error'] = $txt['MATCH_ERR_SAME_TEAMS'] ?? 'Les deux équipes doivent être différentes.';
                $data['match'] = (object)$_POST;
            } else {
                $currentUserId = $_SESSION['user_id'] ?? null;
                $payload = [
                    'round_id'     => $roundId,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'match_order'  => $matchOrder,
                    'match_date'   => $matchDate,
                    'venue_name'   => $venueName,
                    'score_home'   => $scoreHome,
                    'score_away'   => $scoreAway,
                    'note'         => $note,
                    'status_id'    => $statusId,
                ];

                if ($id) {
                    $payload['id'] = (int)$id;
                    $this->matchModel->updateMatch($payload, $currentUserId);

                    // Si match lié à un groupe et joué, recalcul auto du classement
                    if (!empty($matchData['group_id'])) {
                        $this->standingModel->recalculateGroupStandings((int)$matchData['group_id'], $currentUserId);
                    }

                    $_SESSION['flash_message'] = $txt['MATCH_MSG_UPDATED'] ?? 'Match mis à jour avec succès.';
                    return $this->redirect('sport/match/' . $id);
                } else {
                    $newId = $this->matchModel->addMatch($payload, $currentUserId);

                    // Si match lié à un groupe et joué, recalcul auto du classement
                    if (!empty($matchData['group_id']) && $statusId === 3) {
                        $this->standingModel->recalculateGroupStandings((int)$matchData['group_id'], $currentUserId);
                    }

                    $_SESSION['flash_message'] = $txt['MATCH_MSG_ADDED'] ?? 'Match ajouté avec succès.';
                    return $this->redirect('sport/matches');
                }
            }
        }

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/match',    $data);
        $this->view('system/footer',  $data);
    }

    public function edit($id = null) {
        return $this->index($id, 'edit');
    }

    public function score($id = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $id = (int)$id;
        $match = $this->matchModel->getMatchById($id);
        if (!$match) {
            return $this->redirect('sport/matches');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $scoreHome = ($_POST['score_home'] !== '' && $_POST['score_home'] !== null) ? (int)$_POST['score_home'] : null;
            $scoreAway = ($_POST['score_away'] !== '' && $_POST['score_away'] !== null) ? (int)$_POST['score_away'] : null;
            $statusId  = !empty($_POST['status_id']) ? (int)$_POST['status_id'] : 3; // 3 = MATCH_STATUS_PLAYED
            $currentUserId = $_SESSION['user_id'] ?? null;

            $this->matchModel->updateScore($id, $scoreHome, $scoreAway, null, $statusId, $currentUserId);

            // Recalculer le classement du groupe si le match y appartient
            if (!empty($match['group_id'])) {
                $this->standingModel->recalculateGroupStandings((int)$match['group_id'], $currentUserId);
            }

            $_SESSION['flash_message'] = $txt['MATCH_MSG_UPDATED'] ?? 'Score enregistré avec succès.';
            return $this->redirect('sport/match/' . $id);
        }

        return $this->redirect('sport/match/' . $id);
    }

    public function delete($id = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $id    = (int)$id;
        $force = isset($_GET['force']) && $_GET['force'] == '1';

        $match = $this->matchModel->getMatchById($id);
        $this->matchModel->deleteMatch($id, $force);

        if (!empty($match['group_id'])) {
            $this->standingModel->recalculateGroupStandings((int)$match['group_id'], $_SESSION['user_id'] ?? null);
        }

        $_SESSION['flash_message'] = $txt['MATCH_MSG_DELETED'] ?? 'Match supprimé / annulé.';
        return $this->redirect('sport/matches');
    }

    public function activate($id = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $id = (int)$id;
        $this->matchModel->activateMatch($id);

        $_SESSION['flash_message'] = $txt['MATCH_MSG_UPDATED'] ?? 'Match réactivé.';
        return $this->redirect('sport/matches');
    }
}
