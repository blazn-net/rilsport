<?php
/**
 * Vue : Liste des Utilisateurs
 * Utilise le template parent universel list_template.php
 */

// Render : Nom complet avec lien
$renderUserName = function ($val, $row, $data) {
    $fullName = htmlspecialchars(trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? '')));
    $label = ($fullName !== '') ? $fullName : htmlspecialchars($row['username'] ?? '—');
    return '<a href="' . URLROOT . '/user/' . (int)$row['id'] . '" class="text-bold fg-primary" title="Consulter le profil">' . $label . '</a>';
};

// Render : Badges des rôles
$renderRoles = function ($val, $row, $data) {
    if (!isset($row['roles']) || !is_array($row['roles'])) {
        return '<span class="badge primary">none</span>';
    }
    $html = '<div class="d-flex flex-wrap" style="gap:5px;">';
    foreach ($row['roles'] as $roleId) {
        $badgeCode = $data['available_roles'][$roleId]['badge_code'] ?? 'primary';
        $textCode  = $data['available_roles'][$roleId]['text_code'] ?? $roleId;
        $label     = $data['txt'][$textCode] ?? $textCode;
        $html .= '<span class="badge ' . htmlspecialchars($badgeCode) . '">' . htmlspecialchars($label) . '</span>';
    }
    $html .= '</div>';
    return $html;
};

// Render : Date d'inscription
$renderDate = function ($val) {
    return !empty($val) ? date('d/m/Y H:i', strtotime($val)) : '—';
};

$listConfig = [
    'title'          => $data['txt']['USER_USERS_LIST'] ?? 'Utilisateurs',
    'icon'           => 'mif-group',
    'addUrl'         => URLROOT . '/user',
    'addBtnText'     => $data['txt']['USER_ADD_USER_BTN'] ?? 'Ajouter un utilisateur',
    'listUrl'        => URLROOT . '/user/users',
    'searchValue'    => $data['search'] ?? '',
    'searchPlaceholder' => 'Pseudo, prénom, nom, email...',
    'searchFields'   => 'username,email,nom,prenom',
    'items'          => $data['users'] ?? [],
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
            'field'       => 'avatar',
            'label'       => $data['txt']['SYS_COL_ICON'] ?? 'Icône',
            'headerStyle' => 'width:60px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
            'render'      => function () {
                return '<span class="mif-user mif-2x fg-emerald"></span>';
            },
        ],
        [
            'field' => 'username',
            'label' => $data['txt']['USER_USERNAME'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field'  => 'nom',
            'label'  => ($data['txt']['USER_FIRSTNAME'] ?? 'Prénom') . ' ' . ($data['txt']['USER_LASTNAME'] ?? 'Nom'),
            'render' => $renderUserName,
        ],
        [
            'field' => 'email',
            'label' => $data['txt']['USER_EMAIL'] ?? 'Email',
            'type'  => 'text',
        ],
        [
            'field'  => 'roles',
            'label'  => $data['txt']['USER_ROLE'] ?? 'Rôle(s)',
            'render' => $renderRoles,
        ],
        [
            'field'  => 'created_at',
            'label'  => $data['txt']['USER_DATE_REG'] ?? 'Inscription',
            'render' => $renderDate,
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/user/edit/{id}',
        'disableUrl'      => URLROOT . '/user/users?action=disable&id={id}',
        'activateUrl'     => URLROOT . '/user/users?action=activate&id={id}',
        'deleteUrl'       => URLROOT . '/user/users?action=delete&id={id}',
        'disableConfirm'  => $data['txt']['USER_DISABLE_USER_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cet utilisateur ?',
        'activateConfirm' => $data['txt']['USER_ACTIVATE_USER_CONFIRM'] ?? 'Voulez-vous vraiment réactiver cet utilisateur ?',
        'deleteConfirm'   => $data['txt']['USER_DELETE_USER_CONFIRM'] ?? 'Voulez-vous vraiment supprimer cet utilisateur ?',
    ],
];

require 'module/system/view/common/list_template.php';
