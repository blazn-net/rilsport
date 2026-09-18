<!-- Liste des Editions de competitions -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-calendar mr-2"></span><?php echo $data['txt']['COMPETITION_EDITIONS_MGT'] ?? 'Editions'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition" class="button success mt-2 mt-md-0">
            <span class="mif-plus"></span> <?php echo $data['txt']['COMPETITION_ADD_EDITION_BTN'] ?? 'Nouvelle edition'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?><div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>

    <!-- Filtres -->
    <div class="p-3 mb-4 bg-light border bd-default border-radius-4">
        <form method="GET" action="<?php echo URLROOT; ?>/sport/competition-editions" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
            <div style="flex: 2; min-width: 180px;">
                <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
                <input type="text" name="search" data-role="input" placeholder="Nom, code..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>
            <div style="flex: 1.5; min-width: 200px;">
                <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Competition'; ?></label>
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
                <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> Filtrer</button>
                <a href="<?php echo URLROOT; ?>/sport/competition-editions" class="button secondary">Reinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <table class="table striped table-border mt-3 w-100 table-responsive-cards" data-role="table"
           data-rows="25" data-rows-steps="10,25,50" data-show-search="true">
        <thead>
            <tr>
                <th><?php echo $data['txt']['COMPETITION_EDITION_TABLE_NAME'] ?? 'Edition'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Competition'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Debut'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></th>
                <th class="text-center"><?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Phases'; ?></th>
                <th class="text-center"><?php echo $data['txt']['COMPETITION_EDITION_ENTRIES'] ?? 'Equipes'; ?></th>
                <?php if (!empty($data['isAdmin'])): ?>
                <th class="text-center"><?php echo $data['txt']['SYS_ACTIONS'] ?? 'Actions'; ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['editions'])): ?>
                <tr><td colspan="<?php echo !empty($data['isAdmin']) ? 8 : 7; ?>" class="text-center text-muted">Aucune edition trouvee.</td></tr>
            <?php else: ?>
                <?php foreach ($data['editions'] as $e): ?>
                <tr>
                    <td>
                        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$e['id']; ?>">
                            <strong><?php echo htmlspecialchars($e['name']); ?></strong>
                        </a>
                        <?php if (!empty($e['parent_edition_name'])): ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars($e['parent_edition_name']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$e['competition_id']; ?>">
                            <?php echo htmlspecialchars($e['competition_name'] ?? ''); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($e['season_name'] ?? '—'); ?></td>
                    <td><?php echo !empty($e['date_start']) ? date('d/m/Y', strtotime($e['date_start'])) : '—'; ?></td>
                    <td><?php echo !empty($e['date_end'])   ? date('d/m/Y', strtotime($e['date_end']))   : '—'; ?></td>
                    <td class="text-center"><?php echo (int)($e['phases_count'] ?? 0); ?></td>
                    <td class="text-center"><?php echo (int)($e['entries_count'] ?? 0); ?></td>
                    <?php if (!empty($data['isAdmin'])): ?>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="<?php echo URLROOT; ?>/sport/competition-edition/edit/<?php echo (int)$e['id']; ?>" class="button small warning" title="Modifier">
                            <span class="mif-pencil"></span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/sport/competition-edition/delete/<?php echo (int)$e['id']; ?>"
                           class="button small alert" title="Desactiver"
                           onclick="return confirm('<?php echo htmlspecialchars($data['txt']['COMPETITION_EDITION_DELETE_CONFIRM'] ?? 'Desactiver ?'); ?>')">
                            <span class="mif-bin"></span>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
