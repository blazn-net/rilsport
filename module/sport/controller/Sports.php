<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Sports extends ListController {
    private $sportModel;

    public function __construct() {
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt    = $this->loadLanguages(['sport']);
        $search = trim($_GET['search'] ?? '');
        $sports = $this->sportModel->getAllSports($search);

        $data = $this->prepareListData(
            $txt,
            $txt['SPORT_SPORTS_MGT'] ?? 'Sports',
            ['sports' => $sports]
        );

        $this->renderList('sport/sports', $data);
    }
}
