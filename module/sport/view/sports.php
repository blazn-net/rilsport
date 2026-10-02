<?php
/**
 * Vue : Liste des Sports
 * Utilise le template parent universel list_template.php
 */

$listConfig = [
    'title'          => $data['txt']['SPORT_SPORTS_MGT'] ?? 'Sports',
    'icon'           => 'mif-trophy',
    'addUrl'         => URLROOT . '/sport/sport',
    'addBtnText'     => $data['txt']['SPORT_ADD_SPORT_BTN'] ?? 'Ajouter un sport',
    'listUrl'        => URLROOT . '/sport/sports',
    'searchValue'    => $data['search'] ?? '',
    'searchPlaceholder' => 'Code, nom, description...',
    'searchFields'   => 'code,name,description',
    'items'          => $data['sports'] ?? [],
    'idField'        => 'id',
    'statusField'    => 'status_id',
    'columns'        => [
        [
            'field' => 'id',
            'label' => '#',
            'type'  => 'text',
        ],
        [
            'field' => 'code',
            'label' => $data['txt']['SPORT_CODE'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field'     => 'name',
            'label'     => $data['txt']['SPORT_NAME'] ?? 'Nom',
            'type'      => 'link',
            'linkUrl'   => URLROOT . '/sport/sport/{id}',
            'linkTitle' => 'Consulter la fiche du sport',
        ],
        [
            'field'  => 'description',
            'label'  => $data['txt']['SPORT_DESCRIPTION'] ?? 'Description',
            'type'   => 'text',
        ],
        [
            'field'  => 'icon',
            'label'  => $data['txt']['SPORT_ICON'] ?? 'Icône',
            'render' => function ($val) {
                $iconClass = (!empty($val) && $val !== 'mif-dribbble') ? htmlspecialchars($val) : 'mif-trophy';
                return '<span class="' . $iconClass . ' mif-lg mr-1"></span>'
                     . ' <small class="fg-gray">(' . $iconClass . ')</small>';
            },
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/sport/sport/{id}',
        'disableUrl'      => URLROOT . '/sport/sport/delete/{id}',
        'deleteUrl'       => URLROOT . '/sport/sport/forcedelete/{id}',
        'disableConfirm'  => $data['txt']['SPORT_DELETE_SPORT_CONFIRM'] ?? 'Voulez-vous vraiment désactiver ce sport ?',
        'deleteConfirm'   => $data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement ce sport ?',
    ],
];

require 'module/system/view/common/list_template.php';
