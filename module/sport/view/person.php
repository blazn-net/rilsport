<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center mb-4">
        <h2>
            <span class="mif-contacts mr-2"></span>
            <?php 
                if ($data['mode'] === 'edit') {
                    echo $data['txt']['SPORT_EDIT_PERSON_TITLE'] ?? 'Modifier la personne';
                } elseif ($data['mode'] === 'view') {
                    echo 'Fiche de la personne : ' . htmlspecialchars(($data['person']->first_name ?? '') . ' ' . ($data['person']->last_name ?? ''));
                } else {
                    echo $data['txt']['SPORT_ADD_PERSON_TITLE'] ?? 'Ajouter une personne';
                }
            ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/sport/persons" class="button" title="<?php echo htmlspecialchars($data['txt']['USER_BTN_BACK'] ?? 'Retour à la liste'); ?>">
            <span class="mif-arrow-left"></span> <?php echo $data['txt']['USER_BTN_BACK'] ?? 'Retour à la liste'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($data['mode'] === 'view'): ?>
        <!-- MODE VIEW (Consultation pour utilisateurs simples : Affichage pur en Libellés / Cartes, sans inputs) -->
        <div class="card p-4">
            <div class="d-flex flex-align-center mb-3">
                <div class="avatar bg-primary fg-white border-radius-half d-flex flex-justify-center flex-align-center mr-3" style="width: 60px; height: 60px; min-width: 60px;">
                    <span class="mif-user mif-3x"></span>
                </div>
                <div>
                    <h3 class="m-0"><?php echo htmlspecialchars(($data['person']->first_name ?? '') . ' ' . ($data['person']->last_name ?? '')); ?></h3>
                    <small class="fg-gray">Code : <code><?php echo htmlspecialchars($data['person']->code ?? ''); ?></code></small>
                </div>
                <div class="ml-auto">
                    <?php 
                        $isActive   = isset($data['person']->status_id) && intval($data['person']->status_id) === 1;
                        $badgeClass = $isActive ? 'success' : 'secondary';
                        $statusText = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    ?>
                    <span class="badge <?php echo $badgeClass; ?> p-2"><?php echo htmlspecialchars($statusText); ?></span>
                </div>
            </div>

            <div class="divider my-3"></div>

            <div class="row mb-4">
                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_PERSON_ROLE'] ?? 'Rôle / Fonction'; ?></h5>
                    <p class="text-leader">
                        <?php 
                            $roleName  = !empty($data['person']->role_name) ? $data['person']->role_name : ($data['person']->role_code ?? 'Joueur');
                            $roleBadge = 'primary';
                            if (($data['person']->role_code ?? '') === 'coach') $roleBadge = 'warning';
                            elseif (($data['person']->role_code ?? '') === 'referee') $roleBadge = 'alert';
                            elseif (($data['person']->role_code ?? '') === 'official') $roleBadge = 'dark';
                            elseif (($data['person']->role_code ?? '') === 'staff') $roleBadge = 'secondary';
                        ?>
                        <span class="badge <?php echo $roleBadge; ?> p-2"><?php echo htmlspecialchars($roleName); ?></span>
                    </p>
                </div>

                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_PERSON_GENDER'] ?? 'Genre'; ?></h5>
                    <p class="text-leader">
                        <?php echo (isset($data['person']->gender) && $data['person']->gender === 'F') ? 'Féminin (F)' : 'Masculin (M)'; ?>
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_PERSON_BIRTHDATE'] ?? 'Date de naissance'; ?></h5>
                    <p class="text-leader">
                        <span class="mif-calendar fg-blue mr-1"></span>
                        <?php echo isset($data['person']->birth_date) && $data['person']->birth_date ? htmlspecialchars(date('d/m/Y', strtotime($data['person']->birth_date))) : '-'; ?>
                    </p>
                </div>
                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_PERSON_NATIONALITY'] ?? 'Nationalité'; ?></h5>
                    <p class="text-leader">
                        <span class="mif-earth fg-orange mr-1"></span>
                        <?php echo isset($data['person']->nationality) && $data['person']->nationality ? htmlspecialchars($data['person']->nationality) : '-'; ?>
                    </p>
                </div>
            </div>

            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="false" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['person']->created_at) && $data['person']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['person']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['person']->created_by_name) && $data['person']->created_by_name ? htmlspecialchars($data['person']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['person']->modified_at) && $data['person']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['person']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['person']->modified_by_name) && $data['person']->modified_by_name ? htmlspecialchars($data['person']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- MODE EDIT / ADD (Formulaire interactif pour Administrateurs) -->
        <form method="POST" action="<?php echo URLROOT; ?>/sport/person<?php echo ($data['mode'] === 'edit' && isset($data['person']->id)) ? '/' . htmlspecialchars($data['person']->id) : ''; ?>">
            
            <div class="form-group">
                <label><?php echo $data['txt']['SPORT_PERSON_CODE_LABEL'] ?? 'Code unique (ex: p-mbappe)'; ?></label>
                <input type="text" name="code" data-role="input" placeholder="ex: p-mbappe" maxlength="50" value="<?php echo htmlspecialchars($data['person']->code ?? ''); ?>" <?php echo ($data['mode'] === 'edit') ? 'disabled' : 'required'; ?>>
                <?php if ($data['mode'] === 'edit'): ?>
                <small class="fg-gray"><?php echo $data['txt']['SPORT_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
                <?php endif; ?>
            </div>

            <div class="row mt-3">
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_FIRSTNAME_LABEL'] ?? 'Prénom'; ?></label>
                        <input type="text" name="first_name" data-role="input" placeholder="ex: Kylian" maxlength="100" value="<?php echo htmlspecialchars($data['person']->first_name ?? ''); ?>" required>
                    </div>
                </div>
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_LASTNAME_LABEL'] ?? 'Nom de famille'; ?></label>
                        <input type="text" name="last_name" data-role="input" placeholder="ex: Mbappé" maxlength="100" value="<?php echo htmlspecialchars($data['person']->last_name ?? ''); ?>" required>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_ROLE_LABEL'] ?? 'Rôle principal'; ?></label>
                        <select name="role_code" data-role="select">
                            <?php if (!empty($data['roles'])): ?>
                                <?php foreach ($data['roles'] as $r): ?>
                                    <option value="<?php echo htmlspecialchars($r['code']); ?>" <?php echo (isset($data['person']->role_code) && $data['person']->role_code === $r['code']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($r['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_GENDER_LABEL'] ?? 'Genre'; ?></label>
                        <select name="gender" data-role="select">
                            <option value="M" <?php echo (!isset($data['person']->gender) || $data['person']->gender === 'M') ? 'selected' : ''; ?>>Masculin (M)</option>
                            <option value="F" <?php echo (isset($data['person']->gender) && $data['person']->gender === 'F') ? 'selected' : ''; ?>>Féminin (F)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_BIRTHDATE_LABEL'] ?? 'Date de naissance'; ?></label>
                        <input type="date" name="birth_date" data-role="input" value="<?php echo htmlspecialchars($data['person']->birth_date ?? ''); ?>">
                    </div>
                </div>
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_PERSON_NATIONALITY_LABEL'] ?? 'Nationalité'; ?></label>
                        <input type="text" name="nationality" data-role="input" placeholder="ex: Française" maxlength="50" value="<?php echo htmlspecialchars($data['person']->nationality ?? ''); ?>">
                    </div>
                </div>
            </div>

            <?php if ($data['mode'] === 'edit'): ?>
            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_STATUS_LABEL'] ?? 'Statut'; ?></label>
                <select name="status_id" data-role="select">
                    <option value="1" <?php echo (isset($data['person']->status_id) && intval($data['person']->status_id) === 1) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_ACTIVE'] ?? 'Actif'; ?></option>
                    <option value="2" <?php echo (isset($data['person']->status_id) && intval($data['person']->status_id) === 2) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_INACTIVE'] ?? 'Inactif'; ?></option>
                </select>
            </div>

            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="true" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['person']->created_at) && $data['person']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['person']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['person']->created_by_name) && $data['person']->created_by_name ? htmlspecialchars($data['person']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['person']->modified_at) && $data['person']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['person']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['person']->modified_by_name) && $data['person']->modified_by_name ? htmlspecialchars($data['person']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <button class="button primary" type="submit" title="<?php echo htmlspecialchars($data['mode'] === 'edit' ? ($data['txt']['USER_BTN_UPDATE'] ?? 'Mettre à jour') : ($data['txt']['USER_BTN_SAVE'] ?? 'Enregistrer')); ?>"><?php echo ($data['mode'] === 'edit') ? ($data['txt']['USER_BTN_UPDATE'] ?? 'Mettre à jour') : ($data['txt']['USER_BTN_SAVE'] ?? 'Enregistrer'); ?></button>
            </div>
        </form>
    <?php endif; ?>
</main>
