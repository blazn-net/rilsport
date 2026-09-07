<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Team extends Controller {
    private $teamModel;

    public function __construct() {
        $this->teamModel = $this->model('sport/Team');
    }

    public function index($id = null) {
        $txt     = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer une nouvelle équipe
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé. Vous devez être administrateur.');
        }

        $teamData = null;

        if ($id !== null && $id !== '') {
            if (is_numeric($id)) {
                $teamData = $this->teamModel->getTeamById((int)$id);
            } else {
                $teamData = $this->teamModel->getTeamByCode($id);
            }

            if (!$teamData) {
                return $this->redirect('sport/teams');
            }
        }

        $mode = $id ? ($isAdmin ? 'edit' : 'view') : 'add';

        // Traitement du formulaire POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $sectionId = !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null;
            $code      = strtolower(trim($_POST['code'] ?? ''));
            $name      = trim($_POST['name'] ?? '');

            if (empty($sectionId) || empty($code) || empty($name)) {
                $_SESSION['flash_error'] = $txt['TEAM_ERR_MISSING_FIELDS'] ?? 'Veuillez remplir tous les champs obligatoires (Section, Code, Nom).';
                return $this->redirect($teamData ? 'sport/team/' . $teamData['id'] : 'sport/team');
            }

            // Vérification de l'unicité du code lors de la création
            if (!$teamData) {
                $existing = $this->teamModel->getTeamByCode($code);
                if ($existing) {
                    $_SESSION['flash_error'] = $txt['TEAM_ERR_CODE_EXISTS'] ?? 'Une équipe avec cet identifiant/code existe déjà.';
                    return $this->redirect('sport/team');
                }
            }

            $postData = [
                'id'         => $teamData ? $teamData['id'] : null,
                'section_id' => $sectionId,
                'code'       => $code,
                'name'       => $name,
                'short_name' => trim($_POST['short_name'] ?? ''),
                'gender'     => trim($_POST['gender'] ?? 'M'),
                'category'   => trim($_POST['category'] ?? 'Senior'),
                'level'      => trim($_POST['level'] ?? 'National'),
                'status_id'  => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by' => $_SESSION['user_id'] ?? null,
                'modified_by'=> $_SESSION['user_id'] ?? null
            ];

            if ($teamData) {
                $updated = $this->teamModel->updateTeam($postData);
                if ($updated) {
                    $_SESSION['flash_message'] = $txt['TEAM_MSG_UPDATED'] ?? 'L\'équipe a été mise à jour avec succès.';
                }
                return $this->redirect('sport/team/' . $teamData['id']);
            } else {
                $newId = $this->teamModel->addTeam($postData);
                if ($newId) {
                    $_SESSION['flash_message'] = $txt['TEAM_MSG_ADDED'] ?? 'L\'équipe a été créée avec succès.';
                    return $this->redirect('sport/team/' . $newId);
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de la création de l\'équipe.';
                    return $this->redirect('sport/team');
                }
            }
        }

        $allSections = $this->teamModel->getAllSections();
        $statuses    = $this->teamModel->getAllStatuses();

        $title = $id
            ? ($isAdmin
                ? ($txt['SPORT_EDIT_TEAM_TITLE'] ?? 'Modifier l\'équipe')
                : ($txt['SPORT_VIEW_TEAM_TITLE'] ?? 'Fiche de l\'équipe'))
            : ($txt['SPORT_ADD_TEAM_TITLE'] ?? 'Créer une équipe');

        $data = [
            'txt'         => $txt,
            'title'       => $title . ' - ' . SITENAME,
            'team'        => $teamData ? (object) $teamData : null,
            'allSections' => $allSections,
            'statuses'    => $statuses,
            'mode'        => $mode,
            'isAdmin'     => $isAdmin,
            'error'       => $_SESSION['flash_error'] ?? '',
            'message'     => $_SESSION['flash_message'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/team', $data);
        $this->view('system/footer', $data);
    }

    /**
     * Suppression / Désactivation d'une équipe
     */
    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin || !$id) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $force  = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->teamModel->forceDeleteTeam((int)$id);
            $_SESSION['flash_message'] = $txt['TEAM_MSG_DELETED'] ?? 'L\'équipe a été supprimée.';
        } else {
            $this->teamModel->softDeleteTeam((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['TEAM_MSG_DEACTIVATED'] ?? 'L\'équipe a été désactivée.';
        }

        $this->redirect('sport/teams');
    }
}
