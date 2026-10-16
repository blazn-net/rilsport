<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Standing extends Controller {
    private $standingModel;

    public function __construct() {
        $this->standingModel = $this->model('sport/Standing');
    }

    public function index($id = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$id) {
            return $this->redirect('sport/standings');
        }

        $standingData = $this->standingModel->getStandingById((int)$id);
        if (!$standingData) {
            return $this->redirect('sport/standings');
        }

        $isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');
        $mode   = (($action === 'edit' || $isPost) && $isAdmin) ? 'edit' : 'view';

        $data = [
            'txt'      => $txt,
            'title'    => ($mode === 'edit' ? ($txt['STANDING_BTN_EDIT'] ?? 'Modifier le classement') : 'Classement : ' . ($standingData['team_name'] ?? '')) . ' - ' . SITENAME,
            'standing' => (object)$standingData,
            'mode'     => $mode,
            'isAdmin'  => $isAdmin,
            'error'    => $_SESSION['flash_error'] ?? '',
            'message'  => $_SESSION['flash_message'] ?? '',
        ];

        if ($isPost) {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $currentUserId = $_SESSION['user_id'] ?? null;
            $payload = [
                'id'            => (int)$id,
                'rank'          => ($_POST['rank'] !== '' && $_POST['rank'] !== null) ? (int)$_POST['rank'] : null,
                'points'        => (int)($_POST['points'] ?? 0),
                'played'        => (int)($_POST['played'] ?? 0),
                'won'           => (int)($_POST['won'] ?? 0),
                'drawn'         => (int)($_POST['drawn'] ?? 0),
                'lost'          => (int)($_POST['lost'] ?? 0),
                'goals_for'     => (int)($_POST['goals_for'] ?? 0),
                'goals_against' => (int)($_POST['goals_against'] ?? 0),
                'goal_diff'     => (int)($_POST['goal_diff'] ?? 0),
                'note'          => !empty($_POST['note']) ? trim($_POST['note']) : null,
            ];

            $this->standingModel->updateStandingManual($payload, $currentUserId);
            $_SESSION['flash_message'] = $txt['STANDING_MSG_UPDATED'] ?? 'Classement mis à jour avec succès.';
            return $this->redirect('sport/standing/' . $id);
        }

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/standing', $data);
        $this->view('system/footer',  $data);
    }

    public function edit($id = null) {
        return $this->index($id, 'edit');
    }

    public function reset($id = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $id = (int)$id;
        $row = $this->standingModel->getStandingById($id);
        if ($row) {
            $this->standingModel->resetStandingAuto($id);
            if (!empty($row['group_id'])) {
                $this->standingModel->recalculateGroupStandings((int)$row['group_id'], $_SESSION['user_id'] ?? null);
            }
            $_SESSION['flash_message'] = $txt['STANDING_MSG_RECALCULATED'] ?? 'Ligne réinitialisée en calcul automatique.';
            return $this->redirect('sport/standings?group=' . $row['group_id']);
        }

        return $this->redirect('sport/standings');
    }
}
