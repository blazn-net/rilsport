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
- **Accès aux Fiches et Colonne Actions (Point [20260908-2304])** :
  - Le lien sur le nom de l'objet dans la liste ouvre **toujours** la fiche en mode **Consultation (`View`)** pour tous (visiteurs et admins).
  - La colonne **Actions** est visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`), totalement masquée pour les non-admins.
  - Le mode Édition est accessible aux admins via le crayon `mif-pencil` de la colonne Actions ou via le bouton « Modifier » en haut de la fiche `View`.
- **Visibilité des Statuts (Point [20260908-2330])** : Visibles **uniquement pour les administrateurs** (colonne "Statut" en liste et badge statut en fiche masqués pour les non-admins).
- **Mobile First obligatoire (Point [20260908-2319])** :
  - Tout écran (`List`, `View`, `Form`) est conçu et testé en priorité sur mobile étroit (≤ 414px).
  - Tous les tableaux utilisent `.table-responsive-cards` avec l'attribut `data-label="..."` obligatoire sur chaque `<td>`.
  - Zéro débordement/scroll horizontal global (`overflow-x`).
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

