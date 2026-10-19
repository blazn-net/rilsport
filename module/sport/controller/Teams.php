<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Teams extends ListController {
    private $teamModel;
    private $clubModel;
    private $sportModel;

    public function __construct() {
        $this->teamModel  = $this->model('sport/Team');
        $this->clubModel  = $this->model('sport/Club');
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt     = $this->loadLanguages(['sport']);
        $search  = trim($_GET['search'] ?? '');
        $clubId  = !empty($_GET['club']) ? (int)$_GET['club'] : null;
        $sportId = !empty($_GET['sport']) ? (int)$_GET['sport'] : null;
        $gender  = !empty($_GET['gender']) ? trim($_GET['gender']) : null;

        $teams  = $this->teamModel->getAllTeams($search, $clubId, $sportId, $gender);
        $clubs  = $this->clubModel->getAllClubs();
        $sports = $this->sportModel->getAllSports();

        $data = $this->prepareListData(
            $txt,
            $txt['SPORT_TEAMS_MGT'] ?? 'Équipes',
            [
                'teams'          => $teams,
                'clubs'          => $clubs,
                'sports'         => $sports,
                'selectedClub'   => $clubId,
                'selectedSport'  => $sportId,
                'selectedGender' => $gender,
            ]
        );

        $this->renderList('sport/teams', $data);
    }
}
