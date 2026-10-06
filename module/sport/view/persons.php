<?php
/**
 * Vue : Liste des Personnes / Acteurs
 * Utilise le template parent universel list_template.php
 */

// Render : Nom complet (prénom + nom)
$renderFullName = function ($val, $row, $data) {
    $fullName  = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
    $viewUrl   = URLROOT . '/sport/person/' . htmlspecialchars((string)$row['id']);
    $title     = !empty($data['isAdmin']) ? 'Modifier la personne' : 'Consulter la fiche';
    $linkClass = !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark';
    return '<a href="' . $viewUrl . '" class="text-bold ' . $linkClass . '" title="' . htmlspecialchars($title) . '">'
         . htmlspecialchars($fullName) . '</a>';
};

// Render : Badge rôle coloré
$renderRole = function ($val, $row) {
    $roleBadge = 'primary';
    if (($row['role_code'] ?? '') === 'coach')    $roleBadge = 'warning';
    elseif (($row['role_code'] ?? '') === 'referee') $roleBadge = 'alert';
    elseif (($row['role_code'] ?? '') === 'official') $roleBadge = 'dark';
    elseif (($row['role_code'] ?? '') === 'staff')    $roleBadge = 'secondary';
    $roleName = !empty($row['role_name']) ? $row['role_name'] : ($row['role_code'] ?? 'Autre');
    return '<span class="badge ' . $roleBadge . '">' . htmlspecialchars($roleName) . '</span>';
};

// Render : Photo ou Avatar
$renderPersonPhoto = function ($val, $row) {
    if (!empty($row['photo'])) {
        $photoUrl = URLROOT . '/' . ltrim($row['photo'], '/');
        return '<img src="' . htmlspecialchars($photoUrl) . '" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;vertical-align:middle;">';
    }
    return '<span class="mif-user mif-2x fg-darkCyan"></span>';
};

$listConfig = [
    'title'          => $data['txt']['SPORT_PERSONS_MGT'] ?? 'Personnes / Acteurs',
    'icon'           => 'mif-contacts',
    'addUrl'         => URLROOT . '/sport/person',
    'addBtnText'     => $data['txt']['SPORT_ADD_PERSON_BTN'] ?? 'Ajouter une personne',
    'listUrl'        => URLROOT . '/sport/persons',
    'searchValue'    => $data['search'] ?? '',
    'searchPlaceholder' => 'Code, prénom, nom, rôle...',
    'searchFields'   => 'code,first_name,last_name,role_name,nationality',
    'items'          => $data['persons'] ?? [],
    'idField'        => 'id',
    'statusField'    => 'status_id',
    'columns'        => [
        [
            'field'       => 'id',
            'label'       => '#',
            'type'        => 'text',
            'headerStyle' => 'width:50px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
        [
            'field'       => 'photo',
            'label'       => $data['txt']['SYS_COL_ICON'] ?? 'Icône',
            'headerStyle' => 'width:60px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
            'render'      => $renderPersonPhoto,
        ],
        [
            'field' => 'code',
            'label' => $data['txt']['SPORT_PERSON_CODE'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field'  => 'last_name',
            'label'  => $data['txt']['SPORT_PERSON_FULLNAME'] ?? 'Nom complet',
            'render' => $renderFullName,
        ],
        [
            'field'  => 'role_name',
            'label'  => $data['txt']['SPORT_PERSON_ROLE'] ?? 'Rôle / Fonction',
            'render' => $renderRole,
        ],
        [
            'field' => 'nationality',
            'label' => $data['txt']['SPORT_PERSON_NATIONALITY'] ?? 'Nationalité',
            'type'  => 'text',
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/sport/person/edit/{id}',
        'disableUrl'      => URLROOT . '/sport/person/delete/{id}',
        'deleteUrl'       => URLROOT . '/sport/person/forcedelete/{id}',
        'disableConfirm'  => $data['txt']['SPORT_DELETE_PERSON_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette personne ?',
        'deleteConfirm'   => $data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement cet élément ?',
    ],
];

require 'module/system/view/common/list_template.php';
