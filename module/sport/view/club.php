<!-- Contenu Fiche / Formulaire Club -->
<main class="p-4" style="margin-top: 60px;">
    <?php
    $isViewMode = ($data['mode'] === 'view');
    $club       = $data['club'] ?? null;
    $sections   = $data['sections'] ?? [];
    $allSports  = $data['allSports'] ?? [];
    $activeSportIds = $data['activeSportIds'] ?? [];
    $logoUrl    = (!empty($club) && !empty($club->logo)) ? URLROOT . '/' . ltrim($club->logo, '/') : null;
    ?>

    <!-- Fil d'ariane & Bouton retour -->
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <div>
            <a href="<?php echo URLROOT; ?>/sport/clubs" class="button light mr-2">
                <span class="mif-arrow-left"></span> <?php echo $data['txt']['SPORT_CLUBS_MGT'] ?? 'Clubs'; ?>
            </a>
            <span class="text-leader ml-2">
                <?php if ($isViewMode): ?>
                    <span class="mif-shield mr-1"></span> <?php echo htmlspecialchars($club->name ?? ''); ?>
                <?php else: ?>
                    <span class="mif-pencil mr-1"></span> <?php echo $club ? ($data['txt']['SPORT_EDIT_CLUB_TITLE'] ?? 'Modifier le club') : ($data['txt']['SPORT_ADD_CLUB_TITLE'] ?? 'Créer un club'); ?>
                <?php endif; ?>
            </span>
        </div>

        <?php if ($isViewMode && !empty($data['isAdmin'])): ?>
            <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($club->id); ?>" class="button info">
                <span class="mif-pencil"></span> <?php echo $data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'; ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success mb-3"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert mb-3"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($isViewMode && $club): ?>
        <!-- ============================================================ -->
        <!-- MODE CONSULTATION (VIEW) : PUR HTML, SANS BALISES INPUT/SELECT -->
        <!-- ============================================================ -->
        <div class="row">
            <!-- Colonne gauche : Carte d'identité du Club -->
            <div class="cell-md-4 mb-4">
                <div class="card">
                    <!-- Bannière aux couleurs du club -->
                    <div style="height: 100px; background: linear-gradient(135deg, <?php echo !empty($club->primary_color) ? htmlspecialchars($club->primary_color) : '#001C58'; ?>, <?php echo !empty($club->secondary_color) ? htmlspecialchars($club->secondary_color) : '#DA291C'; ?>); border-radius: 4px 4px 0 0; position: relative;">
                    </div>

                    <!-- Logo en médaillon -->
                    <div class="text-center" style="margin-top: -50px;">
                        <?php if ($logoUrl && file_exists(dirname(__DIR__, 3) . '/' . ltrim($club->logo, '/'))): ?>
                            <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" class="shadow-2" style="width: 100px; height: 100px; border-radius: 50%; background: #fff; padding: 5px; object-fit: contain; border: 3px solid #fff;">
                        <?php else: ?>
                            <div class="shadow-2" style="display: inline-block; width: 100px; height: 100px; line-height: 94px; border-radius: 50%; background: <?php echo !empty($club->primary_color) ? htmlspecialchars($club->primary_color) : '#0072c6'; ?>; color: #fff; font-size: 28px; font-weight: bold; border: 3px solid #fff;">
                                <?php echo htmlspecialchars(!empty($club->acronym) ? $club->acronym : strtoupper(substr($club->name, 0, 3))); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card-content p-3 text-center">
                        <h4 class="mb-1"><?php echo htmlspecialchars($club->name); ?></h4>
                        <?php if (!empty($club->short_name)): ?>
                            <div class="fg-gray"><?php echo htmlspecialchars($club->short_name); ?></div>
                        <?php endif; ?>

                        <div class="mt-2">
                            <?php if (!empty($club->acronym)): ?>
                                <span class="badge info mr-1"><?php echo htmlspecialchars($club->acronym); ?></span>
                            <?php endif; ?>
                            <code><?php echo htmlspecialchars($club->code); ?></code>
                        </div>

                        <hr class="thin my-3">

                        <!-- Ancrage & Détails -->
                        <div class="text-left">
                            <p class="mb-1"><span class="mif-location fg-crimson mr-2"></span><strong>Ville :</strong> <?php echo htmlspecialchars($club->city_name); ?> (<?php echo htmlspecialchars($club->country_code); ?>)</p>
                            <?php if (!empty($club->postal_code)): ?>
                                <p class="mb-1"><span class="mif-mail mr-2"></span><strong>Code postal :</strong> <?php echo htmlspecialchars($club->postal_code); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($club->address)): ?>
                                <p class="mb-1"><span class="mif-map mr-2"></span><strong>Adresse :</strong> <?php echo htmlspecialchars($club->address); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($club->foundation_year)): ?>
                                <p class="mb-1"><span class="mif-calendar mr-2"></span><strong>Fondation :</strong> <?php echo htmlspecialchars($club->foundation_year); ?></p>
                            <?php endif; ?>
                            <p class="mb-1">
                                <span class="mif-info mr-2"></span><strong>Statut :</strong>
                                <?php 
                                    $sText = $data['txt'][$club->status_text_code] ?? 'Actif';
                                    $bCls = ((int)$club->status_id === 1) ? 'success' : 'secondary';
                                ?>
                                <span class="badge <?php echo $bCls; ?>"><?php echo htmlspecialchars($sText); ?></span>
                            </p>
                        </div>

                        <?php if (!empty($club->website) || !empty($club->email) || !empty($club->phone)): ?>
                            <hr class="thin my-3">
                            <div class="text-left">
                                <h6 class="mb-2"><span class="mif-contacts mr-1"></span>Contact & Web</h6>
                                <?php if (!empty($club->website)): ?>
                                    <p class="mb-1"><span class="mif-link mr-2"></span><a href="<?php echo htmlspecialchars($club->website); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($club->website); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($club->email)): ?>
                                    <p class="mb-1"><span class="mif-envelop mr-2"></span><a href="mailto:<?php echo htmlspecialchars($club->email); ?>"><?php echo htmlspecialchars($club->email); ?></a></p>
                                <?php endif; ?>
                                <?php if (!empty($club->phone)): ?>
                                    <p class="mb-1"><span class="mif-phone mr-2"></span><?php echo htmlspecialchars($club->phone); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Sections / Disciplines sportives et Histoire -->
            <div class="cell-md-8">
                <!-- Description / Histoire -->
                <?php if (!empty($club->description)): ?>
                    <div class="card mb-4 p-4">
                        <h5><span class="mif-file-text mr-2"></span>Présentation & Histoire</h5>
                        <p class="mt-2" style="white-space: pre-line;"><?php echo htmlspecialchars($club->description); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Sections sportives du club -->
                <div class="card p-4">
                    <div class="d-flex flex-justify-between flex-align-center mb-3">
                        <h5 class="mb-0"><span class="mif-trophy mr-2"></span>Sections & Disciplines sportives</h5>
                        <span class="badge secondary"><?php echo count($sections); ?> discipline(s)</span>
                    </div>

                    <?php if (!empty($sections)): ?>
                        <div class="row">
                            <?php foreach ($sections as $sec): ?>
                                <div class="cell-md-6 mb-3">
                                    <div class="p-3 border bd-default border-radius-4 d-flex flex-align-center" style="background: #fafafa;">
                                        <span class="<?php echo !empty($sec['sport_icon']) ? htmlspecialchars($sec['sport_icon']) : 'mif-trophy'; ?> mif-3x fg-primary mr-3"></span>
                                        <div>
                                            <strong class="d-block text-leader"><?php echo htmlspecialchars($sec['name']); ?></strong>
                                            <small class="fg-gray">Sport : <?php echo htmlspecialchars($sec['sport_name']); ?></small>
                                            <?php if (!empty($sec['creation_year'])): ?>
                                                <br><small class="fg-gray">Depuis <?php echo htmlspecialchars($sec['creation_year']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="remark info">
                            Ce club ne possède aucune section sportive enregistrée pour le moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- ============================================================ -->
        <!-- MODE FORMULAIRE / ÉDITION (ADMIN)                           -->
        <!-- ============================================================ -->
        <form method="POST" action="<?php echo URLROOT; ?>/sport/club<?php echo $club ? '/' . $club->id : ''; ?>" enctype="multipart/form-data" id="clubForm">
            <div class="row">
                <!-- Colonne 1 : Données Générales -->
                <div class="cell-md-6">
                    <div class="card p-4 mb-4">
                        <h5><span class="mif-info mr-2"></span>Identité du Club</h5>
                        <hr class="thin my-3">

                        <!-- Code / Slug -->
                        <div class="form-group mb-3">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_CODE'] ?? 'Code / Slug'; ?> <span class="fg-red">*</span></label>
                            <input type="text" name="code" data-role="input" required pattern="[a-z0-9_-]+" placeholder="ex: psg, real-madrid, as-rillieux" value="<?php echo htmlspecialchars($club->code ?? ''); ?>" <?php echo $club ? 'readonly' : ''; ?>>
                            <small class="text-muted">Minuscules, chiffres et tirets uniquement (utilisé pour les URLs et sous-domaines).</small>
                        </div>

                        <!-- Nom officiel -->
                        <div class="form-group mb-3">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_NAME'] ?? 'Nom officiel'; ?> <span class="fg-red">*</span></label>
                            <input type="text" name="name" data-role="input" required placeholder="ex: Paris Saint-Germain" value="<?php echo htmlspecialchars($club->name ?? ''); ?>">
                        </div>

                        <!-- Nom court & Sigle -->
                        <div class="row mb-3">
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_SHORT_NAME'] ?? 'Nom court'; ?></label>
                                <input type="text" name="short_name" data-role="input" placeholder="ex: Paris SG" value="<?php echo htmlspecialchars($club->short_name ?? ''); ?>">
                            </div>
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_ACRONYM'] ?? 'Sigle'; ?></label>
                                <input type="text" name="acronym" data-role="input" placeholder="ex: PSG" value="<?php echo htmlspecialchars($club->acronym ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Année de fondation -->
                        <div class="form-group mb-3">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_FOUNDATION'] ?? 'Année de fondation'; ?></label>
                            <input type="number" name="foundation_year" data-role="input" min="1800" max="<?php echo date('Y'); ?>" placeholder="ex: 1970" value="<?php echo htmlspecialchars($club->foundation_year ?? ''); ?>">
                        </div>

                        <!-- Couleurs du club -->
                        <div class="row mb-3">
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_PRIMARY_COLOR'] ?? 'Couleur principale'; ?></label>
                                <input type="text" name="primary_color" data-role="input" placeholder="#001C58" value="<?php echo htmlspecialchars($club->primary_color ?? ''); ?>">
                            </div>
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_SECONDARY_COLOR'] ?? 'Couleur secondaire'; ?></label>
                                <input type="text" name="secondary_color" data-role="input" placeholder="#DA291C" value="<?php echo htmlspecialchars($club->secondary_color ?? ''); ?>">
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

                        <!-- Statut -->
                        <div class="form-group mb-3">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_STATUS'] ?? 'Statut'; ?></label>
                            <select name="status_id" data-role="select">
                                <?php foreach ($data['statuses'] as $st): ?>
                                    <option value="<?php echo htmlspecialchars($st['id']); ?>" <?php echo ($club && $club->status_id == $st['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($data['txt'][$st['text_code']] ?? $st['text_code']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Colonne 2 : Ancrage Géographique & Sections & Contact -->
                <div class="cell-md-6">
                    <!-- Localisation -->
                    <div class="card p-4 mb-4">
                        <h5><span class="mif-location mr-2"></span>Ancrage Géographique</h5>
                        <hr class="thin my-3">

                        <!-- Pays & Ville -->
                        <div class="row mb-3">
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?> <span class="fg-red">*</span></label>
                                <select name="country_code" data-role="select" required>
                                    <option value="">-- Choisir un pays --</option>
                                    <?php foreach ($data['allCountries'] as $country): ?>
                                        <option value="<?php echo htmlspecialchars($country['country_code']); ?>" <?php echo ($club && $club->country_code === $country['country_code']) ? 'selected' : (($country['country_code'] === 'FR' && !$club) ? 'selected' : ''); ?>>
                                            <?php echo htmlspecialchars($country['name']); ?> (<?php echo htmlspecialchars($country['country_code']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="cell-sm-6">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_CITY'] ?? 'Ville'; ?> <span class="fg-red">*</span></label>
                                <input type="text" name="city_name" data-role="input" required placeholder="ex: Paris, Lyon, Madrid..." value="<?php echo htmlspecialchars($club->city_name ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Code postal & Adresse -->
                        <div class="row mb-3">
                            <div class="cell-sm-4">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_POSTAL_CODE'] ?? 'Code postal'; ?></label>
                                <input type="text" name="postal_code" data-role="input" placeholder="ex: 75016" value="<?php echo htmlspecialchars($club->postal_code ?? ''); ?>">
                            </div>
                            <div class="cell-sm-8">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_ADDRESS'] ?? 'Adresse'; ?></label>
                                <input type="text" name="address" data-role="input" placeholder="Adresse du siège ou stade" value="<?php echo htmlspecialchars($club->address ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Sections / Disciplines Sportives -->
                    <div class="card p-4 mb-4">
                        <h5><span class="mif-trophy mr-2"></span>Disciplines Pratiquées (Sections)</h5>
                        <p class="fg-gray"><small>Cochez les sports actifs dans ce club. Une section sera automatiquement créée pour chaque sport coché.</small></p>
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
                    <div class="card p-4 mb-4">
                        <h5><span class="mif-contacts mr-2"></span>Coordonnées & Histoire</h5>
                        <hr class="thin my-3">

                        <div class="row mb-3">
                            <div class="cell-sm-4">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_WEBSITE'] ?? 'Site Web'; ?></label>
                                <input type="url" name="website" data-role="input" placeholder="https://..." value="<?php echo htmlspecialchars($club->website ?? ''); ?>">
                            </div>
                            <div class="cell-sm-4">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_EMAIL'] ?? 'Email'; ?></label>
                                <input type="email" name="email" data-role="input" placeholder="contact@club.com" value="<?php echo htmlspecialchars($club->email ?? ''); ?>">
                            </div>
                            <div class="cell-sm-4">
                                <label class="text-bold"><?php echo $data['txt']['CLUB_PHONE'] ?? 'Téléphone'; ?></label>
                                <input type="text" name="phone" data-role="input" placeholder="+33..." value="<?php echo htmlspecialchars($club->phone ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-bold"><?php echo $data['txt']['CLUB_DESCRIPTION'] ?? 'Description / Histoire'; ?></label>
                            <textarea name="description" data-role="textarea" data-auto-size="true" rows="3" placeholder="Histoire du club, palmarès, valeurs..."><?php echo htmlspecialchars($club->description ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="mt-4 d-flex flex-align-center" style="gap: 10px;">
                <button type="submit" class="button success large">
                    <span class="mif-floppy-disk mr-1"></span> <?php echo $data['txt']['SYS_BTN_SAVE'] ?? 'Enregistrer'; ?>
                </button>
                <a href="<?php echo URLROOT; ?>/sport/clubs" class="button secondary large">
                    <?php echo $data['txt']['SYS_BTN_CANCEL'] ?? 'Annuler'; ?>
                </a>
            </div>
        </form>

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
    <?php endif; ?>
</main>
