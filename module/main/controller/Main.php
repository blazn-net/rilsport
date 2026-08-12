<?php
namespace Module\Main\Controller;

use Core\Controller;

class Main extends Controller {
    public function __construct() {
        // Optionnel : charger des modèles si besoin
    }

    public function index() {
        $txt = $this->loadLanguage('system');
        
        $data = [
            'txt' => $txt,
            'title' => ($txt['SYS_HOME'] ?? 'SYS_HOME') . ' - ' . SITENAME,
            'description' => $txt['SYS_HOME'] ?? 'SYS_HOME'
        ];

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('main/index', $data);
        $this->view('system/footer', $data);
    }
}
