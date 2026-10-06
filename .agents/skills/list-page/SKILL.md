---
name: list-page
description: >
  Standards complets pour créer ou modifier une page List (tableau) dans RIL Sport.
  Lire OBLIGATOIREMENT avant toute création ou modification d'une page List.
  Couvre : structure $listConfig, colonnes, actions, recherche GET, contrôleur, modèle, conventions UI, mobile, checklist.
---

# Skill : Pages List — RIL Sport

## Quand utiliser ce skill

Activer ce skill dès que la tâche implique :
- Créer une **nouvelle page List** (tableau de données)
- Modifier une **page List existante**
- Ajouter une liste dans un **nouveau module**

> Pour les pages Form/View (fiche individuelle), utiliser le skill `form-page`.

---

## 1. Principe

Toute page List **DOIT** utiliser `module/system/view/common/list_template.php`.
Ce template centralise : en-tête, alertes flash, barre de recherche GET, tableau responsive Metro UI,
colonnes admin-only (Statut, Actions), pagination et contrôles scroll horizontal mobile.

---

## 2. Structure du fichier Vue (`[objets].php`)

```php
<?php
/**
 * Vue : Liste des [Objets]
 * Utilise le template parent universel list_template.php
 */

// [Optionnel] Fonctions render personnalisées
$renderNom = function ($val, $row, $data) {
    return '<a href="' . URLROOT . '/module/objet/' . (int)$row['id'] . '" class="fg-primary text-bold">'
         . htmlspecialchars($val) . '</a>';
};

// [Optionnel] Filtres GET multiples → customFiltersHtml
// ob_start(); ?>
// <form method="GET" action="..."> ... </form>
// <?php $filtersHtml = ob_get_clean();

$listConfig = [
    'title'          => $data['txt']['MODULE_OBJS_MGT'] ?? 'Objets',
    'icon'           => 'mif-icon',
    'addUrl'         => URLROOT . '/module/objet',
    'addBtnText'     => $data['txt']['MODULE_ADD_OBJ_BTN'] ?? 'Ajouter un objet', // tooltip title seulement
    'listUrl'        => URLROOT . '/module/objets',   // active la recherche GET simple
    'searchValue'    => $data['search'] ?? '',
    'searchPlaceholder' => 'Code, nom...',
    'items'          => $data['objets'] ?? [],
    'idField'        => 'id',
    'statusField'    => 'status_id',
    'columns'        => [ /* voir §3 */ ],
    'actions'        => [ /* voir §4 */ ],
];

require 'module/system/view/common/list_template.php';
```

---

## 3. Clés de `$listConfig`

| Clé | Requis | Description |
|-----|--------|-------------|
| `title` | OUI | Titre `<h2>` |
| `icon` | OUI | Icône Metro UI (ex: `mif-calendar`) |
| `addUrl` | OUI | URL bouton "+ Ajouter" (admin only) |
| `addBtnText` | OUI | Infobulle `title` uniquement. Le texte affiché = toujours **"Ajouter"** (`SYS_BTN_ADD`) |
| `listUrl` | Sauf custom | URL de la page → active la recherche GET simple |
| `searchValue` | Sauf custom | `$data['search'] ?? ''` |
| `searchPlaceholder` | NON | Placeholder du champ |
| `showSearch` | NON | `false` si filtres gérés par `customFiltersHtml` |
| `customFiltersHtml` | NON | HTML d'un `<form>` GET complet avec filtres multiples |
| `items` | OUI | Tableau de données |
| `idField` | OUI | Clé primaire (défaut: `id`) |
| `statusField` | OUI | Champ statut (défaut: `status_id`) |
| `columns` | OUI | Définition des colonnes (§3) |
| `actions` | OUI | URLs des actions admin (§4) |

---

## 4. Définition du Grid et des colonnes obligatoires

Chaque page de type List **DOIT** posséder un tableau (grid) contenant les colonnes suivantes :

| N° | Colonne | Rôle | Configuration / Rendu |
|----|---------|------|------------------------|
| 1 | **`#` (id)** | Identifiant unique | `['field' => 'id', 'label' => '#', 'type' => 'text']` |
| 2 | **`Icône ou Logo`** | Visuel représentatif (logo club/compétition, icône Metro UI `mif-...`, drapeau ou avatar) | `['field' => 'icon', 'label' => 'Icône', 'render' => ...]` ou `renderLogo` |
| 3 | **`Code`** | Code court unique de l'entité | `['field' => 'code', 'label' => 'Code', 'type' => 'code']` |
| 4 | **`Nom`** | Nom de l'entité avec **lien de consultation** vers la fiche View | `['field' => 'name', 'label' => 'Nom', 'type' => 'link', 'linkUrl' => URLROOT . '/module/objet/{id}']` |
| 5 | **`Colonnes persos`** | Informations métier spécifiques (ex: dates début/fin, rôle, localisation, sport, etc.) | Colonnes text, date, badge ou render personnalisé |
| 6 | **`Statut`** | Badge Actif / Inactif (visible **uniquement pour les administrateurs**) | Géré automatiquement par `list_template.php` en fin de tableau |
| 7 | **`Actions`** | Boutons d'action (Modifier, Désactiver/Activer, Supprimer — visible **admins uniquement**) | Géré automatiquement par `list_template.php` via la clé `'actions'` |

> **Règle stricte** : **Ne pas toucher à la zone de filtres ni à la pagination**. Elles sont gérées automatiquement par `list_template.php`.

### Exemple de configuration `$listConfig['columns']` :

