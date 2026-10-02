<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Seasons extends Controller {
    private $seasonModel;

    public function __construct() {
        $this->seasonModel = $this->model('sport/Season');
    }

    public function index() {
        $txt    = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $search = trim($_GET['search'] ?? '');
        $seasons = $this->seasonModel->getAllSeasons($search);

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'         => $txt,
            'title'       => ($txt['SPORT_SEASONS_MGT'] ?? 'Saisons') . ' - ' . SITENAME,
            'seasons'     => $seasons,
            'search'      => $search,
            'isAdmin'     => $isAdmin,
            'message'     => $_SESSION['flash_message'] ?? '',
            'error'       => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/seasons', $data);
        $this->view('system/footer', $data);
    }
}
