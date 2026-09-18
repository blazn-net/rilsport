<?php
namespace Core;

/*
 * Classe de Routage App Core
 * Formatte l'URL et instancie le bon Contrôleur
 */
class App
{
    protected $currentModule = 'main';
    protected $currentController = 'Main';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $urlOrig = $this->getUrl();
        if (empty($urlOrig)) {
            $urlOrig = ['main', 'main', 'index'];
        }
        $url = $urlOrig;

        // 1. Module Routing
        if (isset($url[0]) && is_dir('module/' . $url[0] . '/controller')) {
            $this->currentModule = $url[0];
            unset($url[0]);
            $url = $url ? array_values($url) : [];

            // 2. Load module controller
            if (isset($url[0])) {
                // Alias : mappe les pages nommées vers leur controller
                // Ex: /user/login → Controller Auth
                $aliases = [
                    'user' => [
                        'login'    => 'Auth',
                        'register' => 'Auth',
                        'logout'   => 'Auth',
                    ]
                ];

                $candidateClass = str_replace('-', '', ucwords($url[0], '-'));
                $candidateFile  = 'module/' . $this->currentModule . '/controller/' . $candidateClass . '.php';

                if (isset($aliases[$this->currentModule][$url[0]])) {
                    // On garde $url[0] intact : il sera récupéré comme méthode juste après
                    $this->currentController = $aliases[$this->currentModule][$url[0]];
                } elseif (file_exists($candidateFile)) {
                    // Contrôleur spécifique existant (ex: /lang/langs → Controller Langs)
                    $this->currentController = $candidateClass;
                    unset($url[0]);
                } else {
                    // Contrôleur par défaut du module (ex: /lang/en ou /user/1 → Controller Lang / User, param 'en' / '1')
                    $this->currentController = ucwords($this->currentModule);
                }
            } else {
                $this->currentController = ucwords($this->currentModule);
            }
        }

        $className = '\\Module\\' . ucfirst($this->currentModule) . '\\Controller\\' . $this->currentController;

        if (class_exists($className)) {
            $this->currentController = new $className;
        } else {
            $txt = \Core\Language::load('system');
            die(sprintf($txt['ERR_CONTROLLER_NOT_FOUND'] ?? 'ERR_CONTROLLER_NOT_FOUND', $className));
        }

        // Re-index
        $url = $url ? array_values($url) : [];

        // Check for method in controller
        if (isset($url[0])) {
            if (method_exists($this->currentController, $url[0])) {
                $this->currentMethod = $url[0];
                unset($url[0]);
            }
        }

        // Setup params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
