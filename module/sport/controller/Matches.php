<?php
namespace Module\Sport\Controller;

use Core\Controller;

class Matches extends Controller {
    private $matchModel;
    private $competitionModel;

    public function __construct() {
        $this->matchModel       = $this->model('sport/SportMatch');
        $this->competitionModel = $this->model('sport/Competition');
    }

    public function index() {
        $txt = array_merge(
            $this->loadLanguage('system'),
            $this->loadLanguage('user'),
            $this->loadLanguage('sport')
        );

        $search        = trim($_GET['search'] ?? '');
        $competitionId = !empty($_GET['competition']) ? (int)$_GET['competition'] : null;
        $roundId       = !empty($_GET['round'])       ? (int)$_GET['round']       : null;
        $statusId      = !empty($_GET['status'])      ? (int)$_GET['status']      : null;

        $matches      = $this->matchModel->getAllMatches($search, $competitionId, $roundId, $statusId);
        $competitions = $this->competitionModel->getAllCompetitions();
        $rounds       = $this->matchModel->getRoundsList();
        $statuses     = $this->matchModel->getAllStatuses();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'                 => $txt,
            'title'               => ($txt['MATCH_TITLE_LIST'] ?? 'Matchs') . ' - ' . SITENAME,
            'matches'             => $matches,
            'competitions'        => $competitions,
            'rounds'              => $rounds,
            'statuses'            => $statuses,
            'search'              => $search,
            'selectedCompetition' => $competitionId,
            'selectedRound'       => $roundId,
            'selectedStatus'      => $statusId,
            'isAdmin'             => $isAdmin,
            'message'             => $_SESSION['flash_message'] ?? '',
            'error'               => $_SESSION['flash_error']   ?? ''
        ];

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/matches',  $data);
        $this->view('system/footer',  $data);
    }
}
