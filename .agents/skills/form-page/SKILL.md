---
name: form-page
description: >
  Standards complets pour créer ou modifier une page Form/View (fiche individuelle) dans RIL Sport.
  Lire OBLIGATOIREMENT avant toute création ou modification d'une page Form ou View.
  Couvre : le template parent universel form_template.php, les 3 modes (add/view/edit), en-tête, mode View sans input, formulaire Edit/Add, boutons, contrôleur, modèle, checklist.
---

# Skill : Pages Form & View — RIL Sport

## Quand utiliser ce skill

Activer ce skill dès que la tâche implique :
- Créer ou modifier une **page Form** (formulaire de création/édition)
- Créer ou modifier une **page View** (fiche consultation)
- Gérer un **contrôleur d'objet singulier** (create/read/update)

> Pour les pages List (tableau), utiliser le skill `list-page`.

---

## 1. Principe : Template Parent Universel `form_template.php`

Toute fiche objet est gérée dans **un seul fichier** `[objet].php` qui hérite du template parent :
`module/system/view/common/form_template.php`

Ce template centralise et standardise :
- L'en-tête (titre selon mode, bouton « Modifier » pour admin en view, bouton « Retour » universel).
- Les alertes flash (`message` et `error`).
- Le mode **VIEW** : card blanche, avatar/logo circulaire coloré, nom, code monospace, badge statut (vert Actif / gris Inactif), divider, corps de lecture, et panneau d'audit déplié par défaut.
- Le mode **EDIT / ADD** : card blanche, conteneur `<form>`, panneau d'audit replié par défaut (en `edit`), boutons de validation verts (`button success`) et annulation neutres (`button secondary`).

---

## 2. Les 3 modes dans un seul fichier

| Mode | Accès | Description |
|------|-------|-------------|
| `view` | Tous | Fiche consultation, lecture seule, **aucun input** |
| `edit` | Admin | Formulaire pré-rempli, modification (Code verrouillé) |
| `add` | Admin | Formulaire vide, création (Code requis) |

Le contrôleur détermine le mode et le passe dans `$data['mode']`.

---

## 3. Structure type d'un fichier Vue (`[objet].php`)

```php
<?php
/**
 * Vue : [Objet] (Consultation View & Formulaire Add/Edit)
 * Utilise le template parent universel form_template.php
 */

$item = $data['objet'] ?? null;

$formConfig = [
    'mode'       => $data['mode'] ?? 'view',
    'item'       => $item,
    'icon'       => 'mif-calendar',
    'viewTitle'  => 'Fiche : ' . htmlspecialchars($item->name ?? ''),
    'editTitle'  => $data['txt']['MODULE_EDIT_OBJ_TITLE'] ?? 'Modifier',
    'addTitle'   => $data['txt']['MODULE_ADD_OBJ_TITLE'] ?? 'Ajouter',
    'backUrl'    => URLROOT . '/module/objets',
    'editUrl'    => isset($item->id) ? URLROOT . '/module/objet/edit/' . (int)$item->id : null,
    'cancelUrl'  => isset($item->id) ? URLROOT . '/module/objet/' . (int)$item->id : URLROOT . '/module/objets',
    'formAction' => URLROOT . '/module/objet' . (($data['mode'] === 'edit' && isset($item->id)) ? '/' . (int)$item->id : ''),

    // Avatar / Icône en mode View (optionnel, prend $icon par défaut)
    'viewAvatarIcon' => 'mif-calendar',

    // ── Mode VIEW : Consultation pure (Zéro input) ──────────────────────────
    'viewContent' => function($item, $data) { ?>
        <div class="row">
            <div class="cell-md-6 mb-3">
                <div class="p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <div class="text-muted text-upper text-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                        <span class="mif-tag fg-emerald mr-1"></span> Libellé du champ
                    </div>
                    <div class="mt-1 text-bold" style="font-size: 18px; color: #1e293b;">
                        <?php echo !empty($item->field) ? htmlspecialchars($item->field) : '—'; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php },

    // ── Mode EDIT / ADD : Formulaire interactif (Admin) ──────────────────────
    'formContent' => function($item, $data, $mode) { ?>
        <div class="form-group">
            <label class="text-bold"><?php echo $data['txt']['MODULE_CODE_LABEL'] ?? 'Code'; ?></label>
            <input type="text" name="code" data-role="input" placeholder="ex: CODE_OBJET" maxlength="50"
                   value="<?php echo htmlspecialchars($item->code ?? ''); ?>"
                   <?php echo ($mode === 'edit') ? 'readonly' : 'required'; ?>>
            <?php if ($mode === 'edit'): ?>
                <small class="fg-gray d-block mt-1"><span class="mif-lock mr-1"></span> Le code ne peut plus être modifié après la création.</small>
            <?php else: ?>
                <small class="fg-gray d-block mt-1">Identifiant unique (lettres, chiffres, tirets).</small>
            <?php endif; ?>
        </div>

        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['MODULE_NAME_LABEL'] ?? 'Nom'; ?></label>
            <input type="text" name="name" data-role="input" placeholder="ex: Nom complet" maxlength="100"
                   value="<?php echo htmlspecialchars($item->name ?? ''); ?>" required>
        </div>

        <?php if ($mode === 'edit'): ?>
        <div class="form-group mt-3">
            <label class="text-bold"><?php echo $data['txt']['SYS_STATUS_LABEL'] ?? 'Statut'; ?></label>
            <select name="status_id" data-role="select">
                <option value="1" <?php echo (isset($item->status_id) && (int)$item->status_id === 1) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SYS_STATUS_ACTIVE'] ?? 'Actif'; ?>
                </option>
                <option value="2" <?php echo (isset($item->status_id) && (int)$item->status_id === 2) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SYS_STATUS_INACTIVE'] ?? 'Inactif'; ?>
                </option>
            </select>
        </div>
        <?php endif; ?>
    <?php }
];

require 'module/system/view/common/form_template.php';
```

