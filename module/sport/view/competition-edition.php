<?php
/**
 * Vue : Édition de compétition (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$edition     = $data['edition'] ?? null;
$phases      = $data['phases'] ?? [];
$entries     = $data['entries'] ?? [];
$subEditions = $data['subEditions'] ?? [];

$allCompetitions = $data['allCompetitions'] ?? [];
$allSeasons      = $data['allSeasons'] ?? [];
$statuses        = $data['statuses'] ?? [];
$parentCandidates = $data['parentCandidates'] ?? [];
$preselectedCompetitionId = $data['preselectedCompetitionId'] ?? null;

$backUrl = !empty($edition->competition_id)
    ? URLROOT . '/sport/competition/' . (int)$edition->competition_id
    : URLROOT . '/sport/competition-editions';

$cancelUrl = isset($edition->id)
    ? URLROOT . '/sport/competition-edition/' . (int)$edition->id
    : $backUrl;

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $edition,
    'icon'           => 'mif-calendar',
    'maxWidth'       => '1000px',
    'viewTitle'      => 'Fiche de l\'édition : ' . htmlspecialchars($edition->name ?? ''),
    'editTitle'      => $data['txt']['COMPETITION_EDITION_EDIT_TITLE'] ?? 'Modifier l\'édition',
    'addTitle'       => $data['txt']['COMPETITION_EDITION_ADD_TITLE'] ?? 'Nouvelle édition',
    'backUrl'        => $backUrl,
    'editUrl'        => isset($edition->id) ? URLROOT . '/sport/competition-edition/edit/' . (int)$edition->id : null,
    'cancelUrl'      => $cancelUrl,
    'formAction'     => URLROOT . '/sport/competition-edition' . (($data['mode'] === 'edit' && isset($edition->id)) ? '/edit/' . (int)$edition->id : ''),
    'viewAvatarIcon' => 'mif-calendar',

    // Contenu spécifique du mode View (Consultation pure)
    'viewContent' => function($item, $data) use ($phases, $entries, $subEditions) { ?>
        <div class="row">
            <div class="cell-md-7 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-info mr-1"></span> Informations
                    </h5>

                    <p class="mb-2">
                        <strong><?php echo $data['txt']['COMPETITION_EDITION_COMPETITION'] ?? 'Compétition'; ?> :</strong>
                        <a href="<?php echo URLROOT; ?>/sport/competition/<?php echo (int)($item->competition_id ?? 0); ?>" class="fg-primary text-bold">
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
                        <span class="mif-calendar mr-1"></span> Période & Structure
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

                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <span class="badge secondary p-2"><span class="mif-layers mr-1"></span> <?php echo count($phases); ?> phase(s)</span>
                        <span class="badge secondary p-2"><span class="mif-groups mr-1"></span> <?php echo count($entries); ?> équipe(s)</span>
                    </div>
                </div>
            </div>
        </div>

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
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($allCompetitions, $allSeasons, $statuses, $parentCandidates, $preselectedCompetitionId) { ?>
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

                <!-- Sous-édition de -->
                <?php if (!empty($parentCandidates)): ?>
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_PARENT'] ?? 'Sous-édition de'; ?></label>
                    <select name="parent_edition_id" data-role="select">
                        <option value="">-- Édition principale (aucune) --</option>
                        <?php foreach ($parentCandidates as $pe): ?>
                            <option value="<?php echo (int)$pe['id']; ?>"
                                <?php echo (isset($item->parent_edition_id) && $item->parent_edition_id == $pe['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pe['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted d-block mt-1">Uniquement si cette édition est une catégorie d'une édition parente.</small>
                </div>
                <?php endif; ?>

                <!-- Code -->
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_EDITION_CODE'] ?? 'Code'; ?> <span class="fg-red">*</span></label>
                    <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: ligue1-2025-2026, ucl-2025-26"
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
                    <input type="text" name="name" data-role="input" required placeholder="ex: Ligue 1 2025-2026"
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
            </div>
        </div>
    <?php }
];

require 'module/system/view/common/form_template.php';
