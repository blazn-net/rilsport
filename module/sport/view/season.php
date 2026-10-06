<?php
/**
 * Vue : Saison (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$season = $data['season'] ?? null;

$formConfig = [
    'mode'       => $data['mode'] ?? 'view',
    'item'       => $season,
    'icon'       => 'mif-calendar',
    'viewTitle'  => 'Fiche de la saison : ' . htmlspecialchars($season->name ?? ''),
    'editTitle'  => $data['txt']['SPORT_EDIT_SEASON_TITLE'] ?? 'Modifier la saison',
    'addTitle'   => $data['txt']['SPORT_ADD_SEASON_TITLE'] ?? 'Ajouter une saison',
    'backUrl'    => URLROOT . '/sport/seasons',
    'editUrl'    => isset($season->id) ? URLROOT . '/sport/season/edit/' . (int)$season->id : null,
    'cancelUrl'  => isset($season->id) ? URLROOT . '/sport/season/' . (int)$season->id : URLROOT . '/sport/seasons',
    'formAction' => URLROOT . '/sport/season' . (($data['mode'] === 'edit' && isset($season->id)) ? '/' . (int)$season->id : ''),

    // Icône de l'avatar en mode View
    'viewAvatarIcon' => 'mif-calendar',

    // Contenu spécifique du mode View (Consultation)
    'viewContent' => function($item, $data) { ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-event-available fg-emerald mr-1"></span> <?php echo $data['txt']['SPORT_SEASON_DATE_START'] ?? 'Date de début'; ?>
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 18px; color: #1e293b;">
                        <?php echo !empty($item->date_start) ? htmlspecialchars(date('d/m/Y', strtotime($item->date_start))) : '—'; ?>
                    </div>
                </div>
            </div>
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-event-busy fg-crimson mr-1"></span> <?php echo $data['txt']['SPORT_SEASON_DATE_END'] ?? 'Date de fin'; ?>
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 18px; color: #1e293b;">
                        <?php echo !empty($item->date_end) ? htmlspecialchars(date('d/m/Y', strtotime($item->date_end))) : '—'; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire)
    'formContent' => function($item, $data, $mode) { ?>
        <div class="form-group">
            <label class="text-bold"><?php echo $data['txt']['SPORT_SEASON_CODE_LABEL'] ?? 'Code unique de la saison'; ?></label>
            <input type="text" name="code" data-role="input" placeholder="ex: 2026-2027 ou 2027" maxlength="50"
                   value="<?php echo htmlspecialchars($item->code ?? ''); ?>"
                   <?php echo ($mode === 'edit') ? 'readonly' : 'required'; ?>>
            <?php if ($mode === 'edit'): ?>
                <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> <?php echo $data['txt']['SPORT_SEASON_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
            <?php else: ?>
                <small class="fg-gray d-block mt-1">Identifiant unique (lettres, chiffres, tirets).</small>
            <?php endif; ?>
        </div>

        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SPORT_SEASON_NAME_LABEL'] ?? 'Nom de la saison'; ?></label>
            <input type="text" name="name" data-role="input" placeholder="ex: Saison 2026-2027" maxlength="100"
                   value="<?php echo htmlspecialchars($item->name ?? ''); ?>" required>
        </div>

        <div class="row mt-3">
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><span class="mif-event-available fg-emerald mr-1"></span> <?php echo $data['txt']['SPORT_SEASON_START_LABEL'] ?? 'Date de début'; ?></label>
                    <input type="date" name="date_start" data-role="input"
                           value="<?php echo htmlspecialchars($item->date_start ?? ''); ?>" required>
                </div>
            </div>
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><span class="mif-event-busy fg-crimson mr-1"></span> <?php echo $data['txt']['SPORT_SEASON_END_LABEL'] ?? 'Date de fin'; ?></label>
                    <input type="date" name="date_end" data-role="input"
                           value="<?php echo htmlspecialchars($item->date_end ?? ''); ?>" required>
                </div>
            </div>
        </div>

        <?php if ($mode === 'edit'): ?>
        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SPORT_STATUS_LABEL'] ?? 'Statut'; ?></label>
            <select name="status_id" data-role="select">
                <option value="1" <?php echo (isset($item->status_id) && (int)$item->status_id === 1) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SPORT_ACTIVE'] ?? 'Actif'; ?>
                </option>
                <option value="2" <?php echo (isset($item->status_id) && (int)$item->status_id === 2) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SPORT_INACTIVE'] ?? 'Inactif'; ?>
                </option>
            </select>
        </div>
        <?php endif; ?>
    <?php }
];

require 'module/system/view/common/form_template.php';
