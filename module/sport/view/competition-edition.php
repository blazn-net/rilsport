<?php
/**
 * Vue : Édition de compétition (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel entity_template.php
 */

$edition     = $data['edition'] ?? null;
$phases      = $data['phases'] ?? [];
$entries     = $data['entries'] ?? [];
$subEditions = $data['subEditions'] ?? [];

$allCompetitions = $data['allCompetitions'] ?? [];
$allSeasons      = $data['allSeasons'] ?? [];
$statuses        = $data['statuses'] ?? [];
$parentCandidates = $data['parentCandidates'] ?? [];
$preselectedCompetitionId  = $data['preselectedCompetitionId'] ?? null;
$preselectedParentEditionId = $data['preselectedParentEditionId'] ?? null;

$editionCodeOrId = !empty($edition->code) ? htmlspecialchars($edition->code) : (isset($edition->id) ? (int)$edition->id : null);
$compCodeOrId    = !empty($edition->competition_code) ? htmlspecialchars($edition->competition_code) : (!empty($edition->competition_id) ? (int)$edition->competition_id : null);

// is_container : édition chapeau qui regroupe plusieurs sous-tournois (ex: Roland Garros 2024)
$isContainer = !empty($edition->is_container);

$backUrl = !empty($compCodeOrId)
    ? URLROOT . '/sport/competition/' . $compCodeOrId
    : URLROOT . '/sport/competition-editions';

