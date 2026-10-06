<?php
/**
 * Vue : Sport (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$sport = $data['sport'] ?? null;
$sportIcon = (!empty($sport->icon) && $sport->icon !== 'mif-dribbble') ? $sport->icon : 'mif-trophy';

$formConfig = [
    'mode'       => $data['mode'] ?? 'view',
    'item'       => $sport,
    'icon'       => $sportIcon,
    'viewTitle'  => 'Fiche du sport : ' . htmlspecialchars($sport->name ?? ''),
    'editTitle'  => $data['txt']['SPORT_EDIT_SPORT_TITLE'] ?? 'Modifier le sport',
    'addTitle'   => $data['txt']['SPORT_ADD_SPORT_TITLE'] ?? 'Ajouter un sport',
    'backUrl'    => URLROOT . '/sport/sports',
    'editUrl'    => isset($sport->id) ? URLROOT . '/sport/sport/edit/' . (int)$sport->id : null,
    'cancelUrl'  => isset($sport->id) ? URLROOT . '/sport/sport/' . (int)$sport->id : URLROOT . '/sport/sports',
    'formAction' => URLROOT . '/sport/sport' . (($data['mode'] === 'edit' && isset($sport->id)) ? '/' . (int)$sport->id : ''),

    // Icône de l'avatar en mode View
    'viewAvatarIcon' => $sportIcon,

    // Contenu spécifique du mode View (Consultation)
    'viewContent' => function($item, $data) { ?>
        <div class="mb-2">
            <h5 class="text-bold mb-1" style="color: #475569; font-size: 14px;">
                <span class="mif-file-text mr-1"></span> <?php echo $data['txt']['SPORT_DESCRIPTION_LABEL'] ?? 'Description'; ?>
            </h5>
            <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 15px; color: #1e293b; min-height: 60px;">
                <?php echo !empty($item->description) ? nl2br(htmlspecialchars($item->description)) : '<em class="text-muted">Aucune description renseignée.</em>'; ?>
            </div>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire)
    'formContent' => function($item, $data, $mode) { ?>
        <div class="form-group">
            <label class="text-bold"><?php echo $data['txt']['SPORT_CODE_LABEL'] ?? 'Code unique du sport'; ?></label>
            <input type="text" name="code" data-role="input" placeholder="ex: football, basketball..." maxlength="50"
                   value="<?php echo htmlspecialchars($item->code ?? ''); ?>"
                   <?php echo ($mode === 'edit') ? 'readonly' : 'required'; ?>>
            <?php if ($mode === 'edit'): ?>
                <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> <?php echo $data['txt']['SPORT_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
            <?php else: ?>
                <small class="fg-gray d-block mt-1">Identifiant unique (lettres minuscules, chiffres, tirets).</small>
            <?php endif; ?>
        </div>

        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SPORT_NAME_LABEL'] ?? 'Nom de la discipline'; ?></label>
            <input type="text" name="name" data-role="input" placeholder="ex: Football, Basketball..." maxlength="100"
                   value="<?php echo htmlspecialchars($item->name ?? ''); ?>" required>
        </div>

        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SPORT_DESCRIPTION_LABEL'] ?? 'Description'; ?></label>
            <textarea name="description" data-role="textarea" data-auto-size="true" rows="3"
                      placeholder="Description de la discipline sportive..."><?php echo htmlspecialchars($item->description ?? ''); ?></textarea>
        </div>

        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SPORT_ICON_LABEL'] ?? 'Classe d\'icône Metro UI'; ?></label>
            <input type="text" name="icon" data-role="input" placeholder="ex: mif-trophy"
                   value="<?php echo htmlspecialchars((!empty($item->icon) && $item->icon !== 'mif-dribbble') ? $item->icon : 'mif-trophy'); ?>">
            <small class="fg-gray d-block mt-1"><?php echo $data['txt']['SPORT_ICON_HELP'] ?? 'Nom de classe d\'icône Metro UI (mif-*).'; ?></small>
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
