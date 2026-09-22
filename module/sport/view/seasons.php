<?php
/**
 * Vue : Liste des Saisons
 * Utilise le template parent universel list_template.php
 */

$listConfig = [
    'title'          => $data['txt']['SPORT_SEASONS_MGT'] ?? 'Saisons',
    'icon'           => 'mif-calendar',
    'addUrl'         => URLROOT . '/sport/season',
    'addBtnText'     => $data['txt']['SYS_BTN_ADD'] ?? 'Ajouter',
    'searchFields'   => 'code,name',
    'items'          => $data['seasons'] ?? [],
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
            'label' => $data['txt']['SYS_COL_CODE'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field'     => 'name',
            'label'     => $data['txt']['SYS_COL_NAME'] ?? 'Nom',
            'type'      => 'link',
            'linkUrl'   => URLROOT . '/sport/season/{id}',
            'linkTitle' => 'Consulter la fiche de la saison',
        ],
        [
            'field'      => 'date_start',
            'label'      => $data['txt']['SPORT_SEASON_DATE_START'] ?? 'Début',
            'type'       => 'date',
            'dateFormat' => 'd/m/Y',
        ],
        [
            'field'      => 'date_end',
            'label'      => $data['txt']['SPORT_SEASON_DATE_END'] ?? 'Fin',
            'type'       => 'date',
            'dateFormat' => 'd/m/Y',
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/sport/season/edit/{id}',
        'disableUrl'      => URLROOT . '/sport/season/delete/{id}',
        'activateUrl'     => URLROOT . '/sport/season/activate/{id}',
        'deleteUrl'       => URLROOT . '/sport/season/forcedelete/{id}',
        'disableConfirm'  => $data['txt']['SPORT_DELETE_SEASON_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette saison ?',
        'activateConfirm' => $data['txt']['SYS_CONFIRM_ACTIVATE'] ?? 'Voulez-vous vraiment réactiver cette saison ?',
        'deleteConfirm'   => $data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement cette saison ?',
    ],
];

require 'module/system/view/common/list_template.php';
