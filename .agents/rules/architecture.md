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

## 5. Colonne Actions et Accès Fiche (Point [20260908-2304])
- **Lien sur le nom de l'objet** : Ouvre systématiquement la fiche en mode consultation (`View`), pour tous les utilisateurs (visiteurs comme administrateurs).
- **Colonne Actions** : Visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`). Masquée pour les visiteurs et utilisateurs non-administrateurs.
- **Accès au mode Édition (`Edit`)** : Réservé aux administrateurs via le bouton Modifier (crayon) de la colonne Actions ou depuis la fiche en mode View.
