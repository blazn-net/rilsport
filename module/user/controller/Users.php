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
            'users'             => [],
            'message'           => '',
            'error'             => '',
            'available_roles'   => $this->userModel->getAllRoles(),
            'available_statuses'=> $this->userModel->getUserStatuses()
        ];

        // Gérer la suppression si demandée
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            if ($id !== $_SESSION['user_id']) {
                if ($this->userModel->deleteUser($id)) {
                    $data['message'] = $txt['USER_MSG_USER_DELETED'] ?? 'USER_MSG_USER_DELETED';
                } else {
                    $data['error'] = $txt['USER_ERR_DELETE_FAIL'] ?? 'USER_ERR_DELETE_FAIL';
                }
            } else {
                $data['error'] = $txt['USER_ERR_DELETE_SELF'] ?? 'USER_ERR_DELETE_SELF';
            }
        }

        $data['users'] = $this->userModel->getUsers();

        $this->view('system/header', $data);
        $this->view('system/sidebar', $data);
        $this->view('user/users', $data);
        $this->view('system/footer', $data);
    }
}
