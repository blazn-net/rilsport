<?php
/**
 * Vue : Liste des Éditions de compétitions
 * Utilise le template parent universel list_template.php
 */

// ── Barre de filtres GET ──────────────────────────────────────────────────────
ob_start(); ?>
<form method="GET" action="<?php echo URLROOT; ?>/sport/competition-editions" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
    <div style="flex: 2; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
        <input type="text" name="search" data-role="input" placeholder="Nom, code..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
    </div>
    <div style="flex: 1.5; min-width: 200px;">
        <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Compétition'; ?></label>
        <select name="competition" data-role="select">
            <option value="">-- Toutes --</option>
            <?php foreach ($data['competitions'] as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>" <?php echo ($data['selectedCompetition'] == $c['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 1; min-width: 150px;">
        <label class="text-bold d-block"><span class="mif-calendar mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></label>
        <select name="season" data-role="select">
            <option value="">-- Toutes --</option>
            <?php foreach ($data['seasons'] as $s): ?>
                <option value="<?php echo (int)$s['id']; ?>" <?php echo ($data['selectedSeason'] == $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 0 0 auto; padding-bottom: 2px;">
        <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> <span class="btn-text">Filtrer</span></button>
        <a href="<?php echo URLROOT; ?>/sport/competition-editions" class="button secondary"><span class="mif-reload"></span> <span class="btn-text">Réinitialiser</span></a>
    </div>
</form>
<?php $editionsFiltersHtml = ob_get_clean();

// ── Render Nom édition + édition parente ──────────────────────────────────────
$renderEditionName = function ($val, $row) {
    $html = '<a href="' . URLROOT . '/sport/competition-edition/' . (int)$row['id'] . '" class="text-bold fg-primary">'
          . htmlspecialchars($row['name']) . '</a>';
    if (!empty($row['parent_edition_name'])) {
        $html .= '<br><small class="fg-gray">' . htmlspecialchars($row['parent_edition_name']) . '</small>';
    }
    return $html;
};

// ── Render Compétition (lien) ─────────────────────────────────────────────────
$renderCompetition = function ($val, $row) {
    return '<a href="' . URLROOT . '/sport/competition/' . (int)$row['competition_id'] . '" class="fg-dark">'
         . htmlspecialchars($row['competition_name'] ?? '') . '</a>';
};

// ── Render Date ───────────────────────────────────────────────────────────────
$renderDate = function ($val) {
    return !empty($val) ? date('d/m/Y', strtotime($val)) : '—';
};

// ── Config template ───────────────────────────────────────────────────────────
$listConfig = [
    'title'             => $data['txt']['COMPETITION_EDITIONS_MGT'] ?? 'Éditions',
    'icon'              => 'mif-calendar',
    'addUrl'            => URLROOT . '/sport/competition-edition',
    'addBtnText'        => $data['txt']['COMPETITION_ADD_EDITION_BTN'] ?? 'Nouvelle édition',
    'showSearch'        => false,
    'customFiltersHtml' => $editionsFiltersHtml,
    'items'             => $data['editions'] ?? [],
    'idField'           => 'id',
    'statusField'       => 'status_id',
    'columns'           => [
        [
            'field'  => 'name',
            'label'  => $data['txt']['COMPETITION_EDITION_TABLE_NAME'] ?? 'Édition',
            'render' => $renderEditionName,
        ],
        [
            'field'  => 'competition_name',
            'label'  => $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Compétition',
            'render' => $renderCompetition,
        ],
        [
            'field' => 'season_name',
            'label' => $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison',
            'type'  => 'text',
        ],
        [
            'field'  => 'date_start',
            'label'  => $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Début',
            'render' => $renderDate,
        ],
        [
            'field'  => 'date_end',
            'label'  => $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin',
            'render' => $renderDate,
        ],
        [
            'field'       => 'phases_count',
            'label'       => $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Phases',
            'type'        => 'text',
            'headerStyle' => 'text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
        [
            'field'       => 'entries_count',
            'label'       => $data['txt']['COMPETITION_EDITION_ENTRIES'] ?? 'Équipes',
            'type'        => 'text',
            'headerStyle' => 'text-align:center;',
            'cellStyle'   => 'text-align:center;',
        ],
    ],
    'actions' => [
        'editUrl'        => URLROOT . '/sport/competition-edition/edit/{id}',
        'disableUrl'     => URLROOT . '/sport/competition-edition/delete/{id}',
        'disableConfirm' => $data['txt']['COMPETITION_EDITION_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette édition ?',
    ],
];

require 'module/system/view/common/list_template.php';
