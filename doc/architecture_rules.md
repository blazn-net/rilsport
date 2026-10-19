# Directives et Architecture Logicielle — Projet RIL Sport

Ce document définit les règles architecturales impératives relatives à la découpe en modules, aux objets métier, au modèle MVC et à l'interface utilisateur.

---

## 1. Principes Fondamentaux de l'Architecture

> **1 module = N objets**  
> **1 objet = 1 page List + 1 page Entity**  
> **1 page List = 1 lien obligatoire dans `module/system/view/navview.php`**

Chaque module regroupe un ensemble cohérent d'objets métier. Chaque objet métier déclaré au sein d'un module doit **obligatoirement** comporter ses 2 pages dédiées :
1. **Page List (tableau récapitulatif)** : affichage de tous les enregistrements de l'objet sous forme de grille/tableau (`Core\ListController`).
2. **Page Entity (fiche individuelle : View / Edit / Add)** : consultation pure (View) sans input, ou modification/création (Edit/Add) pour les admins (`Core\EntityController`).

---

## 2. Structure Standardisée d'un Module

Tout module sous `module/[nom]/` doit strictement respecter l'arborescence standardisée suivante :

```text
module/[nom]/
├── controller/
│   ├── ObjetA.php
│   └── ObjetB.php
├── model/
│   ├── ObjetA.php
│   └── ObjetB.php
├── view/
│   ├── objeta/
│   │   ├── list.php
│   │   └── form.php
│   └── objetb/
│       ├── list.php
│       └── form.php
├── database/
│   └── [nom].sql
├── md_dependencies.md
└── test_plan.md
```

- **Contrôleur** : Étend `Core\ListController` pour les listes (`[Objets].php`) ou `Core\EntityController` pour les fiches individuelles (`[Objet].php`). Réceptionne les requêtes HTTP, vérifie les droits et permissions, appelle le modèle et charge la vue.
- **Modèle** : Échange avec la base de données via PDO (`Core\Database`). Gère les requêtes de sélection, validation, insertion, mise à jour et suppression.
- **Vues** : Fichiers PHP de rendu HTML purs, s'appuyant sur les templates parents universels `list_template.php` et `entity_template.php`.
- **Database** : Un unique fichier SQL `[nom].sql` par module, strictement ré-entrant.
- **Documentation de dépendances** : Un fichier `md_dependencies.md` à la racine du module.
- **Plan de test et recette** : Un fichier `test_plan.md` à la racine de chaque module consignant la recette fonctionnelle, le respect du mobile first et des règles d'interface.

---

## 3. Typologie des Modules

| Type | Répertoire | Description |
|---|---|---|
| **Obligatoire système** | `module/main/` | Entités indispensables transversales (`user`, `role`, `auth`) |
| **Obligatoire autonome** | `module/lang/` | Gestion centralisée des langues |
| **Obligatoire autonome** | `module/zone/` | Découpage géographique (Monde → Continents → Pays → Régions → Départements → Villes) |
| **Technique interne** | `module/system/` | Métadonnées système, navigation globale (`navview.php`), menus, logs |
| **Métier optionnel** | `module/[nom]/` | Modules métier activables selon les besoins (`sport`, `blog`, `shop`, etc.) |

---

## 4. Règles d'Interface et Navigation

