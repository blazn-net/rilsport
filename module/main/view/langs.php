<!-- Contenu principal -->
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center flex-wrap mb-4">
        <h2><?php echo $data['txt']['LANGS_MGT'] ?? 'LANGS_MGT'; ?></h2>
        <a href="<?php echo URLROOT; ?>/main/lang" class="button success mt-2 mt-md-0" title="<?php echo htmlspecialchars($data['txt']['ADD_LANG_BTN'] ?? 'ADD_LANG_BTN'); ?>">
            <span class="mif-plus"></span> <?php echo $data['txt']['ADD_LANG_BTN'] ?? 'ADD_LANG_BTN'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <table class="table striped table-border mt-4 w-100 table-responsive-cards" data-role="table"
        data-show-search="true" data-show-rows-steps="false" data-check="false" data-rownum="false"
        data-search-fields="lang_code,lang_name">
        <thead>
            <tr>
                <th data-name="lang_code"><?php echo $data['txt']['LANG_CODE'] ?? 'LANG_CODE'; ?></th>
                <th data-name="lang_name"><?php echo $data['txt']['LANG_NAME'] ?? 'LANG_NAME'; ?></th>
                <th><?php echo $data['txt']['LANG_FLAG'] ?? 'LANG_FLAG'; ?></th>
                <th><?php echo $data['txt']['STATUS'] ?? 'STATUS'; ?></th>
                <th><?php echo $data['txt']['ACTIONS'] ?? 'ACTIONS'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['langs'])): ?>
                <?php foreach ($data['langs'] as $lang): 
                    $statusCode = intval($lang['status_id']) === 1 ? ($data['txt']['LANG_ACTIVE'] ?? 'LANG_ACTIVE') : (intval($lang['status_id']) === 2 ? ($data['txt']['LANG_DRAFT'] ?? 'LANG_DRAFT') : ($data['txt']['LANG_UNKNOWN'] ?? 'LANG_UNKNOWN'));
                    $badgeClass = intval($lang['status_id']) === 1 ? 'success' : 'secondary';
                ?>
                    <tr>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['LANG_CODE'] ?? 'LANG_CODE'); ?>"><?php echo htmlspecialchars($lang['lang_code'] ?? ''); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['LANG_NAME'] ?? 'LANG_NAME'); ?>"><?php echo htmlspecialchars($lang['lang_name'] ?? ''); ?></td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['LANG_FLAG'] ?? 'LANG_FLAG'); ?>">
                            <span class="<?php echo !empty($lang['lang_flag']) ? 'fi ' . htmlspecialchars($lang['lang_flag']) : 'mif-earth'; ?>"></span> 
                            (<?php echo !empty($lang['lang_flag']) ? htmlspecialchars($lang['lang_flag']) : ($data['txt']['NO_FLAG'] ?? 'Aucun drapeau'); ?>)
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['STATUS'] ?? 'STATUS'); ?>">
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusCode; ?></span>
                        </td>
                        <td data-label="<?php echo htmlspecialchars($data['txt']['ACTIONS'] ?? 'ACTIONS'); ?>">
                            <div class="d-flex flex-row flex-wrap" style="gap: 5px;">
                                <!-- Modifier -->
                                <a href="<?php echo URLROOT; ?>/main/lang/<?php echo htmlspecialchars($lang['lang_code']); ?>" class="button small info" title="<?php echo htmlspecialchars($data['txt']['BTN_EDIT'] ?? 'BTN_EDIT'); ?>"><span class="mif-pencil"></span></a>

                                <!-- Action Désactiver (Draft) -->
                                <?php if (intval($lang['status_id']) === 1): ?>
                                <button onclick="if(confirm('<?php echo addslashes($data['txt']['DELETE_LANG_CONFIRM'] ?? 'DELETE_LANG_CONFIRM'); ?>')) location.href='<?php echo URLROOT; ?>/main/lang/delete/<?php echo htmlspecialchars($lang['lang_code']); ?>';" class="button small warning" title="<?php echo htmlspecialchars($data['txt']['BTN_DISABLE'] ?? 'BTN_DISABLE'); ?>"><span class="mif-cancel"></span></button>
                                <?php endif; ?>

                                <!-- Action Supprimer Définitivement -->
                                <button onclick="if(confirm('<?php echo addslashes($data['txt']['FORCE_DELETE_LANG_CONFIRM'] ?? 'FORCE_DELETE_LANG_CONFIRM'); ?>')) location.href='<?php echo URLROOT; ?>/main/lang/forcedelete/<?php echo htmlspecialchars($lang['lang_code']); ?>';" class="button small alert" title="<?php echo htmlspecialchars($data['txt']['BTN_DELETE'] ?? 'BTN_DELETE'); ?>"><span class="mif-bin"></span></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>
