<!-- Contenu principal Liste des Competitions -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-trophy mr-2"></span><?php echo $data['txt']['COMPETITION_COMPETITIONS_MGT'] ?? 'Competitions'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/competition" class="button success mt-2 mt-md-0">
            <span class="mif-plus"></span> <?php echo $data['txt']['COMPETITION_ADD_BTN'] ?? 'Nouvelle competition'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Barre de filtres -->
    <div class="p-3 mb-4 bg-light border bd-default border-radius-4">
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
                <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> Filtrer</button>
                <a href="<?php echo URLROOT; ?>/sport/competitions" class="button secondary">Reinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau des Competitions -->
    <div class="table-scroll-wrapper"><table class="table striped table-border mt-3 w-100 " data-role="table"
           data-rows="25" data-rows-steps="10,25,50,100" data-show-search="true"
           data-search-placeholder="Filtrer..." data-show-rows-steps="true">
        <thead>
            <tr>
                <th><?php echo $data['txt']['COMPETITION_TABLE_NAME'] ?? 'Nom'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?></th>
                <th><?php echo $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays'; ?></th>
                <th class="text-center">Editions</th>
                <?php if (!empty($data['isAdmin'])): ?>
                <th class="text-center"><?php echo $data['txt']['SYS_ACTIONS'] ?? 'Actions'; ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['competitions'])): ?>
                <tr><td colspan="<?php echo !empty($data['isAdmin']) ? 6 : 5; ?>" class="text-center text-muted">Aucune competition trouvee.</td></tr>
            <?php else: ?>
                <?php foreach ($data['competitions'] as $c): ?>
                <tr>
                    <td>
                        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$c['id']; ?>">
                            <?php if (!empty($c['logo'])): ?>
                                <img src="<?php echo URLROOT . '/' . htmlspecialchars($c['logo']); ?>" alt="" style="height:20px;width:auto;margin-right:6px;vertical-align:middle;">
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                            <?php if (!empty($c['acronym'])): ?>
                                <span class="text-muted ml-1">(<?php echo htmlspecialchars($c['acronym']); ?>)</span>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($c['sport_name'] ?? ''); ?></td>
                    <td>
                        <span class="badge inline bg-dark fg-white">
                            <?php echo htmlspecialchars($data['txt'][$c['type_text_code'] ?? ''] ?? ($c['type_text_code'] ?? '')); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($c['country_name'] ?? '—'); ?></td>
                    <td class="text-center"><?php echo (int)($c['editions_count'] ?? 0); ?></td>
                    <?php if (!empty($data['isAdmin'])): ?>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="<?php echo URLROOT; ?>/sport/competition/edit/<?php echo (int)$c['id']; ?>" class="button small warning" title="Modifier">
                            <span class="mif-pencil"></span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/sport/competition/delete/<?php echo (int)$c['id']; ?>"
                           class="button small alert" title="Desactiver"
                           onclick="return confirm('<?php echo htmlspecialchars($data['txt']['COMPETITION_DELETE_CONFIRM'] ?? 'Desactiver ?'); ?>')">
                            <span class="mif-bin"></span>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->
</main>


