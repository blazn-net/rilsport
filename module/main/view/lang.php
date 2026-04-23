<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center mb-4">
        <h2><?php echo ($data['mode'] === 'edit') ? ($data['txt']['EDIT_LANG_TITLE'] ?? 'EDIT_LANG_TITLE') : ($data['txt']['ADD_LANG_TITLE'] ?? 'ADD_LANG_TITLE'); ?></h2>
        <a href="<?php echo URLROOT; ?>/main/langs" class="button" title="<?php echo htmlspecialchars($data['txt']['BTN_BACK'] ?? 'BTN_BACK'); ?>">
            <span class="mif-arrow-left"></span> <?php echo $data['txt']['BTN_BACK'] ?? 'BTN_BACK'; ?>
        </a>
    </div>

    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <?php if ($data['mode'] === 'add'): ?>
    <div class="remark info">
        <?php echo $data['txt']['LANG_ADD_NOTE'] ?? 'LANG_ADD_NOTE'; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo URLROOT; ?>/main/lang<?php echo ($data['mode'] === 'edit' && isset($data['lang']->lang_code)) ? '/' . htmlspecialchars($data['lang']->lang_code) : ''; ?>">
        
        <div class="form-group">
            <label><?php echo $data['txt']['LANG_CODE_LABEL'] ?? 'LANG_CODE_LABEL'; ?></label>
            <input type="text" name="lang_code" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_CODE_PH'] ?? 'LANG_CODE_PH'); ?>" maxlength="5" value="<?php echo htmlspecialchars($data['lang']->lang_code ?? ''); ?>" <?php echo ($data['mode'] === 'edit') ? 'disabled' : 'required'; ?>>
            <?php if ($data['mode'] === 'edit'): ?>
            <small class="fg-gray"><?php echo $data['txt']['LANG_CODE_HELP'] ?? 'LANG_CODE_HELP'; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group mt-2">
            <label><?php echo $data['txt']['LANG_NAME_LABEL'] ?? 'LANG_NAME_LABEL'; ?></label>
            <input type="text" name="lang_name" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_NAME_PH'] ?? 'LANG_NAME_PH'); ?>" value="<?php echo htmlspecialchars($data['lang']->lang_name ?? ''); ?>" required>
        </div>

        <div class="form-group mt-2">
            <label><?php echo $data['txt']['LANG_FLAG_LABEL'] ?? 'LANG_FLAG_LABEL'; ?></label>
            <input type="text" name="lang_flag" data-role="input" placeholder="<?php echo htmlspecialchars($data['txt']['LANG_FLAG_PH'] ?? 'LANG_FLAG_PH'); ?>" value="<?php echo htmlspecialchars($data['lang']->lang_flag ?? ''); ?>">
            <small class="fg-gray"><?php echo $data['txt']['LANG_FLAG_HELP'] ?? 'LANG_FLAG_HELP'; ?></small>
        </div>

        <?php if ($data['mode'] === 'edit'): ?>
        <div class="form-group mt-2">
            <label><?php echo $data['txt']['STATUS'] ?? 'STATUS'; ?></label>
            <select name="status_id" data-role="select">
                <option value="1" <?php echo (isset($data['lang']->status_id) && intval($data['lang']->status_id) === 1) ? 'selected' : ''; ?>><?php echo $data['txt']['LANG_ACTIVE'] ?? 'LANG_ACTIVE'; ?></option>
                <option value="2" <?php echo (isset($data['lang']->status_id) && intval($data['lang']->status_id) === 2) ? 'selected' : ''; ?>><?php echo $data['txt']['LANG_DRAFT'] ?? 'LANG_DRAFT'; ?></option>
            </select>
        </div>

        <div data-role="panel" 
             data-title-caption="<?php echo htmlspecialchars($data['txt']['INFO_PANEL'] ?? 'INFO_PANEL'); ?>" 
             data-collapsible="true" 
             data-collapsed="true" 
             class="mt-4">
            <div class="row">
                <div class="cell-md-6">
                    <p><strong><?php echo $data['txt']['CREATED_AT'] ?? 'CREATED_AT'; ?></strong> <?php echo isset($data['lang']->created_at) && $data['lang']->created_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['lang']->created_at))) : '-'; ?></p>
                    <p><strong><?php echo $data['txt']['CREATED_BY'] ?? 'CREATED_BY'; ?></strong> <?php echo isset($data['lang']->created_by_name) && $data['lang']->created_by_name ? htmlspecialchars($data['lang']->created_by_name) : '-'; ?></p>
                </div>
                <div class="cell-md-6">
                    <p><strong><?php echo $data['txt']['MODIFIED_AT'] ?? 'MODIFIED_AT'; ?></strong> <?php echo isset($data['lang']->modified_at) && $data['lang']->modified_at ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['lang']->modified_at))) : '-'; ?></p>
                    <p><strong><?php echo $data['txt']['MODIFIED_BY'] ?? 'MODIFIED_BY'; ?></strong> <?php echo isset($data['lang']->modified_by_name) && $data['lang']->modified_by_name ? htmlspecialchars($data['lang']->modified_by_name) : '-'; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="form-group mt-4">
            <button class="button primary" type="submit" title="<?php echo htmlspecialchars($data['mode'] === 'edit' ? ($data['txt']['BTN_UPDATE'] ?? 'BTN_UPDATE') : ($data['txt']['BTN_SAVE'] ?? 'BTN_SAVE')); ?>"><?php echo ($data['mode'] === 'edit') ? ($data['txt']['BTN_UPDATE'] ?? 'BTN_UPDATE') : ($data['txt']['BTN_SAVE'] ?? 'BTN_SAVE'); ?></button>
        </div>
    </form>
</main>
