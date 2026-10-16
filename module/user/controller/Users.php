<?php
namespace Module\User\Controller;

use Core\Controller;

class Users extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = $this->loadLanguage('user');
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
        }
        $this->userModel = $this->model('user/User');
    }

    public function index()
    {
        $txt = $this->loadLanguage('user');

        $data = [
            'txt'               => $txt,
            'title'             => ($txt['USER_USERS_LIST'] ?? 'USER_USERS_LIST') . ' - ' . SITENAME,
            'isAdmin'           => true,
            'is_admin'          => true,
            'users'             => [],
            'message'           => '',
            'error'             => '',
            'available_roles'   => $this->userModel->getAllRoles(),
            'available_statuses'=> $this->userModel->getUserStatuses()
        ];

        // Gérer les actions (désactiver, réactiver, supprimer)
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $action = $_GET['action'];
            $id = (int) $_GET['id'];
            if ($id === (int)$_SESSION['user_id'] && in_array($action, ['disable', 'delete'])) {
                $data['error'] = $txt['USER_ERR_ACTION_SELF'] ?? 'Vous ne pouvez pas effectuer cette action sur votre propre compte.';
            } else {
                if ($action === 'disable') {
                    if ($this->userModel->disableUser($id, $_SESSION['user_id'])) {
                        $data['message'] = $txt['USER_MSG_USER_DISABLED'] ?? 'Utilisateur désactivé avec succès.';
                    } else {
                        $data['error'] = $txt['USER_ERR_DISABLE_FAIL'] ?? 'Erreur lors de la désactivation.';
                    }
                } elseif ($action === 'activate') {
                    if ($this->userModel->activateUser($id, $_SESSION['user_id'])) {
                        $data['message'] = $txt['USER_MSG_USER_ACTIVATED'] ?? 'Utilisateur réactivé avec succès.';
                    } else {
                        $data['error'] = $txt['USER_ERR_ACTIVATE_FAIL'] ?? 'Erreur lors de la réactivation.';
                    }
                } elseif ($action === 'delete') {
                    if ($this->userModel->deleteUser($id)) {
                        $data['message'] = $txt['USER_MSG_USER_DELETED'] ?? 'Utilisateur supprimé avec succès.';
                    } else {
                        $data['error'] = $txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur lors de la suppression.';
                    }
                }
            }
        }

        $search        = trim($_GET['search'] ?? '');
        $data['users'] = $this->userModel->getUsers($search);
        $data['search'] = $search;

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('user/users', $data);
        $this->view('system/footer', $data);
    }
}
