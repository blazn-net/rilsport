<?php
/**
 * Vue : Langue (Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$lang = $data['lang'] ?? null;

$formConfig = [
    'mode'        => $data['mode'] ?? 'edit',
    'item'        => $lang,
    'idField'     => 'lang_code',
    'codeField'   => 'lang_code',
    'nameField'   => 'lang_name',
    'icon'        => 'mif-language',
    'viewTitle'   => 'Langue : ' . htmlspecialchars($lang->lang_name ?? ''),
    'editTitle'   => $data['txt']['LANG_EDIT_LANG_TITLE'] ?? 'Modifier la langue',
    'addTitle'    => $data['txt']['LANG_ADD_LANG_TITLE'] ?? 'Ajouter une langue',
    'backUrl'     => URLROOT . '/lang/langs',
    'cancelUrl'   => URLROOT . '/lang/langs',
    'formAction'  => URLROOT . '/lang' . (($data['mode'] === 'edit' && isset($lang->lang_code)) ? '/' . htmlspecialchars($lang->lang_code) : ''),

    // Formulaire interactif Add/Edit
    'formContent' => function($item, $data, $mode) { ?>
        <?php if ($mode === 'add' && !empty($data['txt']['LANG_ADD_NOTE'])): ?>
            <div class="remark info mb-3">
                <?php echo htmlspecialchars($data['txt']['LANG_ADD_NOTE']); ?>
            </div>
        <?php endif; ?>

        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['LANG_CODE_LABEL'] ?? 'Code de la langue (ISO 639-1)'; ?></label>
            <input type="text" name="lang_code" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_CODE_PH'] ?? 'ex: fr, en, es...'); ?>" maxlength="5"
                   value="<?php echo htmlspecialchars($item->lang_code ?? ''); ?>"
                   <?php echo ($mode === 'edit') ? 'readonly' : 'required'; ?>>
            <?php if ($mode === 'edit'): ?>
                <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> <?php echo $data['txt']['LANG_CODE_HELP'] ?? 'Le code langue ne peut pas être modifié.'; ?></small>
            <?php else: ?>
                <small class="text-muted d-block mt-1">Code sur 2 caractères minuscules (ex: fr, en, es, de, it).</small>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['LANG_NAME_LABEL'] ?? 'Nom complet de la langue'; ?></label>
            <input type="text" name="lang_name" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_NAME_PH'] ?? 'ex: Français, English...'); ?>"
                   value="<?php echo htmlspecialchars($item->lang_name ?? ''); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['LANG_FLAG_LABEL'] ?? 'Classe d\'icône du drapeau'; ?></label>
            <input type="text" name="lang_flag" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_FLAG_PH'] ?? 'ex: fi-fr, fi-gb, fi-es'); ?>"
                   value="<?php echo htmlspecialchars($item->lang_flag ?? ''); ?>">
            <small class="fg-gray d-block mt-1"><?php echo $data['txt']['LANG_FLAG_HELP'] ?? 'Nom de classe du drapeau (flag-icons).'; ?></small>
        </div>

        <?php if ($mode === 'edit'): ?>
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['LANG_STATUS'] ?? 'Statut'; ?></label>
            <select name="status_id" data-role="select">
                <option value="1" <?php echo (isset($item->status_id) && (int)$item->status_id === 1) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['LANG_ACTIVE'] ?? 'Actif'; ?>
                </option>
                <option value="2" <?php echo (isset($item->status_id) && (int)$item->status_id === 2) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['LANG_DRAFT'] ?? 'Inactif / Brouillon'; ?>
                </option>
            </select>
        </div>
        <?php endif; ?>
    <?php }
];

require 'module/system/view/common/form_template.php';
