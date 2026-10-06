<?php
/**
 * Vue : Compétition (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$comp      = $data['competition'] ?? null;
$editions  = $data['editions'] ?? [];
$allSports = $data['allSports'] ?? [];
$allTypes  = $data['allTypes'] ?? [];
$countries = $data['countries'] ?? [];
$statuses  = $data['statuses'] ?? [];

$logoUrl = (!empty($comp->logo)) ? URLROOT . '/' . ltrim($comp->logo, '/') : null;

$avatarHtml = '<div style="width: 56px; height: 56px; min-width: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #0072c6; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.15); border: 2px solid #fff; overflow: hidden;">';
if ($logoUrl) {
    $avatarHtml .= '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; background: #fff; padding: 2px;">';
} else {
    $avatarHtml .= '<span class="mif-trophy mif-2x"></span>';
}
$avatarHtml .= '</div>';

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $comp,
    'icon'           => 'mif-trophy',
    'maxWidth'       => '1000px',
    'enctype'        => 'multipart/form-data',
    'viewTitle'      => 'Fiche de la compétition : ' . htmlspecialchars($comp->name ?? ''),
    'editTitle'      => $data['txt']['COMPETITION_EDIT_TITLE'] ?? 'Modifier la compétition',
    'addTitle'       => $data['txt']['COMPETITION_ADD_TITLE'] ?? 'Nouvelle compétition',
    'backUrl'        => URLROOT . '/sport/competitions',
    'editUrl'        => isset($comp->id) ? URLROOT . '/sport/competition/edit/' . (int)$comp->id : null,
    'cancelUrl'      => isset($comp->id) ? URLROOT . '/sport/competition/' . (int)$comp->id : URLROOT . '/sport/competitions',
    'formAction'     => URLROOT . '/sport/competition' . (($data['mode'] === 'edit' && isset($comp->id)) ? '/edit/' . (int)$comp->id : ''),
    'viewAvatarHtml' => $avatarHtml,

    // Contenu spécifique du mode View (Consultation pure)
    'viewContent' => function($item, $data) use ($editions) { ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-info mr-1"></span> Caractéristiques
                    </h5>

                    <p class="mb-2"><strong><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?> :</strong> <?php echo htmlspecialchars($item->sport_name ?? ''); ?></p>
                    <p class="mb-2">
                        <strong><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?> :</strong>
                        <span class="badge inline bg-dark fg-white ml-1">
                            <?php echo htmlspecialchars($data['txt'][$item->type_text_code ?? ''] ?? ($item->type_text_code ?? '')); ?>
                        </span>
                    </p>
                    <?php if (!empty($item->country_name)): ?>
                        <p class="mb-2"><strong><?php echo $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays'; ?> :</strong> <?php echo htmlspecialchars($item->country_name); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item->acronym)): ?>
                        <p class="mb-2"><strong><?php echo $data['txt']['COMPETITION_ACRONYM'] ?? 'Sigle'; ?> :</strong> <?php echo htmlspecialchars($item->acronym); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; min-height: 100%;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-file-text mr-1"></span> Description
                    </h5>
                    <p class="mb-0" style="white-space: pre-line;">
                        <?php echo !empty($item->description) ? htmlspecialchars($item->description) : '<em class="text-muted">Aucune description renseignée.</em>'; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tableau des Éditions -->
        <div class="mt-4">
            <div class="d-flex flex-justify-between flex-align-center mb-2">
                <h5 class="text-bold mb-0" style="font-size: 15px; color: #1e293b;">
                    <span class="mif-calendar mr-1"></span> <?php echo $data['txt']['COMPETITION_EDITION_PHASES'] ?? 'Éditions associées'; ?>
                </h5>
                <?php if (!empty($data['isAdmin'])): ?>
                    <a href="<?php echo URLROOT; ?>/sport/competition-edition?competition_id=<?php echo (int)$item->id; ?>" class="button small success">
                        <span class="mif-plus mr-1"></span> <?php echo $data['txt']['COMPETITION_ADD_EDITION_BTN'] ?? 'Nouvelle édition'; ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($editions)): ?>
                <table class="table striped compact w-100" style="border: 1px solid #e2e8f0;">
                    <thead>
                        <tr>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_TABLE_NAME'] ?? 'Édition'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_SEASON'] ?? 'Saison'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_START'] ?? 'Début'; ?></th>
                            <th><?php echo $data['txt']['COMPETITION_EDITION_DATE_END'] ?? 'Fin'; ?></th>
                            <?php if (!empty($data['isAdmin'])): ?>
                                <th class="text-center" style="width: 80px;"><?php echo $data['txt']['SYS_ACTIONS'] ?? 'Actions'; ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($editions as $e): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/sport/competition-edition/<?php echo (int)$e['id']; ?>" class="text-bold">
                                        <?php echo htmlspecialchars($e['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($e['season_name'] ?? '—'); ?></td>
                                <td><?php echo !empty($e['date_start']) ? date('d/m/Y', strtotime($e['date_start'])) : '—'; ?></td>
                                <td><?php echo !empty($e['date_end'])   ? date('d/m/Y', strtotime($e['date_end']))   : '—'; ?></td>
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <td class="text-center">
                                        <a href="<?php echo URLROOT; ?>/sport/competition-edition/edit/<?php echo (int)$e['id']; ?>" class="button small info square" title="Modifier">
                                            <span class="mif-pencil"></span>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="remark info mb-0">
                    Aucune édition enregistrée pour cette compétition pour le moment.
                </div>
            <?php endif; ?>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($allSports, $allTypes, $countries, $statuses, $logoUrl) { ?>
        <div class="row">
            <div class="cell-md-8">
                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_CODE'] ?? 'Code'; ?> <span class="fg-red">*</span></label>
                    <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: ligue1, ucl, ehf-euro"
                           value="<?php echo htmlspecialchars($item->code ?? ''); ?>" <?php echo ($mode === 'edit') ? 'readonly' : ''; ?>>
                    <?php if ($mode === 'edit'): ?>
                        <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> Le code ne peut plus être modifié.</small>
                    <?php else: ?>
                        <small class="text-muted d-block mt-1">Identifiant unique (lettres minuscules, chiffres, tirets).</small>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_NAME'] ?? 'Nom officiel'; ?> <span class="fg-red">*</span></label>
                    <input type="text" name="name" data-role="input" required placeholder="ex: Ligue 1, UEFA Champions League"
                           value="<?php echo htmlspecialchars($item->name ?? ''); ?>">
                </div>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_SHORT_NAME'] ?? 'Nom abrégé'; ?></label>
                        <input type="text" name="short_name" data-role="input" placeholder="ex: L1, UCL"
                               value="<?php echo htmlspecialchars($item->short_name ?? ''); ?>">
                    </div>
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_ACRONYM'] ?? 'Sigle'; ?></label>
                        <input type="text" name="acronym" data-role="input" placeholder="ex: UCL, EHF"
                               value="<?php echo htmlspecialchars($item->acronym ?? ''); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_SPORT'] ?? 'Sport'; ?> <span class="fg-red">*</span></label>
                        <select name="sport_id" data-role="select" required>
                            <option value="">-- Choisir un sport --</option>
                            <?php foreach ($allSports as $s): ?>
                                <option value="<?php echo (int)$s['id']; ?>" <?php echo (isset($item->sport_id) && (int)$item->sport_id === (int)$s['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="cell-md-6">
                        <label class="text-bold"><?php echo $data['txt']['COMPETITION_TYPE'] ?? 'Type'; ?> <span class="fg-red">*</span></label>
                        <select name="type_code" data-role="select" required>
                            <option value="">-- Choisir un type --</option>
                            <?php foreach ($allTypes as $t): ?>
                                <option value="<?php echo htmlspecialchars($t['code']); ?>" <?php echo (isset($item->type_code) && $item->type_code === $t['code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($data['txt'][$t['text_code']] ?? $t['code']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_COUNTRY'] ?? 'Pays'; ?></label>
                    <select name="country_code" data-role="select">
                        <option value="">-- International / Aucun --</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo (isset($item->country_code) && $item->country_code === $c['country_code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['country_code']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="text-bold"><?php echo $data['txt']['COMPETITION_DESCRIPTION'] ?? 'Description'; ?></label>
                    <textarea name="description" data-role="textarea" data-auto-size="true" rows="3"
                              placeholder="Description de la compétition..."><?php echo htmlspecialchars($item->description ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Colonne latérale : Logo et Statut -->
            <div class="cell-md-4">
                <div class="p-3 mb-3 border bd-default border-radius-4 text-center bg-white">
                    <h5 class="text-bold mb-3" style="font-size: 14px;"><?php echo $data['txt']['COMPETITION_LOGO'] ?? 'Logo'; ?></h5>
                    <?php if ($logoUrl): ?>
                        <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" style="max-height: 80px; max-width: 100%; object-fit: contain;" class="mb-2">
                    <?php else: ?>
                        <span class="mif-trophy fg-gray mif-4x d-block mb-2"></span>
                    <?php endif; ?>
                    <input type="file" name="logo_file" id="compLogoInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-role="file" data-button-title="<span class='mif-folder'></span> Parcourir">
                    <small class="text-muted d-block mt-1">PNG, JPG, WEBP, SVG — Max 2 Mo</small>
                </div>

                <?php if ($mode === 'edit'): ?>
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <label class="text-bold mb-2 d-block"><?php echo $data['txt']['COMPETITION_STATUS'] ?? 'Statut'; ?></label>
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
