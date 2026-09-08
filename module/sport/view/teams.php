<!-- Contenu principal Liste des Équipes -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-groups mr-2"></span><?php echo $data['txt']['SPORT_TEAMS_MGT'] ?? 'Équipes'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/team" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['SPORT_ADD_TEAM_BTN'] ?? 'Nouvelle équipe'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['SPORT_ADD_TEAM_BTN'] ?? 'Nouvelle équipe'; ?>
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
        <form method="GET" action="<?php echo URLROOT; ?>/sport/teams" class="d-flex flex-row flex-wrap flex-align-end" style="gap: 10px;">
            <div style="flex: 1.5; min-width: 160px;">
                <label class="text-bold d-block"><span class="mif-search mr-1"></span><?php echo $data['txt']['SYS_SEARCH'] ?? 'Recherche'; ?></label>
                <input type="text" name="search" data-role="input" placeholder="Nom d'équipe, code..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
            </div>

            <div style="flex: 1.2; min-width: 150px;">
                <label class="text-bold d-block"><span class="mif-security mr-1"></span><?php echo $data['txt']['TEAM_CLUB'] ?? 'Club'; ?></label>
                <select name="club" data-role="select">
                    <option value="">-- Tous les clubs --</option>
                    <?php foreach ($data['clubs'] as $cl): ?>
                        <option value="<?php echo htmlspecialchars($cl['id']); ?>" <?php echo ($data['selectedClub'] == $cl['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cl['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1.2; min-width: 140px;">
                <label class="text-bold d-block"><span class="mif-trophy mr-1"></span><?php echo $data['txt']['TEAM_SPORT'] ?? 'Discipline'; ?></label>
                <select name="sport" data-role="select">
                    <option value="">-- Tous les sports --</option>
                    <?php foreach ($data['sports'] as $sp): ?>
                        <option value="<?php echo htmlspecialchars($sp['id']); ?>" <?php echo ($data['selectedSport'] == $sp['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 0.9; min-width: 110px;">
                <label class="text-bold d-block"><span class="mif-user mr-1"></span><?php echo $data['txt']['TEAM_GENDER'] ?? 'Genre'; ?></label>
                <select name="gender" data-role="select">
                    <option value="">-- Tous --</option>
                    <option value="M" <?php echo ($data['selectedGender'] === 'M') ? 'selected' : ''; ?>>Masculin</option>
                    <option value="F" <?php echo ($data['selectedGender'] === 'F') ? 'selected' : ''; ?>>Féminin</option>
                    <option value="MIXED" <?php echo ($data['selectedGender'] === 'MIXED') ? 'selected' : ''; ?>>Mixte</option>
                </select>
            </div>

            <div style="flex: 0 0 auto; padding-bottom: 2px;">
                <button type="submit" class="button primary mr-1"><span class="mif-filter"></span> Filtrer</button>
                <a href="<?php echo URLROOT; ?>/sport/teams" class="button secondary">Réinitialiser</a>
            </div>
        </form>
    </div>

    <!-- Tableau des Équipes -->
    <table class="table striped table-border mt-3 w-100 table-responsive-cards" data-role="table"
        data-show-search="false" data-show-rows-steps="false" data-check="false" data-rownum="false">
        <thead>
            <tr>
                <th><?php echo $data['txt']['TEAM_NAME'] ?? 'Équipe'; ?></th>
                <th><?php echo $data['txt']['TEAM_CLUB'] ?? 'Club'; ?></th>
                <th><?php echo $data['txt']['TEAM_SPORT'] ?? 'Discipline'; ?></th>
                <th><?php echo $data['txt']['TEAM_CATEGORY'] ?? 'Catégorie'; ?> / <?php echo $data['txt']['TEAM_GENDER'] ?? 'Genre'; ?></th>
                <th><?php echo $data['txt']['TEAM_LEVEL'] ?? 'Niveau'; ?></th>
                <?php if (!empty($data['isAdmin'])): ?>
                    <th><?php echo $data['txt']['TEAM_STATUS'] ?? 'Statut'; ?></th>
                <?php endif; ?>
                <th style="width: 130px;"><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['teams'])): ?>
                <?php foreach ($data['teams'] as $team): 
                    $statusId = (int)$team['status_id'];
                    $statusText = $data['txt'][$team['status_text_code']] ?? ($statusId === 1 ? 'Active' : 'Inactive');
                    $badgeClass = ($statusId === 1) ? 'success' : 'secondary';
                    $genderLabel = ($team['gender'] === 'F') ? 'Féminin' : (($team['gender'] === 'MIXED') ? 'Mixte' : 'Masculin');
                    $genderBadge = ($team['gender'] === 'F') ? 'alert' : (($team['gender'] === 'MIXED') ? 'warning' : 'info');
                ?>
                    <tr>
                        <!-- Nom de l'équipe -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_NAME'] ?? 'Équipe'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/team/<?php echo htmlspecialchars($team['id']); ?>" 
                               class="text-bold <?php echo !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark'; ?>" 
                               title="<?php echo !empty($data['isAdmin']) ? 'Modifier l\'équipe' : 'Consulter la fiche'; ?>">
                                <?php echo htmlspecialchars($team['name']); ?>
                            </a>
                            <?php if (!empty($team['short_name'])): ?>
                                <small class="fg-gray">(<?php echo htmlspecialchars($team['short_name']); ?>)</small>
                            <?php endif; ?>
                            <br>
                            <small class="fg-gray"><code><?php echo htmlspecialchars($team['code']); ?></code></small>
                        </td>

                        <!-- Club -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_CLUB'] ?? 'Club'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/club/<?php echo htmlspecialchars($team['club_id']); ?>" class="fg-dark text-bold">
                                <span class="mif-security mr-1" style="color: <?php echo !empty($team['club_primary_color']) ? htmlspecialchars($team['club_primary_color']) : '#0072c6'; ?>;"></span>
                                <?php echo htmlspecialchars($team['club_name']); ?>
                            </a>
                        </td>

                        <!-- Discipline / Sport -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_SPORT'] ?? 'Discipline'); ?>">
                            <span class="<?php echo !empty($team['sport_icon']) ? htmlspecialchars($team['sport_icon']) : 'mif-trophy'; ?> mr-1"></span>
                            <?php echo htmlspecialchars($team['sport_name']); ?>
                        </td>

                        <!-- Catégorie / Genre -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_CATEGORY'] ?? 'Catégorie'); ?>">
                            <span class="badge light mr-1"><?php echo htmlspecialchars($team['category']); ?></span>
                            <span class="badge <?php echo $genderBadge; ?>"><?php echo htmlspecialchars($genderLabel); ?></span>
                        </td>

                        <!-- Niveau -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_LEVEL'] ?? 'Niveau'); ?>">
                            <?php echo htmlspecialchars($team['level'] ?? '-'); ?>
                        </td>

                        <!-- Statut (Admin uniquement) -->
                        <?php if (!empty($data['isAdmin'])): ?>
                            <td data-label="<?php echo htmlspecialchars($data['txt']['TEAM_STATUS'] ?? 'Statut'); ?>">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                            </td>
                        <?php endif; ?>

                        <!-- Actions -->
                        <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/team/<?php echo htmlspecialchars($team['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                                        <span class="mif-pencil"></span>
                                    </a>

                                    <!-- Désactiver (Admin) -->
                                    <?php if ($statusId === 1): ?>
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['TEAM_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette équipe ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/team/delete/<?php echo htmlspecialchars($team['id']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>">
                                        <span class="mif-cancel"></span>
                                    </button>
                                    <?php endif; ?>

                                    <!-- Supprimer (Admin) -->
                                    <button onclick="if(confirm('Voulez-vous supprimer définitivement cette équipe ?')) location.href='<?php echo URLROOT; ?>/sport/team/delete/<?php echo htmlspecialchars($team['id']); ?>?force=1';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>">
                                        <span class="mif-bin"></span>
                                    </button>
                                <?php else: ?>
                                    <!-- Consulter (Visiteur) -->
                                    <a href="<?php echo URLROOT; ?>/sport/team/<?php echo htmlspecialchars($team['id']); ?>" class="button small primary" title="Consulter la fiche">
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
                        <em>Aucune équipe trouvée.</em>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
