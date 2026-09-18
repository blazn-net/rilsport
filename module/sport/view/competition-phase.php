<!-- Fiche / Formulaire Phase de competition -->
<main class="p-4" style="margin-top: 60px;">

    <?php if (!empty($data['message'])): ?><div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>

    <!-- Fil d ariane -->
    <?php if (!empty($data['phase'])): ?>
    <nav class="breadcrumbs mb-3">
        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['phase']->competition_id; ?>"><?php echo htmlspecialchars($data['phase']->competition_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['phase']->edition_id; ?>"><?php echo htmlspecialchars($data['phase']->edition_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <span><?php echo htmlspecialchars($data['phase']->name ?? ''); ?></span>
    </nav>
    <?php elseif (!empty($data['editionData'])): ?>
    <nav class="breadcrumbs mb-3">
        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['editionData']['competition_id']; ?>"><?php echo htmlspecialchars($data['editionData']['competition_name'] ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['editionData']['id']; ?>"><?php echo htmlspecialchars($data['editionData']['name'] ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <span>Nouvelle phase</span>
    </nav>
    <?php endif; ?>

    <!-- En-tete -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2 class="mb-0">
            <span class="mif-layers mr-2"></span>
            <?php echo htmlspecialchars($data['phase']->name ?? ($data['txt']['COMPETITION_PHASE_ADD_TITLE'] ?? 'Nouvelle phase')); ?>
        </h2>
        <div class="d-flex" style="gap: 8px;">
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && !empty($data['phase'])): ?>
                <a href="<?php echo URLROOT; ?>/sport/competition-phase/edit/<?php echo (int)$data['phase']->id; ?>" class="button warning">
                    <span class="mif-pencil mr-1"></span><?php echo $data['txt']['SYS_EDIT'] ?? 'Modifier'; ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)($data['phase']->edition_id ?? $data['editionData']['id'] ?? 0); ?>" class="button secondary">
                <span class="mif-arrow-left mr-1"></span><?php echo $data['txt']['SYS_BACK'] ?? 'Retour'; ?>
            </a>
        </div>
    </div>

    <?php if ($data['mode'] === 'view'): ?>
    <!-- ====== VIEW ====== -->
    <div class="grid">
        <div class="row">
            <div class="cell-md-8">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3">Informations</h5>
                    <table class="table compact w-100">
                        <tbody>
                            <tr><td class="text-bold" style="width:35%">Code</td><td><code><?php echo htmlspecialchars($data['phase']->code ?? ''); ?></code></td></tr>
                            <tr><td class="text-bold">Nom</td><td><?php echo htmlspecialchars($data['phase']->name ?? ''); ?></td></tr>
                            <tr>
                                <td class="text-bold">Type</td>
                                <td>
                                    <?php $types = ['league'=>'Championnat','cup'=>'Elimination directe','tournament'=>'Tournoi','ranking'=>'Classement']; ?>
                                    <span class="badge inline bg-dark fg-white"><?php echo htmlspecialchars($types[$data['phase']->phase_type ?? ''] ?? ($data['phase']->phase_type ?? '')); ?></span>
                                </td>
                            </tr>
                            <tr><td class="text-bold">Ordre</td><td><?php echo (int)($data['phase']->phase_order ?? 1); ?></td></tr>
                            <tr><td class="text-bold">Debut</td><td><?php echo !empty($data['phase']->date_start) ? date('d/m/Y', strtotime($data['phase']->date_start)) : '—'; ?></td></tr>
                            <tr><td class="text-bold">Fin</td><td><?php echo !empty($data['phase']->date_end)   ? date('d/m/Y', strtotime($data['phase']->date_end))   : '—'; ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cell-md-4">
                <div class="p-3 border bd-default border-radius-4 mb-4 text-center">
                    <div style="font-size:2em;font-weight:700;"><?php echo count($data['groups']); ?></div>
                    <div class="text-muted">Groupes / Poules</div>
                    <hr>
                    <div style="font-size:2em;font-weight:700;"><?php echo count($data['rounds']); ?></div>
                    <div class="text-muted">Journees / Tours</div>
                </div>
            </div>
        </div>

        <!-- Groupes -->
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="mb-0"><span class="mif-users mr-1"></span>Groupes / Poules</h5>
                        <?php if (!empty($data['isAdmin'])): ?>
                        <a href="<?php echo URLROOT; ?>/sport/competition-group?phase_id=<?php echo (int)$data['phase']->id; ?>" class="button small success">
                            <span class="mif-plus"></span> Nouveau groupe
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($data['groups'])): ?>
                        <p class="text-muted text-center">Aucun groupe defini pour cette phase.</p>
                    <?php else: ?>
                    <table class="table striped compact w-100">
                        <thead><tr>
                            <th>#</th><th>Nom</th><th>Code</th><th>Parent</th>
                            <?php if (!empty($data['isAdmin'])): ?><th class="text-center">Actions</th><?php endif; ?>
                        </tr></thead>
                        <tbody>
                            <?php foreach ($data['groups'] as $g): ?>
                            <tr>
                                <td><?php echo (int)$g['group_order']; ?></td>
                                <td><a href="<?php echo URLROOT; ?>/sport/competition-group/<?php echo (int)$g['id']; ?>"><?php echo htmlspecialchars($g['name']); ?></a></td>
                                <td><code><?php echo htmlspecialchars($g['code']); ?></code></td>
                                <td><?php echo htmlspecialchars($g['parent_group_name'] ?? '—'); ?></td>
                                <?php if (!empty($data['isAdmin'])): ?>
                                <td class="text-center" style="white-space:nowrap;">
                                    <a href="<?php echo URLROOT; ?>/sport/competition-group/edit/<?php echo (int)$g['id']; ?>" class="button small warning"><span class="mif-pencil"></span></a>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-group/delete/<?php echo (int)$g['id']; ?>" class="button small alert" onclick="return confirm('Supprimer ?')"><span class="mif-bin"></span></a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ====== FORM ADD/EDIT ====== -->
    <form method="POST" action="<?php echo URLROOT; ?>/sport/competition-phase<?php echo !empty($data['phase']) ? '/edit/' . (int)$data['phase']->id : ''; ?>">
        <div class="grid">
            <div class="row">
                <div class="cell-md-8">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Informations</h5>
                        <input type="hidden" name="edition_id" value="<?php echo (int)($data['phase']->edition_id ?? $data['editionData']['id'] ?? 0); ?>">

                        <div class="form-group">
                            <label>Code <span class="text-alert">*</span></label>
                            <input type="text" name="code" id="phase-code" data-role="input"
                                   value="<?php echo htmlspecialchars($data['phase']->code ?? ''); ?>"
                                   <?php echo !empty($data['phase']) ? 'readonly' : ''; ?>
                                   placeholder="ex: poules, finale">
                        </div>
                        <div class="form-group">
                            <label>Nom <span class="text-alert">*</span></label>
                            <input type="text" name="name" id="phase-name" data-role="input"
                                   value="<?php echo htmlspecialchars($data['phase']->name ?? ''); ?>"
                                   placeholder="ex: Phase de groupes, Demi-finales...">
                        </div>
                        <div class="row">
                            <div class="cell-md-4">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="phase_type" id="phase-type" data-role="select">
                                        <?php foreach ($data['phaseTypes'] as $typeKey => $typeLabel): ?>
                                        <option value="<?php echo htmlspecialchars($typeKey); ?>" <?php echo (isset($data['phase']->phase_type) && $data['phase']->phase_type === $typeKey) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($typeLabel); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="cell-md-4">
                                <div class="form-group">
                                    <label>Ordre d affichage</label>
                                    <input type="number" name="phase_order" id="phase-order" data-role="input" min="1"
                                           value="<?php echo (int)($data['phase']->phase_order ?? $data['nextOrder']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label>Debut</label>
                                    <input type="date" name="date_start" id="phase-date-start" data-role="input" value="<?php echo htmlspecialchars($data['phase']->date_start ?? ''); ?>">
                                </div>
                            </div>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label>Fin</label>
                                    <input type="date" name="date_end" id="phase-date-end" data-role="input" value="<?php echo htmlspecialchars($data['phase']->date_end ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cell-md-4">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Statut</h5>
                        <select name="status_id" id="phase-status" data-role="select">
                            <?php foreach ($data['statuses'] as $st): ?>
                            <option value="<?php echo (int)$st['id']; ?>" <?php echo (isset($data['phase']->status_id) && $data['phase']->status_id == $st['id']) ? 'selected' : (($st['id']==1)?'selected':''); ?>>
                                <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="cell-12">
                    <button type="submit" class="button primary mr-2" id="phase-submit">
                        <span class="mif-checkmark mr-1"></span><?php echo !empty($data['phase']) ? ($data['txt']['SYS_SAVE'] ?? 'Enregistrer') : 'Creer'; ?>
                    </button>
                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)($data['phase']->edition_id ?? $data['editionData']['id'] ?? 0); ?>" class="button secondary">
                        <span class="mif-cancel mr-1"></span><?php echo $data['txt']['SYS_CANCEL'] ?? 'Annuler'; ?>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</main>
