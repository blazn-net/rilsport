<?php
/**
 * Vue : Liste des Équipes
 * Utilise le template parent universel list_template.php
 */

// Barre de filtres personnalisée (club / sport / genre) — non gérée nativement
// par le template, on la construit ici et on la passe via customFiltersHtml.
ob_start();
?>
<form method="GET" action="<?php echo URLROOT; ?>/sport/teams" class="d-flex flex-row flex-wrap flex-align-end"
    style="gap: 10px;">
    <div style="flex: 1.5; min-width: 160px;">
        <label class="text-bold d-block"><span
                class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
        <input type="text" name="search" data-role="input" placeholder="Nom d'équipe, code..."
            value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
    </div>

    <div style="flex: 1.2; min-width: 150px;">
        <label class="text-bold d-block"><span
                class="mif-security mr-1"></span><?php echo $data['txt']['TEAM_CLUB'] ?? 'Club'; ?></label>
        <select name="club" data-role="select">
            <option value="">-- Tous les clubs --</option>
            <?php foreach ($data['clubs'] as $cl): ?>
                <option value="<?php echo htmlspecialchars($cl['id']); ?>" <?php echo ($data['selectedClub'] == $cl['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cl['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 1.2; min-width: 140px;">
        <label class="text-bold d-block"><span
                class="mif-trophy mr-1"></span><?php echo $data['txt']['TEAM_SPORT'] ?? 'Discipline'; ?></label>
        <select name="sport" data-role="select">
            <option value="">-- Tous les sports --</option>
            <?php foreach ($data['sports'] as $sp): ?>
                <option value="<?php echo htmlspecialchars($sp['id']); ?>" <?php echo ($data['selectedSport'] == $sp['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sp['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 0.9; min-width: 110px;">
        <label class="text-bold d-block"><span
                class="mif-user mr-1"></span><?php echo $data['txt']['TEAM_GENDER'] ?? 'Genre'; ?></label>
        <select name="gender" data-role="select">
            <option value="">-- Tous --</option>
            <option value="M" <?php echo ($data['selectedGender'] === 'M') ? 'selected' : ''; ?>>Masculin</option>
            <option value="F" <?php echo ($data['selectedGender'] === 'F') ? 'selected' : ''; ?>>Féminin</option>
            <option value="MIXED" <?php echo ($data['selectedGender'] === 'MIXED') ? 'selected' : ''; ?>>Mixte</option>
        </select>
    </div>

    <div style="flex: 0 0 auto; padding-bottom: 2px;">
        <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> Filtrer</button>
        <a href="<?php echo URLROOT; ?>/sport/teams" class="button secondary">Réinitialiser</a>
    </div>
</form>
<?php
$teamsFiltersHtml = ob_get_clean();

// Rendu personnalisé de la colonne "Équipe" (nom + sous-titre + code)
$renderTeamName = function ($val, $row, $data) {
    $html = '<a href="' . URLROOT . '/sport/team/' . htmlspecialchars((string) $row['id']) . '" ';
    $html .= 'class="text-bold ' . (!empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark') . '" ';
    $html .= 'title="' . (!empty($data['isAdmin']) ? "Modifier l'équipe" : 'Consulter la fiche') . '">';
    $html .= htmlspecialchars($val);
    $html .= '</a>';
    if (!empty($row['short_name'])) {
        $html .= ' <small class="fg-gray">(' . htmlspecialchars($row['short_name']) . ')</small>';
    }
    $html .= '<br><small class="fg-gray"><code>' . htmlspecialchars($row['code']) . '</code></small>';
    return $html;
};

// Rendu personnalisé de la colonne "Club" (icône couleur + nom, lien vers la fiche club)
$renderTeamClub = function ($val, $row) {
    $color = !empty($row['club_primary_color']) ? htmlspecialchars($row['club_primary_color']) : '#0072c6';
    $html = '<a href="' . URLROOT . '/sport/club/' . htmlspecialchars((string) $row['club_id']) . '" class="fg-dark text-bold">';
    $html .= '<span class="mif-security mr-1" style="color: ' . $color . ';"></span>';
    $html .= htmlspecialchars($val);
    $html .= '</a>';
    return $html;
};

// Rendu personnalisé de la colonne "Discipline" (icône + nom)
$renderTeamSport = function ($val, $row) {
    $icon = !empty($row['sport_icon']) ? htmlspecialchars($row['sport_icon']) : 'mif-trophy';
    return '<span class="' . $icon . ' mr-1"></span>' . htmlspecialchars($val);
};

// Rendu personnalisé de la colonne "Catégorie / Genre" (2 badges)
$renderTeamCategoryGender = function ($val, $row) {
    $genderLabel = ($row['gender'] === 'F') ? 'Féminin' : (($row['gender'] === 'MIXED') ? 'Mixte' : 'Masculin');
    $genderBadge = ($row['gender'] === 'F') ? 'alert' : (($row['gender'] === 'MIXED') ? 'warning' : 'info');
    $html = '<span class="badge light mr-1">' . htmlspecialchars($row['category']) . '</span>';
    $html .= '<span class="badge ' . $genderBadge . '">' . htmlspecialchars($genderLabel) . '</span>';
    return $html;
};

// Rendu avec repli "-" si vide (colonne Niveau)
$renderOrDash = function ($val) {
    return htmlspecialchars(($val !== null && $val !== '') ? $val : '-');
};

$listConfig = [
    'title' => $data['txt']['SPORT_TEAMS_MGT'] ?? 'Équipes',
    'icon' => 'mif-groups',
    'addUrl' => URLROOT . '/sport/team',
    'addBtnText' => $data['txt']['SPORT_ADD_TEAM_BTN'] ?? 'Nouvelle équipe',
    'showSearch' => false, // recherche gérée par la barre de filtres serveur ci-dessus
    'customFiltersHtml' => $teamsFiltersHtml,
    'items' => $data['teams'] ?? [],
    'idField' => 'id',
    'statusField' => 'status_id',
    'columns' => [
        [
            'field' => 'name',
            'label' => $data['txt']['TEAM_NAME'] ?? 'Équipe',
            'render' => $renderTeamName,
        ],
        [
            'field' => 'club_name',
            'label' => $data['txt']['TEAM_CLUB'] ?? 'Club',
            'render' => $renderTeamClub,
        ],
        [
            'field' => 'sport_name',
            'label' => $data['txt']['TEAM_SPORT'] ?? 'Discipline',
            'render' => $renderTeamSport,
        ],
        [
            'field' => 'category',
            'label' => ($data['txt']['TEAM_CATEGORY'] ?? 'Catégorie') . ' / ' . ($data['txt']['TEAM_GENDER'] ?? 'Genre'),
            'render' => $renderTeamCategoryGender,
        ],
        [
            'field' => 'level',
            'label' => $data['txt']['TEAM_LEVEL'] ?? 'Niveau',
            'render' => $renderOrDash,
        ],
    ],
    'actions' => [
        'editUrl' => URLROOT . '/sport/team/{id}',
        'disableUrl' => URLROOT . '/sport/team/delete/{id}',
        'deleteUrl' => URLROOT . '/sport/team/delete/{id}?force=1',
        'disableConfirm' => $data['txt']['TEAM_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette équipe ?',
        'deleteConfirm' => 'Voulez-vous vraiment supprimer définitivement cette équipe ?',
        // Pas de activateUrl : comme dans l'original, aucune réactivation proposée depuis la liste.
    ],
];

require 'module/system/view/common/list_template.php';