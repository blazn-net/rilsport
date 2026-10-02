---
name: form-page
description: >
  Standards complets pour créer ou modifier une page Form/View (fiche individuelle) dans RIL Sport.
  Lire OBLIGATOIREMENT avant toute création ou modification d'une page Form ou View.
  Couvre : les 3 modes (add/view/edit), en-tête, mode View sans input, formulaire Edit/Add, boutons, contrôleur, modèle, checklist.
---

# Skill : Pages Form & View — RIL Sport

## Quand utiliser ce skill

Activer ce skill dès que la tâche implique :
- Créer ou modifier une **page Form** (formulaire de création/édition)
- Créer ou modifier une **page View** (fiche consultation)
- Gérer un **contrôleur d'objet singulier** (create/read/update)

> Pour les pages List (tableau), utiliser le skill `list-page`.

---

## 1. Principe : 3 modes dans 1 seul fichier

Toute fiche objet est gérée dans **un seul fichier** `[objet].php` qui gère 3 modes :

| Mode | Accès | Description |
|------|-------|-------------|
| `add` | Admin | Formulaire vide, création |
| `view` | Tous | Fiche consultation, lecture seule, **aucun input** |
| `edit` | Admin | Formulaire pré-rempli, modification |

Le contrôleur détermine le mode et le passe dans `$data['mode']`.

---

## 2. En-tête standard

```php
<main class="p-4" style="margin-top: 60px;">
    <div class="d-flex flex-justify-between flex-align-center mb-4">
        <h2>
            <span class="mif-icon mr-2"></span>
            <?php
                if ($data['mode'] === 'edit') {
                    echo $data['txt']['MODULE_EDIT_OBJ_TITLE'] ?? 'Modifier';
                } elseif ($data['mode'] === 'view') {
                    echo 'Fiche : ' . htmlspecialchars($data['objet']->name ?? '');
                } else {
                    echo $data['txt']['MODULE_ADD_OBJ_TITLE'] ?? 'Ajouter';
                }
            ?>
        </h2>
        <div class="d-flex flex-align-center" style="gap: 10px;">
            <!-- Bouton Modifier : mode View + admin uniquement -->
            <?php if ($data['mode'] === 'view' && !empty($data['isAdmin']) && isset($data['objet']->id)): ?>
                <a href="<?php echo URLROOT; ?>/module/objet/edit/<?php echo (int)$data['objet']->id; ?>"
                   class="button info"
                   title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'); ?>">
                    <span class="mif-pencil"></span>
                    <span class="btn-text"><?php echo $data['txt']['SYS_BTN_EDIT'] ?? 'Modifier'; ?></span>
                </a>
            <?php endif; ?>
            <!-- Bouton Retour : toujours présent -->
            <a href="<?php echo URLROOT; ?>/module/objets"
               class="button"
               title="<?php echo htmlspecialchars($data['txt']['SYS_BTN_BACK'] ?? 'Retour à la liste'); ?>">
                <span class="mif-arrow-left"></span>
                <span class="btn-text"><?php echo $data['txt']['SYS_BTN_BACK'] ?? 'Retour'; ?></span>
            </a>
        </div>
    </div>

    <!-- Alertes flash -->
    <?php if (!empty($data['message'])): ?>
        <div class="remark success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="remark alert"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>
```

---

## 3. Mode VIEW — Consultation (lecture seule)

### Règles absolues
- **ZÉRO** `<input>`, `<select>`, `<textarea>` — même `readonly` ou `disabled`
- **ZÉRO** bouton `submit`
- Données affichées via textes, `<span>`, badges, `<p>`
- Le bouton "Modifier" est dans l'en-tête (§2), pas ici

