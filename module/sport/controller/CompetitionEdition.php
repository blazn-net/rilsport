<?php
namespace Module\Sport\Controller;

use Core\Controller;

class CompetitionEdition extends Controller {
    private $editionModel;
    private $competitionModel;

    public function __construct() {
        $this->editionModel     = $this->model('sport/CompetitionEdition');
        $this->competitionModel = $this->model('sport/Competition');
    }

    public function index($id = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
        }

        $editionData = null;
        $phases      = [];
        $entries     = [];
        $subEditions = [];

        // Preselection competition via GET (creation depuis la fiche competition)
        $preselectedCompetitionId = !empty($_GET['competition_id']) ? (int)$_GET['competition_id'] : null;

        if ($id !== null && $id !== '') {
            $editionData = is_numeric($id)
                ? $this->editionModel->getEditionById((int)$id)
                : $this->editionModel->getEditionByCode($id);

            if (!$editionData) {
                return $this->redirect('sport/competition-editions');
            }

            $phases      = $this->editionModel->getPhasesByEdition($editionData['id']);
            $entries     = $this->editionModel->getEntriesByEdition($editionData['id']);
            $subEditions = $this->editionModel->getSubEditions($editionData['id']);
        }

        $mode = $id ? (($action === 'edit' && $isAdmin) ? 'edit' : 'view') : 'add';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
            }

            $code          = strtolower(trim($_POST['code'] ?? ''));
            $name          = trim($_POST['name'] ?? '');
            $competitionId = !empty($_POST['competition_id']) ? (int)$_POST['competition_id'] : 0;

            if (empty($code) || empty($name) || empty($competitionId)) {
                $_SESSION['flash_error'] = $txt['COMPETITION_EDITION_ERR_MISSING_FIELDS'] ?? 'Champs obligatoires manquants.';
                return $this->redirect($editionData ? 'sport/competition-edition/edit/' . $editionData['id'] : 'sport/competition-edition');
            }

            if (!$editionData) {
                $existing = $this->editionModel->getEditionByCode($code);
                if ($existing) {
                    $_SESSION['flash_error'] = $txt['COMPETITION_EDITION_ERR_CODE_EXISTS'] ?? 'Ce code existe deja.';
                    return $this->redirect('sport/competition-edition');
                }
            }

            $postData = [
                'id'                => $editionData ? $editionData['id'] : null,
                'code'              => $code,
                'name'              => $name,
                'competition_id'    => $competitionId,
                'parent_edition_id' => !empty($_POST['parent_edition_id']) ? (int)$_POST['parent_edition_id'] : null,
                'season_id'         => !empty($_POST['season_id'])         ? (int)$_POST['season_id']         : null,
                'edition_number'    => !empty($_POST['edition_number'])     ? (int)$_POST['edition_number']    : null,
                'date_start'        => !empty($_POST['date_start'])         ? $_POST['date_start']             : null,
                'date_end'          => !empty($_POST['date_end'])           ? $_POST['date_end']               : null,
                'status_id'         => !empty($_POST['status_id'])          ? (int)$_POST['status_id']         : 1,
                'created_by'        => $_SESSION['user_id'] ?? null,
                'modified_by'       => $_SESSION['user_id'] ?? null,
            ];

            if ($editionData) {
                $this->editionModel->updateEdition($postData);
                $_SESSION['flash_message'] = $txt['COMPETITION_EDITION_MSG_UPDATED'] ?? 'Edition mise a jour.';
                return $this->redirect('sport/competition-edition/' . $editionData['id']);
            } else {
                $newId = $this->editionModel->addEdition($postData);
                if ($newId) {
                    $_SESSION['flash_message'] = $txt['COMPETITION_EDITION_MSG_ADDED'] ?? 'Edition creee.';
                    // Redirige vers la fiche competition parente
                    return $this->redirect('sport/competition/' . $competitionId);
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de la creation.';
                    return $this->redirect('sport/competition-edition');
                }
            }
        }

        $allCompetitions   = $this->competitionModel->getAllCompetitions();
        $allSeasons        = $this->editionModel->getAllSeasons();
        $statuses          = $this->editionModel->getAllStatuses();
        $parentCandidates  = [];
        $competitionIdUsed = $editionData['competition_id'] ?? $preselectedCompetitionId ?? 0;
        if ($competitionIdUsed) {
            $parentCandidates = $this->editionModel->getParentEditionCandidates($competitionIdUsed, $editionData ? $editionData['id'] : null);
        }

        $title = $id
            ? ($mode === 'edit'
                ? ($txt['COMPETITION_EDITION_EDIT_TITLE'] ?? 'Modifier l\'edition')
                : ($txt['COMPETITION_EDITION_VIEW_TITLE'] ?? 'Fiche edition'))
            : ($txt['COMPETITION_EDITION_ADD_TITLE'] ?? 'Creer une edition');

        $data = [
            'txt'               => $txt,
            'title'             => $title . ' - ' . SITENAME,
            'edition'           => $editionData ? (object) $editionData : null,
            'phases'            => $phases,
            'entries'           => $entries,
            'subEditions'       => $subEditions,
            'allCompetitions'   => $allCompetitions,
            'allSeasons'        => $allSeasons,
            'statuses'          => $statuses,
            'parentCandidates'  => $parentCandidates,
            'preselectedCompetitionId' => $preselectedCompetitionId,
            'mode'              => $mode,
            'isAdmin'           => $isAdmin,
            'error'             => $_SESSION['flash_error']   ?? '',
            'message'           => $_SESSION['flash_message'] ?? '',
        ];

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competition-edition', $data);
        $this->view('system/footer',  $data);
    }

    public function edit($id = null) {
        return $this->index($id, 'edit');
    }

    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin || !$id) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
        }

        $edition    = $this->editionModel->getEditionById((int)$id);
        $returnUrl  = $edition ? 'sport/competition/' . $edition['competition_id'] : 'sport/competition-editions';

        $force  = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->editionModel->forceDeleteEdition((int)$id);
            $_SESSION['flash_message'] = $txt['COMPETITION_EDITION_MSG_DELETED'] ?? 'Edition supprimee.';
        } else {
            $this->editionModel->softDeleteEdition((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['COMPETITION_EDITION_MSG_DEACTIVATED'] ?? 'Edition desactivee.';
        }

        $this->redirect($returnUrl);
    }
}
