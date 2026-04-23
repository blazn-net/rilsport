<?php
namespace Core;

/*
 * Contrôleur de Base
 * Charge le modèle et les vues
 * Supporte uniquement l'architecture par Modules avec Namespaces
 */
class Controller {

    // Charge le modèle
    public function model($modelPath) {
        $parts = explode('/', $modelPath);
        if (count($parts) >= 2) {
            $module = $parts[0];
            $modelName = array_pop($parts);
            
            $className = '\\Module\\' . ucfirst($module) . '\\Model\\' . ucfirst($modelName);
            return new $className();
        }
        
        $txt = Language::load('system');
        die($txt['ERR_INVALID_MODEL_FORMAT'] ?? 'ERR_INVALID_MODEL_FORMAT');
    }

    // Charge le dictionnaire multilingue complet (global + contextuel)
    public function loadLanguage($module) {
        $globalText = Language::load('system');
        $moduleText = ($module !== 'system') ? Language::load($module) : [];
        return array_merge($globalText, $moduleText);
    }

    // Charge la vue
    public function view($view, $data = []) {
        $parts = explode('/', $view);
        if (count($parts) >= 2) {
            $module = $parts[0];
            $viewPath = implode('/', array_slice($parts, 1));
            $path = 'module/' . $module . '/view/' . $viewPath . '.php';
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
        $txt = Language::load('system');
        die(($txt['ERR_VIEW_NOT_FOUND'] ?? 'ERR_VIEW_NOT_FOUND') . $view);
    }

    // Méthode utilitaire de redirection
    public function redirect($page) {
        header('Location: ' . URLROOT . '/' . $page);
        exit;
    }
}
