<?php
// Paramètres de configuration globaux
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'rilsport_db');
define('DB_USER', 'postgres');
define('DB_PASS', 'YOUR_PASSWORD_HERE');

// URL de base de l'application (modifiez si nécessaire)
define('URLROOT', 'http://localhost/rilsport');
define('SITENAME', 'RIL Sport');
define('SITEDESCRIPTION', 'Results Infos Live');

// Langue par défaut
define('DEFAULT_LANG', 'fr');

// Module Zone — API GeoNames (https://www.geonames.org/login)
// Créez un compte gratuit et activez le web service dans vos paramètres.
define('GEONAMES_USERNAME', 'blazn.net');
