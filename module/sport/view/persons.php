<!-- Contenu principal Liste des Personnes / Acteurs -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-contacts mr-2"></span><?php echo $data['txt']['SPORT_PERSONS_MGT'] ?? 'Personnes / Acteurs'; ?></h2>
        <?php if (!empty($data['isAdmin'])): ?>
        <a href="<?php echo URLROOT; ?>/sport/person" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['SPORT_ADD_PERSON_BTN'] ?? 'Ajouter une personne'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['SPORT_ADD_PERSON_BTN'] ?? 'Ajouter une personne'; ?>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <div class="table-scroll-wrapper"><table class="table striped table-border mt-4 w-100 " data-role="table"
        data-show-search="true" data-show-rows-steps="false" data-check="false" data-rownum="false"
        data-search-fields="code,first_name,last_name,role_name,nationality">
        <thead>
            <tr>
                <th data-name="id">#</th>
                <th data-name="code"><?php echo $data['txt']['SPORT_PERSON_CODE'] ?? 'Code'; ?></th>
                <th data-name="last_name"><?php echo $data['txt']['SPORT_PERSON_FULLNAME'] ?? 'Nom complet'; ?></th>
                <th data-name="role_name"><?php echo $data['txt']['SPORT_PERSON_ROLE'] ?? 'Rôle / Fonction'; ?></th>
                <th data-name="nationality"><?php echo $data['txt']['SPORT_PERSON_NATIONALITY'] ?? 'Nationalité'; ?></th>
                <th><?php echo $data['txt']['SPORT_STATUS'] ?? 'Statut'; ?></th>
                <th><?php echo $data['txt']['USER_ACTIONS'] ?? 'Actions'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['persons'])): ?>
                <?php foreach ($data['persons'] as $person): 
                    $isActive   = intval($person['status_id']) === 1;
                    $statusCode = $isActive ? ($data['txt']['SPORT_ACTIVE'] ?? 'Actif') : ($data['txt']['SPORT_INACTIVE'] ?? 'Inactif');
                    $badgeClass = $isActive ? 'success' : 'secondary';
                    $fullName   = trim(($person['first_name'] ?? '') . ' ' . ($person['last_name'] ?? ''));
                    $roleName   = !empty($person['role_name']) ? $person['role_name'] : ($person['role_code'] ?? 'Autre');
                    
                    // Couleur badge rôle
                    $roleBadge = 'primary';
                    if ($person['role_code'] === 'coach') $roleBadge = 'warning';
                    elseif ($person['role_code'] === 'referee') $roleBadge = 'alert';
                    elseif ($person['role_code'] === 'official') $roleBadge = 'dark';
                    elseif ($person['role_code'] === 'staff') $roleBadge = 'secondary';
                ?>
                    <tr>
                        <td data-label="#"><?php echo htmlspecialchars($person['id']); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_PERSON_CODE'] ?? 'Code'); ?>"><code><?php echo htmlspecialchars($person['code'] ?? ''); ?></code></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_PERSON_FULLNAME'] ?? 'Nom complet'); ?>">
                            <a href="<?php echo URLROOT; ?>/sport/person/<?php echo htmlspecialchars($person['id']); ?>" 
                               class="text-bold <?php echo !empty($data['isAdmin']) ? 'fg-primary' : 'fg-dark'; ?>" 
                               title="<?php echo !empty($data['isAdmin']) ? 'Modifier la personne' : 'Consulter la fiche'; ?>">
                                <?php echo htmlspecialchars($fullName); ?>
                            </a>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_PERSON_ROLE'] ?? 'Rôle'); ?>">
                            <span class="badge <?php echo $roleBadge; ?>"><?php echo htmlspecialchars($roleName); ?></span>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_PERSON_NATIONALITY'] ?? 'Nationalité'); ?>">
                            <?php echo htmlspecialchars($person['nationality'] ?? '-'); ?>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['SPORT_STATUS'] ?? 'Statut'); ?>">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusCode); ?></span>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['USER_ACTIONS'] ?? 'Actions'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <?php if (!empty($data['isAdmin'])): ?>
                                    <!-- Action Modifier (Admin) -->
                                    <a href="<?php echo URLROOT; ?>/sport/person/<?php echo htmlspecialchars($person['id']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>"><span class="mif-pencil"></span></a>

                                    <!-- Action Désactiver (Admin) -->
                                    <?php if ($isActive): ?>
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_DELETE_PERSON_CONFIRM'] ?? 'Voulez-vous vraiment désactiver cette personne ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/person/delete/<?php echo htmlspecialchars($person['id']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DISABLE'] ?? 'Désactiver'); ?>"><span class="mif-cancel"></span></button>
                                    <?php endif; ?>

                                    <!-- Action Supprimer Définitivement (Admin) -->
                                    <button onclick="if(confirm('<?php echo addslashes($data['txt']['SPORT_FORCE_DELETE_CONFIRM'] ?? 'Voulez-vous vraiment supprimer définitivement cet élément ?'); ?>')) location.href='<?php echo URLROOT; ?>/sport/person/forcedelete/<?php echo htmlspecialchars($person['id']); ?>';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'Supprimer'); ?>"><span class="mif-bin"></span></button>
                                <?php else: ?>
                                    <!-- Action Consulter (Utilisateur simple) -->
                                    <a href="<?php echo URLROOT; ?>/sport/person/<?php echo htmlspecialchars($person['id']); ?>" class="button small primary" title="Consulter la fiche"><span class="mif-eye"></span> Consulter</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->
</main>


