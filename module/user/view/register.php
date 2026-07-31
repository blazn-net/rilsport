<!-- Contenu principal -->
<main class="p-4 d-flex flex-justify-center flex-align-center flex-column" style="margin-top: 60px; min-height: calc(100vh - 150px); width: 100%;">
    <div class="card p-5" style="width: 400px; max-width: 100%;">
        <h2 class="text-center mb-4"><?php echo $data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'; ?></h2>
        
        <?php if (!empty($data['error'])): ?>
            <div class="remark warning"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($data['success'])): ?>
            <div class="remark success">
                <?php echo htmlspecialchars($data['success']); ?><br>
                <a href="<?php echo URLROOT; ?>/user/login"><strong><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></strong></a>
            </div>
        <?php else: ?>

            <form method="POST" action="<?php echo URLROOT; ?>/user/register">
                <div class="form-group">
                    <label><?php echo $data['txt']['USER_USERNAME'] ?? 'USER_USERNAME'; ?></label>
                    <input type="text" name="username" data-role="input" required value="<?php echo htmlspecialchars($data['username']); ?>">
                </div>
                
                <div class="form-group mt-3">
                    <label><?php echo $data['txt']['USER_EMAIL'] ?? 'USER_EMAIL'; ?></label>
                    <input type="email" name="email" data-role="input" required value="<?php echo htmlspecialchars($data['email']); ?>">
                </div>
                
                <div class="form-group mt-3">
                    <label><?php echo $data['txt']['USER_PASSWORD'] ?? 'USER_PASSWORD'; ?></label>
                    <input type="password" name="password" data-role="input" required>
                </div>

                <div class="form-group mt-3">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="password_confirm" data-role="input" required>
                </div>
                
                <div class="form-group mt-5">
                    <button type="submit" class="button primary w-100" title="<?php echo htmlspecialchars($data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'); ?>"><?php echo $data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'; ?></button>
                </div>
                
                <div class="text-center mt-3">
                    <small><?php echo $data['txt']['USER_ALREADY_ACCOUNT'] ?? 'USER_ALREADY_ACCOUNT'; ?> <a href="<?php echo URLROOT; ?>/user/login"><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></a></small>
                </div>
                <div class="text-center mt-2">
                    <small><a href="<?php echo URLROOT; ?>"><?php echo $data['txt']['MAIN'] ?? 'MAIN'; ?></a></small>
                </div>
            </form>
            
        <?php endif; ?>
    </div>
</main>
