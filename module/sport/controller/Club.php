<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Club extends Controller {
    private $clubModel;
    private $sportModel;
    const MAX_FILE_SIZE = 2097152; // 2 Mo en octets

    public function __construct() {
        $this->clubModel  = $this->model('sport/Club');
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index($id = null) {
        $txt     = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        // Seuls les admins peuvent créer un nouveau club
        if (!$id && !$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé. Vous devez être administrateur.');
        }

        $clubData = null;
        $sections = [];

        if ($id !== null && $id !== '') {
            if (is_numeric($id)) {
                $clubData = $this->clubModel->getClubById((int)$id);
            } else {
                $clubData = $this->clubModel->getClubByCode($id);
            }

            if (!$clubData) {
                return $this->redirect('sport/clubs');
            }
            $sections = $this->clubModel->getClubSections($clubData['id']);
        }

        // Mode : view (consultation simple) ou edit/add (formulaire)
        $mode = $id ? ($isAdmin ? 'edit' : 'view') : 'add';

        // Traitement du formulaire POST (création / modification)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$isAdmin) {
                die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
            }

            $code         = strtolower(trim($_POST['code'] ?? ''));
            $name         = trim($_POST['name'] ?? '');
            $countryCode  = strtoupper(trim($_POST['country_code'] ?? ''));
            $cityName     = trim($_POST['city_name'] ?? '');
            $sportsChosen = isset($_POST['sports']) && is_array($_POST['sports']) ? array_map('intval', $_POST['sports']) : [];

            // Validation des champs obligatoires
            if (empty($code) || empty($name) || empty($countryCode) || empty($cityName)) {
                $_SESSION['flash_error'] = $txt['CLUB_ERR_MISSING_FIELDS'] ?? 'Veuillez remplir les champs obligatoires (Code, Nom, Pays, Ville).';
                return $this->redirect($clubData ? 'sport/club/' . $clubData['id'] : 'sport/club');
            }

            // Vérification de l'unicité du code lors de l'ajout
            if (!$clubData) {
                $existing = $this->clubModel->getClubByCode($code);
                if ($existing) {
                    $_SESSION['flash_error'] = $txt['CLUB_ERR_CODE_EXISTS'] ?? 'Un club avec cet identifiant/code existe déjà.';
                    return $this->redirect('sport/club');
                }
            }

            // Gestion de l'upload du logo
            $logoPath = $clubData['logo'] ?? null;
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['logo_file'];

                // 1. Vérification du code d'erreur PHP
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['flash_error'] = $txt['CLUB_ERR_INVALID_IMAGE'] ?? 'Erreur lors du téléversement du fichier.';
                    return $this->redirect($clubData ? 'sport/club/' . $clubData['id'] : 'sport/club');
                }

                // 2. Limitation stricte du poids à 2 Mo
                if ($file['size'] > self::MAX_FILE_SIZE) {
                    $_SESSION['flash_error'] = $txt['CLUB_ERR_FILE_TOO_LARGE'] ?? 'Le fichier est trop volumineux (2 Mo maximum).';
                    return $this->redirect($clubData ? 'sport/club/' . $clubData['id'] : 'sport/club');
                }

                // 3. Validation du type MIME réel
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = [
                    'image/png'     => 'png',
                    'image/jpeg'    => 'jpg',
                    'image/pjpeg'   => 'jpg',
                    'image/webp'    => 'webp',
                    'image/svg+xml' => 'svg'
                ];

                if (!array_key_exists($mimeType, $allowedMimes)) {
                    $_SESSION['flash_error'] = $txt['CLUB_ERR_INVALID_IMAGE'] ?? 'Format de fichier non autorisé (PNG, JPG, WEBP, SVG uniquement).';
                    return $this->redirect($clubData ? 'sport/club/' . $clubData['id'] : 'sport/club');
                }

                // 4. Déplacement sécurisé vers public/uploads/clubs/
                $extension = $allowedMimes[$mimeType];
                $uploadDir = dirname(__DIR__, 3) . '/public/uploads/clubs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = 'club_' . preg_replace('/[^a-z0-9_-]/', '', $code) . '_' . time() . '.' . $extension;
                $destination = $uploadDir . $fileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $logoPath = 'public/uploads/clubs/' . $fileName;
                }
            }

            $postData = [
                'id'              => $clubData ? $clubData['id'] : null,
                'code'            => $code,
                'name'            => $name,
                'short_name'      => trim($_POST['short_name'] ?? ''),
                'acronym'         => trim($_POST['acronym'] ?? ''),
                'foundation_year' => !empty($_POST['foundation_year']) ? (int)$_POST['foundation_year'] : null,
                'logo'            => $logoPath,
                'primary_color'   => trim($_POST['primary_color'] ?? ''),
                'secondary_color' => trim($_POST['secondary_color'] ?? ''),
                'country_code'    => $countryCode,
                'city_name'       => $cityName,
                'city_id'         => !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null,
                'postal_code'     => trim($_POST['postal_code'] ?? ''),
                'address'         => trim($_POST['address'] ?? ''),
                'website'         => trim($_POST['website'] ?? ''),
                'email'           => trim($_POST['email'] ?? ''),
                'phone'           => trim($_POST['phone'] ?? ''),
                'description'     => trim($_POST['description'] ?? ''),
                'status_id'       => isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1,
                'created_by'      => $_SESSION['user_id'] ?? null,
                'modified_by'     => $_SESSION['user_id'] ?? null
            ];

            if ($clubData) {
                $updated = $this->clubModel->updateClub($postData);
                if ($updated) {
                    $this->clubModel->syncSections($clubData['id'], $sportsChosen, $_SESSION['user_id'] ?? null);
                    $_SESSION['flash_message'] = $txt['CLUB_MSG_UPDATED'] ?? 'Club mis à jour avec succès.';
                }
                return $this->redirect('sport/club/' . $clubData['id']);
            } else {
                $newId = $this->clubModel->addClub($postData);
                if ($newId) {
                    $this->clubModel->syncSections($newId, $sportsChosen, $_SESSION['user_id'] ?? null);
                    $_SESSION['flash_message'] = $txt['CLUB_MSG_ADDED'] ?? 'Club créé avec succès.';
                    return $this->redirect('sport/club/' . $newId);
                } else {
                    $_SESSION['flash_error'] = 'Erreur lors de la création du club.';
                    return $this->redirect('sport/club');
                }
            }
        }

        $allSports     = $this->sportModel->getAllSports();
        $allCountries  = $this->clubModel->getAllCountries();
        $statuses      = $this->clubModel->getAllStatuses();
        $activeSectionSportIds = array_map(function($sec) {
            return (int)$sec['sport_id'];
        }, $sections);

        $title = $id
            ? ($isAdmin
                ? ($txt['SPORT_EDIT_CLUB_TITLE'] ?? 'Modifier le club')
                : ($txt['SPORT_VIEW_CLUB_TITLE'] ?? 'Fiche du club'))
            : ($txt['SPORT_ADD_CLUB_TITLE'] ?? 'Créer un club');

        $data = [
            'txt'             => $txt,
            'title'           => $title . ' - ' . SITENAME,
            'club'            => $clubData ? (object) $clubData : null,
            'sections'        => $sections,
            'activeSportIds'  => $activeSectionSportIds,
            'allSports'       => $allSports,
            'allCountries'    => $allCountries,
            'statuses'        => $statuses,
            'mode'            => $mode,
            'isAdmin'         => $isAdmin,
            'error'           => $_SESSION['flash_error'] ?? '',
            'message'         => $_SESSION['flash_message'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/club', $data);
        $this->view('system/footer', $data);
    }

    /**
     * Suppression / Désactivation d'un club
     */
    public function delete($id = null) {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('sport'));
        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        if (!$isAdmin || !$id) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $force = isset($_GET['force']) && $_GET['force'] == 1;
        $userId = $_SESSION['user_id'] ?? null;

        if ($force) {
            $this->clubModel->forceDeleteClub((int)$id);
            $_SESSION['flash_message'] = $txt['CLUB_MSG_DELETED'] ?? 'Club supprimé.';
        } else {
            $this->clubModel->softDeleteClub((int)$id, $userId);
            $_SESSION['flash_message'] = $txt['CLUB_MSG_DEACTIVATED'] ?? 'Club désactivé.';
        }

        $this->redirect('sport/clubs');
    }
}
