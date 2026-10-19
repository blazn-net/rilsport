<?php
namespace Module\Sport\Controller;

use Core\ListController;

class Persons extends ListController {
    private $personModel;

    public function __construct() {
        $this->personModel = $this->model('sport/Person');
    }

    public function index() {
        $txt     = $this->loadLanguages(['sport']);
        $search  = trim($_GET['search'] ?? '');
        $persons = $this->personModel->getAllPersons($search);

        $data = $this->prepareListData(
            $txt,
            $txt['SPORT_PERSONS_MGT'] ?? 'Personnes / Acteurs',
            ['persons' => $persons]
        );

        $this->renderList('sport/persons', $data);
    }
}