```php
'columns' => [
    // 1. # (id)
    [
        'field'       => 'id',
        'label'       => '#',
        'type'        => 'text',
        'headerStyle' => 'width:50px;text-align:center;',
        'cellStyle'   => 'text-align:center;',
    ],

    // 2. Icône ou Logo
    [
        'field'       => 'icon',
        'label'       => 'Icône',
        'headerStyle' => 'width:60px;text-align:center;',
        'cellStyle'   => 'text-align:center;',
        'render'      => function ($val, $row) {
            $icon = !empty($val) ? htmlspecialchars($val) : 'mif-trophy';
            return '<span class="' . $icon . ' mif-2x"></span>';
        },
    ],

    // 3. Code
    [
        'field' => 'code',
        'label' => 'Code',
        'type'  => 'code',
    ],

    // 4. Nom (Lien vers fiche View en consultation)
    [
        'field'     => 'name',
        'label'     => 'Nom',
        'type'      => 'link',
        'linkUrl'   => URLROOT . '/module/objet/{id}',
        'linkTitle' => 'Consulter la fiche',
    ],

    // 5. Colonnes persos (métier)
    [
        'field' => 'description',
        'label' => 'Description',
        'type'  => 'text',
    ],
],
```


---

## 5. Actions admin (colonne Actions)

```php
'actions' => [
    'editUrl'         => URLROOT . '/module/objet/{id}',
    'viewUrl'         => URLROOT . '/module/objet/{id}',          // bouton Consulter (non-admins)
    'disableUrl'      => URLROOT . '/module/objet/delete/{id}',
    'activateUrl'     => URLROOT . '/module/objet/activate/{id}',
    'deleteUrl'       => URLROOT . '/module/objet/delete/{id}?force=1',
    'disableConfirm'  => 'Désactiver cet élément ?',
    'activateConfirm' => 'Réactiver cet élément ?',
    'deleteConfirm'   => 'Supprimer définitivement ?',
],
```

Les colonnes **Statut** et **Actions** sont masquées automatiquement pour les non-admins par le template.

Couleurs des boutons d'action :
- Modifier    → `button small info`    + `mif-pencil`
- Désactiver  → `button small warning` + `mif-cancel`
- Réactiver   → `button small success` + `mif-checkmark`
- Supprimer   → `button small alert`   + `mif-bin`
- Consulter   → `button small primary` + `mif-eye` (non-admins)

---

## 6. Contrôleur List

```php
public function index() {
    $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('module'));
    $search  = trim($_GET['search'] ?? '');
    $items   = $this->model->getAllObjects($search);
    $isAdmin = isset($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

    $data = [
        'txt'     => $txt,
        'title'   => ($txt['MODULE_OBJS_MGT'] ?? 'Objets') . ' - ' . SITENAME,
        'objets'  => $items,
        'search'  => $search,          // TOUJOURS inclure pour le template
        'isAdmin' => $isAdmin,
        'message' => $_SESSION['flash_message'] ?? '',
        'error'   => $_SESSION['flash_error'] ?? '',
    ];
    unset($_SESSION['flash_message'], $_SESSION['flash_error']);

    $this->view('system/header', $data);
    $this->view('system/navview', $data);
    $this->view('module/objets', $data);
    $this->view('system/footer', $data);
}
```

---

## 7. Modèle List

```php
public function getAllObjects(string $search = '') {
    if ($search !== '') {
        $like = '%' . $search . '%';
        $this->db->query("
            SELECT * FROM t_module_objet
            WHERE code LIKE :s1 OR name LIKE :s2
            ORDER BY id ASC
        ");
        $this->db->bind(':s1', $like);
        $this->db->bind(':s2', $like);
    } else {
        $this->db->query("SELECT * FROM t_module_objet ORDER BY id ASC");
    }
    return $this->db->resultSet();
}
```

---

## 8. Conventions UI

### Bouton "+ Ajouter"
- Classe : **`button info`** (bleu — PAS `primary` qui est rouge dans ce thème)
- Libellé affiché : **toujours "Ajouter"** via `SYS_BTN_ADD`
- `addBtnText` = infobulle `title` uniquement
- Icône : `mif-plus` + `<span class="btn-text">Ajouter</span>`
- Visible admins seulement
- Mobile ≤ 414px : icône seule (`.btn-text` masqué)

### Badges statut
- `status_id = 1` → `badge success`
- `status_id = 2` → `badge secondary`
- `status_id = 3` → `badge warning`

### Boutons Filtrer / Réinitialiser
- Filtrer       : `button primary` + `mif-filter`
- Réinitialiser : `button secondary` + `mif-reload`

---

## 9. Navigation (`navview.php`)

Tout nouvel objet List **DOIT** avoir un lien dans `module/system/view/navview.php` :
- Libellé : **pluriel direct** (`Sports`, `Clubs`, `Utilisateurs`)
- **JAMAIS** : `Gestion Sports`, `Liste des Clubs`

---

## 10. Checklist avant livraison

- [ ] `[objets].php` utilise `require list_template.php`
- [ ] Grid standard : colonnes `#`, `Icône ou Logo`, `Code`, `Nom`, `Colonnes persos`, `Statut` (admin), `Actions` (admin)
- [ ] Zone de filtres et pagination intactes (gérées par le template)
- [ ] `listUrl` défini → recherche GET active
- [ ] Contrôleur lit `$_GET['search']` et passe `$data['search']`
- [ ] Modèle `getAllXxx(string $search = '')` supporte LIKE
- [ ] Bouton "+ Ajouter" : `button info`, libellé "Ajouter", admin-only
- [ ] Clic sur le nom → fiche **View** (lecture), jamais Edit
- [ ] Colonnes Statut & Actions : admin-only (géré par le template)
- [ ] Lien `navview.php` (pluriel, sans "Gestion")
- [ ] Mobile : icône-only pour les boutons, scroll horizontal tableau

