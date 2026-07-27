<?php
namespace Module\Main\Controller;

use Core\Controller;
use Core\Database;

class Langs extends Controller {
    private $langModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $this->langModel = $this->model('main/Lang');
    }

    public function index() {
        $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('main'));

        $langs = $this->langModel->getAllLangs();

        $data = [
            'txt' => $txt,
            'title' => 'Gestion des Langues - ' . SITENAME,
            'langs' => $langs,
            'message' => $_SESSION['flash_message'] ?? '',
            'error' => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/sidebar', $data);
        $this->view('main/langs', $data); // vue affiche la liste
        $this->view('system/footer', $data);
    }
}
