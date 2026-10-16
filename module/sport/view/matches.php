<?php
/**
 * Vue : Liste des Matchs
 * Utilise le template parent universel list_template.php
 */

// ── Barre de filtres GET ──────────────────────────────────────────────────────
ob_start(); ?>
<form method="GET" action="<?php echo URLROOT; ?>/sport/matches" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
    <div style="flex: 2; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
        <input type="text" name="search" data-role="input" placeholder="Équipe, lieu..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
    </div>
    <div style="flex: 1.5; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['COMPETITION_COMPETITIONS_MGT'] ?? 'Compétition'; ?></label>
        <select name="competition" data-role="select">
            <option value="">-- Toutes les compétitions --</option>
            <?php foreach ($data['competitions'] as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>" <?php echo ($data['selectedCompetition'] == $c['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 1.5; min-width: 180px;">
        <label class="text-bold d-block"><span class="mif-calendar mr-1"></span><?php echo $data['txt']['MATCH_LBL_ROUND'] ?? 'Journée / Tour'; ?></label>
        <select name="round" data-role="select">
            <option value="">-- Toutes les journées --</option>
            <?php foreach ($data['rounds'] as $r): ?>
                <option value="<?php echo (int)$r['id']; ?>" <?php echo ($data['selectedRound'] == $r['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(($r['competition_name'] ? $r['competition_name'] . ' - ' : '') . $r['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 1.2; min-width: 140px;">
        <label class="text-bold d-block"><span class="mif-filter mr-1"></span><?php echo $data['txt']['MATCH_LBL_STATUS'] ?? 'Statut'; ?></label>
        <select name="status" data-role="select">
            <option value="">-- Tous les statuts --</option>
            <?php foreach ($data['statuses'] as $st): ?>
                <option value="<?php echo (int)$st['id']; ?>" <?php echo ($data['selectedStatus'] == $st['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="flex: 0 0 auto; padding-bottom: 2px;">
        <button type="submit" class="button info mr-1"><span class="mif-filter"></span> <span class="btn-text">Filtrer</span></button>
        <a href="<?php echo URLROOT; ?>/sport/matches" class="button light"><span class="mif-reload"></span> <span class="btn-text">Réinitialiser</span></a>
    </div>
</form>
<?php $matchFiltersHtml = ob_get_clean();

// ── Render Rencontre (Home vs Away avec logos et consultation link) ─────────
$renderMatchTeams = function ($val, $row, $data) {
    $homeName = !empty($row['home_team_name']) ? htmlspecialchars($row['home_team_name']) : 'À déterminer';
    $awayName = !empty($row['away_team_name']) ? htmlspecialchars($row['away_team_name']) : 'À déterminer';

    $homeLogo = '';
    if (!empty($row['home_club_logo'])) {
        $homeLogo = '<img src="' . URLROOT . '/' . ltrim($row['home_club_logo'], '/') . '" alt="" style="width:20px;height:20px;object-fit:contain;vertical-align:middle;margin-right:4px;">';
    }

    $awayLogo = '';
    if (!empty($row['away_club_logo'])) {
        $awayLogo = '<img src="' . URLROOT . '/' . ltrim($row['away_club_logo'], '/') . '" alt="" style="width:20px;height:20px;object-fit:contain;vertical-align:middle;margin-left:4px;">';
    }

    $link = URLROOT . '/sport/match/' . (int)$row['id'];

    $html = '<a href="' . $link . '" class="text-bold fg-primary" title="Consulter le match" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">';
    $html .= '<span>' . $homeLogo . $homeName . '</span>';
    $html .= '<span class="text-muted" style="font-weight:normal;font-size:12px;margin:0 4px;">vs</span>';
    $html .= '<span>' . $awayName . $awayLogo . '</span>';
    $html .= '</a>';

    return $html;
};

// ── Render Score ──────────────────────────────────────────────────────────────
$renderScore = function ($val, $row, $data) {
    $link = URLROOT . '/sport/match/' . (int)$row['id'];
    $isPlayed = ((int)$row['status_id'] === 3);

    if ($row['score_home'] !== null && $row['score_away'] !== null) {
        $badgeClass = $isPlayed ? 'success' : 'info';
        return '<a href="' . $link . '" class="badge ' . $badgeClass . ' text-bold" style="font-size:14px;padding:4px 10px;text-decoration:none;">'
             . (int)$row['score_home'] . ' - ' . (int)$row['score_away'] . '</a>';
    }

    return '<a href="' . $link . '" class="badge secondary" style="padding:4px 8px;text-decoration:none;">— - —</a>';
};

// ── Render Statut du match ────────────────────────────────────────────────────
$renderMatchStatus = function ($val, $row, $data) {
    $label = $data['txt'][$row['status_text_code'] ?? ''] ?? ($row['status_text_code'] ?? 'Programmé');
    $statusId = (int)($row['status_id'] ?? 1);

    $badgeClass = 'info';
    switch ($statusId) {
        case 2: $badgeClass = 'warning'; break; // In progress
        case 3: $badgeClass = 'success'; break; // Played
        case 4: $badgeClass = 'secondary'; break; // Postponed
        case 5: $badgeClass = 'alert'; break; // Cancelled
        case 6: $badgeClass = 'alert'; break; // Forfeited
    }

    return '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($label) . '</span>';
};

// ── Configuration du template universel ───────────────────────────────────────
$listConfig = [
    'title'             => $data['txt']['MATCH_TITLE_LIST'] ?? 'Matchs',
    'icon'              => 'mif-calendar',
    'addUrl'            => URLROOT . '/sport/match',
    'addBtnText'        => $data['txt']['MATCH_BTN_ADD'] ?? 'Ajouter un match',
    'showSearch'        => false,
    'customFiltersHtml' => $matchFiltersHtml,
    'items'             => $data['matches'] ?? [],
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
            'field'       => 'match_date',
            'label'       => $data['txt']['MATCH_LBL_DATE'] ?? 'Date',
            'headerStyle' => 'width:130px;',
            'render'      => function ($val) {
                if (empty($val)) return '<span class="text-muted">—</span>';
                return '<span class="text-bold">' . htmlspecialchars(date('d/m/Y H:i', strtotime($val))) . '</span>';
            }
        ],
        [
            'field'       => 'round_name',
            'label'       => $data['txt']['MATCH_LBL_ROUND'] ?? 'Journée / Tour',
            'render'      => function ($val, $row) {
                $comp = !empty($row['competition_name']) ? htmlspecialchars($row['competition_name']) . '<br>' : '';
                return '<span style="font-size:12px;" class="fg-darkGray">' . $comp . '</span><span class="text-bold">' . htmlspecialchars($row['round_name'] ?? '—') . '</span>';
            }
        ],
        [
            'field'       => 'home_team_name',
            'label'       => 'Rencontre',
            'render'      => $renderMatchTeams,
        ],
        [
            'field'       => 'score',
            'label'       => $data['txt']['MATCH_LBL_SCORE'] ?? 'Score',
            'headerStyle' => 'width:100px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
            'render'      => $renderScore,
        ],
        [
            'field'       => 'venue_name',
            'label'       => $data['txt']['MATCH_LBL_VENUE'] ?? 'Lieu',
            'type'        => 'text',
        ],
        [
            'field'       => 'match_status',
            'label'       => $data['txt']['MATCH_LBL_STATUS'] ?? 'Statut',
            'headerStyle' => 'width:110px;text-align:center;',
            'cellStyle'   => 'text-align:center;',
            'render'      => $renderMatchStatus,
        ],
    ],
    'actions' => [
        'editUrl'         => URLROOT . '/sport/match/edit/{id}',
        'viewUrl'         => URLROOT . '/sport/match/{id}',
        'disableUrl'      => URLROOT . '/sport/match/delete/{id}',
        'activateUrl'     => URLROOT . '/sport/match/activate/{id}',
        'deleteUrl'       => URLROOT . '/sport/match/delete/{id}?force=1',
        'disableConfirm'  => 'Annuler ce match ?',
        'activateConfirm' => 'Réactiver ce match ?',
        'deleteConfirm'   => 'Supprimer définitivement ce match ?',
    ],
];

require 'module/system/view/common/list_template.php';
