<?php
namespace Module\Sport\Controller;

use Core\EntityController;

class Competition extends EntityController {
    private $competitionModel;
    private $sportModel;
    const MAX_FILE_SIZE = 2097152; // 2 Mo

    public function __construct() {
        $this->competitionModel = $this->model('sport/Competition');
        $this->sportModel       = $this->model('sport/Sport');
    }

    public function index($id = null, $action = null) {
        $txt     = $this->loadLanguages(['sport']);
        $isAdmin = $this->isAdmin();

        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
        }

        $competitionData = $this->resolveEntity(
            [$this->competitionModel, 'getCompetitionByCode'],
            [$this->competitionModel, 'getCompetitionById'],
            $id,
            'sport/competition',
            'sport/competitions',
            $action
        );
        $editions = $competitionData ? $this->competitionModel->getEditionsByCompetition($competitionData['id']) : [];

        $isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');
        $mode   = $this->resolveMode($id, $action, $isPost);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
            }

            $code      = strtolower(trim($_POST['code'] ?? ''));
            $name      = trim($_POST['name'] ?? '');
            $sportId   = !empty($_POST['sport_id']) ? (int)$_POST['sport_id'] : 0;
            $typeCode  = trim($_POST['type_code'] ?? '');

            if (empty($code) || empty($name) || empty($sportId) || empty($typeCode)) {
                $_SESSION['flash_error'] = $txt['COMPETITION_ERR_MISSING_FIELDS'] ?? 'Veuillez remplir les champs obligatoires.';
                return $this->redirect($competitionData ? 'sport/competition/edit/' . ($competitionData['code'] ?? $competitionData['id']) : 'sport/competition');
            }

            if (!$competitionData) {
                $existing = $this->competitionModel->getCompetitionByCode($code);
                if ($existing) {
                    $_SESSION['flash_error'] = $txt['COMPETITION_ERR_CODE_EXISTS'] ?? 'Ce code existe deja.';
                    return $this->redirect('sport/competition');
                }
            }

            // Gestion du logo
            $logoPath = $competitionData['logo'] ?? null;
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['logo_file'];
                if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= self::MAX_FILE_SIZE) {
                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                    $allowedMimes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'];
                    if (array_key_exists($mimeType, $allowedMimes)) {
                        $ext       = $allowedMimes[$mimeType];
                        $uploadDir = dirname(__DIR__, 3) . '/public/uploads/competitions/';
                        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
                        $fileName = 'competition_' . preg_replace('/[^a-z0-9_-]/', '', $code) . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                            $logoPath = 'public/uploads/competitions/' . $fileName;
                        }
                    }
                }
            }

            $postData = [
                'id'           => $competitionData ? $competitionData['id'] : null,
                'code'         => $code,
                'name'         => $name,
                'short_name'   => trim($_POST['short_name'] ?? ''),
                'acronym'      => trim($_POST['acronym'] ?? ''),
                'sport_id'     => $sportId,
                'type_code'    => $typeCode,
                'logo'         => $logoPath,
                'country_code' => trim($_POST['country_code'] ?? ''),
                'description'  => trim($_POST['description'] ?? ''),
                'status_id'    => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by'   => $_SESSION['user_id'] ?? null,
                'modified_by'  => $_SESSION['user_id'] ?? null,
            ];

            if ($competitionData) {
                $this->competitionModel->updateCompetition($postData);
                $_SESSION['flash_message'] = $txt['COMPETITION_MSG_UPDATED'] ?? 'Competiton mise a jour.';
                return $this->redirect('sport/competition/' . ($competitionData['code'] ?? $competitionData['id']));
            } else {
                $newId = $this->competitionModel->addCompetition($postData);
                if ($newId) {
                    $_SESSION['flash_message'] = $txt['COMPETITION_MSG_ADDED'] ?? 'Competition creee.';
                    return $this->redirect('sport/competition/' . ($postData['code'] ?? $newId));
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de la creation.';
                    return $this->redirect('sport/competition');
                }
            }
        }

        $allSports  = $this->sportModel->getAllSports();
        $allTypes   = $this->competitionModel->getAllTypes();
        $statuses   = $this->competitionModel->getAllStatuses();
        $countries  = $this->competitionModel->getAllCountries();

        $title = $id
            ? ($mode === 'edit'
                ? ($txt['COMPETITION_EDIT_TITLE'] ?? 'Modifier la competition')
                : ($txt['COMPETITION_VIEW_TITLE'] ?? 'Fiche competition'))
            : ($txt['COMPETITION_ADD_TITLE'] ?? 'Creer une competition');

        $data = [
            'txt'         => $txt,
            'title'       => $title . ' - ' . SITENAME,
            'competition' => $competitionData ? (object) $competitionData : null,
            'editions'    => $editions,
            'allSports'   => $allSports,
            'allTypes'    => $allTypes,
            'statuses'    => $statuses,
            'countries'   => $countries,
            'mode'        => $mode,
            'isAdmin'     => $isAdmin,
            'error'       => $_SESSION['flash_error']   ?? '',
            'message'     => $_SESSION['flash_message'] ?? '',
        ];

        $this->renderEntity('sport/competition', $data);
    }

    public function delete($id = null) {
        $txt     = $this->loadLanguages(['sport']);
        $isAdmin = $this->isAdmin();

        if (!$isAdmin || !$id) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Acces non autorise.');
        }

        $force  = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->competitionModel->forceDeleteCompetition((int)$id);
            $_SESSION['flash_message'] = $txt['COMPETITION_MSG_DELETED'] ?? 'Competition supprimee.';
        } else {
            $this->competitionModel->softDeleteCompetition((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['COMPETITION_MSG_DEACTIVATED'] ?? 'Competition desactivee.';
        }

        $this->redirect('sport/competitions');
    }
}