### A. Liens de navigation (`navview.php`)
Tant que le menu dynamique n'a pas remplacé le système actuel :
- **Toute création d'une nouvelle page List impose d'ajouter son lien de menu dans [navview.php](file:///c:/wamp64/www/rilsport/module/system/view/navview.php).**
- Les liens sous la section "Administration" doivent porter directement le nom au pluriel de l'objet (ex: `Utilisateurs`, `Langues`, `Sports`) et **jamais** la mention "Gestion [Objet]".

### B. Fiches Individuelles Entity : Consultation (`View`) vs Formulaire (`Edit` / `Add`)
Pour l'affichage d'un objet unique via `module/system/view/common/entity_template.php` :
- **Mode Consultation (`View`)** (visiteurs et utilisateurs sans droit d'édition, ou consultation admin) :
  - Affichage propre sous forme de libellés, fiches, cartes, badges HTML.
  - **Interdiction formelle** d'utiliser des champs de formulaire (`<input>`, `<select>`, `<textarea>`) désactivés (`disabled`) ou en lecture seule (`readonly`).
  - Aucun bouton de validation/soumission affiché. Bouton Modifier visible uniquement pour les admins.
- **Mode Formulaire (`Edit` / `Add`)** (administrateurs ou utilisateurs autorisés) :
  - Composants de saisie interactifs de l'UI (ex: Metro UI `textbox`, `dropdownlist`, `checkbox`, `calendarpicker`).
  - Boutons de validation ("Enregistrer" / "Mettre à jour", "Annuler").

### C. Colonne Actions et Accès aux Fiches (Point [20260908-2304])
Sur le tableau d'une page List :
- **Template universel** : Toute page List s'appuie sur `module/system/view/common/list_template.php`.
- **Lien sur le nom de l'objet** : Ouvre systématiquement la fiche en mode consultation (`View`), pour tous les utilisateurs (visiteurs comme administrateurs).
- **Colonne Actions** : Visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`). Elle est totalement masquée pour les visiteurs et utilisateurs non-administrateurs.
- **Accès au mode Édition (`Edit`)** : Réservé aux administrateurs, accessible soit via le bouton Modifier (crayon `mif-pencil`) de la colonne Actions, soit via le bouton Modifier présent en haut de la fiche en mode View.
- **Libellés de dates** : Utiliser « Début » et « Fin » (et non « Date de début » / « Date de fin »).

### D. Visibilité des Données Techniques (ID, Statut) et Règle de Parité List / Entity (Point [20260908-2330])
- **Règle générale de parité List / Entity** : Les champs et informations visibles ou non visibles doivent être **strictement identiques** entre la page List et la page Entity pour chaque profil d'utilisateur (visiteur vs administrateur).
- **Identifiant technique (`id` / `#`)** : Les identifiants techniques internes ne doivent être visibles **que pour les administrateurs** (`!empty($data['isAdmin'])`) :
  - **Pages List** : La colonne `#` (`id`) du tableau est masquée pour les visiteurs et non-administrateurs (géré automatiquement par `list_template.php`).
  - **Pages Entity** : L'ID est masqué pour les visiteurs et visible uniquement pour les administrateurs dans l'en-tête de la fiche.
- **Statuts des entités** : Les statuts ne doivent être visibles **que pour les administrateurs** (`!empty($data['isAdmin'])`), sauf exceptions métier dûment justifiées.
  - **Pages List** : La colonne "Statut" du tableau ne s'affiche que si l'utilisateur possède les privilèges d'administrateur. Les visiteurs et utilisateurs non-administrateurs ne voient pas cette colonne.
  - **Pages Entity** : Le badge "Statut" de l'en-tête (mode View) et le champ statut (mode Edit) sont masqués pour les non-administrateurs et visibles **uniquement pour les administrateurs**.
- **Actions et modification** : Bouton « + Ajouter » et colonne « Actions » sur la page List, ainsi que bouton « Modifier » et formulaires interactifs sur la page Entity, sont réservés exclusivement aux administrateurs.
- **Données métier** : Tout champ masqué sur la page List doit impérativement l'être également sur la fiche Entity pour les visiteurs non-administrateurs.

### E. Mobile First (Point [20260908-2319]) — Viewport étroit ≤ 414px
- **Priorité absolue au mobile** : Tout écran (List, View, Form) doit être conçu, testé et utilisable en priorité sur écran mobile étroit (≤ 414px) avant le desktop.
- **Boutons avec icône seule** : Sur écran mobile (≤ 414px), les boutons masquent leur texte (via `.btn-text` masqué en CSS) et n'affichent que leur icône dans un format carré ergonomique (36×36px) avec infobulle `title`.
- **Tableaux avec ascenseur horizontal** : Les données restent sous forme de tableau. Si les colonnes dépassent la largeur mobile, un ascenseur horizontal est obligatoire (via `data-horizontal-scroll="true"`, styles sur `.table-container` et contrôles tactiles interactifs `.table-scroll-controls` avec boutons `[ ◄ ]` / `[ ► ]`).
- **Isolation du défilement** : Seul le tableau (`.table-container`) défile horizontalement. Le conteneur principal (`main`), le titre, le champ de recherche et la pagination restent fixes à 100% de la largeur.
- **Aucun scroll horizontal parasite sur la page** : La page globale ne doit présenter aucun débordement horizontal (`main { overflow-x: hidden !important; }`).
- **Ergonomie tactile** : Les boutons, sélecteurs et liens doivent offrir une zone de frappe confortable (hauteur minimale de 36px à 44px).
- **Navigation mobile** : Le volet latéral NavView doit se replier en menu hamburger et se fermer automatiquement au clic en dehors.


