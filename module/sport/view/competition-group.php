<!-- Fiche / Formulaire Groupe de competition -->
<main class="p-4" style="margin-top: 60px;">

    <?php if (!empty($data['message'])): ?><div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>

    <!-- Fil d ariane -->
    <?php if (!empty($data['group']) && !empty($data['phaseData'])): ?>
    <nav class="breadcrumbs mb-3">
        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['group']->competition_id; ?>"><?php echo htmlspecialchars($data['group']->competition_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['group']->edition_id; ?>"><?php echo htmlspecialchars($data['group']->edition_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)$data['group']->phase_id; ?>"><?php echo htmlspecialchars($data['group']->phase_name ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <span><?php echo htmlspecialchars($data['group']->name ?? ''); ?></span>
    </nav>
    <?php elseif (!empty($data['phaseData'])): ?>
    <nav class="breadcrumbs mb-3">
        <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)$data['phaseData']['id']; ?>"><?php echo htmlspecialchars($data['phaseData']['name'] ?? ''); ?></a>
        <span class="mif-arrow-right"></span>
        <span>Nouveau groupe</span>
    </nav>
    <?php endif; ?>

    <!-- En-tete -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2 class="mb-0">
            <span class="mif-users mr-2"></span>
            <?php echo htmlspecialchars($data['group']->name ?? ($data['txt']['COMPETITION_GROUP_ADD_TITLE'] ?? 'Nouveau groupe / Poule')); ?>
        </h2>
        <div class="d-flex" style="gap: 8px;">
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && !empty($data['group'])): ?>
                <a href="<?php echo URLROOT; ?>/sport/competition-group/edit/<?php echo (int)$data['group']->id; ?>" class="button warning">
                    <span class="mif-pencil mr-1"></span><?php echo $data['txt']['SYS_EDIT'] ?? 'Modifier'; ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)($data['group']->phase_id ?? $data['phaseData']['id'] ?? 0); ?>" class="button secondary">
                <span class="mif-arrow-left mr-1"></span><?php echo $data['txt']['SYS_BACK'] ?? 'Retour'; ?>
            </a>
        </div>
    </div>

    <?php if ($data['mode'] === 'view'): ?>
    <!-- VIEW -->
    <div class="grid">
        <div class="row">
            <div class="cell-md-8">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3">Informations</h5>
                    <table class="table compact w-100">
                        <tbody>
                            <tr><td class="text-bold" style="width:35%">Code</td><td><code><?php echo htmlspecialchars($data['group']->code ?? ''); ?></code></td></tr>
                            <tr><td class="text-bold">Nom</td><td><?php echo htmlspecialchars($data['group']->name ?? ''); ?></td></tr>
                            <tr><td class="text-bold">Ordre</td><td><?php echo (int)($data['group']->group_order ?? 1); ?></td></tr>
                            <?php if (!empty($data['group']->parent_group_name)): ?>
                            <tr><td class="text-bold">Groupe parent</td><td><?php echo htmlspecialchars($data['group']->parent_group_name); ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cell-md-4">
                <div class="p-3 border bd-default border-radius-4 mb-4 text-center">
                    <div style="font-size:2em;font-weight:700;"><?php echo count($data['entries']); ?></div>
                    <div class="text-muted">Equipes inscrites</div>
                </div>
            </div>
        </div>

        <!-- Equipes du groupe -->
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4">
                    <h5 class="mb-3"><span class="mif-groups mr-1"></span>Equipes dans ce groupe</h5>
                    <?php if (empty($data['entries'])): ?>
                        <p class="text-muted text-center">
                            Aucune equipe dans ce groupe.
                            <?php if (!empty($data['isAdmin'])): ?>
                            <a href="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['group']->edition_id; ?>">Gerer les inscriptions</a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                    <table class="table striped compact w-100">
                        <thead><tr><th>#</th><th>Equipe</th></tr></thead>
                        <tbody>
                            <?php foreach ($data['entries'] as $e): ?>
                            <tr>
                                <td><?php echo !empty($e['entry_order']) ? (int)$e['entry_order'] : '—'; ?></td>
                                <td><?php echo htmlspecialchars($e['team_name'] ?? ''); ?></td>
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
    <!-- FORM -->
    <form method="POST" action="<?php echo URLROOT; ?>/sport/competition-group<?php echo !empty($data['group']) ? '/edit/' . (int)$data['group']->id : ''; ?>">
        <div class="grid">
            <div class="row">
                <div class="cell-md-8">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Informations</h5>
                        <input type="hidden" name="phase_id" value="<?php echo (int)($data['group']->phase_id ?? $data['phaseData']['id'] ?? 0); ?>">

                        <div class="form-group">
                            <label>Code <span class="text-alert">*</span></label>
                            <input type="text" name="code" id="group-code" data-role="input"
                                   value="<?php echo htmlspecialchars($data['group']->code ?? ''); ?>"
                                   <?php echo !empty($data['group']) ? 'readonly' : ''; ?>
                                   placeholder="ex: groupe-a, poule-1, est, atlantique">
                        </div>
                        <div class="form-group">
                            <label>Nom <span class="text-alert">*</span></label>
                            <input type="text" name="name" id="group-name" data-role="input"
                                   value="<?php echo htmlspecialchars($data['group']->name ?? ''); ?>"
                                   placeholder="ex: Groupe A, Poule 1, Conference Est...">
                        </div>
                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label>Ordre d affichage</label>
                                    <input type="number" name="group_order" id="group-order" data-role="input" min="1"
                                           value="<?php echo (int)($data['group']->group_order ?? $data['nextOrder']); ?>">
                                </div>
                            </div>
                            <?php if (!empty($data['parentCandidates'])): ?>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label>Groupe parent <small class="text-muted">(sous-groupe de)</small></label>
                                    <select name="parent_group_id" id="group-parent" data-role="select">
                                        <option value="">-- Groupe principal --</option>
                                        <?php foreach ($data['parentCandidates'] as $pg): ?>
                                        <option value="<?php echo (int)$pg['id']; ?>" <?php echo (isset($data['group']->parent_group_id) && $data['group']->parent_group_id == $pg['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pg['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="cell-md-4">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Statut</h5>
                        <select name="status_id" id="group-status" data-role="select">
                            <?php foreach ($data['statuses'] as $st): ?>
                            <option value="<?php echo (int)$st['id']; ?>" <?php echo (isset($data['group']->status_id) && $data['group']->status_id == $st['id']) ? 'selected' : (($st['id']==1)?'selected':''); ?>>
                                <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="cell-12">
                    <button type="submit" class="button primary mr-2" id="group-submit">
                        <span class="mif-checkmark mr-1"></span><?php echo !empty($data['group']) ? ($data['txt']['SYS_SAVE'] ?? 'Enregistrer') : 'Creer'; ?>
                    </button>
                    <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)($data['group']->phase_id ?? $data['phaseData']['id'] ?? 0); ?>" class="button secondary">
                        <span class="mif-cancel mr-1"></span><?php echo $data['txt']['SYS_CANCEL'] ?? 'Annuler'; ?>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</main>
