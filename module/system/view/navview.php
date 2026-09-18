<!-- Volet latéral NavView -->
<div class="navview-pane">
    <div class="logo-container">
        <button class="pull-button">
            <span class="mif-menu"></span>
        </button>
        <a href="<?php echo URLROOT; ?>/" class="d-flex flex-align-center text-logo bg-transparent" style="width: calc(100% - 54px);">
            <div class="avatar bg-white border-radius-half d-flex flex-justify-center flex-align-center mr-2" style="width: 30px; height: 30px; min-width: 30px;">
                <span class="mif-earth mif-2x fg-dark"></span>
            </div>
            <div class="enlarge-1 text-weight-9 text-ellipsis fg-default"><?php echo SITENAME; ?></div>
        </a>
    </div>

    <!-- Sélecteur de langue -->
    <div class="p-2 border-bottom bd-light">
        <div class="d-flex flex-justify-around flex-wrap" style="gap: 5px;">
            <?php
            $systemLangs = \Core\Language::getSystemLanguages();
            $currentLang = $_SESSION['lang'] ?? DEFAULT_LANG;
            foreach ($systemLangs as $lang):
                $isActive = ($currentLang === $lang['lang_code']);
                $btnClass = $isActive ? 'primary' : 'light';
                ?>
                <a href="?lang=<?php echo htmlspecialchars($lang['lang_code']); ?>"
                    class="button flex-1 <?php echo $btnClass; ?>" style="min-width: 45px; padding: 2px 5px;"
                    title="<?php echo htmlspecialchars($lang['lang_name'] ?? strtoupper($lang['lang_code'])); ?>">
                    <span
                        class="<?php echo !empty($lang['lang_flag']) ? 'fi ' . htmlspecialchars($lang['lang_flag']) : 'mif-earth'; ?>"></span>
                    <?php echo htmlspecialchars(strtoupper($lang['lang_code'])); ?>
                </a>
            <?php endforeach; ?>
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

        <!-- Accueil (en 1er, sans le header Navigation) -->
        <li class="<?php echo $isHomeActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/">
                <span class="icon"><span class="mif-home"></span></span>
                <span class="caption"><?php echo $data['txt']['SYS_HOME'] ?? 'SYS_HOME'; ?></span>
            </a>
        </li>

        <!-- Clubs (au même niveau qu'Accueil) -->
        <li class="<?php echo $isClubsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/clubs">
                <span class="icon"><span class="mif-security"></span></span>
                <span class="caption"><?php echo $sportTxt['SPORT_CLUBS_MGT'] ?? 'Clubs'; ?></span>
            </a>
        </li>

        <!-- Équipes (au même niveau qu'Accueil et Clubs) -->
        <li class="<?php echo $isTeamsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/teams">
                <span class="icon"><span class="mif-groups"></span></span>
                <span class="caption"><?php echo $sportTxt['SPORT_TEAMS_MGT'] ?? 'Équipes'; ?></span>
            </a>
        </li>

        <!-- Compétitions -->
        <li class="<?php echo $isCompetitionsActive ? 'active' : ''; ?>">
            <a href="<?php echo URLROOT; ?>/sport/competitions">
                <span class="icon"><span class="mif-trophy"></span></span>
                <span class="caption"><?php echo $sportTxt['COMPETITION_NAV_COMPETITIONS'] ?? 'Compétitions'; ?></span>
            </a>
        </li>

        <!-- Personnes / Acteurs (au même niveau qu'Accueil, Clubs et Équipes) -->
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
                    <span class="caption"><?php echo $data['txt']['SYS_MY_PROFILE'] ?? 'SYS_MY_PROFILE'; ?> (<?php echo htmlspecialchars($_SESSION['username']); ?>)</span>
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
                    <ul class="navview-menu" data-role="collapse" data-collapsed="<?php echo $isAdminSystemGroupActive ? 'false' : 'true'; ?>">
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
                                <span class="caption"><?php echo $zoneTxt['ZONE_TITLE_ZONES'] ?? 'Zones géographiques'; ?></span>
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
                    <ul class="navview-menu" data-role="collapse" data-collapsed="<?php echo $isSportsGroupActive ? 'false' : 'true'; ?>">
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
                    <span class="icon"><span class="mif-user-plus"></span></span>
                    <span class="caption"><?php echo $data['txt']['SYS_REGISTER'] ?? 'SYS_REGISTER'; ?></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<!-- Zone de Contenu Principal (navview-content) -->
<div class="navview-content d-flex flex-column min-vh-100">
    <div class="app-bar bg-dark pos-relative z-1 flex-align-center" data-role="appbar" id="app-bar-1">
        <button class="pull-button bg-transparent fg-white bd-none p-2 ml-2 c-pointer d-none-md" title="<?php echo htmlspecialchars($data['txt']['SYS_MENU'] ?? 'Menu'); ?>">
            <span class="mif-menu mif-2x"></span>
        </button>
        <h1 class="m-0 enlarge-1 pl-3 text-weight-normal">
            <a href="<?php echo URLROOT; ?>" class="fg-white" style="text-decoration:none;"><?php echo SITENAME; ?></a>
        </h1>
    </div>