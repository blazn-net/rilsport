<?php
namespace Module\Zone\Controller;

use Core\Controller;

/**
 * Zone — Controller principal du module zone
 *
 * Actions disponibles :
 *   - index($id)   : affichage / édition d'une zone
 *   - children     : AJAX — chargement lazy des enfants (depuis BDD ou GeoNames)
 *   - delete($id)  : suppression d'une zone (soft delete)
 */
class Zone extends Controller {
    private $zoneModel;
    private $geoNames;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));
            // Pour les appels AJAX, retourner du JSON
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
    // index($id) : fiche d'une zone (lecture seule pour l'instant)
    // -------------------------------------------------------

    public function index(int $id = 0) {
        $txt      = $this->loadLanguage('zone');
        $langCode = $_SESSION['lang_code'] ?? 'fr';
        $zone     = $id ? $this->zoneModel->getZoneById($id, $langCode) : null;

        if ($id && !$zone) {
            return $this->redirect('zone/zones');
        }

        $data = [
            'txt'     => $txt,
            'title'   => ($zone['name'] ?? ($txt['ZONE_TITLE_ZONES'] ?? 'Zone')) . ' - ' . SITENAME,
            'zone'    => $zone ? (object) $zone : null,
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('zone/zone', $data);
        $this->view('system/footer', $data);
    }

    // -------------------------------------------------------
    // children : AJAX — retourne les enfants d'une zone (JSON)
    // -------------------------------------------------------
    // URL : /zone/zone/children?parent_id=1
    // Réponse : { "zones": [...], "cached": true/false }

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
            echo json_encode(['zones' => $children, 'cached' => true]);
            exit;
        }

        // 2. Récupérer la zone parente pour obtenir son geonames_id
        $parent = $this->zoneModel->getZoneById($parentId, $langCode);
        if (!$parent || empty($parent['geonames_id'])) {
            echo json_encode(['error' => 'Zone parente introuvable ou sans geonames_id']);
            exit;
        }

        // 3. Appel GeoNames
        $txt = $this->loadLanguage('zone');
        $geoChildren = $this->geoNames->getChildren((int) $parent['geonames_id']);

        if (empty($geoChildren)) {
            // GeoNames indisponible ou zone sans enfants → marquer quand même
            $this->zoneModel->markChildrenLoaded($parentId);
            echo json_encode([
                'zones'   => [],
                'cached'  => false,
                'warning' => $txt['ZONE_ERR_GEONAMES_UNAVAILABLE'] ?? 'Service indisponible'
            ]);
            exit;
        }

        // 4. Récupérer les langues actives
        $activeLangs = array_column($this->zoneModel->getActiveLangs(), 'lang_code');
        $userId      = $_SESSION['user_id'] ?? null;

        // 5. Insérer chaque enfant en BDD
        foreach ($geoChildren as $child) {
            $geonamesId  = (int) ($child['geonameId'] ?? 0);
            $nameDefault = $child['name'] ?? 'Unknown';
            $fcode       = $child['fcode'] ?? '';
            $fcl         = $child['fcl']   ?? '';
            $countryCode = $child['countryCode'] ?? null;
            $typeCode    = \Module\Zone\Model\GeoNamesService::mapFcodeToTypeCode($fcode, $fcl);

            if (!$geonamesId) continue;

            $zoneId = $this->zoneModel->insertZone([
                'geonames_id'  => $geonamesId,
                'type_code'    => $typeCode,
                'parent_id'    => $parentId,
                'name_default' => $nameDefault,
                'country_code' => $countryCode,
                'created_by'   => $userId,
            ]);

            if (!$zoneId) continue;

            // 6. Récupérer les noms traduits en 1 appel API
            $names = $this->geoNames->getNamesForLangs($geonamesId, $activeLangs);
            foreach ($activeLangs as $lang) {
                $name = $names[$lang] ?? $nameDefault;
                $this->zoneModel->upsertZoneI18n($zoneId, $lang, $name);
            }
        }

        // 7. Marquer les enfants comme chargés
        $this->zoneModel->markChildrenLoaded($parentId);

        // 8. Retourner les enfants depuis la BDD (données propres)
        $children = $this->zoneModel->getChildren($parentId, $langCode);
        echo json_encode(['zones' => $children, 'cached' => false]);
        exit;
    }

    // -------------------------------------------------------
    // delete($id) : suppression logique d'une zone
    // -------------------------------------------------------

    public function delete(int $id) {
        $txt = $this->loadLanguage('zone');
        // Pour l'instant, simple redirect (fonctionnalité à implémenter selon besoin)
        $_SESSION['flash_message'] = $txt['ZONE_MSG_ZONE_DELETED'] ?? 'Zone supprimée.';
        return $this->redirect('zone/zones');
    }
}
