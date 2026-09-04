<!-- Contenu principal — Formulaire d'ajout / modification d'une zone -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2>
            <span class="mif-earth mr-2"></span>
            <?php if ($data['mode'] === 'add'): ?>
                <?php echo $data['txt']['ZONE_TITLE_ZONE_ADD'] ?? 'Ajouter une zone'; ?>
            <?php else: ?>
                <?php echo $data['txt']['ZONE_TITLE_ZONE_EDIT'] ?? 'Modifier la zone'; ?> :
                <strong><?php echo htmlspecialchars($data['zone']->name ?? $data['zone']->name_default ?? ''); ?></strong>
            <?php endif; ?>
        </h2>
        <a href="<?php echo URLROOT; ?>/zone/zones" class="button secondary mt-2 mt-md-0">
            <span class="mif-arrow-left mr-1"></span>
            <?php echo $data['txt']['SYS_BTN_BACK'] ?? 'Retour au treeview'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <div class="card" style="max-width: 860px;">
        <div class="card-content p-4">
            <form method="POST" action="<?php echo URLROOT; ?>/zone/<?php echo $data['mode'] === 'edit' ? (int) $data['zone']->id : 'add'; ?>">

                <!-- 1. Informations générales -->
                <h5 class="mb-3 text-secondary">
                    <span class="mif-info mr-1"></span> Informations générales
                </h5>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_NAME'] ?? 'Nom par défaut'; ?> <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_default" class="metro-input" required
                               placeholder="Ex: France, Île-de-France..."
                               value="<?php echo htmlspecialchars($data['zone']->name_default ?? ''); ?>">
                        <small class="text-muted">Nom générique ou international de la zone</small>
                    </div>

                    <div class="cell-md-6">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_TYPE'] ?? 'Type de zone'; ?> <span class="text-danger">*</span>
                        </label>
                        <select name="type_code" class="metro-input" required data-role="select">
                            <?php foreach ($data['types'] as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['code']); ?>"
                                    <?php echo (isset($data['zone']->type_code) && $data['zone']->type_code === $type['code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name']); ?> (<?php echo htmlspecialchars($type['code']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- 2. Traductions -->
                <h5 class="mt-4 mb-3 text-secondary">
                    <span class="mif-language mr-1"></span> Traductions multilingues
                </h5>

                <div class="row mb-3">
                    <?php
                    $langFlags = ['fr' => 'fi fi-fr', 'en' => 'fi fi-gb', 'es' => 'fi fi-es'];
                    $langLabels = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];
                    foreach ($data['activeLangs'] as $lang):
                        $val = $data['i18n'][$lang] ?? ($data['mode'] === 'edit' && $lang === 'fr' ? ($data['zone']->name ?? '') : '');
                    ?>
                        <div class="cell-md-4 mb-2">
                            <label class="form-label font-weight-bold">
                                <span class="<?php echo $langFlags[$lang] ?? 'mif-flag'; ?> mr-1"></span>
                                <?php echo $langLabels[$lang] ?? strtoupper($lang); ?>
                            </label>
                            <input type="text" name="name[<?php echo $lang; ?>]" class="metro-input"
                                   placeholder="Nom en <?php echo $langLabels[$lang] ?? $lang; ?>"
                                   value="<?php echo htmlspecialchars($val); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 3. Hiérarchie & Localisation -->
                <h5 class="mt-4 mb-3 text-secondary">
                    <span class="mif-flow-tree mr-1"></span> Hiérarchie & Identifiants
                </h5>

                <div class="row mb-3">
                    <div class="cell-md-6">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_PARENT'] ?? 'Zone parente'; ?>
                        </label>
                        <select name="parent_id" class="metro-input" data-role="select" data-filter="true">
                            <option value="">— Aucune (Racine Monde) —</option>
                            <?php foreach ($data['parents'] as $parent): ?>
                                <option value="<?php echo (int) $parent['id']; ?>"
                                    <?php echo ((int) $data['parentId'] === (int) $parent['id']) ? 'selected' : ''; ?>>
                                    [<?php echo htmlspecialchars($parent['type_code']); ?>] <?php echo htmlspecialchars($parent['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Continent pour un pays, pays pour une région, etc.</small>
                    </div>

                    <div class="cell-md-3">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_COUNTRY_CODE'] ?? 'Code pays ISO-2'; ?>
                        </label>
                        <input type="text" name="country_code" class="metro-input" maxlength="2"
                               style="text-transform: uppercase;"
                               placeholder="FR, US, ES..."
                               value="<?php echo htmlspecialchars($data['zone']->country_code ?? ''); ?>">
                        <small class="text-muted">Ex: FR, US, DE (optionnel)</small>
                    </div>

                    <div class="cell-md-3">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_GEONAMES_ID'] ?? 'ID GeoNames'; ?>
                        </label>
                        <input type="number" name="geonames_id" class="metro-input"
                               placeholder="Ex: 3017382"
                               value="<?php echo htmlspecialchars($data['zone']->geonames_id ?? ''); ?>">
                        <?php if (!empty($data['zone']->geonames_id)): ?>
                            <small class="text-muted">
                                <a href="https://www.geonames.org/<?php echo (int) $data['zone']->geonames_id; ?>" target="_blank" rel="noopener">
                                    Voir sur geonames.org <span class="mif-external"></span>
                                </a>
                            </small>
                        <?php else: ?>
                            <small class="text-muted">Identifiant API (optionnel)</small>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 4. Statut -->
                <div class="row mb-4">
                    <div class="cell-md-6">
                        <label class="form-label font-weight-bold">
                            <?php echo $data['txt']['ZONE_LABEL_STATUS'] ?? 'Statut'; ?>
                        </label>
                        <select name="status_id" class="metro-input" data-role="select">
                            <option value="1" <?php echo (!isset($data['zone']->status_id) || (int)$data['zone']->status_id === 1) ? 'selected' : ''; ?>>
                                Actif
                            </option>
                            <option value="0" <?php echo (isset($data['zone']->status_id) && (int)$data['zone']->status_id === 0) ? 'selected' : ''; ?>>
                                Inactif
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex flex-align-center mt-4 pt-3 border-top">
                    <button type="submit" class="button success mr-2">
                        <span class="mif-floppy-disk mr-1"></span>
                        <?php echo $data['mode'] === 'add' ? 'Créer la zone' : 'Enregistrer les modifications'; ?>
                    </button>

                    <a href="<?php echo URLROOT; ?>/zone/zones" class="button secondary mr-2">
                        Annuler
                    </a>

                    <?php if ($data['mode'] === 'edit' && ($data['zone']->type_code ?? '') !== 'world'): ?>
                        <a href="<?php echo URLROOT; ?>/zone/delete/<?php echo (int) $data['zone']->id; ?>"
                           class="button alert ml-auto"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?\nCette action est irréversible.');">
                            <span class="mif-bin mr-1"></span> Supprimer cette zone
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>
    </div>
</main>
