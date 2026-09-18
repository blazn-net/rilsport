<!-- Fiche / Formulaire Competition -->
<main class="p-4" style="margin-top: 60px;">

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- En-tete -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <div class="d-flex flex-align-center" style="gap: 16px;">
            <?php if (!empty($data['competition']->logo)): ?>
                <img src="<?php echo URLROOT . '/' . htmlspecialchars($data['competition']->logo); ?>"
                     alt="Logo" style="height:60px;width:auto;object-fit:contain;">
            <?php else: ?>
                <span class="mif-trophy fg-gray" style="font-size:48px;"></span>
            <?php endif; ?>
            <div>
                <h2 class="mb-0">
                    <?php echo htmlspecialchars($data['competition']->name ?? ($data['txt']['COMPETITION_ADD_TITLE'] ?? 'Nouvelle competition')); ?>
                </h2>
                <?php if (!empty($data['competition']->short_name)): ?>
                    <small class="text-muted"><?php echo htmlspecialchars($data['competition']->short_name); ?></small>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex" style="gap: 8px;">
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && !empty($data['competition'])): ?>
                <a href="<?php echo URLROOT; ?>/sport/competition/edit/<?php echo (int)$data['competition']->id; ?>" class="button warning">
                    <span class="mif-pencil mr-1"></span><?php echo $data['txt']['SYS_EDIT'] ?? 'Modifier'; ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo URLROOT; ?>/sport/competitions" class="button secondary">
                <span class="mif-arrow-left mr-1"></span><?php echo $data['txt']['SYS_BACK'] ?? 'Retour'; ?>
            </a>
        </div>
    </div>

    <?php if ($data['mode'] === 'view'): ?>
    <!-- ====== MODE VIEW ====== -->
    <div class="grid">
        <div class="row">
            <!-- Informations generales -->
            <div class="cell-md-8">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3"><span class="mif-info mr-1"></span>Informations generales</h5>
                    <table class="table compact w-100">
                        <tbody>
                            <tr>
                                <td class="text-bold" style="width:35%"><?php echo $data['txt']['COMPETITION_CODE'] ?? 'Code'; ?></td>
                                <td><code><?php echo htmlspecialchars($data['competition']->code ?? ''); ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_NAME'] ?? 'Nom officiel'; ?></td>
                                <td><?php echo htmlspecialchars($data['competition']->name ?? ''); ?></td>
                            </tr>
                            <?php if (!empty($data['competition']->acronym)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_ACRONYM'] ?? 'Sigle'; ?></td>
                                <td><?php echo htmlspecialchars($data['competition']->acronym); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?></td>
                                <td><?php echo htmlspecialchars($data['competition']->sport_name ?? ''); ?></td>
                            </tr>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?></td>
                                <td>
                                    <span class="badge inline bg-dark fg-white">
                                        <?php echo htmlspecialchars($data['txt'][$data['competition']->type_text_code ?? ''] ?? ($data['competition']->type_text_code ?? '')); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (!empty($data['competition']->country_name)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays'; ?></td>
                                <td><?php echo htmlspecialchars($data['competition']->country_name); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['competition']->description)): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_DESCRIPTION'] ?? 'Description'; ?></td>
                                <td><?php echo nl2br(htmlspecialchars($data['competition']->description)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['isAdmin'])): ?>
                            <tr>
                                <td class="text-bold"><?php echo $data['txt']['COMPETITION_STATUS'] ?? 'Statut'; ?></td>
                                <td>
                                    <?php
                                    $stCode = $data['competition']->status_text_code ?? '';
                                    $stClass = ($stCode === 'COMPETITION_STATUS_ACTIVE') ? 'bg-green' : (($stCode === 'COMPETITION_STATUS_ARCHIVED') ? 'bg-gray' : 'bg-orange');
                                    ?>
                                    <span class="badge inline <?php echo $stClass; ?> fg-white">
                                        <?php echo htmlspecialchars($data['txt'][$stCode] ?? $stCode); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Audit -->
            <?php if (!empty($data['isAdmin'])): ?>
            <div class="cell-md-4">
                <div class="p-3 border bd-default border-radius-4 mb-4">
                    <h5 class="mb-3"><span class="mif-clock mr-1"></span>Audit</h5>
                    <table class="table compact w-100">
                        <tbody>
                            <?php if (!empty($data['competition']->created_at)): ?>
                            <tr>
                                <td class="text-bold">Cree le</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($data['competition']->created_at)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['competition']->created_by_name)): ?>
                            <tr>
                                <td class="text-bold">Par</td>
                                <td><?php echo htmlspecialchars($data['competition']->created_by_name); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($data['competition']->modified_at)): ?>
                            <tr>
                                <td class="text-bold">Modifie le</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($data['competition']->modified_at)); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Editions -->
        <?php if (!empty($data['editions'])): ?>
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="mb-0"><span class="mif-calendar mr-1"></span><?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Editions'; ?></h5>
                        <?php if (!empty($data['isAdmin'])): ?>
                        <a href="<?php echo URLROOT; ?>/sport/competition-edition?competition_id=<?php echo (int)$data['competition']->id; ?>" class="button small success">
                            <span class="mif-plus"></span> <?php echo $data['txt']['COMPETITION_ADD_EDITION_BTN'] ?? 'Nouvelle edition'; ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <table class="table striped compact w-100">
                        <thead>
                            <tr>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_TABLE_NAME'] ?? 'Edition'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Debut'; ?></th>
                                <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></th>
                                <?php if (!empty($data['isAdmin'])): ?>
                                <th class="text-center"><?php echo $data['txt']['SYS_ACTIONS'] ?? 'Actions'; ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['editions'] as $e): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$e['id']; ?>">
                                        <?php echo htmlspecialchars($e['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($e['season_name'] ?? '—'); ?></td>
                                <td><?php echo !empty($e['date_start']) ? date('d/m/Y', strtotime($e['date_start'])) : '—'; ?></td>
                                <td><?php echo !empty($e['date_end'])   ? date('d/m/Y', strtotime($e['date_end']))   : '—'; ?></td>
                                <?php if (!empty($data['isAdmin'])): ?>
                                <td class="text-center">
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/edit/<?php echo (int)$e['id']; ?>" class="button small warning">
                                        <span class="mif-pencil"></span>
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php elseif (!empty($data['competition'])): ?>
        <div class="row">
            <div class="cell-12">
                <div class="p-3 border bd-default border-radius-4 text-center text-muted">
                    <span class="mif-calendar mr-1"></span>Aucune edition pour cette competition.
                    <?php if (!empty($data['isAdmin'])): ?>
                    <a href="<?php echo URLROOT; ?>/sport/competition-edition?competition_id=<?php echo (int)$data['competition']->id; ?>" class="ml-2">
                        Creer la premiere edition
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ====== MODE ADD / EDIT ====== -->
    <form method="POST" enctype="multipart/form-data"
          action="<?php echo URLROOT; ?>/sport/competition<?php echo !empty($data['competition']) ? '/edit/' . (int)$data['competition']->id : ''; ?>">

        <div class="grid">
            <div class="row">
                <!-- Colonne principale -->
                <div class="cell-md-8">
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3">Informations generales</h5>

                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_CODE'] ?? 'Code'; ?> <span class="text-alert">*</span></label>
                            <input type="text" name="code" id="competition-code" data-role="input"
                                   value="<?php echo htmlspecialchars($data['competition']->code ?? ''); ?>"
                                   <?php echo !empty($data['competition']) ? 'readonly' : ''; ?>
                                   placeholder="ex: ligue1, ucl, ehf-euro">
                            <small class="text-muted">Identifiant unique, en minuscules, sans espaces.</small>
                        </div>

                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_NAME'] ?? 'Nom officiel'; ?> <span class="text-alert">*</span></label>
                            <input type="text" name="name" id="competition-name" data-role="input"
                                   value="<?php echo htmlspecialchars($data['competition']->name ?? ''); ?>"
                                   placeholder="ex: Ligue 1, UEFA Champions League">
                        </div>

                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_SHORT_NAME'] ?? 'Nom abrege'; ?></label>
                                    <input type="text" name="short_name" id="competition-short-name" data-role="input"
                                           value="<?php echo htmlspecialchars($data['competition']->short_name ?? ''); ?>">
                                </div>
                            </div>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_ACRONYM'] ?? 'Sigle'; ?></label>
                                    <input type="text" name="acronym" id="competition-acronym" data-role="input"
                                           value="<?php echo htmlspecialchars($data['competition']->acronym ?? ''); ?>"
                                           placeholder="ex: UCL, EHF">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?> <span class="text-alert">*</span></label>
                                    <select name="sport_id" id="competition-sport" data-role="select">
                                        <option value="">-- Choisir --</option>
                                        <?php foreach ($data['allSports'] as $s): ?>
                                            <option value="<?php echo (int)$s['id']; ?>"
                                                <?php echo (isset($data['competition']->sport_id) && $data['competition']->sport_id == $s['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($s['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="cell-md-6">
                                <div class="form-group">
                                    <label><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?> <span class="text-alert">*</span></label>
                                    <select name="type_code" id="competition-type" data-role="select">
                                        <option value="">-- Choisir --</option>
                                        <?php foreach ($data['allTypes'] as $t): ?>
                                            <option value="<?php echo htmlspecialchars($t['code']); ?>"
                                                <?php echo (isset($data['competition']->type_code) && $data['competition']->type_code === $t['code']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($data['txt'][$t['text_code']] ?? $t['code']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays'; ?></label>
                            <select name="country_code" id="competition-country" data-role="select">
                                <option value="">-- International / Aucun --</option>
                                <?php foreach ($data['countries'] as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c['country_code']); ?>"
                                        <?php echo (isset($data['competition']->country_code) && $data['competition']->country_code === $c['country_code']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['country_code']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo $data['txt']['COMPETITION_DESCRIPTION'] ?? 'Description'; ?></label>
                            <textarea name="description" id="competition-description" data-role="textarea" rows="3"
                                      placeholder="Description de la competition..."><?php echo htmlspecialchars($data['competition']->description ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne logo + statut -->
                <div class="cell-md-4">
                    <!-- Logo -->
                    <div class="p-3 border bd-default border-radius-4 mb-4 text-center">
                        <h5 class="mb-3"><?php echo $data['txt']['COMPETITION_LOGO'] ?? 'Logo'; ?></h5>
                        <?php if (!empty($data['competition']->logo)): ?>
                            <img src="<?php echo URLROOT . '/' . htmlspecialchars($data['competition']->logo); ?>"
                                 alt="Logo" style="max-height:80px;max-width:100%;object-fit:contain;" class="mb-2">
                        <?php else: ?>
                            <span class="mif-trophy fg-gray" style="font-size:48px;display:block;" class="mb-2"></span>
                        <?php endif; ?>
                        <input type="file" name="logo_file" id="competition-logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-role="file">
                        <small class="text-muted d-block mt-1">PNG, JPG, WEBP, SVG — 2 Mo max</small>
                    </div>

                    <!-- Statut -->
                    <div class="p-3 border bd-default border-radius-4 mb-4">
                        <h5 class="mb-3"><?php echo $data['txt']['COMPETITION_STATUS'] ?? 'Statut'; ?></h5>
                        <select name="status_id" id="competition-status" data-role="select">
                            <?php foreach ($data['statuses'] as $st): ?>
                                <option value="<?php echo (int)$st['id']; ?>"
                                    <?php echo (isset($data['competition']->status_id) && $data['competition']->status_id == $st['id']) ? 'selected' : (($st['id'] == 1) ? 'selected' : ''); ?>>
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
                    <button type="submit" class="button primary mr-2" id="competition-submit">
                        <span class="mif-checkmark mr-1"></span>
                        <?php echo !empty($data['competition'])
                            ? ($data['txt']['SYS_SAVE'] ?? 'Enregistrer')
                            : ($data['txt']['COMPETITION_ADD_BTN'] ?? 'Creer'); ?>
                    </button>
                    <a href="<?php echo URLROOT; ?>/sport/competitions" class="button secondary">
                        <span class="mif-cancel mr-1"></span><?php echo $data['txt']['SYS_CANCEL'] ?? 'Annuler'; ?>
                    </a>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>

</main>
