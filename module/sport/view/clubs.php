<?php
/**
 * Vue : Liste des Clubs
 * Utilise le template parent universel list_template.php
 * Les filtres (recherche, pays, sport) sont gérés côté serveur via GET.
 */

// ── Barre de filtres GET (Recherche + Pays + Sport) ──────────────────────────
ob_start(); ?>
<form method="GET" action="<?php echo URLROOT; ?>/sport/clubs" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
    <div style="flex: 2; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
        <input type="text" name="search" data-role="input" placeholder="Nom, ville, sigle..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
    </div>

    <div style="flex: 1.2; min-width: 160px;">
        <label class="text-bold d-block"><span class="mif-earth mr-1"></span><?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?></label>
        <select name="country" data-role="select">
            <option value="">-- Tous les pays --</option>
            <?php foreach ($data['countries'] as $c): ?>
                <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo ($data['selectedCountry'] === $c['country_code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['country_code']); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 1.2; min-width: 160px;">
        <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['CLUB_FILTER_SPORT'] ?? 'Sport'; ?></label>
        <select name="sport" data-role="select">
            <option value="">-- Tous les sports --</option>
            <?php foreach ($data['sports'] as $s): ?>
                <option value="<?php echo htmlspecialchars($s['id']); ?>" <?php echo ($data['selectedSport'] == $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 0 0 auto; padding-bottom: 2px;">
        <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> <span class="btn-text">Filtrer</span></button>
        <a href="<?php echo URLROOT; ?>/sport/clubs" class="button secondary"><span class="mif-reload"></span> <span class="btn-text">Réinitialiser</span></a>
    </div>
</form>
<?php $clubsFiltersHtml = ob_get_clean();

// ── Render Logo ───────────────────────────────────────────────────────────────
$renderLogo = function ($val, $row) {
    $logoUrl = !empty($row['logo']) ? URLROOT . '/' . ltrim($row['logo'], '/') : null;
    $logoPath = !empty($row['logo']) ? dirname(__DIR__, 3) . '/' . ltrim($row['logo'], '/') : null;
    if ($logoUrl && $logoPath && file_exists($logoPath)) {
        return '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="max-height:40px;max-width:40px;object-fit:contain;">';
    }
    $bg = (!empty($row['primary_color']) && strtolower($row['primary_color']) !== '#ffffff' && strtolower($row['primary_color']) !== '#fff')
        ? htmlspecialchars($row['primary_color']) : '#0072c6';
    return '<div style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:' . $bg . ';color:#fff;">'
         . '<span class="mif-security" style="font-size:18px;"></span></div>';
};

// ── Render Nom + Sigle ─────────────────────────────────────────────────────────
$renderClubName = function ($val, $row, $data) {
    $html  = '<a href="' . URLROOT . '/sport/club/' . htmlspecialchars((string)$row['id']) . '" ';
    $html .= 'class="text-bold fg-primary" title="' . htmlspecialchars($data['txt']['SPORT_VIEW_CLUB_TITLE'] ?? 'Fiche du club') . '">';
    $html .= htmlspecialchars($row['name']) . '</a>';
    if (!empty($row['acronym'])) {
        $html .= ' <span class="badge info ml-1">' . htmlspecialchars($row['acronym']) . '</span>';
    }
    return $html;
};

// ── Render Localisation ───────────────────────────────────────────────────────
$renderLocation = function ($val, $row) {
    return '<span class="mif-location fg-crimson mr-1"></span>'
         . '<strong>' . htmlspecialchars($row['city_name']) . '</strong> '
         . '<span class="fg-gray">(' . htmlspecialchars($row['country_code']) . ')</span>';
};

// ── Render Sports ─────────────────────────────────────────────────────────────
$renderSports = function ($val, $row) {
    if (empty($row['sports_names'])) {
        return '<span class="fg-muted"><em>Aucune section</em></span>';
    }
    $html = '';
    foreach (explode(', ', $row['sports_names']) as $sName) {
        $html .= '<span class="badge secondary mr-1 mb-1"><span class="mif-trophy mr-1"></span>' . htmlspecialchars($sName) . '</span>';
    }
    return $html;
};

// ── Config template ───────────────────────────────────────────────────────────
$listConfig = [
    'title'             => $data['txt']['SPORT_CLUBS_MGT'] ?? 'Clubs',
    'icon'              => 'mif-security',
    'addUrl'            => URLROOT . '/sport/club',
    'addBtnText'        => $data['txt']['SPORT_ADD_CLUB_BTN'] ?? 'Nouveau club',
    'showSearch'        => false,          // recherche intégrée dans customFiltersHtml
    'customFiltersHtml' => $clubsFiltersHtml,
    'items'             => $data['clubs'] ?? [],
    'idField'           => 'id',
    'statusField'       => 'status_id',
    'columns'           => [
        [
            'field'       => 'id',
            'label'       => '#',
            'type'        => 'text',
            'headerStyle' => 'width:50px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
        [
            'field'       => 'logo',
            'label'       => $data['txt']['CLUB_TABLE_LOGO'] ?? 'Logo',
            'render'      => $renderLogo,
            'headerStyle' => 'width:60px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
        [
            'field' => 'code',
            'label' => $data['txt']['SYS_COL_CODE'] ?? 'Code',
            'type'  => 'code',
        ],
        [
            'field'  => 'name',
            'label'  => $data['txt']['CLUB_TABLE_NAME'] ?? 'Nom',
            'render' => $renderClubName,
        ],
        [
            'field'  => 'city_name',
            'label'  => $data['txt']['CLUB_TABLE_LOCATION'] ?? 'Localisation',
            'render' => $renderLocation,
        ],
        [
            'field'  => 'sports_names',
            'label'  => $data['txt']['CLUB_SECTIONS'] ?? 'Sport',
            'render' => $renderSports,
        ],
    ],
    'actions' => [
        'editUrl'        => URLROOT . '/sport/club/edit/{id}',
        'disableUrl'     => URLROOT . '/sport/club/delete/{id}',
        'deleteUrl'      => URLROOT . '/sport/club/delete/{id}?force=1',
        'disableConfirm' => $data['txt']['CLUB_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver ce club ?',
        'deleteConfirm'  => 'Voulez-vous supprimer définitivement ce club ?',
    ],
];

require 'module/system/view/common/list_template.php';
