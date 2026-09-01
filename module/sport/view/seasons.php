<!-- Contenu principal Liste des Saisons -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-calendar mr-2"></span><?php echo $data['txt']['SPORT_SEASONS_MGT'] ?? 'Saisons'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/season" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['SPORT_ADD_SEASON_BTN'] ?? 'Ajouter une saison'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['SPORT_ADD_SEASON_BTN'] ?? 'Ajouter une saison'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <table class="table striped table-border mt-4 w-100 table-responsive-cards" data-role="table"
        data-show-search="true" data-show-rows-steps="false" data-check="false" data-rownum="false"
        data-search-fields="code,name">
        <thead>
            <tr>
                <th data-name="id">#</th>
                <th data-name="code"><?php echo $data['txt']['SPORT_SEASON_CODE'] ?? 'Code'; ?></th>
                <th data-name="name"><?php echo $data['txt']['SPORT_SEASON_NAME'] ?? 'Nom'; ?></th>
                <th data-name="date_start"><?php echo $data['txt']['SPORT_SEASON_DATE_START'] ?? 'Date de début'; ?></th>
                <th data-name="date_end"><?php echo $data['txt']['SPORT_SEASON_DATE_END'] ?? 'Date de fin'; ?></th>
                <th><?php echo $data['txt']['SPORT_STATUS'] ?? 'Statut'; ?></th>
                <th><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['seasons'])): ?>
                <?php foreach ($data['seasons'] as $season): 
                    $isActive   = intval($season['status_id']) === 1;
                    $statusCode = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    $badgeClass = $isActive ? 'success' : 'secondary';
                    $dateStart  = !empty($season['date_start']) ? date('d/m/Y', strtotime($season['date_start'])) : '-';
                    $dateEnd    = !empty($season['date_end']) ? date('d/m/Y', strtotime($season['date_end'])) : '-';
                ?>
                    <tr>
                        <td data-label="#"><?php echo htmlspecialchars($season['id']); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_SEASON_CODE'] ?? 'Code'); ?>"><code><?php echo htmlspecialchars($season['code'] ?? ''); ?></code></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_SEASON_NAME'] ?? 'Nom'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/season/<?php echo htmlspecialchars($season['id']); ?>" 
                               class="text-bold <?php echo !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark'; ?>" 
                               title="<?php echo !empty($data['isAdmin']) ? 'Modifier la saison' : 'Consulter la fiche de la saison'; ?>">
                                <?php echo htmlspecialchars($season['name'] ?? ''); ?>
                            </a>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_SEASON_DATE_START'] ?? 'Date de début'); ?>"><?php echo htmlspecialchars($dateStart); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_SEASON_DATE_END'] ?? 'Date de fin'); ?>"><?php echo htmlspecialchars($dateEnd); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_STATUS'] ?? 'Statut'); ?>">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusCode); ?></span>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Action Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/season/<?php echo htmlspecialchars($season['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>"><span class="mif-pencil"></span></a>

                                    <!-- Action Désactiver (Admin) -->
                                    <?php if ($isActive): ?>
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_DELETE_SEASON_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette saison ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/season/delete/<?php echo htmlspecialchars($season['id']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>"><span class="mif-cancel"></span></button>
                                    <?php endif; ?>

                                    <!-- Action Supprimer Définitivement (Admin) -->
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement cette saison ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/season/forcedelete/<?php echo htmlspecialchars($season['id']); ?>';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>"><span class="mif-bin"></span></button>
                                <?php else: ?>
                                    <!-- Action Consulter (Utilisateur simple) -->
                                    <a href="<?php echo URLROOT; ?>/sport/season/<?php echo htmlspecialchars($season['id']); ?>" class="button small primary" title="Consulter la fiche"><span class="mif-eye"></span> Consulter</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
