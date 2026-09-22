<!-- Volet latéral NavView -->
<div class="navview-pane">
    <div class="logo-container">
        <button class="pull-button">
            <span class="mif-menu"></span>
        </button>
        <a href="<?php echo URLROOT; ?>/" class="d-flex flex-align-center text-logo bg-transparent"
            style="width: calc(100% - 54px);">
            <div class="enlarge-1 text-weight-9 text-ellipsis fg-default">
                <?php echo $data['txt']['SYS_HOME'] ?? 'SYS_HOME'; ?>
            </div>
        </a>
    </div>

    <!-- Sélecteur de langue sous forme de liste déroulante -->
    <div class="lang-selector-box border-bottom bd-light">
        <?php
        $systemLangs = \Core\Language::getSystemLanguages();
        $currentLang = $_SESSION['lang'] ?? DEFAULT_LANG;
        $activeLang = null;
        foreach ($systemLangs as $lang) {
            if ($lang['lang_code'] === $currentLang) {
                $activeLang = $lang;
                break;
            }
        }
        if (!$activeLang && !empty($systemLangs)) {
            $activeLang = $systemLangs[0];
        }
        ?>
        <div class="dropdown-button">
            <button class="button dropdown-toggle lang-btn" type="button"
                title="<?php echo htmlspecialchars($activeLang['lang_name'] ?? 'Langue'); ?>">
                <span class="lang-flag">
                    <span
                        class="<?php echo !empty($activeLang['lang_flag']) ? 'fi ' . htmlspecialchars($activeLang['lang_flag']) : 'mif-earth'; ?>"></span>
                </span>
                <span class="lang-name">
                    <?php echo htmlspecialchars($activeLang['lang_name'] ?? strtoupper($activeLang['lang_code'])); ?>
                </span>
                <span class="mif-chevron-down ml-auto lang-chevron"></span>
            </button>
            <ul class="d-menu" data-role="dropdown">
                <?php foreach ($systemLangs as $lang):
                    $isSelected = ($currentLang === $lang['lang_code']);
                    ?>
                    <li class="<?php echo $isSelected ? 'active' : ''; ?>">
                        <a href="?lang=<?php echo htmlspecialchars($lang['lang_code']); ?>"
                            class="d-flex flex-align-center">
                            <span
                                class="<?php echo !empty($lang['lang_flag']) ? 'fi ' . htmlspecialchars($lang['lang_flag']) : 'mif-earth'; ?> mr-2"></span>
                            <span><?php echo htmlspecialchars($lang['lang_name'] ?? strtoupper($lang['lang_code'])); ?></span>
                            <?php if ($isSelected): ?>
                                <span class="mif-checkmark ml-auto fg-primary pl-2"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php
    $currentRoute = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
    $isHomeActive = ($currentRoute === '' || $currentRoute === 'main' || $currentRoute === 'main/index');
    $isProfileActive = (isset($_SESSION['user_id']) && $currentRoute === 'user/' . $_SESSION['user_id']);
    $isLoginActive = ($currentRoute === 'user/login');
    $isRegisterActive = ($currentRoute === 'user/register');
    $isUsersListActive = ($currentRoute === 'user/users' || (strpos($currentRoute, 'user') === 0 && !$isProfileActive && !$isLoginActive && !$isRegisterActive));
    $isLangsActive = (strpos($currentRoute, 'lang') === 0);
    $isZonesActive = (strpos($currentRoute, 'zone') === 0);
    $sportTxt = \Core\Language::load('sport');
    $isSeasonsActive = (strpos($currentRoute, 'sport/season') === 0 || strpos($currentRoute, 'sport/seasons') === 0);
    $isPersonsActive = (strpos($currentRoute, 'sport/person') === 0 || strpos($currentRoute, 'sport/persons') === 0);
    $isClubsActive = (strpos($currentRoute, 'sport/club') === 0 || strpos($currentRoute, 'sport/clubs') === 0);
    $isTeamsActive = (strpos($currentRoute, 'sport/team') === 0 || strpos($currentRoute, 'sport/teams') === 0);
    $isCompetitionsActive = (strpos($currentRoute, 'sport/competition') === 0);
    $isSportsActive = ($currentRoute === 'sport/sports' || $currentRoute === 'sport/sport' || strpos($currentRoute, 'sport/sport/') === 0);
    $isSportsGroupActive = ($isSportsActive || $isSeasonsActive);
    $isAdminSystemGroupActive = ($isUsersListActive || $isLangsActive || $isZonesActive);
    ?>

    <ul class="navview-menu pad-second-level" id="side-menu">

        <!-- Compétitions -->
        <li class="<?php echo $isCompetitionsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/competitions">
                <span class="icon"><span class="mif-trophy"></span></span>
                <span class="caption">
                    <?php echo $sportTxt['COMPETITION_NAV_COMPETITIONS'] ?? 'Compétitions'; ?>
                </span>
            </a>
        </li>

        <!-- Clubs -->
        <li class="<?php echo $isClubsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/clubs">
                <span class="icon"><span class="mif-security"></span></span>
                <span class="caption"><?php echo $sportTxt['SPORT_CLUBS_MGT'] ?? 'Clubs'; ?></span>
            </a>
        </li>

        <!-- Équipes -->
        <li class="<?php echo $isTeamsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/teams">
                <span class="icon"><span class="mif-groups"></span></span>
                <span class="caption"><?php echo $sportTxt['SPORT_TEAMS_MGT'] ?? 'Équipes'; ?></span>
            </a>
        </li>

        <!-- Personnes / Acteurs -->
        <li class="<?php echo $isPersonsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/persons">
                <span class="icon"><span class="mif-contacts"></span></span>
                <span class="caption"><?php echo $sportTxt['SPORT_PERSONS_MGT'] ?? 'Personnes / Acteurs'; ?></span>
            </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Groupe : Mon Compte -->
            <li class="item-header"><?php echo $data['txt']['SYS_ACCOUNT'] ?? 'Mon Compte'; ?></li>
            <li class="<?php echo $isProfileActive ? 'active' : ''; ?>">
                <a href="<?php echo URLROOT; ?>/user/<?php echo $_SESSION['user_id']; ?>">
                    <span class="icon"><span class="mif-profile"></span></span>
                    <span class="caption"><?php echo $data['txt']['SYS_MY_PROFILE'] ?? 'SYS_MY_PROFILE'; ?>
                        (<?php echo htmlspecialchars($_SESSION['username']); ?>)</span>
                </a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/user/logout">
                    <span class="icon"><span class="mif-exit"></span></span>
                    <span class="caption"><?php echo $data['txt']['SYS_LOGOUT'] ?? 'SYS_LOGOUT'; ?></span>
                </a>
            </li>

            <?php if (isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles'])): ?>
                <?php
                $userTxt = \Core\Language::load('user');
                $sportTxt = \Core\Language::load('sport');
                $zoneTxt = \Core\Language::load('zone');
                ?>
                <!-- Groupe : Administration -->
                <li class="item-header"><?php echo $data['txt']['SYS_ADMINISTRATION'] ?? 'Administration'; ?></li>

                <!-- Sous-groupe : System -->
                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-cogs"></span></span>
                        <span class="caption"><?php echo $userTxt['USER_MANAGEMENT'] ?? 'System'; ?></span>
                    </a>
                    <ul class="navview-menu" data-role="collapse"
                        data-collapsed="<?php echo $isAdminSystemGroupActive ? 'false' : 'true'; ?>">
                        <li class="<?php echo $isUsersListActive ? 'active' : ''; ?>">
                            <a href="<?php echo URLROOT; ?>/user/users">
                                <span class="icon"><span class="mif-group"></span></span>
                                <span class="caption"><?php echo $userTxt['USER_USERS_LIST'] ?? 'Utilisateurs'; ?></span>
                            </a>
                        </li>
                        <li class="<?php echo $isLangsActive ? 'active' : ''; ?>">
                            <a href="<?php echo URLROOT; ?>/lang/langs">
                                <span class="icon"><span class="mif-language"></span></span>
                                <span class="caption"><?php echo $userTxt['LANG_LANGS_MGT'] ?? 'Langues'; ?></span>
                            </a>
                        </li>
                        <li class="<?php echo $isZonesActive ? 'active' : ''; ?>">
                            <a href="<?php echo URLROOT; ?>/zone/zones">
                                <span class="icon"><span class="mif-earth"></span></span>
                                <span
                                    class="caption"><?php echo $zoneTxt['ZONE_TITLE_ZONES'] ?? 'Zones géographiques'; ?></span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sous-groupe : Sports -->
                <li>
                    <a href="#" class="dropdown-toggle">
                        <span class="icon"><span class="mif-trophy"></span></span>
                        <span class="caption"><?php echo $sportTxt['SPORT_SPORTS_MGT'] ?? 'Sports'; ?></span>
                    </a>
                    <ul class="navview-menu" data-role="collapse"
                        data-collapsed="<?php echo $isSportsGroupActive ? 'false' : 'true'; ?>">
                        <li class="<?php echo $isSportsActive ? 'active' : ''; ?>">
                            <a href="<?php echo URLROOT; ?>/sport/sports">
                                <span class="icon"><span class="mif-trophy"></span></span>
                                <span class="caption"><?php echo $sportTxt['SPORT_SPORTS_MGT'] ?? 'Sports'; ?></span>
                            </a>
                        </li>
                        <li class="<?php echo $isSeasonsActive ? 'active' : ''; ?>">
                            <a href="<?php echo URLROOT; ?>/sport/seasons">
                                <span class="icon"><span class="mif-calendar"></span></span>
                                <span class="caption"><?php echo $sportTxt['SPORT_SEASONS_MGT'] ?? 'Saisons'; ?></span>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <!-- Groupe : Mon Compte (Non connecté) -->
            <li class="item-header"><?php echo $data['txt']['SYS_ACCOUNT'] ?? 'Mon Compte'; ?></li>
            <li class="<?php echo $isLoginActive ? 'active' : ''; ?>">
                <a href="<?php echo URLROOT; ?>/user/login">
                    <span class="icon"><span class="mif-enter"></span></span>
                    <span class="caption"><?php echo $data['txt']['SYS_LOGIN'] ?? 'SYS_LOGIN'; ?></span>
                </a>
            </li>
            <li class="<?php echo $isRegisterActive ? 'active' : ''; ?>">
                <a href="<?php echo URLROOT; ?>/user/register">
                    <span class="icon"><span class="mif-add-person"></span></span>
                    <span class="caption"><?php echo $data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'; ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<!-- Zone de Contenu Principal (navview-content) -->
<div class="navview-content d-flex flex-column min-vh-100">
    <div class="app-bar bg-dark pos-relative z-1 flex-align-center" data-role="appbar" id="app-bar-1">
        <a href="<?php echo URLROOT; ?>/"
            class="button square bg-transparent fg-white bd-white border-radius-4 p-2 ml-2 d-flex flex-align-center flex-justify-center"
            style="width: 38px; height: 38px; text-decoration: none;"
            title="<?php echo htmlspecialchars($data['txt']['SYS_HOME'] ?? 'Accueil'); ?>">
            <span class="mif-security mif-2x"></span>
        </a>
        <h1 class="m-0 enlarge-1 pl-3 text-weight-normal">
            <a href="<?php echo URLROOT; ?>/" class="fg-white" style="text-decoration:none;"><?php echo SITENAME; ?></a>
        </h1>
    </div>