<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Competitions extends ListController {
    private $competitionModel;
    private $sportModel;

    public function __construct() {
        $this->competitionModel = $this->model('sport/Competition');
        $this->sportModel       = $this->model('sport/Sport');
    }

    public function index() {
        $txt      = $this->loadLanguages(['sport']);
        $search   = trim($_GET['search'] ?? '');
        $sportId  = !empty($_GET['sport'])  ? (int)$_GET['sport']  : null;
        $typeCode = trim($_GET['type'] ?? '');

        $competitions = $this->competitionModel->getAllCompetitions($search, $sportId, $typeCode);
        $sports       = $this->sportModel->getAllSports();
        $types        = $this->competitionModel->getAllTypes();

        $data = $this->prepareListData(
            $txt,
            $txt['COMPETITION_COMPETITIONS_MGT'] ?? 'Compétitions',
            [
                'competitions'  => $competitions,
                'sports'        => $sports,
                'types'         => $types,
                'selectedSport' => $sportId,
                'selectedType'  => $typeCode,
            ]
        );

        $this->renderList('sport/competitions', $data);
    }
}
