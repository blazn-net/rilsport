<?php
namespace Core;

/**
 * Contrôleur parent universel pour les pages de type List (tableaux de données)
 * Projet RIL Sport
 */
abstract class ListController extends Controller {

    /**
     * Vérifie si l'utilisateur actuellement connecté possède le rôle admin
     */
    protected function isAdmin(): bool {
        return isset($_SESSION['roles']) && is_array($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);
    }

    /**
     * Charge et fusionne les dictionnaires de langues spécifiés
     * Par défaut charge 'system' et 'user' en plus des modules fournis.
     */
    protected function loadLanguages(array $modules = []): array {
        $allModules = array_unique(array_merge(['system', 'user'], $modules));
        $merged = [];
        foreach ($allModules as $mod) {
            $merged = array_merge($merged, $this->loadLanguage($mod));
        }
        return $merged;
    }

    /**
     * Prépare le tableau standard de données pour les vues List
     */
    protected function prepareListData(array $txt, string $title, array $extraData = []): array {
        $search = trim($_GET['search'] ?? '');
        $data = array_merge([
            'txt'     => $txt,
            'title'   => $title . ' - ' . SITENAME,
            'search'  => $search,
            'isAdmin' => $this->isAdmin(),
            'message' => $_SESSION['flash_message'] ?? '',
            'error'   => $_SESSION['flash_error'] ?? '',
        ], $extraData);

        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        return $data;
    }

    /**
     * Rendu standardisé de la page List (header, navview, vue de contenu, footer)
     */
    protected function renderList(string $viewPath, array $data): void {
        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view($viewPath, $data);
        $this->view('system/footer', $data);
    }
}
