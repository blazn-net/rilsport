    <!-- Contenu principal -->
    <main class="p-4" style="margin-top: 60px;">
        <h1><?php echo htmlspecialchars($data['description']); ?></h1>
        <p><?php echo $data['txt']['MVC_DESC'] ?? 'MVC_DESC'; ?></p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="remark success">
                <?php echo $data['txt']['LOGGED_IN_AS'] ?? 'LOGGED_IN_AS'; ?> <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                (<?php echo $data['txt']['ROLE_LABEL'] ?? 'ROLE_LABEL'; ?>: <?php echo htmlspecialchars(implode(', ', $_SESSION['roles'] ?? [])); ?>).
            </div>
        <?php else: ?>
            <div class="remark info">
                <?php echo $data['txt']['NOT_LOGGED_IN'] ?? 'NOT_LOGGED_IN'; ?> <a href="<?php echo URLROOT; ?>/user/login"><?php echo $data['txt']['LOGIN'] ?? 'LOGIN'; ?></a> <?php echo $data['txt']['MORE_FEATURES'] ?? 'MORE_FEATURES'; ?>
            </div>
        <?php endif; ?>
    </main>
