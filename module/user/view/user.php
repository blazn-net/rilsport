    <main class="p-4" style="margin-top: 60px;">
        <div class="d-flex flex-justify-between flex-align-center mb-4">
            <h2><?php echo $data['mode'] === 'edit' ? ($data['txt']['USER_EDIT_USER_TITLE'] ?? 'USER_EDIT_USER_TITLE') : ($data['txt']['USER_ADD_USER_BTN'] ?? 'USER_ADD_USER_BTN'); ?></h2>
            <a href="<?php echo URLROOT; ?>/<?php echo $data['is_admin'] ? 'user/users' : 'main'; ?>" class="button" title="<?php echo htmlspecialchars($data['txt']['USER_BTN_BACK'] ?? 'USER_BTN_BACK'); ?>">
                <span class="mif-arrow-left"></span> <?php echo $data['txt']['USER_BTN_BACK'] ?? 'USER_BTN_BACK'; ?>
            </a>
        </div>

        <?php if (!empty($data['message'])): ?>
            <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
        <?php endif; ?>

        <?php if (!empty($data['error'])): ?>
            <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo URLROOT; ?>/user/<?php echo $data['id'] ? $data['id'] : ''; ?>">
            <div class="form-group">
                <label><?php echo $data['txt']['USER_USERNAME'] ?? 'USER_USERNAME'; ?></label>
                <input type="text" name="username" data-role="input" value="<?php echo isset($data['user']->username) ? htmlspecialchars($data['user']->username) : ''; ?>" required>
            </div>
            <div class="row mt-2">
                <div class="cell-md-6 form-group">
                    <label><?php echo $data['txt']['USER_LASTNAME'] ?? 'USER_LASTNAME'; ?></label>
                    <input type="text" name="nom" data-role="input" value="<?php echo isset($data['user']->nom) ? htmlspecialchars($data['user']->nom) : ''; ?>">
                </div>
                <div class="cell-md-6 form-group">
                    <label><?php echo $data['txt']['USER_FIRSTNAME'] ?? 'USER_FIRSTNAME'; ?></label>
                    <input type="text" name="prenom" data-role="input" value="<?php echo isset($data['user']->prenom) ? htmlspecialchars($data['user']->prenom) : ''; ?>">
                </div>
            </div>
            <div class="form-group mt-2">
                <label><?php echo $data['txt']['USER_EMAIL'] ?? 'USER_EMAIL'; ?></label>
                <input type="email" name="email" data-role="input" value="<?php echo isset($data['user']->email) ? htmlspecialchars($data['user']->email) : ''; ?>" required>
            </div>
            <div class="form-group mt-2">
                <label><?php echo $data['txt']['USER_PASSWORD'] ?? 'USER_PASSWORD'; ?> <?php echo $data['mode'] === 'edit' ? ($data['txt']['USER_LEAVE_BLANK_NO_CHANGE'] ?? 'USER_LEAVE_BLANK_NO_CHANGE') : ''; ?></label>
                <input type="password" name="password" data-role="input" <?php echo $data['mode'] === 'add' ? 'required' : ''; ?>>
            </div>
            <?php if ($data['is_admin']): ?>
            <div class="form-group mt-2">
                <label><?php echo $data['txt']['USER_ROLE'] ?? 'USER_ROLE'; ?></label>
                <select name="roles[]" multiple data-role="select">
                    <?php foreach ($data['available_roles'] as $role_id => $role_info): ?>
                        <?php $selected = (isset($data['user']->roles) && is_array($data['user']->roles) && in_array($role_id, $data['user']->roles)) ? 'selected' : ''; ?>
                        <option value="<?php echo htmlspecialchars($role_id); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($data['txt'][$role_info['text_code']] ?? $role_info['text_code']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mt-2">
                <label><?php echo $data['txt']['STATUS'] ?? 'STATUS'; ?></label>
                <select name="status_id" data-role="select">
                    <?php foreach ($data['available_statuses'] as $status_id => $text_code): ?>
                        <?php 
                        $statusValue = isset($data['user']->status_id) ? $data['user']->status_id : 2;
                        $selected = ($statusValue == $status_id) ? 'selected' : ''; 
                        ?>
                        <option value="<?php echo htmlspecialchars($status_id); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($data['txt'][$text_code] ?? $text_code); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if ($data['mode'] === 'edit'): ?>
            <div data-role="panel" 
                 data-title-caption="<?php echo htmlspecialchars($data['txt']['INFO'] ?? 'Infos'); ?>" 
                 data-collapsible="true" 
                 data-collapsed="true" 
                 class="mt-4">
                <div class="row">
                    <div class="cell-md-6">
                        <p><strong><?php echo htmlspecialchars($data['txt']['CREATED_AT'] ?? 'Cree le'); ?> :</strong> <?php echo isset($data['user']->created_at) && $data['user']->created_at ? htmlspecialchars(date('Y-m-d H:i:s (T)', strtotime($data['user']->created_at))) : '-'; ?></p>
                        <p><strong><?php echo htmlspecialchars($data['txt']['CREATED_BY'] ?? 'Cree par'); ?> :</strong> <?php echo isset($data['user']->created_by_name) && $data['user']->created_by_name ? htmlspecialchars($data['user']->created_by_name) : '-'; ?></p>
                    </div>
                    <div class="cell-md-6">
                        <p><strong><?php echo htmlspecialchars($data['txt']['MODIFIED_AT'] ?? 'Modifie le'); ?> :</strong> <?php echo isset($data['user']->modified_at) && $data['user']->modified_at ? htmlspecialchars(date('Y-m-d H:i:s (T)', strtotime($data['user']->modified_at))) : '-'; ?></p>
                        <p><strong><?php echo htmlspecialchars($data['txt']['MODIFIED_BY'] ?? 'Modifie par'); ?> :</strong> <?php echo isset($data['user']->modified_by_name) && $data['user']->modified_by_name ? htmlspecialchars($data['user']->modified_by_name) : '-'; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <button class="button primary" type="submit" title="<?php echo htmlspecialchars($data['mode'] === 'edit' ? ($data['txt']['USER_BTN_UPDATE'] ?? 'USER_BTN_UPDATE') : ($data['txt']['USER_BTN_SAVE'] ?? 'USER_BTN_SAVE')); ?>"><?php echo $data['mode'] === 'edit' ? ($data['txt']['USER_BTN_UPDATE'] ?? 'USER_BTN_UPDATE') : ($data['txt']['USER_BTN_SAVE'] ?? 'USER_BTN_SAVE'); ?></button>
            </div>
        </form>
    </main>
