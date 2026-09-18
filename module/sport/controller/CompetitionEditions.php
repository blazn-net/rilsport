<?php
namespace Module\Sport\Controller;

use Core\Controller;

class CompetitionEditions extends Controller {
    private $editionModel;
    private $competitionModel;

    public function __construct() {
        $this->editionModel     = $this->model('sport/CompetitionEdition');
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
        $seasonId      = !empty($_GET['season'])      ? (int)$_GET['season']      : null;

        $editions      = $this->editionModel->getAllEditions($search, $competitionId, $seasonId);
        $competitions  = $this->competitionModel->getAllCompetitions();
        $seasons       = $this->editionModel->getAllSeasons();

        $isAdmin = isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

        $data = [
            'txt'                 => $txt,
            'title'               => ($txt['COMPETITION_EDITIONS_MGT'] ?? 'Editions') . ' - ' . SITENAME,
            'editions'            => $editions,
            'competitions'        => $competitions,
            'seasons'             => $seasons,
            'search'              => $search,
            'selectedCompetition' => $competitionId,
            'selectedSeason'      => $seasonId,
            'isAdmin'             => $isAdmin,
            'message'             => $_SESSION['flash_message'] ?? '',
            'error'               => $_SESSION['flash_error']   ?? '',
        ];

        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $this->view('system/header',  $data);
        $this->view('system/navview', $data);
        $this->view('sport/competition-editions', $data);
        $this->view('system/footer',  $data);
    }
}
