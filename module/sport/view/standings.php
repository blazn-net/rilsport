<div class="container-fluid p-4">
    <!-- En-tête -->
    <div class="d-flex flex-row flex-wrap flex-align-center flex-justify-between mb-4" style="gap: 15px;">
        <div class="d-flex flex-row flex-align-center" style="gap: 12px;">
            <span class="mif-table mif-2x fg-primary"></span>
            <div>
                <h2 class="mb-0 text-bold" style="font-size: 24px; color: #1e293b;">
                    <?php echo htmlspecialchars($data['txt']['STANDING_LBL_STANDINGS'] ?? 'Classement'); ?>
                </h2>
                <?php if (!empty($data['selectedGroup'])): ?>
                    <small class="fg-darkGray">
                        <?php echo htmlspecialchars(($data['selectedGroup']['competition_name'] ? $data['selectedGroup']['competition_name'] . ' — ' : '') . ($data['selectedGroup']['edition_name'] ? $data['selectedGroup']['edition_name'] . ' — ' : '') . $data['selectedGroup']['name']); ?>
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($data['isAdmin']) && !empty($data['groupId'])): ?>
            <div class="d-flex flex-row" style="gap: 8px;">
                <a href="<?php echo URLROOT; ?>/sport/standings/recalculate?group=<?php echo (int)$data['groupId']; ?>" 
                   class="button success" title="Recalculer automatiquement les points et le classement à partir des matchs joués">
                    <span class="mif-calculator2"></span>
                    <span class="btn-text ml-1"><?php echo htmlspecialchars($data['txt']['STANDING_BTN_RECALCULATE'] ?? 'Recalculer le classement'); ?></span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alertes Flash -->
    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3 p-3">
            <span class="mif-checkmark mr-1"></span> <?php echo htmlspecialchars($data['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3 p-3">
            <span class="mif-warning mr-1"></span> <?php echo htmlspecialchars($data['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Sélecteur de groupe / filtres -->
    <div class="list-filter-section mb-4">
        <div class="list-filter-summary d-flex flex-justify-between flex-align-center" onclick="this.parentElement.classList.toggle('collapsed')">
            <span><span class="mif-filter mr-1"></span> Filtres de sélection</span>
            <span class="list-filter-arrow">▼</span>
        </div>
        <div class="list-filter-body">
            <form method="GET" action="<?php echo URLROOT; ?>/sport/standings" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
                <div style="flex: 3; min-width: 250px;">
                    <label class="text-bold d-block mb-1"><span class="mif-organization mr-1"></span> Groupe / Poule</label>
                    <select name="group" data-role="select">
                        <option value="">-- Sélectionner un groupe --</option>
                        <?php foreach ($data['groups'] as $g): ?>
                            <option value="<?php echo (int)$g['id']; ?>" <?php echo ($data['groupId'] == $g['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(($g['competition_name'] ? $g['competition_name'] . ' — ' : '') . ($g['edition_name'] ? $g['edition_name'] . ' — ' : '') . $g['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 0 0 auto; padding-bottom: 2px;">
                    <button type="submit" class="button info mr-1"><span class="mif-filter"></span> <span class="btn-text">Afficher</span></button>
                    <a href="<?php echo URLROOT; ?>/sport/standings" class="button light"><span class="mif-reload"></span> <span class="btn-text">Réinitialiser</span></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau de Classement -->
    <div class="card p-3 shadow-1" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;">
        <?php if (empty($data['standings'])): ?>
            <div class="p-5 text-center text-muted">
                <span class="mif-table mif-4x d-block mb-2 fg-gray"></span>
                <p class="text-bold" style="font-size: 16px;">Aucune donnée de classement disponible pour ce groupe.</p>
                <?php if (!empty($data['isAdmin']) && !empty($data['groupId'])): ?>
                    <a href="<?php echo URLROOT; ?>/sport/standings/recalculate?group=<?php echo (int)$data['groupId']; ?>" class="button success mt-2">
                        <span class="mif-calculator2 mr-1"></span> Lancer le premier calcul
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-container" style="overflow-x: auto;">
                <table class="table striped compact cell-border" data-horizontal-scroll="true" style="margin-bottom: 0;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 13px;">
                            <th style="width: 50px; text-align: center;">#</th>
                            <th><?php echo htmlspecialchars($data['txt']['STANDING_LBL_TEAM'] ?? 'Équipe'); ?></th>
                            <th style="width: 60px; text-align: center; background: #e0f2fe; color: #0369a1; font-weight: bold;"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_PTS'] ?? 'Pts'); ?></th>
                            <th style="width: 50px; text-align: center;" title="Matchs joués"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_PLAYED'] ?? 'J'); ?></th>
                            <th style="width: 50px; text-align: center;" title="Victoires"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_WON'] ?? 'G'); ?></th>
                            <th style="width: 50px; text-align: center;" title="Matchs nuls"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_DRAWN'] ?? 'N'); ?></th>
                            <th style="width: 50px; text-align: center;" title="Défaites"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_LOST'] ?? 'P'); ?></th>
                            <th style="width: 55px; text-align: center;" title="Buts pour (marqués)"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_GF'] ?? 'BP'); ?></th>
                            <th style="width: 55px; text-align: center;" title="Buts contre (encaissés)"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_GA'] ?? 'BC'); ?></th>
                            <th style="width: 55px; text-align: center;" title="Différence de buts"><?php echo htmlspecialchars($data['txt']['STANDING_LBL_GD'] ?? 'Diff'); ?></th>
                            <?php if (!empty($data['isAdmin'])): ?>
                                <th style="width: 90px; text-align: center;"><?php echo htmlspecialchars($data['txt']['SYS_COL_ACTIONS'] ?? 'Actions'); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['standings'] as $idx => $row): ?>
                            <?php
                            $rank = (int)($row['rank'] ?? ($idx + 1));
                            $rankBadge = 'secondary';
                            if ($rank === 1) $rankBadge = 'warning';      // 1er = or/doré
                            elseif ($rank <= 3) $rankBadge = 'info';     // Top 3 = bleu
                            ?>
                            <tr>
                                <!-- Rang -->
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge <?php echo $rankBadge; ?> text-bold" style="font-size: 13px; min-width: 24px;">
                                        <?php echo $rank; ?>
                                    </span>
                                </td>

                                <!-- Équipe -->
                                <td style="vertical-align: middle;">
                                    <div class="d-flex flex-row flex-align-center" style="gap: 8px;">
                                        <?php if (!empty($row['club_logo'])): ?>
                                            <img src="<?php echo URLROOT . '/' . ltrim($row['club_logo'], '/'); ?>" alt="" style="width: 24px; height: 24px; object-fit: contain;">
                                        <?php endif; ?>
                                        <a href="<?php echo URLROOT; ?>/sport/standing/<?php echo (int)$row['id']; ?>" class="text-bold fg-primary" style="text-decoration: none;">
                                            <?php echo htmlspecialchars($row['team_name']); ?>
                                        </a>
                                        <?php if (!empty($row['is_manual'])): ?>
                                            <span class="badge warning ml-1" title="Ligne ajustée manuellement<?php echo !empty($row['note']) ? ' : ' . htmlspecialchars($row['note']) : ''; ?>" style="font-size: 10px; cursor: help;">
                                                <span class="mif-lock"></span> Manuel
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Points -->
                                <td style="text-align: center; vertical-align: middle; background: #f0f9ff; font-size: 15px; font-weight: bold; color: #0284c7;">
                                    <?php echo (int)$row['points']; ?>
                                </td>

                                <!-- J, G, N, P, BP, BC, Diff -->
                                <td style="text-align: center; vertical-align: middle;"><?php echo (int)$row['played']; ?></td>
                                <td style="text-align: center; vertical-align: middle; font-weight: 600; color: #16a34a;"><?php echo (int)$row['won']; ?></td>
                                <td style="text-align: center; vertical-align: middle; color: #64748b;"><?php echo (int)$row['drawn']; ?></td>
                                <td style="text-align: center; vertical-align: middle; color: #dc2626;"><?php echo (int)$row['lost']; ?></td>
                                <td style="text-align: center; vertical-align: middle;"><?php echo (int)$row['goals_for']; ?></td>
                                <td style="text-align: center; vertical-align: middle;"><?php echo (int)$row['goals_against']; ?></td>
                                <td style="text-align: center; vertical-align: middle; font-weight: bold; <?php echo ($row['goal_diff'] > 0) ? 'color: #16a34a;' : (($row['goal_diff'] < 0) ? 'color: #dc2626;' : ''); ?>">
                                    <?php echo ($row['goal_diff'] > 0 ? '+' : '') . (int)$row['goal_diff']; ?>
                                </td>

                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Actions admin -->
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="d-flex flex-row flex-justify-center" style="gap: 5px;">
                                            <a href="<?php echo URLROOT; ?>/sport/standing/edit/<?php echo (int)$row['id']; ?>" class="button small info" title="Ajuster manuellement">
                                                <span class="mif-pencil"></span>
                                            </a>
                                            <?php if (!empty($row['is_manual'])): ?>
                                                <a href="<?php echo URLROOT; ?>/sport/standing/reset/<?php echo (int)$row['id']; ?>" class="button small light" title="Réinitialiser en calcul auto" onclick="return confirm('Réinitialiser cette ligne en calcul automatique ?');">
                                                    <span class="mif-reload"></span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.list-filter-section { background: #fff; border: 1px solid #e3e3e3; border-radius: 6px; }
.list-filter-summary { padding: 10px 14px; font-size: 13px; font-weight: 600; color: #444; cursor: pointer; user-select: none; border-bottom: 1px solid #eee; }
.list-filter-arrow { display: inline-block; font-size: 11px; color: #999; transition: transform .15s; }
.list-filter-section.collapsed .list-filter-arrow { transform: rotate(-90deg); }
.list-filter-section.collapsed .list-filter-summary { border-bottom: none; }
.list-filter-body { padding: 12px 14px; }
.list-filter-section .button.info { background-color: #0072c6 !important; border-color: #0072c6 !important; color: #ffffff !important; }
.list-filter-section .button.light { background-color: #e2e8f0 !important; border: 1px solid #cbd5e1 !important; color: #334155 !important; }
</style>
