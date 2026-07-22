<!-- Menu latéral (Sidebar) -->
<aside class="sidebar pos-absolute z-2" data-role="sidebar" data-toggle="#sidebar-toggle" id="sb1">
    <div class="sidebar-header bg-dark">
        <a href="#" class="fg-white sub-action" onclick="Metro.sidebar.close('#sb1'); return false;">
            <span class="mif-arrow-left mif-2x fg-white"></span>
        </a>
        <div class="avatar"
            style="background:#fff; border-radius:50%; display:flex; justify-content:center; align-items:center;">
            <span class="mif-earth mif-3x fg-dark"></span>
        </div>
        <span class="title fg-white"><strong><?php echo SITENAME; ?></strong></span>
        <span class="subtitle fg-white"><strong><?php echo SITEDESCRIPTION; ?><br>toto</strong></span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <div class="d-flex flex-justify-around p-2 flex-wrap" style="gap: 5px;">
                <?php
                $systemLangs = \Core\Language::getSystemLanguages();
                $currentLang = $_SESSION['lang'] ?? DEFAULT_LANG;
                foreach ($systemLangs as $lang):
                    $isActive = ($currentLang === $lang['lang_code']);
                    $btnClass = $isActive ? 'primary' : 'light';
                    ?>
                    <a href="?lang=<?php echo htmlspecialchars($lang['lang_code']); ?>"
                        class="button flex-1 <?php echo $btnClass; ?>" style="min-width: 60px;"
                        title="<?php echo htmlspecialchars($lang['lang_name'] ?? strtoupper($lang['lang_code'])); ?>">
                        <span
                            class="<?php echo !empty($lang['lang_flag']) ? 'fi ' . htmlspecialchars($lang['lang_flag']) : 'mif-earth'; ?>"></span>
                        <?php echo htmlspecialchars(strtoupper($lang['lang_code'])); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </li>
        <li class="divider"></li>
        <li><a href="<?php echo URLROOT; ?>/"><span
                    class="mif-home icon"></span><?php echo $data['txt']['HOME'] ?? 'HOME'; ?></a></li>
        <li class="divider"></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo URLROOT; ?>/user/user/<?php echo $_SESSION['user_id']; ?>"><span
                        class="mif-profile icon"></span><?php echo $data['txt']['MY_PROFILE'] ?? 'MY_PROFILE'; ?>
                    (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            <?php if (isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles'])): ?>
                <?php
                $userTxt = \Core\Language::load('user');
                $mainTxt = \Core\Language::load('main');
                ?>
                <li><a href="<?php echo URLROOT; ?>/user/users"><span
                            class="mif-admin-panel icon"></span><?php echo $userTxt['USERS_LIST'] ?? 'USERS_LIST'; ?></a>
                </li>
                <li><a href="<?php echo URLROOT; ?>/main/langs"><span
                            class="mif-language icon"></span><?php echo $mainTxt['LANGS_MGT'] ?? 'LANGS_MGT'; ?></a>
                </li>
            <?php endif; ?>
            <li><a href="<?php echo URLROOT; ?>/user/logout"><span
                        class="mif-exit icon"></span><?php echo $data['txt']['LOGOUT'] ?? 'LOGOUT'; ?></a></li>
        <?php else: ?>
            <li><a href="<?php echo URLROOT; ?>/user/login"><span
                        class="mif-enter icon"></span><?php echo $data['txt']['LOGIN'] ?? 'LOGIN'; ?></a></li>
            <li><a href="<?php echo URLROOT; ?>/user/register"><span
                        class="mif-user-plus icon"></span><?php echo $data['txt']['REGISTER'] ?? 'REGISTER'; ?></a>
            </li>
        <?php endif; ?>
    </ul>
</aside>