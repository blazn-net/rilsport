# Rules — Projet RIL Sport

Ce fichier définit les règles principales du projet RIL Sport pour les agents d'assistance.
Les directives complètes sont détaillées dans le dossier [doc/](file:///c:/wamp64/www/rilsport/doc/) et répliquées dans [.agents/rules/](file:///c:/wamp64/www/rilsport/.agents/rules/).

---

## 1. Règles Fondamentales Base de Données

- **Statut obligatoire par entité** : Toute table de données/métier DOIT comporter `status_id INT NOT NULL DEFAULT 1` avec clé étrangère vers sa table dédiée `t_[module]_[objet]_status`.
- **Traductions UI fixes** : Chaque module possède `t_[module]_text_key` et `t_[module]_text`. Aucun texte d'interface en dur dans le code PHP/HTML. Clés de type `{OBJET}_LBL_...`, `{OBJET}_BTN_...`, etc.
- **Traductions métier dynamiques** : Tables suffixées en `_i18n` : `t_[module]_[objet]_i18n`.
- **Champs d'audit** : `created_at`, `created_by`, `modified_at`, `modified_by` sur toute entité principale.
- **Ré-entrance des scripts SQL** : Tous les scripts doivent pouvoir s'exécuter 2 fois consécutives sans erreur (`CREATE TABLE IF NOT EXISTS`, `ON CONFLICT (...) DO NOTHING` ou `DO UPDATE`, mise à jour des séquences `setval`).
- **Nommage des tables** : Format obligatoire `t_[module]_[objet]`.
- **Maintenance de `md_dependencies.md`** : Chaque module possède et maintient son fichier `module/[module]/md_dependencies.md`.

---

## 2. Règles d'Architecture, Interface et Navigation

- **1 module = N objets**. Structure standard : `controller/`, `model/`, `view/`, `database/[nom].sql`, `md_dependencies.md`, `test_plan.md`.
- **1 objet = 1 page List + 1 page Form**.
- **Liens de navigation (`navview.php`)** :
  - Toute page List impose un lien dans `module/system/view/navview.php`.
  - Intitulé impératif au **pluriel direct** de l'objet (ex: `Sports`, `Clubs`, `Utilisateurs`) et **JAMAIS** la mention « Gestion [Objet] ».
- **Dualité des Fiches : Consultation (`View`) vs Formulaire (`Form` / `Edit`)** :
  - **Consultation (`View`)** : Textes et badges HTML purs, **interdiction formelle** de balises `<input>`, `<select>`, `<textarea>` (même `readonly` ou `disabled`), aucun bouton de soumission.
  - **Formulaire (`Form` / `Edit`)** : Éléments interactifs réservés aux utilisateurs avec privilèges d'édition (Admins).
- **Pages List standardisées (`list_template.php`)** :
  - Toute page List s'appuie de préférence sur le template universel `module/system/view/common/list_template.php`.
  - Le clic sur le nom de l'objet dans la liste ouvre **toujours** la fiche en mode **Consultation (`View`)** pour tous (visiteurs et admins).
  - Le mode Édition (`Edit`) est accessible aux admins via le bouton Modifier (crayon `mif-pencil`) de la colonne Actions ou via le bouton « Modifier » en haut de la fiche `View`.
  - La colonne **Actions** et la colonne **Statut** sont visibles **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`), totalement masquées pour les non-admins.
  - Libellés de dates : Intitulés concis « Début » et « Fin » (et non « Date de début / fin »).
- **Règles Mobile First (Point [20260908-2319]) — Viewport étroit ≤ 414px** :
  - **Boutons avec icône seule** : Sur mobile (≤ 414px), les boutons n'affichent **que leur icône** (pas de texte, classe `.btn-text` masquée), format carré ergonomique 36×36px avec infobulle `title`.
  - **Tableaux avec ascenseur horizontal** : Les données restent sous forme de tableau. Si les colonnes dépassent la largeur mobile, un ascenseur horizontal est obligatoire (via `data-horizontal-scroll="true"` sur la table, styles sur `.table-container`, et contrôles `.table-scroll-controls` avec boutons `[ ◄ ]` / `[ ► ]`).
  - **Isolation du défilement** : Seul le tableau (`.table-container`) défile horizontalement ; la carte (`main`), la barre de recherche et la pagination restent fixes à 100% de largeur.
  - Zéro débordement horizontal global sur la page (`main { overflow-x: hidden !important; }`).
  - Cibles tactiles ≥ 44px, volet `NavView` replié en hamburger et fermeture au tap extérieur.

---

## 3. Conventions de Vocabulaire et Nommage

- **Pluriel = Liste** (ex: `Users`, `Sports`, route `/[module]/[objets]`).
- **Singulier = Formulaire** (ex: `User`, `Sport`, route création `/[module]/[objet]`, route édition `/[module]/[objet]/[id]`).
- Classes PHP en PascalCase, méthodes en camelCase.

---

## 4. Suivi des Tâches (`doc/todo.md`)

Lors de la lecture ou de la mise à jour de [doc/todo.md](file:///c:/wamp64/www/rilsport/doc/todo.md) :
- `[ ]` ou `[]` : Tâche à faire
- `[-]` : Tâche en cours
- `[x]` : Tâche réalisée

