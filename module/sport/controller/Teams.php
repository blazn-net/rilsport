<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Teams extends Controller {
    private $teamModel;
    private $clubModel;
    private $sportModel;

    public function __construct() {
        $this->teamModel  = $this->model('sport/Team');
        $this->clubModel  = $this->model('sport/Club');
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $search  = trim($_GET['search'] ?? '');
        $clubId  = !empty($_GET['club']) ? (int)$_GET['club'] : null;
        $sportId = !empty($_GET['sport']) ? (int)$_GET['sport'] : null;
        $gender  = !empty($_GET['gender']) ? trim($_GET['gender']) : null;

        $teams  = $this->teamModel->getAllTeams($search, $clubId, $sportId, $gender);
        $clubs  = $this->clubModel->getAllClubs();
        $sports = $this->sportModel->getAllSports();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'            => $txt,
            'title'          => ($txt['SPORT_TEAMS_MGT'] ?? 'Équipes') . ' - ' . SITENAME,
            'teams'          => $teams,
            'clubs'          => $clubs,
            'sports'         => $sports,
            'search'         => $search,
            'selectedClub'   => $clubId,
            'selectedSport'  => $sportId,
            'selectedGender' => $gender,
            'isAdmin'        => $isAdmin,
            'message'        => $_SESSION['flash_message'] ?? '',
            'error'          => $_SESSION['flash_error'] ?? ''
        ];

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view('sport/teams', $data);
        $this->view('system/footer', $data);
    }
}
