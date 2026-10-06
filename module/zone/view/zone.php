<?php
/**
 * Vue : Zone géographique (Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$zone        = $data['zone'] ?? null;
$types       = $data['types'] ?? [];
$activeLangs = $data['activeLangs'] ?? [];
$parents     = $data['parents'] ?? [];
$parentId    = $data['parentId'] ?? null;

$deleteUrl = ($data['mode'] === 'edit' && isset($zone->id) && ($zone->type_code ?? '') !== 'world')
    ? URLROOT . '/zone/delete/' . (int)$zone->id
    : null;

$formConfig = [
    'mode'          => $data['mode'] ?? 'edit',
    'item'          => $zone,
    'icon'          => 'mif-earth',
    'maxWidth'      => '900px',
    'nameField'     => 'name_default',
    'showAudit'     => false,
    'editTitle'     => ($data['txt']['ZONE_TITLE_ZONE_EDIT'] ?? 'Modifier la zone') . ' : ' . ($zone->name ?? $zone->name_default ?? ''),
    'addTitle'      => $data['txt']['ZONE_TITLE_ZONE_ADD'] ?? 'Ajouter une zone',
    'backUrl'       => URLROOT . '/zone/zones',
    'cancelUrl'     => URLROOT . '/zone/zones',
    'deleteUrl'     => $deleteUrl,
    'deleteConfirm' => 'Êtes-vous sûr de vouloir supprimer cette zone ?\nCette action est irréversible.',
    'formAction'    => URLROOT . '/zone/' . ($data['mode'] === 'edit' && isset($zone->id) ? (int)$zone->id : 'add'),

    // Contenu du Formulaire Add/Edit
    'formContent' => function($item, $data, $mode) use ($types, $activeLangs, $parents, $parentId) { ?>
        <!-- 1. Informations générales -->
        <h5 class="mb-3 text-secondary text-bold" style="font-size: 15px;">
            <span class="mif-info mr-1"></span> Informations générales
        </h5>

        <div class="row mb-3">
            <div class="cell-md-6">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_NAME'] ?? 'Nom par défaut'; ?> <span class="text-danger">*</span>
                </label>
                <input type="text" name="name_default" class="metro-input" required
                       placeholder="Ex: France, Île-de-France..."
                       value="<?php echo htmlspecialchars($item->name_default ?? ''); ?>">
                <small class="text-muted d-block mt-1">Nom générique ou international de la zone</small>
            </div>

            <div class="cell-md-6">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_TYPE'] ?? 'Type de zone'; ?> <span class="text-danger">*</span>
                </label>
                <select name="type_code" class="metro-input" required data-role="select">
                    <?php foreach ($types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['code']); ?>"
                            <?php echo (isset($item->type_code) && $item->type_code === $type['code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['name']); ?> (<?php echo htmlspecialchars($type['code']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- 2. Traductions -->
        <h5 class="mt-4 mb-3 text-secondary text-bold" style="font-size: 15px;">
            <span class="mif-language mr-1"></span> Traductions multilingues
        </h5>

        <div class="row mb-3">
            <?php
            $langFlags = ['fr' => 'fi fi-fr', 'en' => 'fi fi-gb', 'es' => 'fi fi-es'];
            $langLabels = ['fr' => 'Français', 'en' => 'English', 'es' => 'Español'];
            foreach ($activeLangs as $lang):
                $val = $data['i18n'][$lang] ?? ($mode === 'edit' && $lang === 'fr' ? ($item->name ?? '') : '');
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

        <!-- 3. Hiérarchie & Identifiants -->
        <h5 class="mt-4 mb-3 text-secondary text-bold" style="font-size: 15px;">
            <span class="mif-flow-tree mr-1"></span> Hiérarchie & Identifiants
        </h5>

        <div class="row mb-3">
            <div class="cell-md-6">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_PARENT'] ?? 'Zone parente'; ?>
                </label>
                <select name="parent_id" class="metro-input" data-role="select" data-filter="true">
                    <option value="">— Aucune (Racine Monde) —</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?php echo (int) $parent['id']; ?>"
                            <?php echo ((int) $parentId === (int) $parent['id']) ? 'selected' : ''; ?>>
                            [<?php echo htmlspecialchars($parent['type_code']); ?>] <?php echo htmlspecialchars($parent['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted d-block mt-1">Continent pour un pays, pays pour une région, etc.</small>
            </div>

            <div class="cell-md-3">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_COUNTRY_CODE'] ?? 'Code pays ISO-2'; ?>
                </label>
                <input type="text" name="country_code" class="metro-input" maxlength="2"
                       style="text-transform: uppercase;"
                       placeholder="FR, US, ES..."
                       value="<?php echo htmlspecialchars($item->country_code ?? ''); ?>">
                <small class="text-muted d-block mt-1">Ex: FR, US, DE (optionnel)</small>
            </div>

            <div class="cell-md-3">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_GEONAMES_ID'] ?? 'ID GeoNames'; ?>
                </label>
                <input type="number" name="geonames_id" class="metro-input"
                       placeholder="Ex: 3017382"
                       value="<?php echo htmlspecialchars($item->geonames_id ?? ''); ?>">
                <?php if (!empty($item->geonames_id)): ?>
                    <small class="text-muted d-block mt-1">
                        <a href="https://www.geonames.org/<?php echo (int) $item->geonames_id; ?>" target="_blank" rel="noopener">
                            Voir sur geonames.org <span class="mif-external"></span>
                        </a>
                    </small>
                <?php else: ?>
                    <small class="text-muted d-block mt-1">Identifiant API (optionnel)</small>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4. Statut -->
        <div class="row mb-3">
            <div class="cell-md-6">
                <label class="form-label font-weight-bold">
                    <?php echo $data['txt']['ZONE_LABEL_STATUS'] ?? 'Statut'; ?>
                </label>
                <select name="status_id" class="metro-input" data-role="select">
                    <option value="1" <?php echo (!isset($item->status_id) || (int)$item->status_id === 1) ? 'selected' : ''; ?>>
                        Actif
                    </option>
                    <option value="0" <?php echo (isset($item->status_id) && (int)$item->status_id === 0) ? 'selected' : ''; ?>>
                        Inactif
                    </option>
                </select>
            </div>
        </div>
    <?php }
];

require 'module/system/view/common/form_template.php';