```php
    <?php if ($data['mode'] === 'view'): ?>
    <div class="card p-4">

        <!-- Avatar + Titre + Code + Badge statut -->
        <div class="d-flex flex-align-center mb-3">
            <div class="avatar bg-info fg-white border-radius-half d-flex flex-justify-center flex-align-center mr-3"
                 style="width:50px;height:50px;min-width:50px;">
                <span class="mif-icon mif-2x"></span>
            </div>
            <div>
                <h3 class="m-0"><?php echo htmlspecialchars($data['objet']->name ?? ''); ?></h3>
                <small class="fg-gray">Code : <code><?php echo htmlspecialchars($data['objet']->code ?? ''); ?></code></small>
            </div>
            <div class="ml-auto">
                <?php
                    $isActive   = intval($data['objet']->status_id ?? 0) === 1;
                    $badgeClass = $isActive ? 'success' : 'secondary';
                    $statusTxt  = $isActive ? ($data['txt']['SYS_STATUS_ACTIVE']   ?? 'Actif')
                                            : ($data['txt']['SYS_STATUS_INACTIVE'] ?? 'Inactif');
                ?>
                <span class="badge <?php echo $badgeClass; ?> p-2"><?php echo htmlspecialchars($statusTxt); ?></span>
            </div>
        </div>

        <div class="divider my-3"></div>

        <!-- Champs en lecture -->
        <div class="row mb-4">
            <div class="cell-md-6">
                <h5 class="text-bold mb-1"><?php echo $data['txt']['MODULE_FIELD_LABEL'] ?? 'Champ'; ?></h5>
                <p class="text-leader"><?php echo htmlspecialchars($data['objet']->field ?? '-'); ?></p>
            </div>
        </div>

        <!-- Panneau audit — DÉPLIÉ par défaut en View -->
        <div data-role="panel"
             data-title-caption="<?php echo htmlspecialchars($data['txt']['SYS_AUDIT_PANEL'] ?? 'Informations d\'audit'); ?>"
             data-collapsible="true"
             data-collapsed="false"
             class="mt-4">
            <div class="row">
                <div class="cell-md-6">
                    <p><strong><?php echo $data['txt']['SYS_CREATED_AT'] ?? 'Créé le'; ?> :</strong>
                       <?php echo !empty($data['objet']->created_at) ? date('d/m/Y H:i', strtotime($data['objet']->created_at)) : '-'; ?></p>
                    <p><strong><?php echo $data['txt']['SYS_CREATED_BY'] ?? 'Créé par'; ?> :</strong>
                       <?php echo htmlspecialchars($data['objet']->created_by_name ?? '-'); ?></p>
                </div>
                <div class="cell-md-6">
                    <p><strong><?php echo $data['txt']['SYS_MODIFIED_AT'] ?? 'Modifié le'; ?> :</strong>
                       <?php echo !empty($data['objet']->modified_at) ? date('d/m/Y H:i', strtotime($data['objet']->modified_at)) : '-'; ?></p>
                    <p><strong><?php echo $data['txt']['SYS_MODIFIED_BY'] ?? 'Modifié par'; ?> :</strong>
                       <?php echo htmlspecialchars($data['objet']->modified_by_name ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div>
```

---

## 4. Mode EDIT/ADD — Formulaire (admin uniquement)

