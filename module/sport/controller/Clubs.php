<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Clubs extends ListController {
    private $clubModel;
    private $sportModel;

    public function __construct() {
        $this->clubModel  = $this->model('sport/Club');
        $this->sportModel = $this->model('sport/Sport');
    }

    public function index() {
        $txt         = $this->loadLanguages(['sport']);
        $search      = trim($_GET['search'] ?? '');
        $countryCode = trim($_GET['country'] ?? '');
        $sportId     = !empty($_GET['sport']) ? (int)$_GET['sport'] : null;

        $clubs     = $this->clubModel->getAllClubs($search, $countryCode, $sportId);
        $sports    = $this->sportModel->getAllSports();
        $countries = $this->clubModel->getAllCountries();

        $data = $this->prepareListData(
            $txt,
            $txt['SPORT_CLUBS_MGT'] ?? 'Clubs',
            [
                'clubs'           => $clubs,
                'sports'          => $sports,
                'countries'       => $countries,
                'selectedCountry' => $countryCode,
                'selectedSport'   => $sportId,
            ]
        );

        $this->renderList('sport/clubs', $data);
    }
}
