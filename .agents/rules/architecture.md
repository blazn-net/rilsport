# Règles d'Architecture Logicielle — Projet RIL Sport

Ces règles sont strictement obligatoires pour la structure du code et des interfaces.

## 1. Principes des Modules et Objets
- **1 module = N objets**.
- **1 objet = 1 page List + 1 page Form**.
  - Page List : `view/[objet]/list.php` (tableau de tous les enregistrements).
  - Page Form : `view/[objet]/form.php` (formulaire unifié ajout/édition).
- **1 page List = 1 lien obligatoire dans `module/system/view/navview.php`**.

## 2. Structure Standard d'un Module
Tout module sous `module/[nom]/` comprend :
- `controller/[Objet].php` (classe `[Objet]Controller extends Controller`)
- `model/[Objet].php` (classe `[Objet]Model`)
- `view/[objet]/list.php` et `view/[objet]/form.php`
- `database/[nom].sql`
- `md_dependencies.md`
- `test_plan.md` (Plan de test et recette fonctionnelle, mobile first et interfaces)

## 3. Modes d'Affichage Fiche
- **Mode Consultation (`View`)** :
  - Destiné aux utilisateurs en lecture seule.
  - Textes purs, badges, cartes HTML.
  - **JAMAIS d'éléments `<input>`, `<select>`, `<textarea>` désactivés ou readonly**.
  - Aucun bouton d'enregistrement.
- **Mode Formulaire (`Form` / `Edit`)** :
  - Destiné aux utilisateurs avec droit de modification (Admins).
  - Composants interactifs de formulaire et bouton d'enregistrement.

## 4. Visibilité des Statuts (Point [20260908-2330])
- Les statuts des entités ne doivent être visibles **que pour les administrateurs** (`!empty($data['isAdmin'])`), sauf exceptions métier.
- Sur les pages List, la colonne "Statut" est masquée pour les visiteurs et utilisateurs non-administrateurs.

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

