    <!-- Contenu principal -->
    <main class="p-4" style="margin-top: 60px;">
        <h1><?php echo htmlspecialchars($data['description']); ?></h1>
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
