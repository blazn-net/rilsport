<?php
namespace Module\User\Controller;

use Core\ListController;

class Users extends ListController
{
    private $userModel;

    public function __construct()
    {
        if (!$this->isAdmin()) {
            $txt = $this->loadLanguages();
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'USER_ERR_UNAUTHORIZED');
        }
        $this->userModel = $this->model('user/User');
    }

    public function index()
    {
        $txt = $this->loadLanguages();

        // Gérer les actions (désactiver, réactiver, supprimer)
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $action = $_GET['action'];
            $id = (int) $_GET['id'];
            if ($id === (int)$_SESSION['user_id'] && in_array($action, ['disable', 'delete'])) {
                $_SESSION['flash_error'] = $txt['USER_ERR_ACTION_SELF'] ?? 'Vous ne pouvez pas effectuer cette action sur votre propre compte.';
            } else {
                if ($action === 'disable') {
                    if ($this->userModel->disableUser($id, $_SESSION['user_id'])) {
                        $_SESSION['flash_message'] = $txt['USER_MSG_USER_DISABLED'] ?? 'Utilisateur désactivé avec succès.';
                    } else {
                        $_SESSION['flash_error'] = $txt['USER_ERR_DISABLE_FAIL'] ?? 'Erreur lors de la désactivation.';
                    }
                } elseif ($action === 'activate') {
                    if ($this->userModel->activateUser($id, $_SESSION['user_id'])) {
                        $_SESSION['flash_message'] = $txt['USER_MSG_USER_ACTIVATED'] ?? 'Utilisateur réactivé avec succès.';
                    } else {
                        $_SESSION['flash_error'] = $txt['USER_ERR_ACTIVATE_FAIL'] ?? 'Erreur lors de la réactivation.';
                    }
                } elseif ($action === 'delete') {
                    if ($this->userModel->deleteUser($id)) {
                        $_SESSION['flash_message'] = $txt['USER_MSG_USER_DELETED'] ?? 'Utilisateur supprimé avec succès.';
                    } else {
                        $_SESSION['flash_error'] = $txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur lors de la suppression.';
                    }
                }
            }
        }

        $search = trim($_GET['search'] ?? '');
        $users  = $this->userModel->getUsers($search);

        $data = $this->prepareListData(
            $txt,
            $txt['USER_USERS_LIST'] ?? 'Utilisateurs',
            [
                'users'              => $users,
                'available_roles'    => $this->userModel->getAllRoles(),
                'available_statuses' => $this->userModel->getUserStatuses(),
            ]
        );

        $this->renderList('user/users', $data);
    }
}
