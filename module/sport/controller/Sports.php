<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Sports extends Controller {
    private $sportModel;

    public function __construct() {
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt    = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $sports = $this->sportModel->getAllSports();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'     => $txt,
            'title'   => ($txt['SPORT_SPORTS_MGT'] ?? 'Sports') . ' - ' . SITENAME,
            'sports'  => $sports,
            'isAdmin' => $isAdmin,
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/sports', $data);
        $this->view('system/footer', $data);
    }
}
