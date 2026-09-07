<!-- Contenu principal Liste des Clubs -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-shield mr-2"></span><?php echo $data['txt']['SPORT_CLUBS_MGT'] ?? 'Clubs'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/club" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['SPORT_ADD_CLUB_BTN'] ?? 'Nouveau club'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['SPORT_ADD_CLUB_BTN'] ?? 'Nouveau club'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Barre de filtres -->
    <div class="card p-3 mb-4 bg-light">
        <form method="GET" action="<?php echo URLROOT; ?>/sport/clubs" class="d-flex flex-wrap flex-align-end" style="gap: 15px;">
            <div style="flex: 1; min-width: 220px;">
                <label class="text-bold"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
                <input type="text" name="search" data-role="input" placeholder="Nom, ville, sigle..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>

            <div style="min-width: 180px;">
                <label class="text-bold"><span class="mif-earth mr-1"></span><?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?></label>
                <select name="country" data-role="select">
                    <option value="">-- Tous les pays --</option>
                    <?php foreach ($data['countries'] as $c): ?>
                        <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo ($data['selectedCountry'] === $c['country_code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['country_code']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="min-width: 180px;">
                <label class="text-bold"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['SECTION_LBL_SPORT'] ?? 'Discipline'; ?></label>
                <select name="sport" data-role="select">
                    <option value="">-- Toutes disciplines --</option>
                    <?php foreach ($data['sports'] as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['id']); ?>" <?php echo ($data['selectedSport'] == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="button primary"><span class="mif-filter"></span> Filtrer</button>
                <a href="<?php echo URLROOT; ?>/sport/clubs" class="button secondary">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau des Clubs -->
    <table class="table striped table-border mt-3 w-100 table-responsive-cards" data-role="table"
        data-show-search="false" data-show-rows-steps="false" data-check="false" data-rownum="false">
        <thead>
            <tr>
                <th style="width: 60px;"><?php echo $data['txt']['CLUB_LOGO'] ?? 'Logo'; ?></th>
                <th><?php echo $data['txt']['CLUB_NAME'] ?? 'Nom du club'; ?></th>
                <th><?php echo $data['txt']['CLUB_CITY'] ?? 'Ville'; ?> / <?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?></th>
                <th><?php echo $data['txt']['CLUB_SECTIONS'] ?? 'Sections / Sports'; ?></th>
                <th><?php echo $data['txt']['CLUB_FOUNDATION'] ?? 'Fondation'; ?></th>
                <th><?php echo $data['txt']['CLUB_STATUS'] ?? 'Statut'; ?></th>
                <th style="width: 130px;"><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['clubs'])): ?>
                <?php foreach ($data['clubs'] as $club): 
                    $statusId = (int)$club['status_id'];
                    $statusText = $data['txt'][$club['status_text_code']] ?? ($statusId === 1 ? 'Actif' : 'Inactif');
                    $badgeClass = ($statusId === 1) ? 'success' : (($statusId === 2) ? 'warning' : 'secondary');
                    $logoUrl = !empty($club['logo']) ? URLROOT . '/' . ltrim($club['logo'], '/') : null;
                ?>
                    <tr>
                        <!-- Logo -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_LOGO'] ?? 'Logo'); ?>" class="text-center">
                            <?php if ($logoUrl && file_exists(dirname(__DIR__, 3) . '/' . ltrim($club['logo'], '/'))): ?>
                                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" style="max-height: 40px; max-width: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div style="display: inline-block; width: 36px; height: 36px; line-height: 36px; border-radius: 50%; background: <?php echo !empty($club['primary_color']) ? htmlspecialchars($club['primary_color']) : '#0072c6'; ?>; color: #fff; text-align: center; font-weight: bold; font-size: 11px;">
                                    <?php echo htmlspecialchars(!empty($club['acronym']) ? $club['acronym'] : strtoupper(substr($club['name'], 0, 3))); ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Nom & Sigle -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_NAME'] ?? 'Nom du club'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($club['id']); ?>" 
                               class="text-bold <?php echo !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark'; ?>" 
                               title="<?php echo !empty($data['isAdmin']) ? 'Modifier le club' : 'Consulter la fiche du club'; ?>">
                                <?php echo htmlspecialchars($club['name']); ?>
                            </a>
                            <?php if (!empty($club['acronym'])): ?>
                                <span class="badge info ml-1"><?php echo htmlspecialchars($club['acronym']); ?></span>
                            <?php endif; ?>
                            <br>
                            <small class="fg-gray"><code><?php echo htmlspecialchars($club['code']); ?></code></small>
                        </td>

                        <!-- Localisation -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_CITY'] ?? 'Ville'); ?>">
                            <span class="mif-location fg-crimson mr-1"></span>
                            <strong><?php echo htmlspecialchars($club['city_name']); ?></strong> 
                            <span class="fg-gray">(<?php echo htmlspecialchars($club['country_code']); ?>)</span>
                        </td>

                        <!-- Sections / Disciplines -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_SECTIONS'] ?? 'Sections'); ?>">
                            <?php if (!empty($club['sports_names'])): ?>
                                <?php 
                                    $sportsList = explode(', ', $club['sports_names']);
                                    foreach ($sportsList as $sName): 
                                ?>
                                    <span class="badge secondary mr-1 mb-1"><span class="mif-trophy mr-1"></span><?php echo htmlspecialchars($sName); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="fg-muted"><em>Aucune section</em></span>
                            <?php endif; ?>
                        </td>

                        <!-- Fondation -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_FOUNDATION'] ?? 'Fondation'); ?>">
                            <?php echo !empty($club['foundation_year']) ? htmlspecialchars($club['foundation_year']) : '<span class="fg-muted">-</span>'; ?>
                        </td>

                        <!-- Statut -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_STATUS'] ?? 'Statut'); ?>">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                        </td>

                        <!-- Actions -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($club['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                                        <span class="mif-pencil"></span>
                                    </a>

                                    <!-- Désactiver (Admin) -->
                                    <?php if ($statusId === 1): ?>
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['CLUB_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver ce club ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/club/delete/<?php echo htmlspecialchars($club['id']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>">
                                        <span class="mif-cancel"></span>
                                    </button>
                                    <?php endif; ?>

                                    <!-- Supprimer (Admin) -->
                                    <button onclick="if(confirm('Voulez-vous supprimer définitivement ce club ?')) location.href='<?php echo URLROOT; ?>/sport/club/delete/<?php echo htmlspecialchars($club['id']); ?>?force=1';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>">
                                        <span class="mif-bin"></span>
                                    </button>
                                <?php else: ?>
                                    <!-- Consulter (Visiteur) -->
                                    <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($club['id']); ?>" class="button small primary" title="Consulter la fiche">
                                        <span class="mif-eye"></span> Consulter
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center p-4 fg-muted">
                        <em>Aucun club trouvé correspondant à votre recherche.</em>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
