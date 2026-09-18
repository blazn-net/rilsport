<?php
namespace Module\Sport\Controller;

use Core\Controller;

class CompetitionEntry extends Controller {
    private $entryModel;
    private $editionModel;

    public function __construct() {
        $this->entryModel   = $this->model('sport/CompetitionEntry');
        $this->editionModel = $this->model('sport/CompetitionEdition');
    }

    /**
     * Gestion des inscriptions d une edition
     * Route: /sport/competition-entry/{edition_id}
     */
    public function index($editionId = null, $action = null) {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin) { die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.'); }

        if (!$editionId) {
            return $this->redirect('sport/competition-editions');
        }

        $editionData = $this->editionModel->getEditionById((int)$editionId);
        if (!$editionData) { return $this->redirect('sport/competition-editions'); }

        // POST : inscription(s)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subAction = trim($_POST['_action'] ?? '');

            if ($subAction === 'add') {
                $teamIds = isset($_POST['team_ids']) && is_array($_POST['team_ids'])
                    ? array_map('intval', $_POST['team_ids'])
                    : (!empty($_POST['team_id']) ? [(int)$_POST['team_id']] : []);
                $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
                $userId  = $_SESSION['user_id'] ?? null;

                if (empty($teamIds)) {
                    $_SESSION['flash_error'] = $txt['COMPETITION_ENTRY_ERR_NO_TEAM'] ?? 'Aucune equipe selectionnee.';
                } else {
                    $added = $this->entryModel->addEntriesBatch($teamIds, (int)$editionId, $groupId, $userId);
                    $_SESSION['flash_message'] = sprintf($txt['COMPETITION_ENTRY_MSG_ADDED'] ?? '%d equipe(s) inscrite(s).', $added);
                }
            } elseif ($subAction === 'update_group') {
                $entryId = !empty($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
                $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
                $entryOrder = !empty($_POST['entry_order']) ? (int)$_POST['entry_order'] : 1;
                if ($entryId) {
                    $this->entryModel->updateEntry($entryId, $groupId, $entryOrder, $_SESSION['user_id'] ?? null);
                    $_SESSION['flash_message'] = $txt['COMPETITION_ENTRY_MSG_UPDATED'] ?? 'Inscription mise a jour.';
                }
            } elseif ($subAction === 'remove') {
                $entryId = !empty($_POST['entry_id']) ? (int)$_POST['entry_id'] : 0;
                if ($entryId) {
                    $this->entryModel->deleteEntry($entryId);
                    $_SESSION['flash_message'] = $txt['COMPETITION_ENTRY_MSG_REMOVED'] ?? 'Equipe desinscrite.';
                }
            }
            return $this->redirect('sport/competition-entry/' . $editionId);
        }

        $entries        = $this->entryModel->getEntriesByEdition((int)$editionId);
        $availableTeams = $this->entryModel->getAvailableTeams((int)$editionId);
        $groups         = $this->entryModel->getGroupsForEdition((int)$editionId);

        $data = [
            'txt'            => $txt,
            'title'          => ($txt['COMPETITION_ENTRY_MANAGE_TITLE'] ?? 'Inscriptions') . ' - ' . SITENAME,
            'editionData'    => (object)$editionData,
            'entries'        => $entries,
            'availableTeams' => $availableTeams,
            'groups'         => $groups,
            'isAdmin'        => $isAdmin,
            'error'          => $_SESSION['flash_error']   ?? '',
            'message'        => $_SESSION['flash_message'] ?? '',
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competition-entry', $data);
        $this->view('system/footer',  $data);
    }
}
