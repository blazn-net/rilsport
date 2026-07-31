<?php
namespace Module\User\Controller;

use Core\Controller;

class User extends Controller
{
    private $userModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            $txt = $this->loadLanguage('user');
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
        }
        $this->userModel = $this->model('user/User');
    }

    public function index($id = null)
    {
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        
        if (!$isAdmin) {
            if ($id === null || $id != $_SESSION['user_id']) {
                $txt = $this->loadLanguage('user');
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
            }
        }

        $txt = $this->loadLanguage('user');

        $data = [
            'txt'               => $txt,
            'title'             => ($id ? ($txt['USER_EDIT_USER_TITLE'] ?? 'USER_EDIT_USER_TITLE') : ($txt['USER_ADD_USER_BTN'] ?? 'USER_ADD_USER_BTN')) . ' - ' . SITENAME,
            'user'              => null,
            'message'           => $_SESSION['flash_message'] ?? '',
            'error'             => $_SESSION['flash_error'] ?? '',
            'mode'              => $id ? 'edit' : 'add',
            'id'                => $id,
            'is_admin'          => $isAdmin,
            'available_roles'   => $this->userModel->getAllRoles(),
            'available_statuses'=> $this->userModel->getUserStatuses()
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        if ($id) {
            $userData    = $this->userModel->getUserById($id);
            $data['user'] = $userData ? (object) $userData : null;
            if (!$data['user']) {
                if ($isAdmin) {
                    $this->redirect('user/users');
                } else {
                    $this->redirect('main');
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = [
                'username' => trim($_POST['username']),
                'email'    => trim($_POST['email'])
            ];
            
            if ($isAdmin) {
                $postData['roles']     = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];
                $postData['status_id'] = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 2;
            } else {
                if ($id) {
                    $existingUser          = $this->userModel->getUserById($id);
                    $postData['roles']     = $existingUser ? (is_array($existingUser) ? $existingUser['roles'] : $existingUser->roles) : ['user'];
                    $postData['status_id'] = $existingUser ? (is_array($existingUser) ? $existingUser['status_id'] : $existingUser->status_id) : 2;
                } else {
                    $postData['roles']     = ['user'];
                    $postData['status_id'] = 2;
                }
            }

            if ($id) {
                $postData['id']       = $id;
                $postData['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

                if ($this->userModel->updateUser($postData)) {
                    if ($id == $_SESSION['user_id']) {
                        $_SESSION['username'] = $postData['username'];
                    }
                    $_SESSION['flash_message'] = $txt['USER_SUCCESS_UPDATE_USER'] ?? 'USER_SUCCESS_UPDATE_USER';
                    $this->redirect('user/' . urlencode($id));
                } else {
                    $data['error'] = $txt['USER_ERR_UPDATE_FAIL'] ?? 'USER_ERR_UPDATE_FAIL';
                }
            } else {
                $postData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                if ($this->userModel->addUser($postData)) {
                    $this->redirect('user/users');
                } else {
                    $data['error'] = $txt['USER_ERR_ADD_USER_EXISTS'] ?? 'USER_ERR_ADD_USER_EXISTS';
                }
            }

            // Repopulate user object with failed post data to avoid wiping out the form
            if (!empty($data['error'])) {
                $data['user'] = (object) $postData;
            }
        }

        $this->view('system/header', $data);
        $this->view('system/sidebar', $data);
        $this->view('user/user', $data);
        $this->view('system/footer', $data);
    }
}