$cancelUrl = $editionCodeOrId
    ? URLROOT . '/sport/competition-edition/' . $editionCodeOrId
    : $backUrl;

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $edition,
    'icon'           => $isContainer ? 'mif-apps' : 'mif-calendar',
    'maxWidth'       => '1000px',
    'viewTitle'      => 'Fiche de l\'édition : ' . htmlspecialchars($edition->name ?? ''),
    'editTitle'      => $data['txt']['COMPETITION_EDITION_EDIT_TITLE'] ?? 'Modifier l\'édition',
    'addTitle'       => $data['txt']['COMPETITION_EDITION_ADD_TITLE'] ?? 'Nouvelle édition',
    'backUrl'        => $backUrl,
    'editUrl'        => $editionCodeOrId ? URLROOT . '/sport/competition-edition/edit/' . $editionCodeOrId : null,
    'cancelUrl'      => $cancelUrl,
    'formAction'     => URLROOT . '/sport/competition-edition' . (($data['mode'] === 'edit' && $editionCodeOrId) ? '/edit/' . $editionCodeOrId : ''),
    'viewAvatarIcon' => $isContainer ? 'mif-apps' : 'mif-calendar',

    // Contenu spécifique du mode View (Consultation pure)
    'viewContent' => function($item, $data) use ($phases, $entries, $subEditions, $isContainer, $compCodeOrId) { ?>
        <div class="row">
            <div class="cell-md-7 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-info mr-1"></span> Informations
                    </h5>

                    <p class="mb-2">
                        <strong><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Compétition'; ?> :</strong>
                        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo !empty($item->competition_code) ? htmlspecialchars($item->competition_code) : (int)($item->competition_id ?? 0); ?>" class="fg-primary text-bold">
                            <?php echo htmlspecialchars($item->competition_name ?? ''); ?>
                        </a>
                    </p>

                    <?php if (!empty($item->season_name)): ?>
                        <p class="mb-2">
                            <strong><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?> :</strong>
                            <?php echo htmlspecialchars($item->season_name); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($item->parent_edition_name)): ?>
                        <p class="mb-2">
                            <strong><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Édition parente'; ?> :</strong>
                            <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$item->parent_edition_id; ?>">
                                <?php echo htmlspecialchars($item->parent_edition_name); ?>
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($item->edition_number)): ?>
                        <p class="mb-2">
                            <strong><?php echo $data['txt']['COMPETITION_EDITION_NUMBER'] ?? 'Numéro'; ?> :</strong>
                            <?php echo (int)$item->edition_number; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cell-md-5 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-calendar mr-1"></span> Période &amp; Structure
                    </h5>

                    <p class="mb-2">
                        <span class="mif-event-available fg-emerald mr-1"></span>
                        <strong><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Début'; ?> :</strong>
                        <?php echo !empty($item->date_start) ? date('d/m/Y', strtotime($item->date_start)) : '—'; ?>
                    </p>

                    <p class="mb-3">
                        <span class="mif-event-busy fg-crimson mr-1"></span>
                        <strong><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?> :</strong>
                        <?php echo !empty($item->date_end) ? date('d/m/Y', strtotime($item->date_end)) : '—'; ?>
                    </p>

                    <?php if ($isContainer): ?>
                        <span class="badge inline" style="background:#7c3aed; color:#fff; padding:4px 10px;">
                            <span class="mif-apps mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_MULTI_TOURNAMENTS'] ?? 'Multi-tournois'; ?>
                        </span>
                        <small class="d-block text-muted mt-2"><?php echo count($subEditions); ?> tournoi(s) dans cet événement</small>
                    <?php else: ?>
                        <div class="d-flex flex-wrap" style="gap: 8px;">
                            <span class="badge secondary p-2"><span class="mif-layers mr-1"></span> <?php echo count($phases); ?> phase(s)</span>
                            <span class="badge secondary p-2"><span class="mif-groups mr-1"></span> <?php echo count($entries); ?> équipe(s)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($isContainer): ?>
        <!-- ===== ÉDITION CONTENEUR : afficher les sous-tournois ===== -->
        <div class="mt-4">
            <div class="d-flex flex-justify-between flex-align-center mb-2">
                <h5 class="text-bold mb-0" style="font-size: 15px; color: #1e293b;">
                    <span class="mif-apps mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_SUB_TOURNAMENTS'] ?? 'Tournois de cet événement'; ?>
                </h5>
                <?php if (!empty($data['isAdmin']) && !empty($item)): ?>
                    <a href="<?php echo URLROOT; ?>/sport/competition-edition?competition_id=<?php echo (int)$item->competition_id; ?>&parent_edition_id=<?php echo (int)$item->id; ?>" class="button small success">
                        <span class="mif-plus mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_NEW_SUB_TOURNAMENT'] ?? 'Nouveau tournoi'; ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($subEditions)): ?>
                <div class="row" style="gap: 0;">
                    <?php foreach ($subEditions as $sub): ?>
                        <div class="cell-md-6 mb-3">
                            <div class="p-3 border bd-default border-radius-4 bg-white d-flex flex-align-center flex-justify-between" style="min-height: 72px;">
                                <div>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo htmlspecialchars($sub['code'] ?? $sub['id']); ?>" class="text-bold d-block" style="font-size: 15px;">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                    </a>
                                    <?php if (!empty($sub['date_start']) || !empty($sub['date_end'])): ?>
                                        <small class="text-muted">
                                            <?php echo !empty($sub['date_start']) ? date('d/m/Y', strtotime($sub['date_start'])) : '?'; ?>
                                            →
                                            <?php echo !empty($sub['date_end']) ? date('d/m/Y', strtotime($sub['date_end'])) : '?'; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/edit/<?php echo htmlspecialchars($sub['code'] ?? $sub['id']); ?>" class="button flat-button small ml-2" title="Modifier">
                                        <span class="mif-pencil"></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="remark info mb-0">
                    Aucun tournoi défini pour cet événement. Cliquez sur « Nouveau tournoi » pour en ajouter.
                </div>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <!-- ===== ÉDITION AUTONOME : afficher phases + inscriptions ===== -->

        <!-- Phases -->
        <div class="mt-4">
            <div class="d-flex flex-justify-between flex-align-center mb-2">
                <h5 class="text-bold mb-0" style="font-size: 15px; color: #1e293b;">
                    <span class="mif-layers mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Phases de compétition'; ?>
                </h5>
                <?php if (!empty($data['isAdmin']) && !empty($item)): ?>
                    <a href="<?php echo URLROOT; ?>/sport/competition-phase?edition_id=<?php echo (int)$item->id; ?>" class="button small success">
                        <span class="mif-plus mr-1"></span> Nouvelle phase
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($phases)): ?>
                <table class="table striped compact w-100" style="border: 1px solid #e2e8f0;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th><?php echo $data['txt']['COMPETITION_PHASE_NAME'] ?? 'Phase'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_PHASE_TYPE'] ?? 'Type'; ?></th>
                            <th class="text-center">Groupes</th>
                            <th class="text-center">Tours</th>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Début'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($phases as $ph): ?>
                            <tr>
                                <td><?php echo (int)$ph['phase_order']; ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-phase/<?php echo (int)$ph['id']; ?>" class="text-bold">
                                        <?php echo htmlspecialchars($ph['name']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php $typeLabels = ['league'=>'Championnat','cup'=>'Élimination','tournament'=>'Tournoi','ranking'=>'Classement']; ?>
                                    <span class="badge inline bg-dark fg-white">
                                        <?php echo htmlspecialchars($typeLabels[$ph['phase_type'] ?? ''] ?? $ph['phase_type']); ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo (int)($ph['groups_count'] ?? 0); ?></td>
                                <td class="text-center"><?php echo (int)($ph['rounds_count'] ?? 0); ?></td>
                                <td><?php echo !empty($ph['date_start']) ? date('d/m/Y', strtotime($ph['date_start'])) : '—'; ?></td>
                                <td><?php echo !empty($ph['date_end'])   ? date('d/m/Y', strtotime($ph['date_end']))   : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="remark info mb-0">
                    Aucune phase définie pour cette édition pour le moment.
                </div>
            <?php endif; ?>
        </div>

        <!-- Équipes inscrites -->
        <div class="mt-4">
            <div class="d-flex flex-justify-between flex-align-center mb-2">
                <h5 class="text-bold mb-0" style="font-size: 15px; color: #1e293b;">
                    <span class="mif-groups mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_ENTRIES'] ?? 'Équipes inscrites'; ?>
                </h5>
                <?php if (!empty($data['isAdmin']) && !empty($item)): ?>
                    <a href="<?php echo URLROOT; ?>/sport/competition-entry/<?php echo (int)$item->id; ?>" class="button small success">
                        <span class="mif-plus mr-1"></span> Gérer les inscriptions
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($entries)): ?>
                <table class="table striped compact w-100" style="border: 1px solid #e2e8f0;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th><?php echo $data['txt']['COMPETITION_ENTRY_TEAM'] ?? 'Équipe'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_ENTRY_GROUP'] ?? 'Groupe / Poule'; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $e): ?>
                            <tr>
                                <td><?php echo !empty($e['entry_order']) ? (int)$e['entry_order'] : '—'; ?></td>
                                <td class="text-bold"><?php echo htmlspecialchars($e['team_name'] ?? ''); ?></td>
                                <td><?php echo !empty($e['group_name']) ? htmlspecialchars($e['group_name']) : '—'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="remark info mb-0">
                    Aucune équipe inscrite pour cette édition pour le moment.
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($allCompetitions, $allSeasons, $statuses, $parentCandidates, $preselectedCompetitionId, $preselectedParentEditionId) { ?>
        <div class="row">
            <div class="cell-md-8">
                <!-- Compétition parente -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Compétition'; ?> <span class="fg-red">*</span></label>
                    <select name="competition_id" data-role="select" required>
                        <option value="">-- Choisir une compétition --</option>
                        <?php foreach ($allCompetitions as $c): ?>
                            <option value="<?php echo (int)$c['id']; ?>"
                                <?php $selectedComp = $item->competition_id ?? $preselectedCompetitionId ?? 0; echo ($selectedComp == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['sport_name'] ?? ''); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Type d'édition : autonome ou conteneur multi-tournois -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_TYPE'] ?? 'Type d\'édition'; ?></label>
                    <div class="d-flex flex-wrap" style="gap: 12px; margin-top: 6px;">
                        <label class="d-flex flex-align-center" style="cursor: pointer; gap: 8px;">
                            <input type="radio" name="is_container" value="0" data-role="radio"
                                <?php echo empty($item->is_container) ? 'checked' : ''; ?>>
                            <span>
                                <span class="mif-calendar mr-1"></span>
                                <strong><?php echo $data['txt']['COMPETITION_EDITION_TYPE_SINGLE'] ?? 'Édition simple'; ?></strong>
                                <small class="d-block text-muted" style="font-weight:normal;">Ex: Top 14 2024-2025, Ligue 1 2024-2025</small>
                            </span>
                        </label>
                        <label class="d-flex flex-align-center" style="cursor: pointer; gap: 8px;">
                            <input type="radio" name="is_container" value="1" data-role="radio"
                                <?php echo !empty($item->is_container) ? 'checked' : ''; ?>>
                            <span>
                                <span class="mif-apps mr-1" style="color:#7c3aed;"></span>
                                <strong><?php echo $data['txt']['COMPETITION_EDITION_TYPE_CONTAINER'] ?? 'Événement multi-tournois'; ?></strong>
                                <small class="d-block text-muted" style="font-weight:normal;">Ex: Roland Garros 2024 → Simple H, Simple F, Double...</small>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Sous-édition de (édition parente) -->
                <?php if (!empty($parentCandidates)): ?>
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Sous-tournoi de'; ?></label>
                    <select name="parent_edition_id" data-role="select">
                        <option value="">-- Édition principale (aucune) --</option>
                        <?php foreach ($parentCandidates as $pe): ?>
                            <option value="<?php echo (int)$pe['id']; ?>"
                                <?php echo (isset($item->parent_edition_id) && $item->parent_edition_id == $pe['id']) ? 'selected' : ((!isset($item->parent_edition_id) && $preselectedParentEditionId == $pe['id']) ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($pe['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted d-block mt-1">Uniquement si ce tournoi fait partie d'un événement multi-tournois (ex: Simple Hommes dans Roland Garros).</small>
                </div>
                <?php endif; ?>

                <!-- Pré-sélection parent depuis GET (création depuis fiche édition conteneur) -->
                <?php if (!empty($_GET['parent_edition_id']) && empty($parentCandidates)): ?>
                    <input type="hidden" name="parent_edition_id" value="<?php echo (int)$_GET['parent_edition_id']; ?>">
                <?php endif; ?>

                <!-- Code -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_CODE'] ?? 'Code'; ?> <span class="fg-red">*</span></label>
                    <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: roland-garros-2024, rg-2024-simple-h"
                           value="<?php echo htmlspecialchars($item->code ?? ''); ?>" <?php echo ($mode === 'edit') ? 'readonly' : ''; ?>>
                    <?php if ($mode === 'edit'): ?>
                        <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> Le code ne peut plus être modifié.</small>
                    <?php else: ?>
                        <small class="text-muted d-block mt-1">Identifiant unique (lettres minuscules, chiffres, tirets).</small>
                    <?php endif; ?>
                </div>

                <!-- Nom -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_NAME'] ?? 'Nom de l\'édition'; ?> <span class="fg-red">*</span></label>
                    <input type="text" name="name" data-role="input" required placeholder="ex: Roland Garros 2024 / Simple Hommes 2024"
                           value="<?php echo htmlspecialchars($item->name ?? ''); ?>">
                </div>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></label>
                        <select name="season_id" data-role="select">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($allSeasons as $s): ?>
                                <option value="<?php echo (int)$s['id']; ?>"
                                    <?php echo (isset($item->season_id) && $item->season_id == $s['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_NUMBER'] ?? 'Numéro édition'; ?></label>
                        <input type="number" name="edition_number" data-role="input" min="1"
                               value="<?php echo htmlspecialchars($item->edition_number ?? ''); ?>"
                               placeholder="ex: 1, 33...">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Début'; ?></label>
                        <input type="date" name="date_start" data-role="input"
                               value="<?php echo htmlspecialchars($item->date_start ?? ''); ?>">
                    </div>
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></label>
                        <input type="date" name="date_end" data-role="input"
                               value="<?php echo htmlspecialchars($item->date_end ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- Colonne latérale : Statut -->
            <div class="cell-md-4">
                <?php if ($mode === 'edit'): ?>
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <label class="text-bold mb-2 d-block"><?php echo $data['txt']['COMPETITION_EDITION_STATUS'] ?? 'Statut'; ?></label>
                    <select name="status_id" data-role="select">
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo (int)$st['id']; ?>" <?php echo (isset($item->status_id) && (int)$item->status_id === (int)$st['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Aide contextuelle -->
                <div class="p-3 border bd-default border-radius-4" style="background: #faf5ff;">
                    <h6 class="text-bold mb-2" style="color: #7c3aed; font-size: 13px;">
                        <span class="mif-question mr-1"></span> Aide — Types d'éditions
                    </h6>
                    <p class="mb-2" style="font-size: 12px; color: #374151;">
                        <strong>Édition simple :</strong> une compétition directe avec ses phases, ses poules et ses matchs.
                        <em>Exemples : Top 14 2024-2025, Ligue 1 2024-2025.</em>
                    </p>
                    <p class="mb-0" style="font-size: 12px; color: #374151;">
                        <strong>Multi-tournois :</strong> un événement chapeau qui regroupe plusieurs tournois distincts.
                        <em>Exemples : Roland Garros 2024 (Simple H, Simple F, Double...), Jeux Olympiques Paris 2024.</em>
                    </p>
                </div>
            </div>
        </div>
    <?php }
];

require 'module/system/view/common/entity_template.php';
