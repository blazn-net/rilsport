# Règles d'Architecture Logicielle — Projet RIL Sport

Ces règles sont strictement obligatoires pour la structure du code et des interfaces.

## 1. Principes des Modules et Objets
- **1 module = N objets**.
- **1 objet = 1 page List + 1 page Entity**.
  - Page List : `view/[objet]/list.php` ou `view/[objets].php` (tableau récapitulatif).
  - Page Entity : `view/[objet]/[objet].php` (fiche unifiée View / Edit / Add s'appuyant sur `module/system/view/common/entity_template.php`).
- **1 page List = 1 lien obligatoire dans `module/system/view/navview.php`**.

## 2. Structure Standard d'un Module
Tout module sous `module/[nom]/` comprend :
- `controller/[Objets].php` (classe `[Objets] extends Core\ListController`)
- `controller/[Objet].php` (classe `[Objet] extends Core\EntityController`)
- `model/[Objet].php` (classe `[Objet]Model`)
- `view/[objets].php` et `view/[objet].php`
- `database/[nom].sql`
- `md_dependencies.md`
- `test_plan.md` (Plan de test et recette fonctionnelle, mobile first et interfaces)

## 3. Modes d'Affichage Fiche Entity
- **Mode Consultation (`View`)** :
  - Destiné aux utilisateurs et visiteurs en lecture seule.
  - Textes purs, badges, cartes HTML.
  - **JAMAIS d'éléments `<input>`, `<select>`, `<textarea>` désactivés ou readonly**.
  - Aucun bouton d'enregistrement. Bouton Modifier visible uniquement pour les administrateurs.
- **Mode Formulaire (`Edit` / `Add`)** :
  - Destiné aux utilisateurs avec droit de modification (Admins).
  - Composants interactifs de formulaire et boutons Enregistrer/Mettre à jour et Annuler.

## 4. Visibilité des Données Techniques (ID, Statut) et Règle de Parité (List vs Entity) (Point [20260908-2330])
- **Règle de parité absolue List vs Entity** : Les champs, colonnes et informations visibles ou masqués selon les privilèges de l'utilisateur (visiteur vs administrateur) doivent être **rigoureusement identiques** entre la page List et la page Entity.
- **Identifiant technique (`id` / `#`)** : Les identifiants techniques internes ne doivent être visibles **que pour les administrateurs** (`!empty($data['isAdmin'])`) :
  - Sur les pages **List**, la colonne `#` (`id`) est masquée pour les visiteurs et non-administrateurs (géré automatiquement par `list_template.php`).
  - Sur les pages **Entity**, l'ID technique est masqué pour les visiteurs (qui n'utilisent que le code métier et le nom) et visible uniquement pour les administrateurs dans l'en-tête.
- **Statut (`status_id`)** : Les statuts des entités ne doivent être visibles **que pour les administrateurs** (`!empty($data['isAdmin'])`), sauf exceptions métier dûment justifiées :
  - Sur les pages **List**, la colonne "Statut" est masquée pour les visiteurs et non-administrateurs.
  - Sur les pages **Entity**, le badge de statut dans l'en-tête (mode View) et le champ statut (mode Edit) sont masqués pour les non-administrateurs et visibles **uniquement pour les administrateurs**.
- **Actions et formulaires** :
  - Sur les pages **List**, le bouton « + Ajouter » et la colonne « Actions » sont visibles uniquement pour les administrateurs.
  - Sur les pages **Entity**, le bouton « Modifier » et l'accès au mode Formulaire interactif (`Edit` / `Add`) sont réservés uniquement aux administrateurs.
- **Données métier** : Tout champ masqué sur la page List en raison de permissions ou de droits d'accès doit également être masqué sur la fiche Entity (et réciproquement). Zéro divulgation d'information réservée aux administrateurs sur la fiche Entity.

## 5. Pages List Standardisées et Accès aux Fiches (Point [20260908-2304])
- **Template universel** : Toute page List s'appuie sur `module/system/view/common/list_template.php`.
- **Lien sur le nom de l'objet** : Ouvre systématiquement la fiche en mode consultation (`View`), pour tous les utilisateurs (visiteurs comme administrateurs).
- **Colonne Actions** : Visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`). Masquée pour les visiteurs et utilisateurs non-administrateurs.
- **Accès au mode Édition (`Edit`)** : Réservé aux administrateurs via le bouton Modifier (crayon `mif-pencil`) de la colonne Actions ou depuis la fiche en mode View.
- **Intitulés succincts** : Utiliser « Début » et « Fin » au lieu de « Date de début » et « Date de fin ».

## 6. Mobile First (Point [20260908-2319]) — Viewport étroit ≤ 414px
- Tout écran (`List`, `View`, `Form`) est conçu et testé en priorité sur mobile (viewport ≤ 414px).
- **Boutons avec icône seule** : Sur mobile (≤ 414px), les boutons masquent leur texte (classe `.btn-text` masquée en CSS) et n'affichent que leur icône dans un carré ergonomique (36×36px) avec infobulle `title`.
- **Tableaux avec ascenseur horizontal** : Les données s'affichent sous forme de tableau. Si les colonnes dépassent la largeur mobile, un ascenseur horizontal est obligatoire (via `data-horizontal-scroll="true"`, `.table-container` stylisé, et contrôles interactifs `.table-scroll-controls` avec boutons `[ ◄ ]` / `[ ► ]`).
- **Isolation du défilement** : Seul le tableau (`.table-container`) défile horizontalement. Le conteneur principal (`main`), le titre, le filtre de recherche et la pagination restent fixes à 100% de largeur.
- Zéro débordement horizontal global (`main { overflow-x: hidden !important; }`).
- Cibles tactiles ≥ 44px, volet `NavView` replié en hamburger sur mobile.

