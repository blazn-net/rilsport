<?php
namespace Module\Zone\Controller;

use Core\Controller;

/**
 * Zone — Controller de gestion d'une zone géographique (CRUD + AJAX children)
 *
 * Actions disponibles :
 *   - index($id)   : redirige vers add() ou edit($id)
 *   - add()        : formulaire d'ajout d'une zone (GET/POST)
 *   - edit($id)    : formulaire de modification d'une zone (GET/POST)
 *   - delete($id)  : suppression d'une zone (contrôle des enfants)
 *   - children     : AJAX — lazy-loading des enfants
 */
class Zone extends Controller {
    private $zoneModel;
    private $geoNames;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['error' => $txt['USER_ERR_UNAUTHORIZED'] ?? 'Unauthorized']);
                exit;
            }
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $this->zoneModel = $this->model('zone/Zone');
        $this->geoNames  = $this->model('zone/GeoNamesService');
    }

    // -------------------------------------------------------
    // index($id) : routeur vers add ou edit
    // -------------------------------------------------------

    public function index($id = null) {
        if ($id === null || $id === 'add' || $id === 0 || $id === '0') {
            return $this->add();
        }

        if (is_numeric($id)) {
            return $this->edit((int) $id);
        }

        return $this->redirect('zone/zones');
    }

    // -------------------------------------------------------
    // add() : Formulaire d'ajout d'une zone
    // -------------------------------------------------------

    public function add() {
        $txt      = $this->loadLanguage('zone');
        $langCode = $_SESSION['lang_code'] ?? 'fr';

        $types       = $this->zoneModel->getZoneTypes();
        $parents     = $this->zoneModel->getPotentialParents($langCode);
        $activeLangs = array_column($this->zoneModel->getActiveLangs(), 'lang_code');

        $parentId = isset($_GET['parent_id']) ? (int) $_GET['parent_id'] : null;

        $data = [
            'txt'         => $txt,
            'title'       => ($txt['ZONE_TITLE_ZONE_ADD'] ?? 'Ajouter une zone') . ' - ' . SITENAME,
            'mode'        => 'add',
            'zone'        => null,
            'i18n'        => [],
            'types'       => $types,
            'parents'     => $parents,
            'activeLangs' => $activeLangs,
            'parentId'    => $parentId,
            'message'     => $_SESSION['flash_message'] ?? '',
            'error'       => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $nameDefault = trim($_POST['name_default'] ?? '');
            $typeCode    = trim($_POST['type_code'] ?? '');
            $parentId    = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;
            $countryCode = !empty($_POST['country_code']) ? strtoupper(trim($_POST['country_code'])) : null;
            $geonamesId  = !empty($_POST['geonames_id']) ? (int) $_POST['geonames_id'] : null;
            $statusId    = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 1;
            $namesI18n   = $_POST['name'] ?? [];

            // Si le nom par défaut est vide, utiliser la valeur dans la langue courante
            if (empty($nameDefault) && !empty($namesI18n[$langCode])) {
                $nameDefault = trim($namesI18n[$langCode]);
            }

            if (empty($nameDefault) || empty($typeCode)) {
                $data['error'] = $txt['ZONE_ERR_MISSING_FIELDS'] ?? 'Veuillez renseigner le nom et le type de zone.';
                $data['zone']  = (object) [
                    'name_default' => $nameDefault,
                    'type_code'    => $typeCode,
                    'parent_id'    => $parentId,
                    'country_code' => $countryCode,
                    'geonames_id'  => $geonamesId,
                    'status_id'    => $statusId
                ];
                $data['i18n'] = $namesI18n;
            } else {
                try {
                    $newId = $this->zoneModel->insertZone([
                        'geonames_id'  => $geonamesId,
                        'type_code'    => $typeCode,
                        'parent_id'    => $parentId,
                        'name_default' => $nameDefault,
                        'country_code' => $countryCode,
                        'status_id'    => $statusId,
                        'created_by'   => $_SESSION['user_id'] ?? null
                    ]);

                    if ($newId) {
                        // Sauvegarder les traductions
                        foreach ($activeLangs as $lang) {
                            $translated = !empty($namesI18n[$lang]) ? trim($namesI18n[$lang]) : $nameDefault;
                            $this->zoneModel->upsertZoneI18n($newId, $lang, $translated);
                        }

                        $_SESSION['flash_message'] = $txt['ZONE_MSG_ZONE_ADDED'] ?? 'Zone ajoutée avec succès.';
                        return $this->redirect('zone/zones');
                    } else {
                        $data['error'] = 'Erreur lors de la création de la zone.';
                    }
                } catch (\Exception $e) {
                    $data['error'] = 'Erreur : ' . $e->getMessage();
                }
            }
        }

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('zone/zone', $data);
        $this->view('system/footer', $data);
    }

    // -------------------------------------------------------
    // edit($id) : Formulaire de modification d'une zone
    // -------------------------------------------------------

    public function edit(int $id) {
        $txt      = $this->loadLanguage('zone');
        $langCode = $_SESSION['lang_code'] ?? 'fr';

        $zone = $this->zoneModel->getZoneById($id, $langCode);
        if (!$zone) {
            $_SESSION['flash_error'] = 'Zone introuvable.';
            return $this->redirect('zone/zones');
        }

        $types       = $this->zoneModel->getZoneTypes();
        $parents     = $this->zoneModel->getPotentialParents($langCode, $id);
        $activeLangs = array_column($this->zoneModel->getActiveLangs(), 'lang_code');
        $i18n        = $this->zoneModel->getZoneI18n($id);

        $data = [
            'txt'         => $txt,
            'title'       => ($txt['ZONE_TITLE_ZONE_EDIT'] ?? 'Modifier la zone') . ' : ' . ($zone['name'] ?? '') . ' - ' . SITENAME,
            'mode'        => 'edit',
            'zone'        => (object) $zone,
            'i18n'        => $i18n,
            'types'       => $types,
            'parents'     => $parents,
            'activeLangs' => $activeLangs,
            'parentId'    => $zone['parent_id'],
            'message'     => $_SESSION['flash_message'] ?? '',
            'error'       => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $nameDefault = trim($_POST['name_default'] ?? '');
            $typeCode    = trim($_POST['type_code'] ?? '');
            $parentId    = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;
            $countryCode = !empty($_POST['country_code']) ? strtoupper(trim($_POST['country_code'])) : null;
            $geonamesId  = !empty($_POST['geonames_id']) ? (int) $_POST['geonames_id'] : null;
            $statusId    = isset($_POST['status_id']) ? (int) $_POST['status_id'] : 1;
            $namesI18n   = $_POST['name'] ?? [];

            if (empty($nameDefault) && !empty($namesI18n[$langCode])) {
                $nameDefault = trim($namesI18n[$langCode]);
            }

            if (empty($nameDefault) || empty($typeCode)) {
                $data['error'] = $txt['ZONE_ERR_MISSING_FIELDS'] ?? 'Veuillez renseigner le nom et le type de zone.';
            } else {
                try {
                    $this->zoneModel->updateZone([
                        'id'           => $id,
                        'geonames_id'  => $geonamesId,
                        'type_code'    => $typeCode,
                        'parent_id'    => $parentId,
                        'name_default' => $nameDefault,
                        'country_code' => $countryCode,
                        'status_id'    => $statusId,
                        'modified_by'  => $_SESSION['user_id'] ?? null
                    ]);

                    // Sauvegarder les traductions
                    foreach ($activeLangs as $lang) {
                        $translated = !empty($namesI18n[$lang]) ? trim($namesI18n[$lang]) : $nameDefault;
                        $this->zoneModel->upsertZoneI18n($id, $lang, $translated);
                    }

                    $_SESSION['flash_message'] = $txt['ZONE_MSG_ZONE_UPDATED'] ?? 'Zone mise à jour avec succès.';
                    return $this->redirect('zone/zones');
                } catch (\Exception $e) {
                    $data['error'] = 'Erreur : ' . $e->getMessage();
                }
            }
        }

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('zone/zone', $data);
        $this->view('system/footer', $data);
    }

    // -------------------------------------------------------
    // delete($id) : suppression d'une zone
    // -------------------------------------------------------

    public function delete(int $id) {
        $txt      = $this->loadLanguage('zone');
        $langCode = $_SESSION['lang_code'] ?? 'fr';

        $zone = $this->zoneModel->getZoneById($id, $langCode);
        if (!$zone) {
            $_SESSION['flash_error'] = 'Zone introuvable.';
            return $this->redirect('zone/zones');
        }

        // Interdire la suppression de la racine Monde
        if ($zone['type_code'] === 'world' || empty($zone['parent_id'])) {
            $_SESSION['flash_error'] = 'La zone racine Monde ne peut pas être supprimée.';
            return $this->redirect('zone/zones');
        }

        // Vérifier si la zone a des enfants actifs
        $childrenCount = $this->zoneModel->hasChildren($id);
        if ($childrenCount > 0) {
            $_SESSION['flash_error'] = "Impossible de supprimer « {$zone['name']} » car elle contient {$childrenCount} sous-zone(s). Veuillez d'abord les déplacer ou les supprimer.";
            return $this->redirect('zone/zones');
        }

        try {
            $this->zoneModel->deleteZone($id, true);
            $_SESSION['flash_message'] = $txt['ZONE_MSG_ZONE_DELETED'] ?? 'Zone supprimée avec succès.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }

        return $this->redirect('zone/zones');
    }

    // -------------------------------------------------------
    // children : AJAX — retourne les enfants d'une zone (JSON)
    // -------------------------------------------------------

    public function children() {
        header('Content-Type: application/json');

        $parentId = isset($_GET['parent_id']) ? (int) $_GET['parent_id'] : 0;
        $langCode = $_SESSION['lang_code'] ?? 'fr';

        if (!$parentId) {
            echo json_encode(['error' => 'parent_id requis']);
            exit;
        }

        // 1. Vérifier si les enfants sont déjà en BDD
        if ($this->zoneModel->areChildrenLoaded($parentId)) {
            $children = $this->zoneModel->getChildren($parentId, $langCode);
            if (!empty($children)) {
                echo json_encode(['zones' => $children, 'cached' => true]);
                exit;
            }
        }

        // 2. Récupérer la zone parente pour obtenir son geonames_id
        $parent = $this->zoneModel->getZoneById($parentId, $langCode);
        if (!$parent || empty($parent['geonames_id'])) {
            echo json_encode(['error' => 'Zone parente introuvable ou sans geonames_id']);
            exit;
        }

        // 3. Appel GeoNames (directement avec la langue active)
        $txt = $this->loadLanguage('zone');
        $geoChildren = $this->geoNames->getChildren((int) $parent['geonames_id'], $langCode);

        if ($geoChildren === null) {
            // Erreur réseau ou API GeoNames : NE PAS marquer comme chargé pour permettre de réessayer
            echo json_encode([
                'error'   => $txt['ZONE_ERR_GEONAMES_UNAVAILABLE'] ?? 'Service GeoNames indisponible',
                'zones'   => [],
                'cached'  => false
            ]);
            exit;
        }

        if (empty($geoChildren)) {
            // Vraie zone sans enfants (feuille)
            $this->zoneModel->markChildrenLoaded($parentId);
            echo json_encode([
                'zones'   => [],
                'cached'  => false
            ]);
            exit;
        }

        // 4. Récupérer les langues actives
        $activeLangs = array_column($this->zoneModel->getActiveLangs(), 'lang_code');
        $userId      = $_SESSION['user_id'] ?? null;

        // 5. Insérer chaque enfant en BDD
        foreach ($geoChildren as $child) {
            $geonamesId  = (int) ($child['geonameId'] ?? 0);
            $toponymName = $child['toponymName'] ?? ($child['name'] ?? 'Unknown');
            $childName   = $child['name'] ?? $toponymName;
            $fcode       = $child['fcode'] ?? '';
            $fcl         = $child['fcl']   ?? '';
            $countryCode = $child['countryCode'] ?? null;
            $typeCode    = \Module\Zone\Model\GeoNamesService::mapFcodeToTypeCode($fcode, $fcl);

            if (!$geonamesId) continue;

            $zoneId = $this->zoneModel->insertZone([
                'geonames_id'  => $geonamesId,
                'type_code'    => $typeCode,
                'parent_id'    => $parentId,
                'name_default' => $toponymName,
                'country_code' => $countryCode,
                'created_by'   => $userId,
            ]);

            if (!$zoneId) continue;

            // Insérer la traduction dans la langue courante
            $this->zoneModel->upsertZoneI18n($zoneId, $langCode, $childName);

            // Pour les autres langues actives, initialiser avec toponymName si absent
            foreach ($activeLangs as $l) {
                if ($l !== $langCode) {
                    $this->zoneModel->upsertZoneI18n($zoneId, $l, $toponymName);
                }
            }
        }

        // 6. Marquer les enfants comme chargés
        $this->zoneModel->markChildrenLoaded($parentId);

        // 7. Retourner les enfants depuis la BDD (données propres)
        $children = $this->zoneModel->getChildren($parentId, $langCode);
        echo json_encode(['zones' => $children, 'cached' => false]);
        exit;
    }
}
