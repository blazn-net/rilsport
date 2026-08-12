<?php
namespace Module\User\Controller;

use Core\Controller;

class Auth extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('user/User');
    }

    public function index() {
        $this->redirect('user/login');
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('main');
        }

        $txt = $this->loadLanguage('user');
        
        $data = [
            'txt'      => $txt,
            'title'    => ($txt['SYS_LOGIN'] ?? 'SYS_LOGIN') . ' - ' . SITENAME,
            'login'    => '',
            'password' => '',
            'error'    => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['login']    = trim($_POST['login']);
            $data['password'] = $_POST['password'];

            $user = $this->userModel->findUserByEmailOrUsername($data['login']);

            if ($user && password_verify($data['password'], is_array($user) ? $user['password_hash'] : $user->password_hash)) {
                $status_id = is_array($user) ? $user['status_id'] : $user->status_id;
                if ($status_id == 1) {
                    $_SESSION['user_id']  = is_array($user) ? $user['id'] : $user->id;
                    $_SESSION['username'] = is_array($user) ? $user['username'] : $user->username;
                    $_SESSION['roles']    = is_array($user) ? $user['roles'] : $user->roles;
                    $this->redirect('main');
                } else {
                    $data['error'] = $txt['ERR_ACCOUNT_NOT_VALIDATED'] ?? 'Le compte n\'est pas validé.';
                }
            } else {
                $data['error'] = $txt['USER_ERR_INVALID_CREDS'] ?? 'USER_ERR_INVALID_CREDS';
            }
        }

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('user/login', $data);
        $this->view('system/footer', $data);
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('main');
        }

        $txt = $this->loadLanguage('user');

        $data = [
            'txt'              => $txt,
            'title'            => ($txt['SYS_REGISTER'] ?? 'SYS_REGISTER') . ' - ' . SITENAME,
            'username'         => '',
            'email'            => '',
            'password'         => '',
            'password_confirm' => '',
            'error'            => '',
            'success'          => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data['username']         = trim($_POST['username']);
            $data['email']            = trim($_POST['email']);
            $data['password']         = $_POST['password'];
            $data['password_confirm'] = $_POST['password_confirm'];

            if ($data['password'] !== $data['password_confirm']) {
                $data['error'] = $txt['USER_ERR_PASSWORDS_MISMATCH'] ?? 'USER_ERR_PASSWORDS_MISMATCH';
            } elseif (strlen($data['password']) < 6) {
                $data['error'] = $txt['USER_ERR_PASSWORD_LENGTH'] ?? 'USER_ERR_PASSWORD_LENGTH';
            } else {
                if ($this->userModel->findUserByEmailOrUsername($data['email']) || $this->userModel->findUserByEmailOrUsername($data['username'])) {
                    $data['error'] = $txt['USER_ERR_USER_EXISTS'] ?? 'USER_ERR_USER_EXISTS';
                } else {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                    if ($this->userModel->register($data)) {
                        $data['success'] = $txt['USER_SUCCESS_REGISTER'] ?? 'USER_SUCCESS_REGISTER';
                    } else {
                        $data['error'] = $txt['USER_ERR_REGISTER_FAIL'] ?? 'USER_ERR_REGISTER_FAIL';
                    }
                }
            }
        }

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('user/register', $data);
        $this->view('system/footer', $data);
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('user/login');
    }
}
