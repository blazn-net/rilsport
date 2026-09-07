<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Clubs extends Controller {
    private $clubModel;
    private $sportModel;

    public function __construct() {
        $this->clubModel  = $this->model('sport/Club');
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $search      = trim($_GET['search'] ?? '');
        $countryCode = trim($_GET['country'] ?? '');
        $sportId     = !empty($_GET['sport']) ? (int)$_GET['sport'] : null;

        $clubs     = $this->clubModel->getAllClubs($search, $countryCode, $sportId);
        $sports    = $this->sportModel->getAllSports();
        $countries = $this->clubModel->getAllCountries();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'         => $txt,
            'title'       => ($txt['SPORT_CLUBS_MGT'] ?? 'Clubs') . ' - ' . SITENAME,
            'clubs'       => $clubs,
            'sports'      => $sports,
            'countries'   => $countries,
            'search'      => $search,
            'selectedCountry' => $countryCode,
            'selectedSport'   => $sportId,
            'isAdmin'     => $isAdmin,
            'message'     => $_SESSION['flash_message'] ?? '',
            'error'       => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/clubs', $data);
        $this->view('system/footer', $data);
    }
}
