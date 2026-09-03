<?php
namespace Module\Zone\Controller;

use Core\Controller;

/**
 * Zones — Liste du treeview (page principale du module zone)
 * 
 * Cette page affiche le treeview géographique en lazy-loading.
 * Le chargement des enfants se fait via des appels AJAX vers Zone.php (action 'children').
 */
class Zones extends Controller {
    private $zoneModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $this->zoneModel = $this->model('zone/Zone');
    }

    public function index() {
        $txt      = $this->loadLanguage('zone');
        $langCode = $_SESSION['lang_code'] ?? 'fr';
        $root     = $this->zoneModel->getRootZone($langCode);

        $data = [
            'txt'     => $txt,
            'title'   => ($txt['ZONE_TITLE_ZONES'] ?? 'Zones géographiques') . ' - ' . SITENAME,
            'root'    => $root,
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('zone/zones', $data);
        $this->view('system/footer', $data);
    }
}
