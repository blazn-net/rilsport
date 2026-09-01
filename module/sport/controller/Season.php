<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Season extends Controller {
    private $seasonModel;

    public function __construct() {
        $this->seasonModel = $this->model('sport/Season');
    }

    public function index($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer une nouvelle saison
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé. Vous devez être administrateur.');
        }

        $seasonData = null;

        if ($id !== null && $id !== '') {
            if (is_numeric($id)) {
                $seasonData = $this->seasonModel->getSeasonById((int)$id);
            } else {
                $seasonData = $this->seasonModel->getSeasonByCode($id);
            }

            if (!$seasonData) {
                return $this->redirect('sport/seasons');
            }
        }

        $mode = $id ? ($isAdmin ? 'edit' : 'view') : 'add';

        $data = [
            'txt'     => $txt,
            'title'   => ($id ? ($isAdmin ? ($txt['SPORT_EDIT_SEASON_TITLE'] ?? 'Modifier la saison') : 'Fiche de la saison') : ($txt['SPORT_ADD_SEASON_TITLE'] ?? 'Ajouter une saison')) . ' - ' . SITENAME,
            'season'  => $seasonData ? (object) $seasonData : null,
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
                'id'          => $seasonData ? $seasonData['id'] : null,
                'code'        => $seasonData ? $seasonData['code'] : strtolower(trim($_POST['code'] ?? '')),
                'name'        => trim($_POST['name'] ?? ''),
                'date_start'  => trim($_POST['date_start'] ?? ''),
                'date_end'    => trim($_POST['date_end'] ?? ''),
                'status_id'   => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by'  => $_SESSION['user_id'] ?? null,
                'modified_by' => $_SESSION['user_id'] ?? null
            ];

            if (empty($postData['code']) || empty($postData['name']) || empty($postData['date_start']) || empty($postData['date_end'])) {
                $data['error']  = $txt['SPORT_ERR_SEASON_MISSING_FIELDS'] ?? 'Veuillez remplir tous les champs obligatoires (Code, Nom, Date de début, Date de fin).';
                $data['season'] = (object) $_POST;
            } elseif ($postData['date_start'] > $postData['date_end']) {
                $data['error']  = $txt['SPORT_ERR_DATES_INVALID'] ?? 'La date de début doit être antérieure à la date de fin.';
                $data['season'] = (object) $_POST;
            } else {
                try {
                    if ($id) {
                        $this->seasonModel->updateSeason($postData);
                        $_SESSION['flash_message'] = $txt['SPORT_MSG_SEASON_UPDATED'] ?? 'Saison mise à jour avec succès.';
                        return $this->redirect('sport/season/' . $seasonData['id']);
                    } else {
                        // Vérifier si le code existe déjà
                        $existing = $this->seasonModel->getSeasonByCode($postData['code']);
                        if ($existing) {
                            $data['error']  = $txt['SPORT_ERR_SEASON_CODE_EXISTS'] ?? 'Une saison avec ce code existe déjà.';
                            $data['season'] = (object) $_POST;
                        } else {
                            $this->seasonModel->addSeason($postData);
                            $_SESSION['flash_message'] = $txt['SPORT_MSG_SEASON_ADDED'] ?? 'Saison ajoutée avec succès.';
                            return $this->redirect('sport/seasons');
                        }
                    }
                } catch (\Exception $e) {
                    $data['error']  = ($txt['USER_ERR_UPDATE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
                    $data['season'] = (object) $_POST;
                }
            }
        }

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/season', $data);
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
                    $s        = $this->seasonModel->getSeasonByCode($id);
                    $targetId = $s ? $s['id'] : null;
                }

                if ($targetId) {
                    $this->seasonModel->softDeleteSeason($targetId, $userId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_SEASON_DEACTIVATED'] ?? 'Saison désactivée.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/seasons');
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
                    $s        = $this->seasonModel->getSeasonByCode($id);
                    $targetId = $s ? $s['id'] : null;
                }

                if ($targetId) {
                    $this->seasonModel->forceDeleteSeason($targetId);
                    $_SESSION['flash_message'] = $txt['SPORT_MSG_SEASON_DELETED'] ?? 'Saison supprimée.';
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = ($txt['USER_ERR_DELETE_FAIL'] ?? 'Erreur : ') . $e->getMessage();
            }
        }
        return $this->redirect('sport/seasons');
    }
}
