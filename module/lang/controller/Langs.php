<?php
namespace Module\Lang\Controller;

use Core\Controller;

class Langs extends Controller {
    private $langModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['roles']) || !in_array('admin', $_SESSION['roles'])) {
            $txt = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        $this->langModel = $this->model('lang/Lang');
    }

    public function index() {
        $txt   = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'));
        $langs = $this->langModel->getAllLangs();

        $data = [
            'txt'     => $txt,
            'title'   => ($txt['LANG_LANGS_MGT'] ?? 'Langues') . ' - ' . SITENAME,
            'langs'   => $langs,
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/sidebar', $data);
        $this->view('lang/langs', $data);
        $this->view('system/footer', $data);
    }
}