```php
    <?php else: ?>
    <!-- MODE EDIT / ADD — Formulaire pour administrateurs -->
    <form method="POST" action="<?php echo URLROOT; ?>/module/objet<?php
        echo ($data['mode'] === 'edit' && isset($data['objet']->id)) ? '/' . (int)$data['objet']->id : '';
    ?>">

        <!-- Champ Code : readonly en edit, requis en add -->
        <div class="form-group">
            <label><?php echo $data['txt']['MODULE_CODE_LABEL'] ?? 'Code'; ?></label>
            <input type="text" name="code" data-role="input"
                   value="<?php echo htmlspecialchars($data['objet']->code ?? ''); ?>"
                   <?php echo ($data['mode'] === 'edit') ? 'disabled' : 'required'; ?>>
            <?php if ($data['mode'] === 'edit'): ?>
                <small class="fg-gray"><?php echo $data['txt']['SYS_CODE_READONLY'] ?? 'Le code ne peut pas être modifié.'; ?></small>
            <?php endif; ?>
        </div>

        <!-- Champ Nom -->
        <div class="form-group mt-3">
            <label><?php echo $data['txt']['MODULE_NAME_LABEL'] ?? 'Nom'; ?></label>
            <input type="text" name="name" data-role="input"
                   value="<?php echo htmlspecialchars($data['objet']->name ?? ''); ?>"
                   required>
        </div>

        <!-- Statut — Edit seulement -->
        <?php if ($data['mode'] === 'edit'): ?>
        <div class="form-group mt-3">
            <label><?php echo $data['txt']['SYS_STATUS_LABEL'] ?? 'Statut'; ?></label>
            <select name="status_id" data-role="select">
                <option value="1" <?php echo (intval($data['objet']->status_id ?? 0) === 1) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SYS_STATUS_ACTIVE'] ?? 'Actif'; ?>
                </option>
                <option value="2" <?php echo (intval($data['objet']->status_id ?? 0) === 2) ? 'selected' : ''; ?>>
                    <?php echo $data['txt']['SYS_STATUS_INACTIVE'] ?? 'Inactif'; ?>
                </option>
            </select>
        </div>

        <!-- Panneau audit — REPLIÉ par défaut en Edit -->
        <div data-role="panel"
             data-title-caption="<?php echo htmlspecialchars($data['txt']['SYS_AUDIT_PANEL'] ?? 'Informations d\'audit'); ?>"
             data-collapsible="true"
             data-collapsed="true"
             class="mt-4">
            <div class="row">
                <div class="cell-md-6">
                    <p><strong>Créé le :</strong> <?php echo !empty($data['objet']->created_at) ? date('d/m/Y H:i', strtotime($data['objet']->created_at)) : '-'; ?></p>
                    <p><strong>Créé par :</strong> <?php echo htmlspecialchars($data['objet']->created_by_name ?? '-'); ?></p>
                </div>
                <div class="cell-md-6">
                    <p><strong>Modifié le :</strong> <?php echo !empty($data['objet']->modified_at) ? date('d/m/Y H:i', strtotime($data['objet']->modified_at)) : '-'; ?></p>
                    <p><strong>Modifié par :</strong> <?php echo htmlspecialchars($data['objet']->modified_by_name ?? '-'); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Boutons -->
        <div class="form-group mt-4">
            <button class="button success" type="submit">
                <span class="mif-floppy-disk mr-1"></span>
                <span class="btn-text">
                    <?php echo ($data['mode'] === 'edit')
                        ? ($data['txt']['SYS_BTN_UPDATE'] ?? 'Mettre à jour')
                        : ($data['txt']['SYS_BTN_SAVE']   ?? 'Enregistrer'); ?>
                </span>
            </button>
            <a href="<?php echo URLROOT; ?>/module/objets" class="button ml-2">
                <span class="mif-cancel mr-1"></span>
                <span class="btn-text"><?php echo $data['txt']['SYS_BTN_CANCEL'] ?? 'Annuler'; ?></span>
            </a>
        </div>
    </form>
    <?php endif; ?>
</main>
```

---

## 5. Conventions Boutons — Form

| Bouton | Classe | Icône | Contexte |
|--------|--------|-------|---------|
| Modifier (View→Edit) | `button info` | `mif-pencil` | En-tête, mode View, admin only |
| Retour à la liste | `button` (neutre) | `mif-arrow-left` | En-tête, tous modes |
| Enregistrer | `button success` | `mif-floppy-disk` | Pied form Add |
| Mettre à jour | `button success` | `mif-floppy-disk` | Pied form Edit |
| Annuler | `button` (neutre) | `mif-cancel` | À côté d'Enregistrer |

---

## 6. Contrôleur Form

