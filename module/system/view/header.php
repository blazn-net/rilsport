<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'fr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- Configuration Locale de Metro UI -->
    <meta name="metro4:locale" content="<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') ? 'en-US' : ((isset($_SESSION['lang']) && $_SESSION['lang'] == 'es') ? 'es-MX' : 'fr-FR'); ?>">
    <title><?php echo isset($data['title']) ? $data['title'] : SITENAME; ?></title>
    <!-- Metro UI v5 CSS et Icônes -->
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/current/metro.css">
    <link rel="stylesheet" href="https://cdn.metroui.org.ua/current/icons.css">
    <!-- Emojis/Drapeaux (Windows ne les affichant pas nativement) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.1.0/css/flag-icons.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/style.css">
</head>
<body class="bg-light">

    <div class="app-bar pos-absolute bg-dark z-1" data-role="appbar" id="app-bar-1">
        <button class="app-bar-item c-pointer" id="sidebar-toggle" title="<?php echo htmlspecialchars($data['txt']['SYS_MENU'] ?? 'Menu'); ?>">
            <span class="mif-menu mif-2x fg-white"></span>
        </button>
        <h1 class="m-0" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); white-space: nowrap; pointer-events: none;">
            <a href="<?php echo URLROOT; ?>" class="fg-white" style="text-decoration:none; pointer-events: auto;"><?php echo SITENAME; ?></a>
        </h1>
    </div>
