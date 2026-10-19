<?php
/**
 * Vue : Ligne de Classement (Consultation View & Formulaire Edit)
 * Utilise le template parent universel form_template.php
 */

$item = $data['standing'] ?? null;
$teamName = !empty($item->team_name) ? htmlspecialchars($item->team_name) : 'Équipe';
$groupName = !empty($item->group_name) ? htmlspecialchars($item->group_name) : '';
$compName = !empty($item->competition_name) ? htmlspecialchars($item->competition_name) : '';

$viewTitle = 'Classement : ' . $teamName;
if (!empty($groupName)) {
    $viewTitle .= ' (' . $groupName . ')';
}

$backUrl = URLROOT . '/sport/standings' . (!empty($item->group_id) ? '?group=' . (int)$item->group_id : '');

$formConfig = [
    'mode'       => $data['mode'] ?? 'view',
    'item'       => $item,
    'icon'       => 'mif-table',
    'viewTitle'  => $viewTitle,
    'editTitle'  => $data['txt']['STANDING_BTN_EDIT'] ?? 'Modifier le classement',
    'addTitle'   => 'Ajouter une entrée de classement',
    'backUrl'    => $backUrl,
    'editUrl'    => isset($item->id) ? URLROOT . '/sport/standing/edit/' . (int)$item->id : null,
    'cancelUrl'  => isset($item->id) ? URLROOT . '/sport/standing/' . (int)$item->id : $backUrl,
    'formAction' => URLROOT . '/sport/standing/' . (int)($item->id ?? 0),

    'viewAvatarIcon' => 'mif-trophy',

    // ── Mode VIEW : Consultation pure (Zéro input) ──────────────────────────
    'viewContent' => function($item, $data) use ($teamName, $groupName, $compName) { ?>

        <!-- Bandeau d'en-tête de la ligne -->
        <div class="p-3 mb-4 d-flex flex-row flex-wrap flex-align-center flex-justify-between" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
            <div class="d-flex flex-row flex-align-center" style="gap: 12px;">
                <?php if (!empty($item->club_logo)): ?>
                    <img src="<?php echo URLROOT . '/' . ltrim($item->club_logo, '/'); ?>" alt="" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                <?php endif; ?>
                <div>
                    <h3 class="mb-0 text-bold" style="font-size: 20px; color: #1e293b;"><?php echo $teamName; ?></h3>
                    <small class="fg-darkGray"><?php echo htmlspecialchars(($compName ? $compName . ' — ' : '') . $groupName); ?></small>
                </div>
            </div>

            <div class="d-flex flex-row flex-align-center" style="gap: 10px;">
                <?php if (!empty($item->is_manual)): ?>
                    <span class="badge warning" title="Ligne ajustée manuellement">
                        <span class="mif-lock"></span> Modification manuelle
                    </span>
                <?php else: ?>
                    <span class="badge info" title="Calcul automatique à partir des matchs">
                        <span class="mif-sync"></span> Calcul automatique
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistiques Clés (Cartes récapitulatives) -->
        <div class="row mb-3">
            <!-- Rang -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #fefce8; border: 1px solid #fef08a; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Rang</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #ca8a04;">
                        #<?php echo (int)($item->rank ?? 0); ?>
                    </div>
                </div>
            </div>

            <!-- Points -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Points</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #0284c7;">
                        <?php echo (int)$item->points; ?>
                    </div>
                </div>
            </div>

            <!-- Joués -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Joués (J)</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #334155;">
                        <?php echo (int)$item->played; ?>
                    </div>
                </div>
            </div>

            <!-- Gagnés -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Gagnés (G)</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #16a34a;">
                        <?php echo (int)$item->won; ?>
                    </div>
                </div>
            </div>

            <!-- Nuls -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Nuls (N)</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #64748b;">
                        <?php echo (int)$item->drawn; ?>
                    </div>
                </div>
            </div>

            <!-- Perdus -->
            <div class="cell-md-2 cell-sm-4 mb-2">
                <div class="p-3 text-center" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;">
                    <div class="text-upper text-bold text-muted" style="font-size: 11px;">Perdus (P)</div>
                    <div class="text-bold mt-1" style="font-size: 26px; color: #dc2626;">
                        <?php echo (int)$item->lost; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buts & Différence -->
        <div class="row mb-3">
            <div class="cell-md-4 mb-2">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px;">Buts Pour (BP)</div>
                    <div class="text-bold mt-1" style="font-size: 18px; color: #1e293b;"><?php echo (int)$item->goals_for; ?></div>
                </div>
            </div>
            <div class="cell-md-4 mb-2">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px;">Buts Contre (BC)</div>
                    <div class="text-bold mt-1" style="font-size: 18px; color: #1e293b;"><?php echo (int)$item->goals_against; ?></div>
                </div>
            </div>
            <div class="cell-md-4 mb-2">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px;">Différence (Diff)</div>
                    <div class="text-bold mt-1" style="font-size: 18px; <?php echo ($item->goal_diff > 0) ? 'color: #16a34a;' : (($item->goal_diff < 0) ? 'color: #dc2626;' : 'color: #1e293b;'); ?>">
                        <?php echo ($item->goal_diff > 0 ? '+' : '') . (int)$item->goal_diff; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($item->note)): ?>
        <div class="mb-3">
            <div class="p-3" style="background: #fefce8; border: 1px solid #fef08a; border-radius: 6px;">
                <div class="text-muted text-upper text-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">
                    <span class="mif-info fg-amber mr-1"></span> Note ou pénalité
                </div>
                <div style="color: #854d0e; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($item->note)); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php },

    // ── Mode EDIT : Formulaire interactif d'ajustement manuel (Admin) ─────────
    'formContent' => function($item, $data, $mode) { ?>

        <div class="remark warning mb-3 p-3">
            <span class="mif-warning mr-1"></span>
            <strong>Attention :</strong> La modification manuelle de ces valeurs verrouille cette ligne (statut manuel). Le prochain calcul automatique ne modifiera plus ces valeurs à moins d'utiliser l'action « Réinitialiser en calcul auto ».
        </div>

        <div class="row">
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Rang (#)</label>
                    <input type="number" name="rank" min="1" max="99" data-role="input" value="<?php echo htmlspecialchars($item->rank ?? ''); ?>">
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Points (Pts)</label>
                    <input type="number" name="points" data-role="input" value="<?php echo htmlspecialchars($item->points ?? 0); ?>" required>
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Matchs Joués (J)</label>
                    <input type="number" name="played" min="0" data-role="input" value="<?php echo htmlspecialchars($item->played ?? 0); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Victoires (G)</label>
                    <input type="number" name="won" min="0" data-role="input" value="<?php echo htmlspecialchars($item->won ?? 0); ?>">
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Nuls (N)</label>
                    <input type="number" name="drawn" min="0" data-role="input" value="<?php echo htmlspecialchars($item->drawn ?? 0); ?>">
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Défaites (P)</label>
                    <input type="number" name="lost" min="0" data-role="input" value="<?php echo htmlspecialchars($item->lost ?? 0); ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Buts Pour (BP)</label>
                    <input type="number" name="goals_for" min="0" data-role="input" value="<?php echo htmlspecialchars($item->goals_for ?? 0); ?>">
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Buts Contre (BC)</label>
                    <input type="number" name="goals_against" min="0" data-role="input" value="<?php echo htmlspecialchars($item->goals_against ?? 0); ?>">
                </div>
            </div>

            <div class="cell-md-4 mb-3">
                <div class="form-group">
                    <label class="text-bold">Différence (Diff)</label>
                    <input type="number" name="goal_diff" data-role="input" value="<?php echo htmlspecialchars($item->goal_diff ?? 0); ?>">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="text-bold">Note explicative (pénalité de points, décision fédérale...)</label>
            <textarea name="note" data-role="textarea" rows="3"><?php echo htmlspecialchars($item->note ?? ''); ?></textarea>
        </div>

    <?php }
];

require 'module/system/view/common/entity_template.php';
