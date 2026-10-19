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

    public function index($id = null, $action = null)
    {
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
        
        $userData = null;
        if ($id !== null && $id !== '') {
            $userData = is_numeric($id)
                ? $this->userModel->getUserById((int)$id)
                : $this->userModel->findUserByEmailOrUsername($id);
            if (!$userData) {
                if ($isAdmin) {
                    return $this->redirect('user/users');
                } else {
                    return $this->redirect('main');
                }
            }
        }

        if (!$isAdmin) {
            if ($id === null || ((int)($userData['id'] ?? 0) !== (int)$_SESSION['user_id'])) {
                $txt = $this->loadLanguage('user');
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
            }
        }

        // Redirige vers le username si appelé avec un identifiant numérique
        if ($id !== null && is_numeric($id) && !empty($userData['username']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('user/' . ($action === 'edit' ? 'edit/' : '') . urlencode($userData['username']));
        }

        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));

        $userObj = $userData ? (object) $userData : null;
        if ($userObj) {
            $fullName = trim(($userObj->prenom ?? '') . ' ' . ($userObj->nom ?? ''));
            $userObj->displayName = ($fullName !== '') ? $fullName : ($userObj->username ?? '');
        }

        $isPost  = ($_SERVER['REQUEST_METHOD'] === 'POST');
        $canEdit = $isAdmin || ($userData !== null && (int)$userData['id'] === (int)$_SESSION['user_id']);
        $mode    = $id ? ((($action === 'edit' || $isPost) && $canEdit) ? 'edit' : 'view') : 'add';

        $title = $id
            ? ($mode === 'edit'
                ? ($txt['USER_EDIT_USER_TITLE'] ?? 'Modifier l\'utilisateur')
                : ('Fiche de l\'utilisateur : ' . ($userObj->displayName ?? '')))
            : ($txt['USER_ADD_USER_BTN'] ?? 'Ajouter un utilisateur');

        $data = [
            'txt'               => $txt,
            'title'             => $title . ' - ' . SITENAME,
            'user'              => $userObj,
            'message'           => $_SESSION['flash_message'] ?? '',
            'error'             => $_SESSION['flash_error'] ?? '',
            'mode'              => $mode,
            'id'                => $id,
            'isAdmin'           => $isAdmin,
            'is_admin'          => $isAdmin,
            'available_roles'   => $this->userModel->getAllRoles(),
            'available_statuses'=> $this->userModel->getUserStatuses()
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$canEdit) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $postData = [
                'username' => trim($_POST['username'] ?? ''),
                'email'    => trim($_POST['email'] ?? ''),
                'nom'      => trim($_POST['nom'] ?? ''),
                'prenom'   => trim($_POST['prenom'] ?? '')
            ];
            
            if ($isAdmin) {
                $postData['roles']     = isset($_POST['roles']) && is_array($_POST['roles']) ? $_POST['roles'] : [];
                $postData['status_id'] = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 2;
            } else {
                if ($id) {
                    $postData['roles']     = $userObj ? ($userObj->roles ?? ['user']) : ['user'];
                    $postData['status_id'] = $userObj ? ($userObj->status_id ?? 1) : 1;
                } else {
                    $postData['roles']     = ['user'];
                    $postData['status_id'] = 2;
                }
            }

            if ($userData) {
                $postData['id']       = (int)$userData['id'];
                $postData['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

                if ($this->userModel->updateUser($postData)) {
                    if ($userData['id'] == $_SESSION['user_id']) {
                        $_SESSION['username'] = $postData['username'];
                    }
                    $_SESSION['flash_message'] = $txt['USER_SUCCESS_UPDATE_USER'] ?? 'USER_SUCCESS_UPDATE_USER';
                    return $this->redirect('user/' . urlencode($postData['username'] ?? $userData['username']));
                } else {
                    $data['error'] = $txt['USER_ERR_UPDATE_FAIL'] ?? 'USER_ERR_UPDATE_FAIL';
                }
            } else {
                $postData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                if ($this->userModel->addUser($postData)) {
                    return $this->redirect('user/users');
                } else {
                    $data['error'] = $txt['USER_ERR_ADD_USER_EXISTS'] ?? 'USER_ERR_ADD_USER_EXISTS';
                }
            }

            // Repopulate user object with failed post data to avoid wiping out the form
            if (!empty($data['error'])) {
                $data['user'] = (object) array_merge((array)($userObj ?? []), $postData);
                $data['mode'] = 'edit';
            }
        }

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('user/user', $data);
        $this->view('system/footer', $data);
    }

    /**
     * Accès direct au formulaire d'édition
     */
    public function edit($id = null)
    {
        return $this->index($id, 'edit');
    }
}
