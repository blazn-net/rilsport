<?php
/**
 * Vue : Utilisateur (Formulaire Profil / Add / Edit)
 * Utilise le template parent universel form_template.php
 */

$user      = $data['user'] ?? null;
$isAdmin   = !empty($data['isAdmin']);
$backUrl   = URLROOT . '/' . ($isAdmin ? 'user/users' : 'main');

$userCodeOrId = !empty($user->username) ? htmlspecialchars($user->username) : (isset($user->id) ? (int)$user->id : null);

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $user,
    'idField'        => 'id',
    'codeField'      => 'username',
    'nameField'      => 'displayName',
    'statusField'    => 'status_id',
    'icon'           => 'mif-user',
    'viewTitle'      => 'Fiche de l\'utilisateur : ' . htmlspecialchars($user->displayName ?? ''),
    'editTitle'      => $data['txt']['USER_EDIT_USER_TITLE'] ?? 'Modifier l\'utilisateur',
    'addTitle'       => $data['txt']['USER_ADD_USER_BTN'] ?? 'Ajouter un utilisateur',
    'backUrl'        => $backUrl,
    'editUrl'        => $userCodeOrId ? URLROOT . '/user/edit/' . $userCodeOrId : null,
    'cancelUrl'      => $userCodeOrId ? URLROOT . '/user/' . $userCodeOrId : $backUrl,
    'formAction'     => URLROOT . '/user' . (($data['mode'] === 'edit' && $userCodeOrId) ? '/edit/' . $userCodeOrId : ''),
    'viewAvatarIcon' => 'mif-user',

    // ── Mode VIEW : Consultation pure (Zéro input, textes & badges) ───────────
    'viewContent' => function($item, $data) {
        $roles          = $item->roles ?? [];
        $availableRoles = $data['available_roles'] ?? [];
    ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-user mr-1 fg-primary"></span> <?php echo $data['txt']['USER_FIRSTNAME'] ?? 'Prénom'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->prenom) ? htmlspecialchars($item->prenom) : '—'; ?>
                    </div>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-user mr-1 fg-primary"></span> <?php echo $data['txt']['USER_LASTNAME'] ?? 'Nom'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->nom) ? htmlspecialchars($item->nom) : '—'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-mail mr-1 fg-orange"></span> <?php echo $data['txt']['USER_EMAIL'] ?? 'Email'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->email) ? htmlspecialchars($item->email) : '—'; ?>
                    </div>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-badge mr-1 fg-emerald"></span> <?php echo $data['txt']['USER_ROLE'] ?? 'Rôle(s)'; ?>
                    </div>
                    <div class="mt-2 d-flex flex-wrap" style="gap: 5px;">
                        <?php if (!empty($roles) && is_array($roles)): ?>
                            <?php foreach ($roles as $roleId): ?>
                                <?php 
                                    $badgeCode = $availableRoles[$roleId]['badge_code'] ?? 'primary';
                                    $textCode  = $availableRoles[$roleId]['text_code'] ?? $roleId;
                                    $roleLabel = $data['txt'][$textCode] ?? $textCode;
                                ?>
                                <span class="badge <?php echo htmlspecialchars($badgeCode); ?> p-2" style="font-size: 13px;">
                                    <?php echo htmlspecialchars($roleLabel); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="badge primary p-2" style="font-size: 13px;">—</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php },

    // ── Mode EDIT / ADD : Formulaire interactif ──────────────────────────────
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
