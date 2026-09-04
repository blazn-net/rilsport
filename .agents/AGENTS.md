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

## 2. Règles d'Architecture et MVC

- **1 module = N objets**.
- **1 objet = 1 page List + 1 page Form**.
- **1 page List = 1 lien obligatoire dans `module/system/view/navview.php`**.
- **Modes de Fiche** :
  - **Consultation (`View`)** : Textes et badges HTML purs, jamais de balises `<input>`/`<select>` readonly ou disabled.
  - **Formulaire (`Form` / `Edit`)** : Éléments de formulaire interactifs réservés aux utilisateurs avec privilèges d'édition.

---

## 3. Conventions de Vocabulaire et Nommage

- **Pluriel = Liste** (ex: `Users`, `Sports`, route `/[module]/[objets]`).
- **Singulier = Formulaire** (ex: `User`, `Sport`, route création `/[module]/[objet]`, route édition `/[module]/[objet]/[id]`).
- Classes PHP en PascalCase, méthodes en camelCase.
