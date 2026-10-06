<?php
/**
 * Vue : Club (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$club           = $data['club'] ?? null;
$sections       = $data['sections'] ?? [];
$allSports      = $data['allSports'] ?? [];
$activeSportIds = $data['activeSportIds'] ?? [];
$logoUrl        = (!empty($club) && !empty($club->logo)) ? URLROOT . '/' . ltrim($club->logo, '/') : null;
$logoPath       = (!empty($club) && !empty($club->logo)) ? dirname(__DIR__, 3) . '/' . ltrim($club->logo, '/') : null;
$hasValidLogo   = ($logoUrl && $logoPath && file_exists($logoPath));

// Avatar médaillon aux couleurs du club
$clubPrimaryColor = (!empty($club->primary_color) && strtolower($club->primary_color) !== '#ffffff' && strtolower($club->primary_color) !== '#fff') 
    ? htmlspecialchars($club->primary_color) : '#0072c6';

$avatarHtml = '<div style="width: 56px; height: 56px; min-width: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ' . $clubPrimaryColor . '; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.15); border: 2px solid #fff; overflow: hidden;">';
if ($hasValidLogo) {
    $avatarHtml .= '<img src="' . htmlspecialchars($logoUrl) . '" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; background: #fff; padding: 2px;">';
} else {
    $avatarHtml .= '<span class="mif-security mif-2x"></span>';
}
$avatarHtml .= '</div>';

$formConfig = [
    'mode'           => $data['mode'] ?? 'view',
    'item'           => $club,
    'icon'           => 'mif-security',
    'maxWidth'       => '1060px',
    'enctype'        => 'multipart/form-data',
    'viewTitle'      => 'Fiche du club : ' . htmlspecialchars($club->name ?? ''),
    'editTitle'      => $data['txt']['SPORT_EDIT_CLUB_TITLE'] ?? 'Modifier le club',
    'addTitle'       => $data['txt']['SPORT_ADD_CLUB_TITLE'] ?? 'Créer un club',
    'backUrl'        => URLROOT . '/sport/clubs',
    'editUrl'        => isset($club->id) ? URLROOT . '/sport/club/edit/' . (int)$club->id : null,
    'cancelUrl'      => isset($club->id) ? URLROOT . '/sport/club/' . (int)$club->id : URLROOT . '/sport/clubs',
    'formAction'     => URLROOT . '/sport/club' . (($data['mode'] === 'edit' && isset($club->id)) ? '/' . (int)$club->id : ''),
    'viewAvatarHtml' => $avatarHtml,

    // Contenu spécifique du mode View (Consultation pure, zéro input)
    'viewContent' => function($item, $data) use ($sections, $logoUrl, $hasValidLogo) { ?>
        <div class="row">
            <!-- Colonne gauche : Carte d'identité et Ancrage -->
            <div class="cell-md-5 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <h5 class="text-bold mb-3" style="font-size: 14px; color: #475569;">
                        <span class="mif-location fg-crimson mr-1"></span> Coordonnées & Localisation
                    </h5>
                    
                    <p class="mb-2"><strong>Ville :</strong> <?php echo htmlspecialchars($item->city_name ?? ''); ?> (<?php echo htmlspecialchars($item->country_code ?? ''); ?>)</p>
                    <?php if (!empty($item->postal_code)): ?>
                        <p class="mb-2"><strong>Code postal :</strong> <?php echo htmlspecialchars($item->postal_code); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item->address)): ?>
                        <p class="mb-2"><strong>Adresse :</strong> <?php echo htmlspecialchars($item->address); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item->foundation_year)): ?>
                        <p class="mb-2"><strong>Fondation :</strong> <?php echo htmlspecialchars($item->foundation_year); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($item->website) || !empty($item->email) || !empty($item->phone)): ?>
                        <hr class="thin my-3">
                        <h6 class="text-bold mb-2" style="font-size: 13px; color: #475569;">
                            <span class="mif-contacts mr-1"></span> Contact & Web
                        </h6>
                        <?php if (!empty($item->website)): ?>
                            <p class="mb-1"><span class="mif-link mr-1"></span><a href="<?php echo htmlspecialchars($item->website); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($item->website); ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($item->email)): ?>
                            <p class="mb-1"><span class="mif-envelop mr-1"></span><a href="mailto:<?php echo htmlspecialchars($item->email); ?>"><?php echo htmlspecialchars($item->email); ?></a></p>
                        <?php endif; ?>
                        <?php if (!empty($item->phone)): ?>
                            <p class="mb-1"><span class="mif-phone mr-1"></span><?php echo htmlspecialchars($item->phone); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne droite : Présentation & Sections sportives -->
            <div class="cell-md-7 mb-3">
                <?php if (!empty($item->description)): ?>
                    <div class="p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                        <h5 class="text-bold mb-2" style="font-size: 14px; color: #475569;">
                            <span class="mif-file-text mr-1"></span> Présentation & Histoire
                        </h5>
                        <p class="mb-0" style="white-space: pre-line;"><?php echo htmlspecialchars($item->description); ?></p>
                    </div>
                <?php endif; ?>

                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="text-bold mb-0" style="font-size: 14px; color: #475569;">
                            <span class="mif-trophy mr-1"></span> Sections & Disciplines sportives
                        </h5>
                        <span class="badge secondary"><?php echo count($sections); ?> discipline(s)</span>
                    </div>

                    <?php if (!empty($sections)): ?>
                        <div class="row">
                            <?php foreach ($sections as $sec): ?>
                                <div class="cell-md-6 mb-2">
                                    <div class="p-2 border bd-default border-radius-4 d-flex flex-align-center bg-white">
                                        <span class="<?php echo !empty($sec['sport_icon']) ? htmlspecialchars($sec['sport_icon']) : 'mif-trophy'; ?> mif-2x fg-primary mr-2"></span>
                                        <div>
                                            <strong class="d-block" style="font-size: 13px;"><?php echo htmlspecialchars($sec['name']); ?></strong>
                                            <small class="fg-gray">Sport : <?php echo htmlspecialchars($sec['sport_name']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="remark info mb-0">
                            Ce club ne possède aucune section sportive enregistrée pour le moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php },

    // Contenu spécifique du mode Edit / Add (Formulaire interactif)
    'formContent' => function($item, $data, $mode) use ($allSports, $activeSportIds, $logoUrl) { ?>
        <div class="row">
            <!-- Colonne 1 : Données Générales -->
            <div class="cell-md-6">
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <h5 class="mb-3 text-bold" style="font-size: 15px;"><span class="mif-info mr-2"></span>Identité du Club</h5>
                    <hr class="thin my-2">

                    <!-- Code / Slug -->
                    <div class="form-group mb-3">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_CODE'] ?? 'Code / Slug'; ?> <span class="fg-red">*</span></label>
                        <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: psg, real-madrid, as-rillieux"
                               value="<?php echo htmlspecialchars($item->code ?? ''); ?>" <?php echo ($mode === 'edit') ? 'readonly' : ''; ?>>
                        <small class="text-muted d-block mt-1">Minuscules, chiffres et tirets uniquement.</small>
                    </div>

                    <!-- Nom officiel -->
                    <div class="form-group mb-3">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_NAME'] ?? 'Nom officiel'; ?> <span class="fg-red">*</span></label>
                        <input type="text" name="name" data-role="input" required placeholder="ex: Paris Saint-Germain"
                               value="<?php echo htmlspecialchars($item->name ?? ''); ?>">
                    </div>

                    <!-- Nom court & Sigle -->
                    <div class="row mb-3">
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_SHORT_NAME'] ?? 'Nom court'; ?></label>
                            <input type="text" name="short_name" data-role="input" placeholder="ex: Paris SG"
                                   value="<?php echo htmlspecialchars($item->short_name ?? ''); ?>">
                        </div>
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_ACRONYM'] ?? 'Sigle'; ?></label>
                            <input type="text" name="acronym" data-role="input" placeholder="ex: PSG"
                                   value="<?php echo htmlspecialchars($item->acronym ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Année de fondation -->
                    <div class="form-group mb-3">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_FOUNDATION'] ?? 'Année de fondation'; ?></label>
                        <input type="number" name="foundation_year" data-role="input" min="1800" max="<?php echo date('Y'); ?>" placeholder="ex: 1970"
                               value="<?php echo htmlspecialchars($item->foundation_year ?? ''); ?>">
                    </div>

                    <!-- Couleurs du club -->
                    <div class="row mb-3">
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_PRIMARY_COLOR'] ?? 'Couleur principale'; ?></label>
                            <input type="text" name="primary_color" data-role="input" placeholder="#001C58"
                                   value="<?php echo htmlspecialchars($item->primary_color ?? ''); ?>">
                        </div>
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_SECONDARY_COLOR'] ?? 'Couleur secondaire'; ?></label>
                            <input type="text" name="secondary_color" data-role="input" placeholder="#DA291C"
                                   value="<?php echo htmlspecialchars($item->secondary_color ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Upload de Logo (max 2 Mo) -->
                    <div class="form-group mb-3">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_LOGO'] ?? 'Logo du club'; ?> <small class="fg-gray">(PNG, JPG, WEBP, SVG - Max 2 Mo)</small></label>
                        <input type="file" name="logo_file" id="logoFileInput" data-role="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" data-button-title="<span class='mif-folder'></span> Parcourir">
                        <div id="fileSizeWarning" class="fg-red mt-1" style="display: none;">⚠️ Le fichier dépasse la limite autorisée de 2 Mo.</div>
                        <?php if ($logoUrl): ?>
                            <div class="mt-2 d-flex flex-align-center">
                                <span class="fg-muted mr-2">Logo actuel :</span>
                                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" style="height: 40px; max-width: 80px; object-fit: contain;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Statut (en edit) -->
                    <?php if ($mode === 'edit'): ?>
                    <div class="form-group mb-3">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_STATUS'] ?? 'Statut'; ?></label>
                        <select name="status_id" data-role="select">
                            <?php foreach ($data['statuses'] as $st): ?>
                                <option value="<?php echo htmlspecialchars($st['id']); ?>" <?php echo ($item && (int)$item->status_id === (int)$st['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Colonne 2 : Ancrage Géographique & Sections & Contact -->
            <div class="cell-md-6">
                <!-- Localisation -->
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <h5 class="mb-3 text-bold" style="font-size: 15px;"><span class="mif-location mr-2"></span>Ancrage Géographique</h5>
                    <hr class="thin my-2">

                    <!-- Pays & Ville -->
                    <div class="row mb-3">
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?> <span class="fg-red">*</span></label>
                            <select name="country_code" data-role="select" required>
                                <option value="">-- Choisir un pays --</option>
                                <?php foreach ($data['allCountries'] as $country): ?>
                                    <option value="<?php echo htmlspecialchars($country['country_code']); ?>" <?php echo ($item && $item->country_code === $country['country_code']) ? 'selected' : (($country['country_code'] === 'FR' && !$item) ? 'selected' : ''); ?>>
                                        <?php echo htmlspecialchars($country['name']); ?> (<?php echo htmlspecialchars($country['country_code']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="cell-sm-6">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_CITY'] ?? 'Ville'; ?> <span class="fg-red">*</span></label>
                            <input type="text" name="city_name" data-role="input" required placeholder="ex: Paris, Lyon, Madrid..."
                                   value="<?php echo htmlspecialchars($item->city_name ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Code postal & Adresse -->
                    <div class="row mb-3">
                        <div class="cell-sm-4">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_POSTAL_CODE'] ?? 'Code postal'; ?></label>
                            <input type="text" name="postal_code" data-role="input" placeholder="ex: 75016"
                                   value="<?php echo htmlspecialchars($item->postal_code ?? ''); ?>">
                        </div>
                        <div class="cell-sm-8">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_ADDRESS'] ?? 'Adresse'; ?></label>
                            <input type="text" name="address" data-role="input" placeholder="Adresse du siège ou stade"
                                   value="<?php echo htmlspecialchars($item->address ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Sections / Disciplines Sportives -->
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <h5 class="mb-2 text-bold" style="font-size: 15px;"><span class="mif-trophy mr-2"></span>Disciplines Pratiquées (Sections)</h5>
                    <p class="fg-gray"><small>Cochez les sports actifs dans ce club.</small></p>
                    <hr class="thin my-2">

                    <div class="d-flex flex-wrap mt-2" style="gap: 10px;">
                        <?php foreach ($allSports as $sp): 
                            $isChecked = in_array((int)$sp['id'], $activeSportIds);
                        ?>
                            <label class="checkbox mr-3 mb-2">
                                <input type="checkbox" name="sports[]" value="<?php echo htmlspecialchars($sp['id']); ?>" <?php echo $isChecked ? 'checked' : ''; ?> data-role="checkbox" data-caption="<span class='<?php echo htmlspecialchars($sp['icon'] ?? 'mif-trophy'); ?> mr-1'></span> <?php echo htmlspecialchars($sp['name']); ?>">
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Coordonnées & Histoire -->
                <div class="p-3 mb-3 border bd-default border-radius-4 bg-white">
                    <h5 class="mb-3 text-bold" style="font-size: 15px;"><span class="mif-contacts mr-2"></span>Coordonnées & Histoire</h5>
                    <hr class="thin my-2">

                    <div class="row mb-3">
                        <div class="cell-sm-4">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_WEBSITE'] ?? 'Site Web'; ?></label>
                            <input type="url" name="website" data-role="input" placeholder="https://..."
                                   value="<?php echo htmlspecialchars($item->website ?? ''); ?>">
                        </div>
                        <div class="cell-sm-4">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_EMAIL'] ?? 'Email'; ?></label>
                            <input type="email" name="email" data-role="input" placeholder="contact@club.com"
                                   value="<?php echo htmlspecialchars($item->email ?? ''); ?>">
                        </div>
                        <div class="cell-sm-4">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_PHONE'] ?? 'Téléphone'; ?></label>
                            <input type="text" name="phone" data-role="input" placeholder="+33..."
                                   value="<?php echo htmlspecialchars($item->phone ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-bold"><?php echo $data['txt']['CLUB_DESCRIPTION'] ?? 'Description / Histoire'; ?></label>
                        <textarea name="description" data-role="textarea" data-auto-size="true" rows="3"
                                  placeholder="Histoire du club, palmarès, valeurs..."><?php echo htmlspecialchars($item->description ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.getElementById('logoFileInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const warning = document.getElementById('fileSizeWarning');
            if (file && file.size > 2097152) { // 2 Mo
                warning.style.display = 'block';
                e.target.value = '';
            } else {
                warning.style.display = 'none';
            }
        });
        </script>
    <?php }
];

require 'module/system/view/common/form_template.php';
