<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Competitions extends Controller {
    private $competitionModel;
    private $sportModel;

    public function __construct() {
        $this->competitionModel = $this->model('sport/Competition');
        $this->sportModel       = $this->model('sport/Sport');
    }

    public function index() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $search   = trim($_GET['search'] ?? '');
        $sportId  = !empty($_GET['sport'])  ? (int)$_GET['sport']  : null;
        $typeCode = trim($_GET['type'] ?? '');

        $competitions = $this->competitionModel->getAllCompetitions($search, $sportId, $typeCode);
        $sports       = $this->sportModel->getAllSports();
        $types        = $this->competitionModel->getAllTypes();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'          => $txt,
            'title'        => ($txt['COMPETITION_COMPETITIONS_MGT'] ?? 'Compétitions') . ' - ' . SITENAME,
            'competitions' => $competitions,
            'sports'       => $sports,
            'types'        => $types,
            'search'       => $search,
            'selectedSport'  => $sportId,
            'selectedType'   => $typeCode,
            'isAdmin'      => $isAdmin,
            'message'      => $_SESSION['flash_message'] ?? '',
            'error'        => $_SESSION['flash_error']   ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competitions', $data);
        $this->view('system/footer',  $data);
    }
}
