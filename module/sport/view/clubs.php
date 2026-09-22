<!-- Contenu principal Liste des Clubs -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-security mr-2"></span><?php echo $data['txt']['SPORT_CLUBS_MGT'] ?? 'Clubs'; ?></h2>
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
    <div class="p-3 mb-4 bg-light border bd-default border-radius-4">
        <form method="GET" action="<?php echo URLROOT; ?>/sport/clubs" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 12px;">
            <div style="flex: 2; min-width: 180px;">
                <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
                <input type="text" name="search" data-role="input" placeholder="Nom, ville, sigle..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>

            <div style="flex: 1.2; min-width: 160px;">
                <label class="text-bold d-block"><span class="mif-earth mr-1"></span><?php echo $data['txt']['CLUB_COUNTRY'] ?? 'Pays'; ?></label>
                <select name="country" data-role="select">
                    <option value="">-- Tous les pays --</option>
                    <?php foreach ($data['countries'] as $c): ?>
                        <option value="<?php echo htmlspecialchars($c['country_code']); ?>" <?php echo ($data['selectedCountry'] === $c['country_code']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['country_code']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1.2; min-width: 160px;">
                <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['CLUB_FILTER_SPORT'] ?? $data['txt']['SECTION_LBL_SPORT'] ?? 'Sport'; ?></label>
                <select name="sport" data-role="select">
                    <option value="">-- Tous les sports --</option>
                    <?php foreach ($data['sports'] as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['id']); ?>" <?php echo ($data['selectedSport'] == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 0 0 auto; padding-bottom: 2px;">
                <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> Filtrer</button>
                <a href="<?php echo URLROOT; ?>/sport/clubs" class="button secondary">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau des Clubs -->
    <div class="table-scroll-wrapper"><table class="table striped table-border mt-3 w-100 " data-role="table"
        data-show-search="false" data-show-rows-steps="false" data-check="false" data-rownum="false">
        <thead>
            <tr>
                <th style="width: 60px;"><?php echo $data['txt']['CLUB_TABLE_LOGO'] ?? $data['txt']['CLUB_LOGO'] ?? 'Logo'; ?></th>
                <th><?php echo $data['txt']['CLUB_TABLE_NAME'] ?? 'Nom'; ?></th>
                <th><?php echo $data['txt']['CLUB_TABLE_LOCATION'] ?? 'Localisation'; ?></th>
                <th><?php echo $data['txt']['CLUB_SECTIONS'] ?? 'Sport'; ?></th>
                <?php if (!empty($data['isAdmin'])): ?>
                    <th><?php echo $data['txt']['CLUB_STATUS'] ?? 'Statut'; ?></th>
                    <th style="width: 130px;"><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
                <?php endif; ?>
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
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_TABLE_LOGO'] ?? $data['txt']['CLUB_LOGO'] ?? 'Logo'); ?>" class="text-center">
                            <?php if ($logoUrl && file_exists(dirname(__DIR__, 3) . '/' . ltrim($club['logo'], '/'))): ?>
                                <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo" style="max-height: 40px; max-width: 40px; object-fit: contain;">
                            <?php else: ?>
                                <?php 
                                    $bgColor = (!empty($club['primary_color']) && strtolower($club['primary_color']) !== '#ffffff' && strtolower($club['primary_color']) !== '#fff') ? htmlspecialchars($club['primary_color']) : '#0072c6'; 
                                ?>
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: <?php echo $bgColor; ?>; color: #fff;">
                                    <span class="mif-security" style="font-size: 18px;"></span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Nom & Sigle -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_TABLE_NAME'] ?? 'Nom'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($club['id']); ?>" 
                               class="text-bold fg-primary" 
                               title="<?php echo htmlspecialchars($data['txt']['SPORT_VIEW_CLUB_TITLE'] ?? 'Fiche du club'); ?>">
                                <?php echo htmlspecialchars($club['name']); ?>
                            </a>
                            <?php if (!empty($club['acronym'])): ?>
                                <span class="badge info ml-1"><?php echo htmlspecialchars($club['acronym']); ?></span>
                            <?php endif; ?>
                            <br>
                            <small class="fg-gray"><code><?php echo htmlspecialchars($club['code']); ?></code></small>
                        </td>

                        <!-- Localisation -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_TABLE_LOCATION'] ?? 'Localisation'); ?>">
                            <span class="mif-location fg-crimson mr-1"></span>
                            <strong><?php echo htmlspecialchars($club['city_name']); ?></strong> 
                            <span class="fg-gray">(<?php echo htmlspecialchars($club['country_code']); ?>)</span>
                        </td>

                        <!-- Sport(s) du club -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_SECTIONS'] ?? 'Sport'); ?>">
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

                        <!-- Statut (Admin uniquement) -->
                        <?php if (!empty($data['isAdmin'])): ?>
                            <td data-label="<?php echo htmlspecialchars($data['txt']['CLUB_STATUS'] ?? 'Statut'); ?>">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                            </td>
                        <?php endif; ?>

                        <!-- Actions (Admin uniquement) -->
                        <?php if (!empty($data['isAdmin'])): ?>
                            <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                                <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                    <!-- Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/club/edit/<?php echo htmlspecialchars($club['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
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
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo !empty($data['isAdmin']) ? '6' : '4'; ?>" class="text-center p-4 fg-muted">
                        <em>Aucun club trouvé correspondant à votre recherche.</em>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->
</main>


