<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Sport extends Controller {
    private $sportModel;

    public function __construct() {
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer un nouveau sport
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé. Vous devez être administrateur.');
        }

        $sportData = null;

        if ($id !== null && $id !== '') {
            if (is_numeric($id)) {
                $sportData = $this->sportModel->getSportById((int)$id);
            } else {
                $sportData = $this->sportModel->getSportByCode($id);
            }

            if (!$sportData) {
                return $this->redirect('sport/sports');
            }
        }

        $mode = $id ? ($isAdmin ? 'edit' : 'view') : 'add';

        $data = [
            'txt'     => $txt,
            'title'   => ($id ? ($isAdmin ? ($txt['SPORT_EDIT_SPORT_TITLE'] ?? 'Modifier le sport') : 'Fiche du sport') : ($txt['SPORT_ADD_SPORT_TITLE'] ?? 'Ajouter un sport')) . ' - ' . SITENAME,
            'sport'   => $sportData ? (object) $sportData : null,
            'mode'    => $mode,
            'isAdmin' => $isAdmin,
            'error'   => $_SESSION['flash_error'] ?? '',
            'message' => $_SESSION['flash_message'] ?? ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $postData = [
                'id'          => $sportData ? $sportData['id'] : null,
                'code'        => $sportData ? $sportData['code'] : strtolower(trim($_POST['code'] ?? '')),
                'name'        => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'icon'        => (!empty($_POST['icon']) && $_POST['icon'] !== 'mif-dribbble') ? trim($_POST['icon']) : 'mif-trophy',
                'status_id'   => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by'  => $_SESSION['user_id'] ?? null,
                'modified_by' => $_SESSION['user_id'] ?? null
            ];

            if (empty($postData['code']) || empty($postData['name'])) {
                $data['error'] = $txt['SPORT_ERR_MISSING_FIELDS'] ?? 'Veuillez remplir tous les champs obligatoires (Code et Nom).';
                $data['sport'] = (object) $_POST;
            } else {
                try {
                    if ($id) {
                        $this->sportModel->updateSport($postData);
                        $_SESSION['flash_message'] = $txt['SPORT_MSG_SPORT_UPDATED'] ?? 'Sport mis à jour avec succès.';
                        return $this->redirect('sport/' . $sportData['id']);
                    } else {
                        // Vérifier si le code existe déjà
                        $existing = $this->sportModel->getSportByCode($postData['code']);
                        if ($existing) {
                            $data['error'] = $txt['SPORT_ERR_CODE_EXISTS'] ?? 'Un sport avec ce code existe déjà.';
                            $data['sport'] = (object) $_POST;
                        } else {
                            $this->sportModel->addSport($postData);
                            $_SESSION['flash_message'] = $txt['SPORT_MSG_SPORT_ADDED'] ?? 'Sport ajouté avec succès.';
                            return $this->redirect('sport/sports');
                        }
                    }
                } catch (\Exception $e) {
                    $data['error'] = ($txt['USER_ERR_UPDATE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
                    $data['sport'] = (object) $_POST;
                }
            }
        }

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/sport', $data);
        $this->view('system/footer', $data);
    }

    public function delete($id = null) {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        if (!empty($id)) {
            try {
                $userId = $_SESSION['user_id'] ?? null;
                $targetId = is_numeric($id) ? (int)$id : null;
                if (!$targetId) {
                    $s = $this->sportModel->getSportByCode($id);
                    $targetId = $s ? $s['id'] : null;
                }

                if ($targetId) {
                    $this->sportModel->softDeleteSport($targetId, $userId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_SPORT_DEACTIVATED'] ?? 'Sport désactivé.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/sports');
    }

    public function forcedelete($id = null) {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        if (!empty($id)) {
            try {
                $targetId = is_numeric($id) ? (int)$id : null;
                if (!$targetId) {
                    $s = $this->sportModel->getSportByCode($id);
                    $targetId = $s ? $s['id'] : null;
                }

                if ($targetId) {
                    $this->sportModel->forceDeleteSport($targetId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_SPORT_DELETED'] ?? 'Sport supprimé.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/sports');
    }
}
