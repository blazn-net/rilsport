<?php
/**
 * Vue : Personne (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$person   = $data['person'] ?? null;
$fullName = trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? ''));
$roles    = $data['roles'] ?? [];

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $person,
    'icon'           => 'mif-contacts',
    'viewTitle'      => 'Fiche de la personne : ' . htmlspecialchars($fullName),
    'editTitle'      => $data['txt']['SPORT_EDIT_PERSON_TITLE'] ?? 'Modifier la personne',
    'addTitle'       => $data['txt']['SPORT_ADD_PERSON_TITLE'] ?? 'Ajouter une personne',
    'backUrl'        => URLROOT . '/sport/persons',
    'editUrl'        => isset($person->id) ? URLROOT . '/sport/person/edit/' . (int)$person->id : null,
    'cancelUrl'      => isset($person->id) ? URLROOT . '/sport/person/' . (int)$person->id : URLROOT . '/sport/persons',
    'formAction'     => URLROOT . '/sport/person' . (($data['mode'] === 'edit' && isset($person->id)) ? '/' . (int)$person->id : ''),
    'viewAvatarIcon' => 'mif-user',

    // Contenu spécifique du mode View (Consultation pure)
    'viewContent' => function($item, $data) { 
        $roleName  = !empty($item->role_name) ? $item->role_name : ($item->role_code ?? 'Joueur');
        $roleBadge = 'primary';
        if (($item->role_code ?? '') === 'coach')    $roleBadge = 'warning';
        elseif (($item->role_code ?? '') === 'referee') $roleBadge = 'alert';
        elseif (($item->role_code ?? '') === 'official')$roleBadge = 'dark';
        elseif (($item->role_code ?? '') === 'staff')   $roleBadge = 'secondary';
    ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-badge fg-primary mr-1"></span> <?php echo $data['txt']['SPORT_PERSON_ROLE'] ?? 'Rôle / Fonction'; ?>
                    </div>
                    <div class="mt-2">
                        <span class="badge <?php echo $roleBadge; ?> p-2" style="font-size: 13px;"><?php echo htmlspecialchars($roleName); ?></span>
                    </div>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-user mr-1"></span> <?php echo $data['txt']['SPORT_PERSON_GENDER'] ?? 'Genre'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo (isset($item->gender) && $item->gender === 'F') ? 'Féminin (F)' : 'Masculin (M)'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-calendar fg-blue mr-1"></span> <?php echo $data['txt']['SPORT_PERSON_BIRTHDATE'] ?? 'Date de naissance'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->birth_date) ? htmlspecialchars(date('d/m/Y', strtotime($item->birth_date))) : '—'; ?>
                    </div>
                </div>
            </div>

            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-earth fg-orange mr-1"></span> <?php echo $data['txt']['SPORT_PERSON_NATIONALITY'] ?? 'Nationalité'; ?>
                    </div>
                    <div class="mt-2 text-bold" style="font-size: 16px; color: #1e293b;">
                        <?php echo !empty($item->nationality) ? htmlspecialchars($item->nationality) : '—'; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($roles) { ?>
        <div class="form-group">
            <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_CODE_LABEL'] ?? 'Code unique (ex: p-mbappe)'; ?></label>
            <input type="text" name="code" data-role="input" placeholder="ex: p-mbappe" maxlength="50"
                   value="<?php echo htmlspecialchars($item->code ?? ''); ?>"
                   <?php echo ($mode === 'edit') ? 'readonly' : 'required'; ?>>
            <?php if ($mode === 'edit'): ?>
                <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> <?php echo $data['txt']['SPORT_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
            <?php else: ?>
                <small class="fg-gray d-block mt-1">Identifiant unique (lettres minuscules, chiffres, tirets).</small>
            <?php endif; ?>
        </div>

        <div class="row mt-3">
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_FIRSTNAME_LABEL'] ?? 'Prénom'; ?></label>
                    <input type="text" name="first_name" data-role="input" placeholder="ex: Kylian" maxlength="100"
                           value="<?php echo htmlspecialchars($item->first_name ?? ''); ?>" required>
                </div>
            </div>
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_LASTNAME_LABEL'] ?? 'Nom de famille'; ?></label>
                    <input type="text" name="last_name" data-role="input" placeholder="ex: Mbappé" maxlength="100"
                           value="<?php echo htmlspecialchars($item->last_name ?? ''); ?>" required>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_ROLE_LABEL'] ?? 'Rôle principal'; ?></label>
                    <select name="role_code" data-role="select">
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo htmlspecialchars($r['code']); ?>" <?php echo (isset($item->role_code) && $item->role_code === $r['code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($r['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_GENDER_LABEL'] ?? 'Genre'; ?></label>
                    <select name="gender" data-role="select">
                        <option value="M" <?php echo (!isset($item->gender) || $item->gender === 'M') ? 'selected' : ''; ?>>Masculin (M)</option>
                        <option value="F" <?php echo (isset($item->gender) && $item->gender === 'F') ? 'selected' : ''; ?>>Féminin (F)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_BIRTHDATE_LABEL'] ?? 'Date de naissance'; ?></label>
                    <input type="date" name="birth_date" data-role="input" value="<?php echo htmlspecialchars($item->birth_date ?? ''); ?>">
                </div>
            </div>
            <div class="cell-md-6">
                <div class="form-group">
                    <label class="text-bold"><?php echo $data['txt']['SPORT_PERSON_NATIONALITY_LABEL'] ?? 'Nationalité'; ?></label>
                    <input type="text" name="nationality" data-role="input" placeholder="ex: Française" maxlength="50"
                           value="<?php echo htmlspecialchars($item->nationality ?? ''); ?>">
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
