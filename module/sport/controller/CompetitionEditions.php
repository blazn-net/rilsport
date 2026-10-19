<?php
namespace Module\Sport\Controller;

use Core\ListController;

class CompetitionEditions extends ListController {
    private $editionModel;
    private $competitionModel;

    public function __construct() {
        $this->editionModel     = $this->model('sport/CompetitionEdition');
        $this->competitionModel = $this->model('sport/Competition');
    }

    public function index() {
        $txt           = $this->loadLanguages(['sport']);
        $search        = trim($_GET['search'] ?? '');
        $competitionId = !empty($_GET['competition']) ? (int)$_GET['competition'] : null;
        $seasonId      = !empty($_GET['season'])      ? (int)$_GET['season']      : null;

        $editions      = $this->editionModel->getAllEditions($search, $competitionId, $seasonId);
        $competitions  = $this->competitionModel->getAllCompetitions();
        $seasons       = $this->editionModel->getAllSeasons();

        $data = $this->prepareListData(
            $txt,
            $txt['COMPETITION_EDITIONS_MGT'] ?? 'Editions',
            [
                'editions'            => $editions,
                'competitions'        => $competitions,
                'seasons'             => $seasons,
                'selectedCompetition' => $competitionId,
                'selectedSeason'      => $seasonId,
            ]
        );

        $this->renderList('sport/competition-editions', $data);
    }
}
