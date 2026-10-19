<?php
/**
 * Vue : Match (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$item = $data['match'] ?? null;
$isAdmin = !empty($data['isAdmin']);

// Préparation des libellés
$homeName = !empty($item->home_team_name) ? htmlspecialchars($item->home_team_name) : 'À déterminer';
$awayName = !empty($item->away_team_name) ? htmlspecialchars($item->away_team_name) : 'À déterminer';
$compName = !empty($item->competition_name) ? htmlspecialchars($item->competition_name) : '';
$roundName = !empty($item->round_name) ? htmlspecialchars($item->round_name) : '';

$viewTitle = 'Match : ' . $homeName . ' vs ' . $awayName;
if (!empty($compName)) {
    $viewTitle .= ' (' . $compName . ')';
}

$formConfig = [
    'mode'       => $data['mode'] ?? 'view',
    'item'       => $item,
    'icon'       => 'mif-calendar',
    'viewTitle'  => $viewTitle,
    'editTitle'  => $data['txt']['MATCH_TITLE_EDIT'] ?? 'Modifier le match',
    'addTitle'   => $data['txt']['MATCH_TITLE_ADD']  ?? 'Nouveau match',
    'backUrl'    => URLROOT . '/sport/matches',
    'editUrl'    => isset($item->id) ? URLROOT . '/sport/match/edit/' . (int)$item->id : null,
    'cancelUrl'  => isset($item->id) ? URLROOT . '/sport/match/' . (int)$item->id : URLROOT . '/sport/matches',
    'formAction' => URLROOT . '/sport/match' . (($data['mode'] === 'edit' && isset($item->id)) ? '/' . (int)$item->id : ''),

    'viewAvatarIcon' => 'mif-calendar',

    // ── Mode VIEW : Consultation pure (Zéro input) ──────────────────────────
    'viewContent' => function($item, $data) use ($homeName, $awayName, $compName, $roundName, $isAdmin) { ?>

        <!-- Grand bandeau Matchup & Score -->
        <div class="p-4 mb-4 text-center" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 8px; color: #fff;">
            <div class="text-upper text-bold mb-2" style="font-size: 12px; letter-spacing: 1px; color: #94a3b8;">
                <?php echo !empty($compName) ? htmlspecialchars($compName) . ' — ' : ''; ?>
                <?php echo !empty($roundName) ? htmlspecialchars($roundName) : 'Rencontre'; ?>
            </div>

            <div class="row flex-align-center">
                <!-- Équipe Domicile -->
                <div class="cell-md-4 text-center text-right-md mb-2 mb-0-md">
                    <div class="d-inline-flex flex-align-center" style="gap: 10px;">
                        <?php if (!empty($item->home_club_logo)): ?>
                            <img src="<?php echo URLROOT . '/' . ltrim($item->home_club_logo, '/'); ?>" alt="" style="max-height:48px;max-width:48px;object-fit:contain;">
                        <?php endif; ?>
                        <span class="text-bold" style="font-size: 20px;"><?php echo $homeName; ?></span>
                    </div>
                </div>

                <!-- Score central -->
                <div class="cell-md-4 text-center my-2 my-0-md">
                    <?php if ($item->score_home !== null && $item->score_away !== null): ?>
                        <div class="text-bold" style="font-size: 38px; letter-spacing: 4px; color: #38bdf8;">
                            <?php echo (int)$item->score_home; ?> - <?php echo (int)$item->score_away; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-bold text-muted" style="font-size: 28px; letter-spacing: 2px;">
                            — VS —
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item->status_text_code)): ?>
                        <div class="mt-1">
                            <span class="badge info" style="font-size: 11px;">
                                <?php echo htmlspecialchars($data['txt'][$item->status_text_code] ?? $item->status_text_code); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Équipe Extérieur -->
                <div class="cell-md-4 text-center text-left-md mt-2 mt-0-md">
                    <div class="d-inline-flex flex-align-center" style="gap: 10px;">
                        <span class="text-bold" style="font-size: 20px;"><?php echo $awayName; ?></span>
                        <?php if (!empty($item->away_club_logo)): ?>
                            <img src="<?php echo URLROOT . '/' . ltrim($item->away_club_logo, '/'); ?>" alt="" style="max-height:48px;max-width:48px;object-fit:contain;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails du Match (Blocs stylisés) -->
        <div class="row">
            <!-- Date & Heure -->
            <div class="cell-md-4 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-calendar fg-blue mr-1"></span> <?php echo $data['txt']['MATCH_LBL_DATE'] ?? 'Date'; ?>
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->match_date) ? htmlspecialchars(date('d/m/Y H:i', strtotime($item->match_date))) : '—'; ?>
                    </div>
                </div>
            </div>

            <!-- Lieu / Stade -->
            <div class="cell-md-4 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-location fg-red mr-1"></span> <?php echo $data['txt']['MATCH_LBL_VENUE'] ?? 'Lieu'; ?>
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->venue_name) ? htmlspecialchars($item->venue_name) : '—'; ?>
                    </div>
                </div>
            </div>

            <!-- Journée / Tour -->
            <div class="cell-md-4 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-organization fg-teal mr-1"></span> <?php echo $data['txt']['MATCH_LBL_ROUND'] ?? 'Journée / Tour'; ?>
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->round_name) ? htmlspecialchars($item->round_name) : '—'; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($item->note)): ?>
        <div class="mb-3">
            <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                <div class="text-muted text-upper text-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                    <span class="mif-info fg-amber mr-1"></span> <?php echo $data['txt']['MATCH_LBL_NOTE'] ?? 'Note'; ?>
                </div>
                <div style="color: #334155; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($item->note)); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <!-- Bloc Admin : Saisie rapide du score -->
        <div class="card mt-3 mb-3 p-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;">
            <div class="text-bold fg-emerald mb-2" style="font-size: 14px;">
                <span class="mif-scoreboard mr-1"></span> <?php echo $data['txt']['MATCH_BTN_ENTER_SCORE'] ?? 'Saisie rapide du score'; ?>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/sport/match/score/<?php echo (int)$item->id; ?>" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
                <div style="flex: 1; min-width: 120px;">
                    <label class="text-bold" style="font-size: 12px;"><?php echo $homeName; ?></label>
                    <input type="number" name="score_home" min="0" max="999" data-role="input" value="<?php echo ($item->score_home !== null) ? (int)$item->score_home : ''; ?>" required>
                </div>
                <div style="flex: 1; min-width: 120px;">
                    <label class="text-bold" style="font-size: 12px;"><?php echo $awayName; ?></label>
                    <input type="number" name="score_away" min="0" max="999" data-role="input" value="<?php echo ($item->score_away !== null) ? (int)$item->score_away : ''; ?>" required>
                </div>
                <div style="flex: 1; min-width: 160px;">
                    <label class="text-bold" style="font-size: 12px;"><?php echo $data['txt']['MATCH_LBL_STATUS'] ?? 'Statut'; ?></label>
                    <select name="status_id" data-role="select">
                        <option value="3" <?php echo ($item->status_id == 3) ? 'selected' : ''; ?>>Joué (Terminé)</option>
                        <option value="2" <?php echo ($item->status_id == 2) ? 'selected' : ''; ?>>En cours</option>
                        <option value="1" <?php echo ($item->status_id == 1) ? 'selected' : ''; ?>>Programmé</option>
                        <option value="4" <?php echo ($item->status_id == 4) ? 'selected' : ''; ?>>Reporté</option>
                        <option value="5" <?php echo ($item->status_id == 5) ? 'selected' : ''; ?>>Annulé</option>
                        <option value="6" <?php echo ($item->status_id == 6) ? 'selected' : ''; ?>>Forfait</option>
                    </select>
                </div>
                <div style="flex: 0 0 auto;">
                    <button type="submit" class="button success">
                        <span class="mif-floppy-disk mr-1"></span> Enregistrer le score
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    <?php },

    // ── Mode EDIT / ADD : Formulaire interactif (Admin) ──────────────────────
    'formContent' => function($item, $data, $mode) { ?>

        <!-- Journée / Tour -->
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_ROUND'] ?? 'Journée / Tour'; ?> <span class="fg-red">*</span></label>
            <select name="round_id" data-role="select" required>
                <option value="">-- Sélectionner une journée --</option>
                <?php foreach ($data['rounds'] as $r): ?>
                    <option value="<?php echo (int)$r['id']; ?>" <?php echo (isset($item->round_id) && (int)$item->round_id === (int)$r['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(($r['competition_name'] ? $r['competition_name'] . ' — ' : '') . ($r['edition_name'] ? $r['edition_name'] . ' — ' : '') . $r['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <!-- Équipe Domicile -->
            <div class="cell-md-6 mb-3">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_HOME_TEAM'] ?? 'Équipe domicile'; ?></label>
                    <select name="home_team_id" data-role="select">
                        <option value="">-- À déterminer --</option>
                        <?php foreach ($data['teams'] as $tm): ?>
                            <option value="<?php echo (int)$tm['id']; ?>" <?php echo (isset($item->home_team_id) && (int)$item->home_team_id === (int)$tm['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tm['name'] . ($tm['club_name'] ? ' (' . $tm['club_name'] . ')' : '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Équipe Extérieur -->
            <div class="cell-md-6 mb-3">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_AWAY_TEAM'] ?? 'Équipe extérieur'; ?></label>
                    <select name="away_team_id" data-role="select">
                        <option value="">-- À déterminer --</option>
                        <?php foreach ($data['teams'] as $tm): ?>
                            <option value="<?php echo (int)$tm['id']; ?>" <?php echo (isset($item->away_team_id) && (int)$item->away_team_id === (int)$tm['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tm['name'] . ($tm['club_name'] ? ' (' . $tm['club_name'] . ')' : '')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Date & Heure -->
            <div class="cell-md-6 mb-3">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_DATE'] ?? 'Date et heure'; ?></label>
                    <?php
                    $valDate = '';
                    if (!empty($item->match_date)) {
                        $valDate = date('Y-m-d\TH:i', strtotime($item->match_date));
                    }
                    ?>
                    <input type="datetime-local" name="match_date" data-role="input" value="<?php echo htmlspecialchars($valDate); ?>">
                </div>
            </div>

            <!-- Lieu / Salle / Stade -->
            <div class="cell-md-6 mb-3">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_VENUE'] ?? 'Lieu / Stade / Salle'; ?></label>
                    <input type="text" name="venue_name" data-role="input" placeholder="ex: Stade Municipal, Parc des Princes..."
                           value="<?php echo htmlspecialchars($item->venue_name ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Score Domicile -->
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Score Domicile</label>
                    <input type="number" name="score_home" min="0" max="999" data-role="input"
                           value="<?php echo (isset($item->score_home) && $item->score_home !== null) ? (int)$item->score_home : ''; ?>">
                </div>
            </div>

            <!-- Score Extérieur -->
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Score Extérieur</label>
                    <input type="number" name="score_away" min="0" max="999" data-role="input"
                           value="<?php echo (isset($item->score_away) && $item->score_away !== null) ? (int)$item->score_away : ''; ?>">
                </div>
            </div>

            <!-- Statut du match -->
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_STATUS'] ?? 'Statut'; ?></label>
                    <select name="status_id" data-role="select">
                        <?php foreach ($data['statuses'] as $st): ?>
                            <option value="<?php echo (int)$st['id']; ?>" <?php echo (isset($item->status_id) && (int)$item->status_id === (int)$st['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Note / Remarques -->
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['MATCH_LBL_NOTE'] ?? 'Notes ou commentaires'; ?></label>
            <textarea name="note" data-role="textarea" rows="3" placeholder="Informations complémentaires, arbitres, faits marquants..."><?php echo htmlspecialchars($item->note ?? ''); ?></textarea>
        </div>

    <?php }
];

require 'module/system/view/common/entity_template.php';
