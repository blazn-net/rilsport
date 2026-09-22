<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center mb-4">
        <h2>
            <span class="mif-calendar mr-2"></span>
            <?php 
                if ($data['mode'] === 'edit') {
                    echo $data['txt']['SPORT_EDIT_SEASON_TITLE'] ?? 'Modifier la saison';
                } elseif ($data['mode'] === 'view') {
                    echo 'Fiche de la saison : ' . htmlspecialchars($data['season']->name ?? '');
                } else {
                    echo $data['txt']['SPORT_ADD_SEASON_TITLE'] ?? 'Ajouter une saison';
                }
            ?>
        </h2>
        <div class="d-flex flex-align-center" style="gap: 10px;">
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && isset($data['season']->id)): ?>
                <a href="<?php echo URLROOT; ?>/sport/season/edit/<?php echo htmlspecialchars($data['season']->id); ?>" class="button info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                    <span class="mif-pencil"></span> <span class="btn-text"><?php echo $data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'; ?></span>
                </a>
            <?php endif; ?>
            <a href="<?php echo URLROOT; ?>/sport/seasons" class="button" title="<?php echo htmlspecialchars($data['txt']['USER_BTN_BACK'] ?? 'Retour à la liste'); ?>">
                <span class="mif-arrow-left"></span> <span class="btn-text"><?php echo $data['txt']['USER_BTN_BACK'] ?? 'Retour à la liste'; ?></span>
            </a>
        </div>
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
                <div class="avatar bg-info fg-white border-radius-half d-flex flex-justify-center flex-align-center mr-3" style="width: 50px; height: 50px; min-width: 50px;">
                    <span class="mif-calendar mif-2x"></span>
                </div>
                <div>
                    <h3 class="m-0"><?php echo htmlspecialchars($data['season']->name ?? ''); ?></h3>
                    <small class="fg-gray">Code : <code><?php echo htmlspecialchars($data['season']->code ?? ''); ?></code></small>
                </div>
                <div class="ml-auto">
                    <?php 
                        $isActive   = isset($data['season']->status_id) && intval($data['season']->status_id) === 1;
                        $badgeClass = $isActive ? 'success' : 'secondary';
                        $statusText = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    ?>
                    <span class="badge <?php echo $badgeClass; ?> p-2"><?php echo htmlspecialchars($statusText); ?></span>
                </div>
            </div>

            <div class="divider my-3"></div>

            <div class="row mb-4">
                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_SEASON_DATE_START'] ?? 'Début'; ?></h5>
                    <p class="text-leader">
                        <span class="mif-event-available fg-green mr-1"></span>
                        <?php echo isset($data['season']->date_start) ? htmlspecialchars(date('d/m/Y', strtotime($data['season']->date_start))) : '-'; ?>
                    </p>
                </div>
                <div class="cell-md-6">
                    <h5 class="text-bold mb-1"><?php echo $data['txt']['SPORT_SEASON_DATE_END'] ?? 'Fin'; ?></h5>
                    <p class="text-leader">
                        <span class="mif-event-busy fg-red mr-1"></span>
                        <?php echo isset($data['season']->date_end) ? htmlspecialchars(date('d/m/Y', strtotime($data['season']->date_end))) : '-'; ?>
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
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['season']->created_at) && $data['season']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['season']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['season']->created_by_name) && $data['season']->created_by_name ? htmlspecialchars($data['season']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['season']->modified_at) && $data['season']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['season']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['season']->modified_by_name) && $data['season']->modified_by_name ? htmlspecialchars($data['season']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- MODE EDIT / ADD (Formulaire interactif pour Administrateurs) -->
        <form method="POST" action="<?php echo URLROOT; ?>/sport/season<?php echo ($data['mode'] === 'edit' && isset($data['season']->id)) ? '/' . htmlspecialchars($data['season']->id) : ''; ?>">
            
            <div class="form-group">
                <label><?php echo $data['txt']['SPORT_SEASON_CODE_LABEL'] ?? 'Code unique de la saison'; ?></label>
                <input type="text" name="code" data-role="input" placeholder="ex: 2026-2027 ou 2027" maxlength="50" value="<?php echo htmlspecialchars($data['season']->code ?? ''); ?>" <?php echo ($data['mode'] === 'edit') ? 'disabled' : 'required'; ?>>
                <?php if ($data['mode'] === 'edit'): ?>
                <small class="fg-gray"><?php echo $data['txt']['SPORT_SEASON_CODE_HELP'] ?? 'Le code ne peut plus être modifié après la création.'; ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_SEASON_NAME_LABEL'] ?? 'Nom de la saison'; ?></label>
                <input type="text" name="name" data-role="input" placeholder="ex: Saison 2026-2027" maxlength="100" value="<?php echo htmlspecialchars($data['season']->name ?? ''); ?>" required>
            </div>

            <div class="row mt-3">
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_SEASON_START_LABEL'] ?? 'Début'; ?></label>
                        <input type="date" name="date_start" data-role="input" value="<?php echo htmlspecialchars($data['season']->date_start ?? ''); ?>" required>
                    </div>
                </div>
                <div class="cell-md-6">
                    <div class="form-group">
                        <label><?php echo $data['txt']['SPORT_SEASON_END_LABEL'] ?? 'Fin'; ?></label>
                        <input type="date" name="date_end" data-role="input" value="<?php echo htmlspecialchars($data['season']->date_end ?? ''); ?>" required>
                    </div>
                </div>
            </div>

            <?php if ($data['mode'] === 'edit'): ?>
            <div class="form-group mt-3">
                <label><?php echo $data['txt']['SPORT_STATUS_LABEL'] ?? 'Statut'; ?></label>
                <select name="status_id" data-role="select">
                    <option value="1" <?php echo (isset($data['season']->status_id) && intval($data['season']->status_id) === 1) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_ACTIVE'] ?? 'Actif'; ?></option>
                    <option value="2" <?php echo (isset($data['season']->status_id) && intval($data['season']->status_id) === 2) ? 'selected' : ''; ?>><?php echo $data['txt']['SPORT_INACTIVE'] ?? 'Inactif'; ?></option>
                </select>
            </div>

            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['SPORT_INFO_PANEL'] ?? 'Informations d\'audit'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="true" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'Créé le'; ?> :</strong> <?php echo isset($data['season']->created_at) && $data['season']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['season']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'Créé par'; ?> :</strong> <?php echo isset($data['season']->created_by_name) && $data['season']->created_by_name ? htmlspecialchars($data['season']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong> <?php echo isset($data['season']->modified_at) && $data['season']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['season']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong> <?php echo isset($data['season']->modified_by_name) && $data['season']->modified_by_name ? htmlspecialchars($data['season']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <button class="button primary" type="submit" title="<?php echo htmlspecialchars($data['mode'] === 'edit' ? ($data['txt']['USER_BTN_UPDATE'] ?? 'Mettre à jour') : ($data['txt']['USER_BTN_SAVE'] ?? 'Enregistrer')); ?>">
                    <span class="mif-floppy-disk mr-1"></span><span class="btn-text"><?php echo ($data['mode'] === 'edit') ? ($data['txt']['USER_BTN_UPDATE'] ?? 'Mettre à jour') : ($data['txt']['USER_BTN_SAVE'] ?? 'Enregistrer'); ?></span>
                </button>
            </div>
        </form>
    <?php endif; ?>
</main>
