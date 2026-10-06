<?php
/**
 * Vue : Utilisateur (Formulaire Profil / Add / Edit)
 * Utilise le template parent universel form_template.php
 */

$user      = $data['user'] ?? null;
$isAdmin   = !empty($data['is_admin']);
$backUrl   = URLROOT . '/' . ($isAdmin ? 'user/users' : 'main');

$formConfig = [
    'mode'        => $data['mode'] ?? 'edit',
    'item'        => $user,
    'idField'     => 'id',
    'codeField'   => 'username',
    'nameField'   => 'username',
    'icon'        => 'mif-user',
    'viewTitle'   => 'Profil de l\'utilisateur : ' . htmlspecialchars($user->username ?? ''),
    'editTitle'   => $data['txt']['USER_EDIT_USER_TITLE'] ?? 'Modifier l\'utilisateur',
    'addTitle'    => $data['txt']['USER_ADD_USER_BTN'] ?? 'Ajouter un utilisateur',
    'backUrl'     => $backUrl,
    'cancelUrl'   => $backUrl,
    'formAction'  => URLROOT . '/user/' . (!empty($data['id']) ? (int)$data['id'] : ''),
    'viewAvatarIcon' => 'mif-user',

    // Formulaire interactif Add/Edit
    'formContent' => function($item, $data, $mode) use ($isAdmin) { ?>
        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['USER_USERNAME'] ?? 'Identifiant (Pseudo)'; ?> <span class="fg-red">*</span></label>
            <input type="text" name="username" data-role="input" required
                   value="<?php echo htmlspecialchars($item->username ?? ''); ?>"
                   placeholder="ex: jdupont">
        </div>

        <div class="row mb-3">
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['USER_FIRSTNAME'] ?? 'Prénom'; ?></label>
                    <input type="text" name="prenom" data-role="input" placeholder="ex: Jean"
                           value="<?php echo htmlspecialchars($item->prenom ?? ''); ?>">
                </div>
            </div>
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['USER_LASTNAME'] ?? 'Nom'; ?></label>
                    <input type="text" name="nom" data-role="input" placeholder="ex: Dupont"
                           value="<?php echo htmlspecialchars($item->nom ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="text-bold"><?php echo $data['txt']['USER_EMAIL'] ?? 'Adresse email'; ?> <span class="fg-red">*</span></label>
            <input type="email" name="email" data-role="input" required placeholder="contact@example.com"
                   value="<?php echo htmlspecialchars($item->email ?? ''); ?>">
        </div>

        <div class="form-group mb-3">
            <label class="text-bold">
                <?php echo $data['txt']['USER_PASSWORD'] ?? 'Mot de passe'; ?>
                <?php if ($mode === 'edit'): ?>
                    <small class="fg-gray font-normal">(<?php echo $data['txt']['USER_LEAVE_BLANK_NO_CHANGE'] ?? 'laisser vide pour ne pas modifier'; ?>)</small>
                <?php else: ?>
                    <span class="fg-red">*</span>
                <?php endif; ?>
            </label>
            <input type="password" name="password" data-role="input" <?php echo ($mode === 'add') ? 'required' : ''; ?>>
        </div>

        <?php if ($isAdmin): ?>
            <div class="form-group mb-3">
                <label class="text-bold"><?php echo $data['txt']['USER_ROLE'] ?? 'Rôle(s)'; ?></label>
                <select name="roles[]" multiple data-role="select">
                    <?php foreach ($data['available_roles'] as $role_id => $role_info): ?>
                        <?php $selected = (isset($item->roles) && is_array($item->roles) && in_array($role_id, $item->roles)) ? 'selected' : ''; ?>
                        <option value="<?php echo htmlspecialchars($role_id); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($data['txt'][$role_info['text_code']] ?? $role_info['text_code']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="text-bold"><?php echo $data['txt']['STATUS'] ?? 'Statut'; ?></label>
                <select name="status_id" data-role="select">
                    <?php foreach ($data['available_statuses'] as $status_id => $text_code): ?>
                        <?php 
                        $statusValue = isset($item->status_id) ? (int)$item->status_id : 1;
                        $selected = ($statusValue === (int)$status_id) ? 'selected' : ''; 
                        ?>
                        <option value="<?php echo htmlspecialchars($status_id); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($data['txt'][$text_code] ?? $text_code); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
    <?php }
];

require 'module/system/view/common/form_template.php';
