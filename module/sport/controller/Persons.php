<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Persons extends Controller {
    private $personModel;

    public function __construct() {
        $this->personModel = $this->model('sport/Person');
    }

    public function index() {
        $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('user'), $this->loadLanguage('sport'));
        $persons = $this->personModel->getAllPersons();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'     => $txt,
            'title'   => ($txt['SPORT_PERSONS_MGT'] ?? 'Personnes / Acteurs') . ' - ' . SITENAME,
            'persons' => $persons,
            'isAdmin' => $isAdmin,
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/persons', $data);
        $this->view('system/footer', $data);
    }
}