---

## 4. Clés de configuration `$formConfig`

| Clé | Requis | Description |
|-----|--------|-------------|
| `mode` | OUI | `'view'`, `'edit'`, ou `'add'` (défaut: `$data['mode']`) |
| `item` | OUI | Entité métier (objet ou tableau) |
| `icon` | OUI | Icône Metro UI principale (ex: `mif-calendar`, `mif-trophy`) |
| `viewTitle` | Recommandé | Titre en consultation (ex: `'Fiche : ' . $item->name`) |
| `editTitle` | Recommandé | Titre en édition (ex: `'Modifier la saison'`) |
| `addTitle` | Recommandé | Titre en création (ex: `'Ajouter une saison'`) |
| `backUrl` | OUI | URL du bouton Retour en en-tête (ex: `URLROOT . '/module/objets'`) |
| `editUrl` | Si view | URL d'édition pour le bouton Modifier en mode view (`/module/objet/edit/{id}`) |
| `cancelUrl` | Optionnel | URL du bouton Annuler (défaut : vue en edit, ou liste en add) |
| `formAction` | OUI (edit/add) | Cible `action="..."` du formulaire POST |
| `viewAvatarIcon` | Optionnel | Icône du cercle avatar en view (défaut: `icon`) |
| `viewAvatarHtml` | Optionnel | HTML sur-mesure pour logo/image dans l'avatar en view |
| `viewContent` | OUI (view) | `function($item, $data)` générant les champs de consultation |
| `formContent` | OUI (form) | `function($item, $data, $mode)` générant les inputs du formulaire |
| `showAudit` | Optionnel | `true` par défaut, affiche le panneau d'audit automatique |

---

## 5. Mode VIEW — Consultation (règles strictes)

- **ZÉRO balise d'entrée** : Interdiction totale de `<input>`, `<select>`, `<textarea>`, même `readonly` ou `disabled`.
- **ZÉRO bouton `submit`**.
- Données présentées sous forme de **blocs d'information stylisés** (`style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;"`), textes avec icônes colorées, et badges.
- **Panneau d'audit** : Automatiquement affiché et **déplié par défaut** (`data-collapsed="false"`).
- **Bouton Modifier** : Présent en haut à droite de l'en-tête pour les administrateurs (`button info`, bleu, `mif-pencil`).

---

## 6. Mode EDIT / ADD — Formulaire (ergonomie)

- **Champs dans une card** : Formulaire encapsulé dans une carte propre avec padding.
- **Champ Code** :
  - En mode `edit` : `readonly`, avec avertissement `<small><span class="mif-lock"></span> Code non modifiable.</small>`.
  - En mode `add` : `required`, actif pour saisie initiale.
