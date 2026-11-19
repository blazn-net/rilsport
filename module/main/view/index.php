    <!-- Contenu principal -->
    <main class="p-4" style="margin-top: 60px;">
        <?php if (!empty($data['message'])): ?>
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#d4edda;border:1px solid #c3e6cb;border-radius:4px;color:#155724;margin-bottom:1rem;"><span class="mif-checkmark" style="font-size:1.2em;line-height:1;flex-shrink:0;"></span><span><?php echo htmlspecialchars($data['message']); ?></span></div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;color:#721c24;margin-bottom:1rem;"><span class="mif-warning" style="font-size:1.2em;line-height:1;flex-shrink:0;"></span><span><?php echo htmlspecialchars($data['error']); ?></span></div>
        <?php endif; ?>

        <h1><span class="mif-home mr-2"></span><?php echo htmlspecialchars($data['description']); ?></h1>
        <p><?php echo $data['txt']['SYS_MVC_DESC'] ?? 'SYS_MVC_DESC'; ?></p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="remark success">
                <?php echo $data['txt']['SYS_LOGGED_IN_AS'] ?? 'SYS_LOGGED_IN_AS'; ?> <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                (<?php echo $data['txt']['SYS_ROLE_LABEL'] ?? 'SYS_ROLE_LABEL'; ?>: <?php echo htmlspecialchars(implode(', ', $_SESSION['roles'] ?? [])); ?>).
            </div>
        <?php else: ?>
            <div class="remark info">
                <?php echo $data['txt']['SYS_NOT_LOGGED_IN'] ?? 'SYS_NOT_LOGGED_IN'; ?> <a href="<?php echo URLROOT; ?>/user/login"><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></a> <?php echo $data['txt']['SYS_MORE_FEATURES'] ?? 'SYS_MORE_FEATURES'; ?>
            </div>
        <?php endif; ?>
    </main>
