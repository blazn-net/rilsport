<?php
// Fichier d'initialisation (bootstrapper)

// Charger la configuration
require_once __DIR__ . '/../config/config.php';

// Autoloader PSR-4
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/../';
    $parts = explode('\\', $class);
    
    // Convert module and directory names to lowercase (e.g. Module\System\Controller -> module/system/controller)
    for ($i = 0; $i < count($parts) - 1; $i++) {
        $parts[$i] = strtolower($parts[$i]);
    }
    
    $file = $base_dir . implode('/', $parts) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
