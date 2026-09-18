<!-- Gestion des inscriptions d une edition -->
<main class="p-4" style="margin-top: 60px;">

    <?php if (!empty($data['message'])): ?><div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>

    <!-- Fil d ariane -->
    <?php if (!empty($data['editionData'])): ?>
    <nav class="breadcrumbs mb-3">
        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['editionData']->competition_id; ?>"><?php echo htmlspecialchars($data['editionData']->competition_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['editionData']->id; ?>"><?php echo htmlspecialchars($data['editionData']->name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <span>Inscriptions</span>
    </nav>
    <?php endif; ?>

    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2 class="mb-0">
            <span class="mif-groups mr-2"></span>
            Inscriptions — <?php echo htmlspecialchars($data['editionData']->name ?? ''); ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['editionData']->id; ?>" class="button secondary">
            <span class="mif-arrow-left mr-1"></span>Retour a l'edition
        </a>
    </div>

    <div class="grid">
        <!-- Formulaire d inscription -->
        <?php if (!empty($data['availableTeams'])): ?>
        <div class="row mb-4">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4">
                    <h5 class="mb-3"><span class="mif-plus mr-1"></span>Inscrire des equipes</h5>
                    <form method="POST" action="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['editionData']->id; ?>">
                        <input type="hidden" name="_action" value="add">
                        <div class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
                            <div style="flex: 2; min-width: 200px;">
                                <label class="text-bold d-block">Equipes</label>
                                <select name="team_ids[]" id="entry-teams" data-role="select" multiple>
                                    <?php foreach ($data['availableTeams'] as $t): ?>
                                    <option value="<?php echo (int)$t['id']; ?>">
                                        <?php echo htmlspecialchars($t['club_name'] . ' — ' . $t['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Selection multiple possible (Ctrl+clic)</small>
                            </div>
                            <?php if (!empty($data['groups'])): ?>
                            <div style="flex: 1; min-width: 160px;">
                                <label class="text-bold d-block">Groupe / Poule</label>
                                <select name="group_id" id="entry-group" data-role="select">
                                    <option value="">-- Sans groupe --</option>
                                    <?php
                                    $currentPhase = '';
                                    foreach ($data['groups'] as $g):
                                        if ($g['phase_name'] !== $currentPhase):
                                            $currentPhase = $g['phase_name'];
                                    ?>
                                    <optgroup label="<?php echo htmlspecialchars($currentPhase); ?>">
                                    <?php endif; ?>
                                        <option value="<?php echo (int)$g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div style="flex: 0 0 auto; padding-bottom: 2px;">
                                <button type="submit" class="button success" id="entry-add-btn">
                                    <span class="mif-plus mr-1"></span>Inscrire
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row mb-4">
            <div class="cell-12">
                <div class="remark info">Toutes les equipes disponibles pour ce sport sont deja inscrites.</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Liste des inscrits -->
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4">
                    <h5 class="mb-3">
                        <span class="mif-list mr-1"></span>
                        Equipes inscrites
                        <span class="badge inline bg-dark fg-white ml-2"><?php echo count($data['entries']); ?></span>
                    </h5>
                    <?php if (empty($data['entries'])): ?>
                        <p class="text-muted text-center">Aucune equipe inscrite pour le moment.</p>
                    <?php else: ?>
                    <table class="table striped table-border compact w-100" data-role="table" data-rows="50">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Equipe</th>
                                <th>Groupe / Poule</th>
                                <th>Phase</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['entries'] as $e): ?>
                            <tr>
                                <td><?php echo !empty($e['entry_order']) ? (int)$e['entry_order'] : '—'; ?></td>
                                <td><strong><?php echo htmlspecialchars($e['team_name'] ?? ''); ?></strong></td>
                                <td><?php echo htmlspecialchars($e['group_name'] ?? '—'); ?></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($e['phase_name'] ?? '—'); ?></small></td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <!-- Changer de groupe -->
                                    <?php if (!empty($data['groups'])): ?>
                                    <form method="POST" action="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['editionData']->id; ?>" style="display:inline-flex;gap:4px;align-items:center;">
                                        <input type="hidden" name="_action" value="update_group">
                                        <input type="hidden" name="entry_id" value="<?php echo (int)$e['id']; ?>">
                                        <input type="hidden" name="entry_order" value="<?php echo (int)($e['entry_order'] ?? 1); ?>">
                                        <select name="group_id" style="width:130px;font-size:.85em;">
                                            <option value="">— Aucun —</option>
                                            <?php foreach ($data['groups'] as $g): ?>
                                            <option value="<?php echo (int)$g['id']; ?>" <?php echo ($e['group_id'] == $g['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($g['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="button small primary" title="Changer de groupe"><span class="mif-checkmark"></span></button>
                                    </form>
                                    <?php else: ?>
                                    <span class="text-muted">—</span>
                                    <?php endif; ?>
                                    <!-- Desinscrire -->
                                    <form method="POST" action="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['editionData']->id; ?>" style="display:inline;">
                                        <input type="hidden" name="_action" value="remove">
                                        <input type="hidden" name="entry_id" value="<?php echo (int)$e['id']; ?>">
                                        <button type="submit" class="button small alert ml-1" title="Desinscrire" onclick="return confirm('Desinscrire cette equipe ?')">
                                            <span class="mif-bin"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