```php
public function index($id = null, $action = null) {
    $txt     = array_merge($this->loadLanguage('system'), $this->loadLanguage('module'));
    $isAdmin = isset($_SESSION['roles']) && in_array('admin', $_SESSION['roles']);

    // Détermination du mode
    if ($action === 'edit' && $id) {
        if (!$isAdmin) { header('Location: ' . URLROOT . '/module/objet/' . $id); exit; }
        $mode  = 'edit';
        $objet = $this->model->getObjectById($id);
    } elseif ($id) {
        $mode  = 'view';   // Tout le monde accède en View ; admin peut ensuite cliquer Modifier
        $objet = $this->model->getObjectById($id);
    } else {
        if (!$isAdmin) { header('Location: ' . URLROOT . '/module/objets'); exit; }
        $mode  = 'add';
        $objet = new stdClass();
    }

    // Traitement POST (add ou edit)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
        // Validation et sauvegarde...
        $savedId = ($mode === 'edit') ? $id : $this->model->getLastInsertId();
        $_SESSION['flash_message'] = $txt['MODULE_MSG_SAVED'] ?? 'Enregistré avec succès.';
        header('Location: ' . URLROOT . '/module/objet/' . $savedId);
        exit;
    }

    $data = [
        'txt'     => $txt,
        'title'   => ($txt['MODULE_OBJ_TITLE'] ?? 'Objet') . ' - ' . SITENAME,
        'objet'   => $objet,
        'mode'    => $mode,
        'isAdmin' => $isAdmin,
        'message' => $_SESSION['flash_message'] ?? '',
        'error'   => $_SESSION['flash_error']   ?? '',
    ];
    unset($_SESSION['flash_message'], $_SESSION['flash_error']);

    $this->view('system/header', $data);
    $this->view('system/navview', $data);
    $this->view('module/objet', $data);
    $this->view('system/footer', $data);
}
```

---

## 7. Modèle Form (méthodes standard)

```php
// Lecture — joint les usernames des champs audit
public function getObjectById(int $id) {
    $this->db->query("
        SELECT o.*,
               u_c.username AS created_by_name,
               u_m.username AS modified_by_name
        FROM t_module_objet o
        LEFT JOIN t_user_user u_c ON o.created_by  = u_c.id
        LEFT JOIN t_user_user u_m ON o.modified_by = u_m.id
        WHERE o.id = :id
    ");
    $this->db->bind(':id', $id);
    return $this->db->single();
}

// Création
public function createObject(array $d): bool {
    $this->db->query("
        INSERT INTO t_module_objet (code, name, status_id, created_by)
        VALUES (:code, :name, 1, :created_by)
    ");
    $this->db->bind(':code',       $d['code']);
    $this->db->bind(':name',       $d['name']);
    $this->db->bind(':created_by', $_SESSION['user_id'] ?? null);
    return $this->db->execute();
}

// Mise à jour
public function updateObject(int $id, array $d): bool {
    $this->db->query("
        UPDATE t_module_objet
        SET name = :name, status_id = :status_id,
            modified_at = NOW(), modified_by = :modified_by
        WHERE id = :id
    ");
    $this->db->bind(':name',        $d['name']);
    $this->db->bind(':status_id',   $d['status_id']);
    $this->db->bind(':modified_by', $_SESSION['user_id'] ?? null);
    $this->db->bind(':id',          $id);
    return $this->db->execute();
}
```

---

## 8. Checklist avant livraison

- [ ] Mode **View** : zéro input/select/textarea (même readonly)
- [ ] Mode **View** : bouton "Modifier" dans l'en-tête, admin only
- [ ] Mode **Edit/Add** : accessible admins seulement (redirect sinon)
- [ ] Bouton Enregistrer/Mettre à jour : `button success` + `mif-floppy-disk`
- [ ] Bouton Retour : `button` neutre + `mif-arrow-left`
- [ ] Panneau audit dans les **2 modes** (déplié View, replié Edit)
- [ ] POST → redirect vers View + `$_SESSION['flash_message']`
- [ ] Modèle `getObjectById()` joint `created_by_name` et `modified_by_name`
- [ ] Champ `code` désactivé (`disabled`) en mode Edit
