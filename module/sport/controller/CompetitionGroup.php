<?php
namespace Module\Sport\Controller;

use Core\Controller;

class CompetitionGroup extends Controller {
    private $groupModel;
    private $phaseModel;

    public function __construct() {
        $this->groupModel = $this->model('sport/CompetitionGroup');
        $this->phaseModel = $this->model('sport/CompetitionPhase');
    }

    public function index($id = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$id && !$isAdmin) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

        $groupData  = null;
        $entries    = [];
        $prePhaseId = !empty($_GET['phase_id']) ? (int)$_GET['phase_id'] : null;

        if ($id !== null && $id !== '') {
            $groupData = $this->groupModel->getGroupById((int)$id);
            if (!$groupData) { return $this->redirect('sport/competition-editions'); }
            $entries = $this->groupModel->getEntriesByGroup($groupData['id']);
        }

        $mode = $id ? (($action === 'edit' && $isAdmin) ? 'edit' : 'view') : 'add';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

            $name    = trim($_POST['name'] ?? '');
            $phaseId = !empty($_POST['phase_id']) ? (int)$_POST['phase_id'] : 0;

            if (empty($name) || empty($phaseId)) {
                $_SESSION['flash_error'] = $txt['COMPETITION_GROUP_ERR_MISSING'] ?? 'Champs obligatoires manquants.';
                return $this->redirect($groupData ? 'sport/competition-group/edit/' . $groupData['id'] : 'sport/competition-group');
            }

            $postData = [
                'id'              => $groupData ? $groupData['id'] : null,
                'code'            => strtolower(trim($_POST['code'] ?? '')),
                'phase_id'        => $phaseId,
                'parent_group_id' => !empty($_POST['parent_group_id']) ? (int)$_POST['parent_group_id'] : null,
                'name'            => $name,
                'group_order'     => !empty($_POST['group_order']) ? (int)$_POST['group_order'] : $this->groupModel->getNextOrder($phaseId),
                'status_id'       => !empty($_POST['status_id'])   ? (int)$_POST['status_id']   : 1,
                'created_by'      => $_SESSION['user_id'] ?? null,
                'modified_by'     => $_SESSION['user_id'] ?? null,
            ];

            if ($groupData) {
                $this->groupModel->updateGroup($postData);
                $_SESSION['flash_message'] = $txt['COMPETITION_GROUP_MSG_UPDATED'] ?? 'Groupe mis a jour.';
                return $this->redirect('sport/competition-group/' . $groupData['id']);
            } else {
                $newId = $this->groupModel->addGroup($postData);
                if ($newId) {
                    $_SESSION['flash_message'] = $txt['COMPETITION_GROUP_MSG_ADDED'] ?? 'Groupe cree.';
                    return $this->redirect('sport/competition-phase/' . $phaseId);
                }
                $_SESSION['flash_error'] = 'Erreur lors de la creation.';
                return $this->redirect('sport/competition-group');
            }
        }

        $statuses         = $this->groupModel->getAllStatuses();
        $phaseId          = $groupData['phase_id'] ?? $prePhaseId ?? 0;
        $phaseData        = $phaseId ? $this->phaseModel->getPhaseById($phaseId) : null;
        $nextOrder        = $phaseId ? $this->groupModel->getNextOrder($phaseId) : 1;
        $parentCandidates = $phaseId ? $this->groupModel->getParentGroupCandidates($phaseId, $groupData ? $groupData['id'] : null) : [];

        $data = [
            'txt'              => $txt,
            'title'            => ($groupData ? ($txt['COMPETITION_GROUP_VIEW_TITLE'] ?? 'Groupe / Poule') : ($txt['COMPETITION_GROUP_ADD_TITLE'] ?? 'Nouveau groupe')) . ' - ' . SITENAME,
            'group'            => $groupData ? (object)$groupData : null,
            'entries'          => $entries,
            'phaseData'        => $phaseData,
            'statuses'         => $statuses,
            'nextOrder'        => $nextOrder,
            'parentCandidates' => $parentCandidates,
            'mode'             => $mode,
            'isAdmin'          => $isAdmin,
            'error'            => $_SESSION['flash_error']   ?? '',
            'message'          => $_SESSION['flash_message'] ?? '',
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competition-group', $data);
        $this->view('system/footer',  $data);
    }

    public function edit($id = null) { return $this->index($id, 'edit'); }

    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin || !$id) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

        $group     = $this->groupModel->getGroupById((int)$id);
        $returnUrl = $group ? 'sport/competition-phase/' . $group['phase_id'] : 'sport/competition-editions';

        $force  = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->groupModel->forceDeleteGroup((int)$id);
            $_SESSION['flash_message'] = $txt['COMPETITION_GROUP_MSG_DELETED'] ?? 'Groupe supprime.';
        } else {
            $this->groupModel->softDeleteGroup((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['COMPETITION_GROUP_MSG_DEACTIVATED'] ?? 'Groupe desactive.';
        }
        $this->redirect($returnUrl);
    }
}
