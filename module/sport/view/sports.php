<!-- Contenu principal Liste des Sports -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-trophy mr-2"></span><?php echo $data['txt']['SPORT_SPORTS_MGT'] ?? 'Sports'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/sport" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['SPORT_ADD_SPORT_BTN'] ?? 'Ajouter un sport'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['SPORT_ADD_SPORT_BTN'] ?? 'Ajouter un sport'; ?>
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
        data-search-fields="code,name,description">
        <thead>
            <tr>
                <th data-name="id">#</th>
                <th data-name="code"><?php echo $data['txt']['SPORT_CODE'] ?? 'Code'; ?></th>
                <th data-name="name"><?php echo $data['txt']['SPORT_NAME'] ?? 'Nom'; ?></th>
                <th data-name="description"><?php echo $data['txt']['SPORT_DESCRIPTION'] ?? 'Description'; ?></th>
                <th><?php echo $data['txt']['SPORT_ICON'] ?? 'Icône'; ?></th>
                <th><?php echo $data['txt']['SPORT_STATUS'] ?? 'Statut'; ?></th>
                <th><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['sports'])): ?>
                <?php foreach ($data['sports'] as $sport): 
                    $isActive   = intval($sport['status_id']) === 1;
                    $statusCode = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    $badgeClass = $isActive ? 'success' : 'secondary';
                    $iconClass  = !empty($sport['icon']) && $sport['icon'] !== 'mif-dribbble' ? $sport['icon'] : 'mif-trophy';
                ?>
                    <tr>
                        <td data-label="#"><?php echo htmlspecialchars($sport['id']); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_CODE'] ?? 'Code'); ?>"><code><?php echo htmlspecialchars($sport['code'] ?? ''); ?></code></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_NAME'] ?? 'Nom'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/sport/<?php echo htmlspecialchars($sport['id']); ?>" 
                               class="text-bold <?php echo !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark'; ?>" 
                               title="<?php echo !empty($data['isAdmin']) ? 'Modifier le sport' : 'Consulter la fiche du sport'; ?>">
                                <?php echo htmlspecialchars($sport['name'] ?? ''); ?>
                            </a>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_DESCRIPTION'] ?? 'Description'); ?>"><?php echo htmlspecialchars($sport['description'] ?? ''); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_ICON'] ?? 'Icône'); ?>">
                            <span class="<?php echo htmlspecialchars($iconClass); ?> mif-lg mr-1"></span>
                            <small class="fg-gray">(<?php echo htmlspecialchars($iconClass); ?>)</small>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_STATUS'] ?? 'Statut'); ?>">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusCode); ?></span>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Action Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/sport/<?php echo htmlspecialchars($sport['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>"><span class="mif-pencil"></span></a>

                                    <!-- Action Désactiver (Admin) -->
                                    <?php if ($isActive): ?>
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_DELETE_SPORT_CONFIRM'] ?? 'Voulez-vous vraiment désactiver ce sport ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/sport/delete/<?php echo htmlspecialchars($sport['id']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>"><span class="mif-cancel"></span></button>
                                    <?php endif; ?>

                                    <!-- Action Supprimer Définitivement (Admin) -->
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement ce sport ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/sport/forcedelete/<?php echo htmlspecialchars($sport['id']); ?>';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>"><span class="mif-bin"></span></button>
                                <?php else: ?>
                                    <!-- Action Consulter (Utilisateur simple) -->
                                    <a href="<?php echo URLROOT; ?>/sport/sport/<?php echo htmlspecialchars($sport['id']); ?>" class="button small primary" title="Consulter la fiche"><span class="mif-eye"></span> Consulter</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