- **Champ Statut** : Présent uniquement en mode `edit` via `<select name="status_id">`.
- **Panneau d'audit** : Affiché en mode `edit` uniquement, et **replié par défaut** (`data-collapsed="true"`).
- **Boutons standardisés en pied de formulaire** :
  - Validation : **`button success` (vert)** avec disquette `mif-floppy-disk` (« Enregistrer » en add, « Mettre à jour » en edit).
  - Annulation : **`button secondary`** avec icône `mif-cancel` (« Annuler »).

---

## 7. Conventions Boutons — Form & View

| Bouton | Classe | Icône | Contexte | Destination |
|--------|--------|-------|---------|-------------|
| **Modifier** | `button info` (bleu) | `mif-pencil` | En-tête, mode View, admin only | `/module/objet/edit/{id}` |
| **Retour** | `button` (neutre) | `mif-arrow-left` | En-tête, tous modes | `/module/objets` |
| **Enregistrer** | `button success` (vert) | `mif-floppy-disk` | Pied form, mode Add | Soumission POST |
| **Mettre à jour** | `button success` (vert) | `mif-floppy-disk` | Pied form, mode Edit | Soumission POST |
| **Annuler** | `button secondary` (neutre) | `mif-cancel` | Pied form, Edit / Add | `/module/objet/{id}` (edit) ou `/module/objets` (add) |

---

## 8. Contrôleur Form / View

```php
public function index($id = null, $action = null) {
    $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('module'));
    $isAdmin = isset($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

    // Seuls les admins peuvent créer
    if (!$id && !$isAdmin) {
        die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
    }

    $itemData = null;
    if ($id !== null && $id !== '') {
        $itemData = $this->model->getObjectById((int)$id);
        if (!$itemData) {
            return $this->redirect('module/objets');
        }
    }

    // Détermination robuste du mode (maintient edit en cas d'erreur POST)
    $isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');
    $mode   = $id ? ((($action === 'edit' || $isPost) && $isAdmin) ? 'edit' : 'view') : 'add';

    $data = [
        'txt'     => $txt,
        'title'   => ($id ? ($mode === 'edit' ? 'Modifier' : 'Fiche : ' . ($itemData['name'] ?? '')) : 'Ajouter') . ' - ' . SITENAME,
        'objet'   => $itemData ? (object)$itemData : null,
        'mode'    => $mode,
        'isAdmin' => $isAdmin,
        'error'   => $_SESSION['flash_error'] ?? '',
        'message' => $_SESSION['flash_message'] ?? '',
    ];

    if ($isPost) {
        if (!$isAdmin) {
            die($txt['USER_ERR_UNAUTHORIZED'] ?? 'Accès non autorisé.');
        }

        // Validation...
        if ($validationError) {
            $data['error'] = 'Erreur de saisie...';
            $data['objet'] = (object)$_POST; // préserver la saisie utilisateur
        } else {
            // Sauvegarde...
            if ($id) {
                $this->model->updateObject($postData);
                $_SESSION['flash_message'] = 'Modifié avec succès.';
                return $this->redirect('module/objet/' . $id);
            } else {
                $newId = $this->model->addObject($postData);
                $_SESSION['flash_message'] = 'Créé avec succès.';
                return $this->redirect('module/objets');
            }
        }
    }

    unset($_SESSION['flash_message'], $_SESSION['flash_error']);

    $this->view('system/header', $data);
    $this->view('system/navview', $data);
    $this->view('module/objet', $data);
    $this->view('system/footer', $data);
}

public function edit($id = null) {
    return $this->index($id, 'edit');
}
```

---

## 9. Checklist avant livraison

- [ ] `[objet].php` utilise `require 'module/system/view/common/form_template.php'`
- [ ] En mode **View** : **aucun input**, blocs d'informations propres, panneau d'audit déplié par défaut
- [ ] En mode **Edit/Add** : champ Code `readonly` en edit, boutons de soumission `button success` (vert)
- [ ] Bouton d'annulation présent en pied de formulaire (`button secondary`)
- [ ] Bouton « Modifier » visible uniquement pour les admins en mode View
- [ ] Bouton « Retour » présent dans tous les modes vers la liste
- [ ] Redirection après Edit vers `/module/objet/{id}` (mode View) avec flash success
- [ ] En cas d'erreur de validation POST, l'utilisateur reste sur le formulaire `edit`/`add` avec ses saisies préservées
- [ ] Responsive mobile : boutons avec icônes ergonomiques, cibles tactiles adaptées
