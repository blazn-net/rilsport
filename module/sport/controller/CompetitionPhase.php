<?php
namespace Module\Sport\Controller;

use Core\Controller;

class CompetitionPhase extends Controller {
    private $phaseModel;
    private $editionModel;

    public function __construct() {
        $this->phaseModel   = $this->model('sport/CompetitionPhase');
        $this->editionModel = $this->model('sport/CompetitionEdition');
    }

    public function index($id = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$id && !$isAdmin) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

        $phaseData   = null;
        $groups      = [];
        $rounds      = [];
        $preEditionId = !empty($_GET['edition_id']) ? (int)$_GET['edition_id'] : null;

        if ($id !== null && $id !== '') {
            $phaseData = $this->phaseModel->getPhaseById((int)$id);
            if (!$phaseData) { return $this->redirect('sport/competition-editions'); }
            $groups = $this->phaseModel->getGroupsByPhase($phaseData['id']);
            $rounds = $this->phaseModel->getRoundsByPhase($phaseData['id']);
        }

        $mode = $id ? (($action === 'edit' && $isAdmin) ? 'edit' : 'view') : 'add';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

            $name      = trim($_POST['name'] ?? '');
            $editionId = !empty($_POST['edition_id']) ? (int)$_POST['edition_id'] : 0;
            $phaseType = trim($_POST['phase_type'] ?? 'league');

            if (empty($name) || empty($editionId)) {
                $_SESSION['flash_error'] = $txt['COMPETITION_PHASE_ERR_MISSING'] ?? 'Champs obligatoires manquants.';
                return $this->redirect($phaseData ? 'sport/competition-phase/edit/' . $phaseData['id'] : 'sport/competition-phase');
            }

            $postData = [
                'id'          => $phaseData ? $phaseData['id'] : null,
                'code'        => strtolower(trim($_POST['code'] ?? '')),
                'edition_id'  => $editionId,
                'name'        => $name,
                'phase_type'  => $phaseType,
                'phase_order' => !empty($_POST['phase_order']) ? (int)$_POST['phase_order'] : ($phaseData ? $phaseData['phase_order'] : $this->phaseModel->getNextOrder($editionId)),
                'date_start'  => !empty($_POST['date_start']) ? $_POST['date_start'] : null,
                'date_end'    => !empty($_POST['date_end'])   ? $_POST['date_end']   : null,
                'status_id'   => !empty($_POST['status_id'])  ? (int)$_POST['status_id'] : 1,
                'created_by'  => $_SESSION['user_id'] ?? null,
                'modified_by' => $_SESSION['user_id'] ?? null,
            ];

            if ($phaseData) {
                $this->phaseModel->updatePhase($postData);
                $_SESSION['flash_message'] = $txt['COMPETITION_PHASE_MSG_UPDATED'] ?? 'Phase mise a jour.';
                return $this->redirect('sport/competition-phase/' . $phaseData['id']);
            } else {
                $newId = $this->phaseModel->addPhase($postData);
                if ($newId) {
                    $_SESSION['flash_message'] = $txt['COMPETITION_PHASE_MSG_ADDED'] ?? 'Phase creee.';
                    return $this->redirect('sport/competition-edition/' . $editionId);
                }
                $_SESSION['flash_error'] = 'Erreur lors de la creation.';
                return $this->redirect('sport/competition-phase');
            }
        }

        $statuses    = $this->phaseModel->getAllStatuses();
        $editionId   = $phaseData['edition_id'] ?? $preEditionId ?? 0;
        $editionData = $editionId ? $this->editionModel->getEditionById($editionId) : null;
        $nextOrder   = $editionId ? $this->phaseModel->getNextOrder($editionId) : 1;

        $phaseTypes  = ['league' => 'Championnat', 'cup' => 'Elimination directe', 'tournament' => 'Tournoi', 'ranking' => 'Classement'];

        $data = [
            'txt'         => $txt,
            'title'       => ($phaseData ? ($txt['COMPETITION_PHASE_VIEW_TITLE'] ?? 'Phase') : ($txt['COMPETITION_PHASE_ADD_TITLE'] ?? 'Nouvelle phase')) . ' - ' . SITENAME,
            'phase'       => $phaseData ? (object)$phaseData : null,
            'groups'      => $groups,
            'rounds'      => $rounds,
            'editionData' => $editionData,
            'statuses'    => $statuses,
            'phaseTypes'  => $phaseTypes,
            'nextOrder'   => $nextOrder,
            'mode'        => $mode,
            'isAdmin'     => $isAdmin,
            'error'       => $_SESSION['flash_error']   ?? '',
            'message'     => $_SESSION['flash_message'] ?? '',
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competition-phase', $data);
        $this->view('system/footer',  $data);
    }

    public function edit($id = null) { return $this->index($id, 'edit'); }

    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin || !$id) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

        $phase     = $this->phaseModel->getPhaseById((int)$id);
        $returnUrl = $phase ? 'sport/competition-edition/' . $phase['edition_id'] : 'sport/competition-editions';

        $force  = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->phaseModel->forceDeletePhase((int)$id);
            $_SESSION['flash_message'] = $txt['COMPETITION_PHASE_MSG_DELETED'] ?? 'Phase supprimee.';
        } else {
            $this->phaseModel->softDeletePhase((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['COMPETITION_PHASE_MSG_DEACTIVATED'] ?? 'Phase desactivee.';
        }
        $this->redirect($returnUrl);
    }
}
