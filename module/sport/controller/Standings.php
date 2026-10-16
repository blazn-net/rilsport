<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Standings extends Controller {
    private $standingModel;

    public function __construct() {
        $this->standingModel = $this->model('sport/Standing');
    }

    public function index() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $groups   = $this->standingModel->getGroupsList();
        $groupId  = !empty($_GET['group']) ? (int)$_GET['group'] : (!empty($groups[0]['id']) ? (int)$groups[0]['id'] : null);

        $standings     = $groupId ? $this->standingModel->getStandingsByGroup($groupId) : [];
        $selectedGroup = null;

        if ($groupId) {
            foreach ($groups as $g) {
                if ((int)$g['id'] === $groupId) {
                    $selectedGroup = $g;
                    break;
                }
            }
        }

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'           => $txt,
            'title'         => ($txt['STANDING_LBL_STANDINGS'] ?? 'Classements') . ' - ' . SITENAME,
            'groups'        => $groups,
            'selectedGroup' => $selectedGroup,
            'groupId'       => $groupId,
            'standings'     => $standings,
            'isAdmin'       => $isAdmin,
            'message'       => $_SESSION['flash_message'] ?? '',
            'error'         => $_SESSION['flash_error']   ?? ''
        ];

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',   $data);
        $this->view('system/navview',  $data);
        $this->view('sport/standings', $data);
        $this->view('system/footer',   $data);
    }

    public function recalculate() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('sport')
        );

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $groupId = !empty($_GET['group']) ? (int)$_GET['group'] : (!empty($_POST['group_id']) ? (int)$_POST['group_id'] : 0);
        if ($groupId > 0) {
            $success = $this->standingModel->recalculateGroupStandings($groupId, $_SESSION['user_id'] ?? null);
            if ($success) {
                $_SESSION['flash_message'] = $txt['STANDING_MSG_RECALCULATED'] ?? 'Classement recalculé avec succès.';
            } else {
                $_SESSION['flash_error'] = 'Aucune donnée trouvée pour recalculer ce groupe.';
            }
        }

        return $this->redirect('sport/standings' . ($groupId ? '?group=' . $groupId : ''));
    }
}
