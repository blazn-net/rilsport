<!-- Fiche / Formulaire Edition de competition -->
<main class="p-4" style="margin-top: 60px;">

    <?php if (!empty($data['message'])): ?><div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>

    <!-- En-tete -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <div>
            <h2 class="mb-0">
                <?php echo htmlspecialchars($data['edition']->name ?? ($data['txt']['COMPETITION_EDITION_ADD_TITLE'] ?? 'Nouvelle edition')); ?>
            </h2>
            <?php if (!empty($data['edition']->competition_name)): ?>
                <small class="text-muted">
                    <span class="mif-trophy mr-1"></span>
                    <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['edition']->competition_id; ?>">
                        <?php echo htmlspecialchars($data['edition']->competition_name); ?>
                    </a>
                </small>
            <?php endif; ?>
        </div>
        <div class="d-flex" style="gap: 8px;">
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && !empty($data['edition'])): ?>
                <a href="<?php echo URLROOT; ?>/sport/competition-edition/edit/<?php echo (int)$data['edition']->id; ?>" class="button warning">
                    <span class="mif-pencil mr-1"></span><?php echo $data['txt']['SYS_EDIT'] ?? 'Modifier'; ?>
                </a>
            <?php endif; ?>
            <?php $backUrl = !empty($data['edition']->competition_id)
                    ? URLROOT . '/sport/competition/' . (int)$data['edition']->competition_id
                    : URLROOT . '/sport/competition-editions'; ?>
            <a href="<?php echo $backUrl; ?>" class="button secondary">
                <span class="mif-arrow-left mr-1"></span><?php echo $data['txt']['SYS_BACK'] ?? 'Retour'; ?>
            </a>
        </div>
    </div>

    <?php if ($data['mode'] === 'view'): ?>
    <!-- ====== MODE VIEW ====== -->
    <div class="grid">
        <div class="row">
            <div class="cell-md-8">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3"><span class="mif-info mr-1"></span>Informations</h5>
                    <table class="table compact w-100">
                        <tbody>
                            <tr>
                                <td class="text-bold" style="width:35%"><?php echo $data['txt']['COMPETITION_EDITION_CODE'] ?? 'Code'; ?></td>
                                <td><code><?php echo htmlspecialchars($data['edition']->code ?? ''); ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_NAME'] ?? 'Nom'; ?></td>
                                <td><?php echo htmlspecialchars($data['edition']->name ?? ''); ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Competition'; ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)$data['edition']->competition_id; ?>">
                                        <?php echo htmlspecialchars($data['edition']->competition_name ?? ''); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php if (!empty($data['edition']->season_name)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></td>
                                <td><?php echo htmlspecialchars($data['edition']->season_name); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['edition']->parent_edition_name)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Edition parente'; ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$data['edition']->parent_edition_id; ?>">
                                        <?php echo htmlspecialchars($data['edition']->parent_edition_name); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['edition']->edition_number)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_NUMBER'] ?? 'Numero'; ?></td>
                                <td><?php echo (int)$data['edition']->edition_number; ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Debut'; ?></td>
                                <td><?php echo !empty($data['edition']->date_start) ? date('d/m/Y', strtotime($data['edition']->date_start)) : '—'; ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></td>
                                <td><?php echo !empty($data['edition']->date_end) ? date('d/m/Y', strtotime($data['edition']->date_end)) : '—'; ?></td>
                            </tr>
                            <?php if (!empty($data['isAdmin'])): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_STATUS'] ?? 'Statut'; ?></td>
                                <td>
                                    <?php $stCode = $data['edition']->status_text_code ?? ''; ?>
                                    <span class="badge inline <?php echo ($stCode === 'COMPETITION_STATUS_ACTIVE') ? 'bg-green' : 'bg-orange'; ?> fg-white">
                                        <?php echo htmlspecialchars($data['txt'][$stCode] ?? $stCode); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="cell-md-4">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3"><span class="mif-chart-bars mr-1"></span>Apercu</h5>
                    <div class="d-flex flex-column" style="gap:12px;">
                        <div class="d-flex flex-align-center" style="gap:10px;">
                            <span class="badge bg-dark fg-white" style="min-width:36px;text-align:center;"><?php echo count($data['phases']); ?></span>
                            <span><?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Phases'; ?></span>
                        </div>
                        <div class="d-flex flex-align-center" style="gap:10px;">
                            <span class="badge bg-dark fg-white" style="min-width:36px;text-align:center;"><?php echo count($data['entries']); ?></span>
                            <span><?php echo $data['txt']['COMPETITION_EDITION_ENTRIES'] ?? 'Equipes inscrites'; ?></span>
                        </div>
                        <?php if (!empty($data['subEditions'])): ?>
                        <div class="d-flex flex-align-center" style="gap:10px;">
                            <span class="badge bg-dark fg-white" style="min-width:36px;text-align:center;"><?php echo count($data['subEditions']); ?></span>
                            <span><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Sous-editions'; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phases -->
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="mb-0"><span class="mif-layers mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Phases'; ?></h5>
                        <?php if (!empty($data['isAdmin']) && !empty($data['edition'])): ?>
                        <a href="<?php echo URLROOT; ?>/sport/competition-phase?edition_id=<?php echo (int)$data['edition']->id; ?>" class="button small success">
                            <span class="mif-plus"></span> Nouvelle phase
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($data['phases'])): ?>
                        <p class="text-muted text-center">
                            Aucune phase definie.
                            <?php if (!empty($data['isAdmin']) && !empty($data['edition'])): ?>
                            <a href="<?php echo URLROOT; ?>/sport/competition-phase?edition_id=<?php echo (int)$data['edition']->id; ?>">
                                Creer la premiere phase
                            </a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                    <table class="table striped compact w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $data['txt']['COMPETITION_PHASE_NAME'] ?? 'Phase'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_PHASE_TYPE'] ?? 'Type'; ?></th>
                                <th class="text-center">Groupes</th>
                                <th class="text-center">Tours</th>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Debut'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['phases'] as $ph): ?>
                            <tr>
                                <td><?php echo (int)$ph['phase_order']; ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)$ph['id']; ?>">
                                        <?php echo htmlspecialchars($ph['name']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php $typeLabels = ['league'=>'Championnat','cup'=>'Elimination','tournament'=>'Tournoi','ranking'=>'Classement']; ?>
                                    <span class="badge inline bg-dark fg-white">
                                        <?php echo htmlspecialchars($typeLabels[$ph['phase_type'] ?? ''] ?? $ph['phase_type']); ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo (int)($ph['groups_count'] ?? 0); ?></td>
                                <td class="text-center"><?php echo (int)($ph['rounds_count'] ?? 0); ?></td>
                                <td><?php echo !empty($ph['date_start']) ? date('d/m/Y', strtotime($ph['date_start'])) : '—'; ?></td>
                                <td><?php echo !empty($ph['date_end'])   ? date('d/m/Y', strtotime($ph['date_end']))   : '—'; ?></td>
                                <?php if (!empty($data['isAdmin'])): ?>
                                <td class="text-center" style="white-space:nowrap;">
                                    <a href="<?php echo URLROOT; ?>/sport/competition-phase/edit/<?php echo (int)$ph['id']; ?>" class="button small warning" title="Modifier"><span class="mif-pencil"></span></a>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-phase/delete/<?php echo (int)$ph['id']; ?>" class="button small alert" title="Supprimer" onclick="return confirm('Supprimer cette phase ?')"><span class="mif-bin"></span></a>
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

        <!-- Equipes inscrites -->
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="mb-0"><span class="mif-groups mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_ENTRIES'] ?? 'Equipes inscrites'; ?></h5>
                        <?php if (!empty($data['isAdmin']) && !empty($data['edition'])): ?>
                        <a href="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['edition']->id; ?>" class="button small success">
                            <span class="mif-plus"></span> Gerer les inscriptions
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($data['entries'])): ?>
                        <p class="text-muted text-center">
                            Aucune equipe inscrite.
                            <?php if (!empty($data['isAdmin']) && !empty($data['edition'])): ?>
                            <a href="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$data['edition']->id; ?>">Inscrire des equipes</a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                    <table class="table striped compact w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $data['txt']['COMPETITION_ENTRY_TEAM'] ?? 'Equipe'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_ENTRY_GROUP'] ?? 'Groupe / Poule'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['entries'] as $e): ?>
                            <tr>
                                <td><?php echo !empty($e['entry_order']) ? (int)$e['entry_order'] : '—'; ?></td>
                                <td><?php echo htmlspecialchars($e['team_name'] ?? ''); ?></td>
                                <td><?php echo !empty($e['group_name']) ? htmlspecialchars($e['group_name']) : '—'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sous-editions -->
        <?php if (!empty($data['subEditions'])): ?>
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3"><span class="mif-tree mr-1"></span>Sous-editions (categories)</h5>
                    <table class="table striped compact w-100">
                        <tbody>
                            <?php foreach ($data['subEditions'] as $sub): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$sub['id']; ?>">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                    </a>
                                </td>
                                <td><code><?php echo htmlspecialchars($sub['code']); ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ====== MODE ADD / EDIT ====== -->
    <form method="POST"
          action="<?php echo URLROOT; ?>/sport/competition-edition<?php echo !empty($data['edition']) ? '/edit/' . (int)$data['edition']->id : ''; ?>">

        <div class="grid">
            <div class="row">
                <div class="cell-md-8">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Informations generales</h5>

                        <!-- Competition -->
                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Competition'; ?> <span class="text-alert">*</span></label>
                            <select name="competition_id" id="edition-competition" data-role="select">
                                <option value="">-- Choisir --</option>
                                <?php foreach ($data['allCompetitions'] as $c): ?>
                                    <option value="<?php echo (int)$c['id']; ?>"
                                        <?php $selectedComp = $data['edition']->competition_id ?? $data['preselectedCompetitionId'] ?? 0; echo ($selectedComp == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['sport_name'] ?? ''); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sous-edition de -->
                        <?php if (!empty($data['parentCandidates'])): ?>
                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Sous-edition de'; ?></label>
                            <select name="parent_edition_id" id="edition-parent" data-role="select">
                                <option value="">-- Edition principale --</option>
                                <?php foreach ($data['parentCandidates'] as $pe): ?>
                                    <option value="<?php echo (int)$pe['id']; ?>"
                                        <?php echo (isset($data['edition']->parent_edition_id) && $data['edition']->parent_edition_id == $pe['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($pe['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Uniquement si cette edition est une categorie d'une edition parente.</small>
                        </div>
                        <?php endif; ?>

                        <!-- Code -->
                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_EDITION_CODE'] ?? 'Code'; ?> <span class="text-alert">*</span></label>
                            <input type="text" name="code" id="edition-code" data-role="input"
                                   value="<?php echo htmlspecialchars($data['edition']->code ?? ''); ?>"
                                   <?php echo !empty($data['edition']) ? 'readonly' : ''; ?>
                                   placeholder="ex: ligue1-2025-2026, ucl-2025-26">
                        </div>

                        <!-- Nom -->
                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_EDITION_NAME'] ?? 'Nom'; ?> <span class="text-alert">*</span></label>
                            <input type="text" name="name" id="edition-name" data-role="input"
                                   value="<?php echo htmlspecialchars($data['edition']->name ?? ''); ?>"
                                   placeholder="ex: Ligue 1 2025-2026">
                        </div>

                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></label>
                                    <select name="season_id" id="edition-season" data-role="select">
                                        <option value="">-- Aucune --</option>
                                        <?php foreach ($data['allSeasons'] as $s): ?>
                                            <option value="<?php echo (int)$s['id']; ?>"
                                                <?php echo (isset($data['edition']->season_id) && $data['edition']->season_id == $s['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($s['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_EDITION_NUMBER'] ?? 'Numero edition'; ?></label>
                                    <input type="number" name="edition_number" id="edition-number" data-role="input" min="1"
                                           value="<?php echo htmlspecialchars($data['edition']->edition_number ?? ''); ?>"
                                           placeholder="ex: 1, 33...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Debut'; ?></label>
                                    <input type="date" name="date_start" id="edition-date-start" data-role="input"
                                           value="<?php echo htmlspecialchars($data['edition']->date_start ?? ''); ?>">
                                </div>
                            </div>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></label>
                                    <input type="date" name="date_end" id="edition-date-end" data-role="input"
                                           value="<?php echo htmlspecialchars($data['edition']->date_end ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statut -->
                <div class="cell-md-4">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3"><?php echo $data['txt']['COMPETITION_EDITION_STATUS'] ?? 'Statut'; ?></h5>
                        <select name="status_id" id="edition-status" data-role="select">
                            <?php foreach ($data['statuses'] as $st): ?>
                                <option value="<?php echo (int)$st['id']; ?>"
                                    <?php echo (isset($data['edition']->status_id) && $data['edition']->status_id == $st['id']) ? 'selected' : (($st['id'] == 1) ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="row">
                <div class="cell-12">
                    <button type="submit" class="button primary mr-2" id="edition-submit">
                        <span class="mif-checkmark mr-1"></span>
                        <?php echo !empty($data['edition'])
                            ? ($data['txt']['SYS_SAVE'] ?? 'Enregistrer')
                            : ($data['txt']['COMPETITION_ADD_EDITION_BTN'] ?? 'Creer'); ?>
                    </button>
                    <a href="<?php echo !empty($data['edition']->competition_id) ? URLROOT . '/sport/competition/' . (int)$data['edition']->competition_id : URLROOT . '/sport/competition-editions'; ?>" class="button secondary">
                        <span class="mif-cancel mr-1"></span><?php echo $data['txt']['SYS_CANCEL'] ?? 'Annuler'; ?>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>

</main>
