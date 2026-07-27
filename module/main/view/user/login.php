<!-- Contenu principal -->
<main class="d-flex flex-justify-center flex-align-center flex-column" style="margin-top: 60px; min-height: calc(100vh - 150px); width: 100%;">
    <div class="card p-5" style="width: 400px; max-width: 90vw;">
        <h2 class="text-center mb-4"><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></h2>
        
        <?php if (!empty($data['error'])): ?>
            <div class="remark warning"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo URLROOT; ?>/main/login">
            <div class="form-group">
                <label><?php echo $data['txt']['USER_USERNAME'] ?? 'USER_USERNAME'; ?></label>
                <input type="text" name="login" data-role="input" required value="<?php echo htmlspecialchars($data['login']); ?>">
            </div>
            
            <div class="form-group mt-4">
                <label><?php echo $data['txt']['USER_PASSWORD'] ?? 'USER_PASSWORD'; ?></label>
                <input type="password" name="password" data-role="input" required>
            </div>
            
            <div class="form-group mt-5">
                <button type="submit" class="button primary w-100" title="<?php echo htmlspecialchars($data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'); ?>"><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></button>
            </div>
            
            <div class="text-center mt-3">
                <small><?php echo $data['txt']['USER_NO_ACCOUNT'] ?? 'USER_NO_ACCOUNT'; ?> <a href="<?php echo URLROOT; ?>/main/register"><?php echo $data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'; ?></a></small>
            </div>
            <div class="text-center mt-2">
                <small><a href="<?php echo URLROOT; ?>"><?php echo $data['txt']['MAIN'] ?? 'MAIN'; ?></a></small>
            </div>
        </form>
    </div>
</main>
