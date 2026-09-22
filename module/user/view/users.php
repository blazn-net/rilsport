<!-- Contenu principal -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><span class="mif-group mr-2"></span><?php echo $data['txt']['USER_USERS_LIST'] ?? 'Utilisateurs'; ?></h2>
        <a href="<?php echo URLROOT; ?>/user" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['USER_ADD_USER_BTN'] ?? 'USER_ADD_USER_BTN'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['USER_ADD_USER_BTN'] ?? 'USER_ADD_USER_BTN'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>


    <div class="table-scroll-wrapper"><table class="table striped table-border mt-4 w-100 " data-role="table"
        data-show-search="true" data-show-rows-steps="false" data-check="false" data-rownum="false"
        data-search-fields="username,email,nom,prenom">
        <thead>
            <tr>
                <th>ID</th>
                <th data-name="username"><?php echo $data['txt']['USER_USERNAME'] ?? 'USER_USERNAME'; ?></th>
                <th><?php echo ($data['txt']['USER_FIRSTNAME'] ?? 'Prénom') . ' ' . ($data['txt']['USER_LASTNAME'] ?? 'Nom'); ?></th>
                <th data-name="email"><?php echo $data['txt']['USER_EMAIL'] ?? 'USER_EMAIL'; ?></th>
                <th><?php echo $data['txt']['USER_ROLE'] ?? 'USER_ROLE'; ?></th>
                <th><?php echo $data['txt']['USER_DATE_REG'] ?? 'USER_DATE_REG'; ?></th>
                <th><?php echo $data['txt']['STATUS'] ?? 'STATUS'; ?></th>
                <th><?php echo $data['txt']['USER_ACTIONS'] ?? 'USER_ACTIONS'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['users'] as $u): ?>
                <tr>
                    <td data-label="ID"><?php echo $u['id']; ?></td>
                    <td data-label="<?php echo $data['txt']['USER_USERNAME'] ?? 'USER_USERNAME'; ?>">
                        <?php echo htmlspecialchars($u['username']); ?></td>
                    <td data-label="Nom">
                        <?php echo htmlspecialchars(trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''))); ?></td>
                    <td data-label="<?php echo $data['txt']['USER_EMAIL'] ?? 'USER_EMAIL'; ?>">
                        <?php echo htmlspecialchars($u['email']); ?></td>
                    <td data-label="<?php echo $data['txt']['USER_ROLE'] ?? 'USER_ROLE'; ?>">
                        <?php if (isset($u['roles']) && is_array($u['roles'])): ?>
                            <div class="d-flex flex-wrap" style="gap: 5px; justify-content: flex-begin;">
                                <?php foreach ($u['roles'] as $roleId): ?>
                                    <?php
                                    $badgeCode = $data['available_roles'][$roleId]['badge_code'] ?? 'primary';
                                    $textCode = $data['available_roles'][$roleId]['text_code'] ?? $roleId;
                                    ?>
                                    <span class="badge <?php echo htmlspecialchars($badgeCode); ?>">
                                        <?php echo htmlspecialchars($data['txt'][$textCode] ?? $textCode); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="badge primary">none</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="<?php echo $data['txt']['USER_DATE_REG'] ?? 'USER_DATE_REG'; ?>">
                        <?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></td>
                    <td data-label="<?php echo $data['txt']['STATUS'] ?? 'STATUS'; ?>">
                        <?php
                        $statusCode = $data['available_statuses'][$u['status_id']] ?? '';
                        $statusLabel = $data['txt'][$statusCode] ?? $statusCode;
                        $badgeClass = ($u['status_id'] == 1) ? 'success' : 'secondary';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                    </td>
                    <td data-label="<?php echo $data['txt']['USER_ACTIONS'] ?? 'USER_ACTIONS'; ?>">
                        <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                            <a href="<?php echo URLROOT; ?>/user/<?php echo $u['id']; ?>"
                                class="button small info" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'SYS_BTN_EDIT'); ?>"><span class="mif-pencil"></span></a>
                            <a href="<?php echo URLROOT; ?>/user/users?action=delete&id=<?php echo $u['id']; ?>"
                                class="button small alert" title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_DELETE'] ?? 'SYS_BTN_DELETE'); ?>"
                                onclick="return confirm('<?php echo addslashes($data['txt']['USER_DELETE_USER_CONFIRM'] ?? 'USER_DELETE_USER_CONFIRM'); ?>');"><span
                                    class="mif-bin"></span></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.table-scroll-wrapper -->
</main>


