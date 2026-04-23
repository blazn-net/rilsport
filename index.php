<?php
// Point d'entrée principal de l'application
session_start();

// Gestion de la langue
if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en', 'es'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Inclusion de l'initialiseur
require_once 'core/init.php';

use Core\App;

// Initialisation de la classe App (Routeur)
$app = new App();
