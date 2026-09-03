<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Person extends Controller {
    private $personModel;

    public function __construct() {
        $this->personModel = $this->model('sport/Person');
    }

    public function index($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer une nouvelle personne
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé. Vous devez être administrateur.');
        }

        $personData = null;

        if ($id !== null && $id !== '') {
            if (is_numeric($id)) {
                $personData = $this->personModel->getPersonById((int)$id);
            } else {
                $personData = $this->personModel->getPersonByCode($id);
            }

            if (!$personData) {
                return $this->redirect('sport/persons');
            }
        }

        $roles = $this->personModel->getAllRoles();
        $mode  = $id ? ($isAdmin ? 'edit' : 'view') : 'add';

        $data = [
            'txt'     => $txt,
            'title'   => ($id ? ($isAdmin ? ($txt['SPORT_EDIT_PERSON_TITLE'] ?? 'Modifier la personne') : 'Fiche de la personne') : ($txt['SPORT_ADD_PERSON_TITLE'] ?? 'Ajouter une personne')) . ' - ' . SITENAME,
            'person'  => $personData ? (object) $personData : null,
            'roles'   => $roles,
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
                'id'          => $personData ? $personData['id'] : null,
                'code'        => $personData ? $personData['code'] : strtolower(trim($_POST['code'] ?? '')),
                'first_name'  => trim($_POST['first_name'] ?? ''),
                'last_name'   => trim($_POST['last_name'] ?? ''),
                'gender'      => trim($_POST['gender'] ?? 'M'),
                'birth_date'  => trim($_POST['birth_date'] ?? ''),
                'nationality' => trim($_POST['nationality'] ?? ''),
                'role_code'   => trim($_POST['role_code'] ?? 'player'),
                'status_id'   => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by'  => $_SESSION['user_id'] ?? null,
                'modified_by' => $_SESSION['user_id'] ?? null
            ];

            if (empty($postData['code']) || empty($postData['first_name']) || empty($postData['last_name'])) {
                $data['error']  = $txt['SPORT_ERR_PERSON_MISSING_FIELDS'] ?? 'Veuillez remplir tous les champs obligatoires (Code, Prénom, Nom).';
                $data['person'] = (object) $_POST;
            } else {
                try {
                    if ($id) {
                        $this->personModel->updatePerson($postData);
                        $_SESSION['flash_message'] = $txt['SPORT_MSG_PERSON_UPDATED'] ?? 'Personne mise à jour avec succès.';
                        return $this->redirect('sport/person/' . $personData['id']);
                    } else {
                        // Vérifier si le code existe déjà
                        $existing = $this->personModel->getPersonByCode($postData['code']);
                        if ($existing) {
                            $data['error']  = $txt['SPORT_ERR_PERSON_CODE_EXISTS'] ?? 'Une personne avec ce code existe déjà.';
                            $data['person'] = (object) $_POST;
                        } else {
                            $this->personModel->addPerson($postData);
                            $_SESSION['flash_message'] = $txt['SPORT_MSG_PERSON_ADDED'] ?? 'Personne ajoutée avec succès.';
                            return $this->redirect('sport/persons');
                        }
                    }
                } catch (\Exception $e) {
                    $data['error']  = ($txt['USER_ERR_UPDATE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
                    $data['person'] = (object) $_POST;
                }
            }
        }

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/person', $data);
        $this->view('system/footer', $data);
    }

    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        if (!empty($id)) {
            try {
                $userId   = $_SESSION['user_id'] ?? null;
                $targetId = is_numeric($id) ? (int)$id : null;
                if (!$targetId) {
                    $p        = $this->personModel->getPersonByCode($id);
                    $targetId = $p ? $p['id'] : null;
                }

                if ($targetId) {
                    $this->personModel->softDeletePerson($targetId, $userId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_PERSON_DEACTIVATED'] ?? 'Personne désactivée.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/persons');
    }

    public function forcedelete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        if (!empty($id)) {
            try {
                $targetId = is_numeric($id) ? (int)$id : null;
                if (!$targetId) {
                    $p        = $this->personModel->getPersonByCode($id);
                    $targetId = $p ? $p['id'] : null;
                }

                if ($targetId) {
                    $this->personModel->forceDeletePerson($targetId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_PERSON_DELETED'] ?? 'Personne supprimée.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/persons');
    }
}
