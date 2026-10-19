<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Seasons extends ListController {
    private $seasonModel;

    public function __construct() {
        $this->seasonModel = $this->model('sport/Season');
    }

    public function index() {
        $txt     = $this->loadLanguages(['sport']);
        $search  = trim($_GET['search'] ?? '');
        $seasons = $this->seasonModel->getAllSeasons($search);

        $data = $this->prepareListData(
            $txt,
            $txt['SPORT_SEASONS_MGT'] ?? 'Saisons',
            ['seasons' => $seasons]
        );

        $this->renderList('sport/seasons', $data);
    }
}
