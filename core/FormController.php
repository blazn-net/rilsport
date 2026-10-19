<?php
namespace Core;

/**
 * Contrôleur parent universel pour les pages de type Form / View (fiches individuelles)
 * Projet RIL Sport
 */
abstract class FormController extends Controller {

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
     * Résout une entité par son code métier en priorité, puis par ID avec redirection canonique 302
     *
     * @param callable $byCodeFn Callable de recherche par code métier fn($code)
     * @param callable $byIdFn Callable de recherche par identifiant numérique fn($id)
     * @param string|int|null $id Identifiant passé dans l'URL (code ou ID)
     * @param string $routeBase Route de base pour la redirection (ex: 'sport/season')
     * @param string $listRoute Route de repli si l'entité est introuvable (ex: 'sport/seasons')
     * @param string|null $action Action demandée (ex: 'edit' ou null)
     * @param string $codeKey Nom du champ code dans l'entité (défaut 'code')
     * @return array|object|null L'entité trouvée (ou redirection automatique)
     */
    protected function resolveEntity(
        callable $byCodeFn,
        callable $byIdFn,
        $id,
        string $routeBase,
        string $listRoute,
        ?string $action = null,
        string $codeKey = 'code'
    ) {
        if ($id === null || $id === '') {
            return null;
        }

        // 1. Recherche par code en priorité
        $entity = $byCodeFn($id);

        // 2. Si non trouvé par code et que l'identifiant est numérique, recherche par ID
        if (!$entity && is_numeric($id)) {
            $entity = $byIdFn((int)$id);
            if ($entity) {
                $entityArr = is_object($entity) ? (array)$entity : $entity;
                // Si l'entité possède un code différent de cet ID numérique, redirection canonique 302
                if (!empty($entityArr[$codeKey]) && (string)$entityArr[$codeKey] !== (string)$id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                    $target = rtrim($routeBase, '/') . '/' . ($action === 'edit' ? 'edit/' : '') . $entityArr[$codeKey];
                    return $this->redirect($target);
                }
            }
        }

        // 3. Si introuvable, redirection vers la page List
        if (!$entity) {
            return $this->redirect($listRoute);
        }

        return $entity;
    }

    /**
     * Détermine le mode d'affichage (view, edit, add)
     */
    protected function resolveMode($id, ?string $action = null, bool $isPost = false, ?bool $canEdit = null): string {
        $allowed = ($canEdit !== null) ? $canEdit : $this->isAdmin();
        return $id ? ((($action === 'edit' || $isPost) && $allowed) ? 'edit' : 'view') : 'add';
    }

    /**
     * Rendu standardisé de la fiche Form / View (header, navview, vue de contenu, footer)
     */
    protected function renderForm(string $viewPath, array $data): void {
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_error']);

        $this->view('system/header', $data);
        $this->view('system/navview', $data);
        $this->view($viewPath, $data);
        $this->view('system/footer', $data);
    }

    /**
     * Accès direct au formulaire d'édition (redirection vers index avec action 'edit')
     */
    public function edit($id = null) {
        return $this->index($id, 'edit');
    }
}
