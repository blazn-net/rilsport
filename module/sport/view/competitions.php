<?php
/**
 * Vue : Liste des Compétitions
 * Utilise le template parent universel list_template.php
 */

// ── Barre de filtres GET ──────────────────────────────────────────────────────
ob_start(); ?>
<form method="GET" action="<?php echo URLROOT; ?>/sport/competitions" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
    <div style="flex: 2; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
        <input type="text" name="search" data-role="input" placeholder="Nom, code, sigle..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
    </div>
    <div style="flex: 1.2; min-width: 160px;">
        <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?></label>
        <select name="sport" data-role="select">
            <option value="">-- Tous --</option>
            <?php foreach ($data['sports'] as $s): ?>
                <option value="<?php echo (int)$s['id']; ?>" <?php echo ($data['selectedSport'] == $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 1.2; min-width: 160px;">
        <label class="text-bold d-block"><span class="mif-filter mr-1"></span><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?></label>
        <select name="type" data-role="select">
            <option value="">-- Tous les types --</option>
            <?php foreach ($data['types'] as $t): ?>
                <option value="<?php echo htmlspecialchars($t['code']); ?>" <?php echo ($data['selectedType'] === $t['code']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($data['txt'][$t['text_code']] ?? $t['code']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 0 0 auto; padding-bottom: 2px;">
        <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> <span class="btn-text">Filtrer</span></button>
        <a href="<?php echo URLROOT; ?>/sport/competitions" class="button secondary"><span class="mif-reload"></span> <span class="btn-text">Réinitialiser</span></a>
    </div>
</form>
<?php $compFiltersHtml = ob_get_clean();

// ── Render Nom + logo + sigle ─────────────────────────────────────────────────
$renderCompName = function ($val, $row, $data) {
    $html = '<a href="' . URLROOT . '/sport/competition/' . (int)$row['id'] . '" class="text-bold fg-primary">';
    if (!empty($row['logo'])) {
        $html .= '<img src="' . URLROOT . '/' . htmlspecialchars($row['logo']) . '" alt="" style="height:20px;width:auto;margin-right:6px;vertical-align:middle;">';
    }
    $html .= htmlspecialchars($row['name']);
    if (!empty($row['acronym'])) {
        $html .= ' <span class="fg-gray ml-1">(' . htmlspecialchars($row['acronym']) . ')</span>';
    }
    $html .= '</a>';
    return $html;
};

// ── Render Type badge ─────────────────────────────────────────────────────────
$renderType = function ($val, $row, $data) {
    $label = $data['txt'][$row['type_text_code'] ?? ''] ?? ($row['type_text_code'] ?? '—');
    return '<span class="badge inline bg-dark fg-white">' . htmlspecialchars($label) . '</span>';
};

// ── Config template ───────────────────────────────────────────────────────────
$listConfig = [
    'title'             => $data['txt']['COMPETITION_COMPETITIONS_MGT'] ?? 'Compétitions',
    'icon'              => 'mif-trophy',
    'addUrl'            => URLROOT . '/sport/competition',
    'addBtnText'        => $data['txt']['COMPETITION_ADD_BTN'] ?? 'Nouvelle compétition',
    'showSearch'        => false,
    'customFiltersHtml' => $compFiltersHtml,
    'items'             => $data['competitions'] ?? [],
    'idField'           => 'id',
    'statusField'       => 'status_id',
    'columns'           => [
        [
            'field'  => 'name',
            'label'  => $data['txt']['COMPETITION_TABLE_NAME'] ?? 'Nom',
            'render' => $renderCompName,
        ],
        [
            'field' => 'sport_name',
            'label' => $data['txt']['COMPETITION_SPORT'] ?? 'Sport',
            'type'  => 'text',
        ],
        [
            'field'  => 'type_text_code',
            'label'  => $data['txt']['COMPETITION_TYPE'] ?? 'Type',
            'render' => $renderType,
        ],
        [
            'field' => 'country_name',
            'label' => $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays',
            'type'  => 'text',
        ],
        [
            'field'       => 'editions_count',
            'label'       => 'Éditions',
            'type'        => 'text',
            'headerStyle' => 'text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
    ],
    'actions' => [
        'editUrl'        => URLROOT . '/sport/competition/edit/{id}',
        'disableUrl'     => URLROOT . '/sport/competition/delete/{id}',
        'disableConfirm' => $data['txt']['COMPETITION_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette compétition ?',
    ],
];

require 'module/system/view/common/list_template.php';
