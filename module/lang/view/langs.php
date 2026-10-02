<?php
/**
 * Vue : Liste des Langues
 * Utilise le template parent universel list_template.php
 */

// Render : Drapeau + code CSS
$renderFlag = function ($val, $row) {
    $flag = !empty($row['lang_flag']) ? $row['lang_flag'] : '';
    if ($flag) {
        return '<span class="fi ' . htmlspecialchars($flag) . '"></span> '
             . '(' . htmlspecialchars($flag) . ')';
    }
    return '<span class="mif-earth"></span>';
};

$listConfig = [
    'title'          => $data['txt']['LANG_LANGS_MGT'] ?? 'Langues',
    'icon'           => 'mif-language',
    'addUrl'         => URLROOT . '/lang',
    'addBtnText'     => $data['txt']['LANG_ADD_LANG_BTN'] ?? 'Ajouter une langue',
    'listUrl'        => URLROOT . '/lang/langs',
    'searchValue'    => $data['search'] ?? '',
    'searchPlaceholder' => 'Code, nom de la langue...',
    'searchFields'   => 'lang_code,lang_name',
    'items'          => $data['langs'] ?? [],
    'idField'        => 'lang_code',   // clé primaire = code texte
    'statusField'    => 'status_id',
    'columns'        => [
        [
            'field' => 'lang_code',
            'label' => $data['txt']['LANG_CODE'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field' => 'lang_name',
            'label' => $data['txt']['LANG_NAME'] ?? 'Nom',
            'type'  => 'text',
        ],
        [
            'field'  => 'lang_flag',
            'label'  => $data['txt']['LANG_FLAG'] ?? 'Drapeau',
            'render' => $renderFlag,
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/lang/{id}',
        'disableUrl'      => URLROOT . '/lang/delete/{id}',
        'deleteUrl'       => URLROOT . '/lang/forcedelete/{id}',
        'disableConfirm'  => $data['txt']['LANG_DELETE_LANG_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette langue ?',
        'deleteConfirm'   => $data['txt']['LANG_FORCE_DELETE_LANG_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement cette langue ?',
    ],
];

require 'module/system/view/common/list_template.php';
