<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center mb-4">
        <h2>
            <span class="<?php echo (!empty($data['sport']->icon) && $data['sport']->icon !== 'mif-dribbble') ? htmlspecialchars($data['sport']->icon) : 'mif-trophy'; ?> mr-2"></span>
            <?php 
                if ($data['mode'] === 'edit') {
                    echo $data['txt']['SPORT_EDIT_SPORT_TITLE'] ?? 'Modifier le sport';
                } elseif ($data['mode'] === 'view') {
                    echo 'Fiche du sport : ' . htmlspecialchars($data['sport']->name ?? '');
                } else {
                    echo $data['txt']['SPORT_ADD_SPORT_TITLE'] ?? 'Ajouter un sport';
                }
            ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/sport/sports" class="button" title="<?php echo htmlspecialchars($data['txt']['USER_BTN_BACK'] ?? 'Retour à la liste'); ?>">
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
                <div class="avatar bg-primary fg-white border-radius-half d-flex flex-justify-center flex-align-center mr-3" style="width: 50px; height: 50px; min-width: 50px;">
                    <span class="<?php echo (!empty($data['sport']->icon) && $data['sport']->icon !== 'mif-dribbble') ? htmlspecialchars($data['sport']->icon) : 'mif-trophy'; ?> mif-2x"></span>
                </div>
                <div>
                    <h3 class="m-0"><?php echo htmlspecialchars($data['sport']->name ?? ''); ?></h3>
                    <small class="fg-gray">Code : <code><?php echo htmlspecialchars($data['sport']->code ?? ''); ?></code></small>
                </div>
                <div class="ml-auto">
                    <?php 
                        $isActive = isset($data['sport']->status_id) && intval($data['sport']->status_id) === 1;
                        $badgeClass = $isActive ? 'success' : 'secondary';
                        $statusText = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    ?>
                    <span class="badge <?php echo $badgeClass; ?> p-2"><?php echo htmlspecialchars($statusText); ?></span>
                </div>
            </div>

            <div class="divider my-3"></div>

            <div class="mb-4">
                <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_DESCRIPTION_LABEL'] ?? 'Description'; ?></h5>
                <p class="text-leader">
                    <?php echo !empty($data['sport']->description) ? nl2br(htmlspecialchars($data['sport']->description)) : '<em>Aucune description renseignée.</em>'; ?>
                </p>
            </div>

            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="false" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['sport']->created_at) && $data['sport']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['sport']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['sport']->created_by_name) && $data['sport']->created_by_name ? htmlspecialchars($data['sport']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['sport']->modified_at) && $data['sport']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['sport']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['sport']->modified_by_name) && $data['sport']->modified_by_name ? htmlspecialchars($data['sport']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- MODE EDIT / ADD (Formulaire interactif pour Administrateurs : Textbox, Dropdown, Submit) -->
        <form method="POST" action="<?php echo URLROOT; ?>/sport/sport<?php echo ($data['mode'] === 'edit' && isset($data['sport']->id)) ? '/' . htmlspecialchars($data['sport']->id) : ''; ?>">
            
            <div class="form-group">
                <label><?php echo $data['txt']['SPORT_CODE_LABEL'] ?? 'Code unique du sport'; ?></label>
                <input type="text" name="code" data-role="input" placeholder="ex: football, basketball..." maxlength="50" value="<?php echo htmlspecialchars($data['sport']->code ?? ''); ?>" <?php echo ($data['mode'] === 'edit') ? 'disabled' : 'required'; ?>>
                <?php if ($data['mode'] === 'edit'): ?>
                <small class="fg-gray"><?php echo $data['txt']['SPORT_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_NAME_LABEL'] ?? 'Nom de la discipline'; ?></label>
                <input type="text" name="name" data-role="input" placeholder="ex: Football, Basketball..." maxlength="100" value="<?php echo htmlspecialchars($data['sport']->name ?? ''); ?>" required>
            </div>

            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_DESCRIPTION_LABEL'] ?? 'Description'; ?></label>
                <textarea name="description" data-role="textarea" placeholder="Description de la discipline sportive..."><?php echo htmlspecialchars($data['sport']->description ?? ''); ?></textarea>
            </div>

            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_ICON_LABEL'] ?? 'Classe d\'icône Metro UI'; ?></label>
                <input type="text" name="icon" data-role="input" placeholder="ex: mif-trophy" value="<?php echo htmlspecialchars((!empty($data['sport']->icon) && $data['sport']->icon !== 'mif-dribbble') ? $data['sport']->icon : 'mif-trophy'); ?>">
                <small class="fg-gray"><?php echo $data['txt']['SPORT_ICON_HELP'] ?? 'Nom de classe d\'icône Metro UI (mif-*).'; ?></small>
            </div>

            <?php if ($data['mode'] === 'edit'): ?>
            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_STATUS_LABEL'] ?? 'Statut'; ?></label>
                <select name="status_id" data-role="select">
                    <option value="1" <?php echo (isset($data['sport']->status_id) && intval($data['sport']->status_id) === 1) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_ACTIVE'] ?? 'Actif'; ?></option>
                    <option value="2" <?php echo (isset($data['sport']->status_id) && intval($data['sport']->status_id) === 2) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_INACTIVE'] ?? 'Inactif'; ?></option>
                </select>
            </div>

            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="true" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['sport']->created_at) && $data['sport']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['sport']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['sport']->created_by_name) && $data['sport']->created_by_name ? htmlspecialchars($data['sport']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['sport']->modified_at) && $data['sport']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['sport']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['sport']->modified_by_name) && $data['sport']->modified_by_name ? htmlspecialchars($data['sport']->modified_by_name) : '-'; ?></p>
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
