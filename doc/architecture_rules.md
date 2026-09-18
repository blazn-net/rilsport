# Directives et Architecture Logicielle — Projet RIL Sport

Ce document définit les règles architecturales impératives relatives à la découpe en modules, aux objets métier, au modèle MVC et à l'interface utilisateur.

---

## 1. Principes Fondamentaux de l'Architecture

> **1 module = N objets**  
> **1 objet = 1 page List + 1 page Form**  
> **1 page List = 1 lien obligatoire dans `module/system/view/navview.php`**

Chaque module regroupe un ensemble cohérent d'objets métier. Chaque objet métier déclaré au sein d'un module doit **obligatoirement** comporter ses 2 pages dédiées :
1. **Page List (tableau récapitulatif)** : affichage de tous les enregistrements de l'objet sous forme de grille/tableau.
2. **Page Form (formulaire unifié)** : création d'un nouvel objet (sans ID) ou modification d'un objet existant (avec ID).

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

- **Contrôleur** : Étend `Core\Controller`. Réceptionne les requêtes HTTP, vérifie les droits et permissions, appelle le modèle et charge la vue.
- **Modèle** : Échange avec la base de données via PDO (`Core\Database`). Gère les requêtes de sélection, validation, insertion, mise à jour et suppression.
- **Vues** : Fichiers PHP de rendu HTML purs, séparés par objet en minuscules (`view/[objet]/list.php` et `view/[objet]/form.php`).
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

### B. Dualité des Fiches : Consultation (`View`) vs Formulaire (`Form` / `Edit`)
Pour l'affichage d'un objet unique :
- **Mode Consultation (`View`)** (utilisateurs sans droit d'édition) :
  - Affichage propre sous forme de libellés, fiches, cartes, badges HTML.
  - **Interdiction formelle** d'utiliser des champs de formulaire (`<input>`, `<select>`, `<textarea>`) désactivés (`disabled`) ou en lecture seule (`readonly`).
  - Aucun bouton de validation/soumission affiché.
- **Mode Édition (`Form` / `Edit`)** (administrateurs ou utilisateurs autorisés) :
  - Composants de saisie interactifs de l'UI (ex: Metro UI `textbox`, `dropdownlist`, `checkbox`, `calendarpicker`).
  - Boutons de validation ("Enregistrer", "Annuler").

### C. Colonne Actions et Accès aux Fiches (Point [20260908-2304])
Sur le tableau d'une page List :
- **Lien sur le nom de l'objet** : Ouvre systématiquement la fiche en mode consultation (`View`), pour tous les utilisateurs (visiteurs comme administrateurs).
- **Colonne Actions** : Visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`). Elle est totalement masquée pour les visiteurs et utilisateurs non-administrateurs.
- **Accès au mode Édition (`Edit`)** : Réservé aux administrateurs, accessible soit via le bouton Modifier (crayon `mif-pencil`) de la colonne Actions, soit via le bouton Modifier présent en haut de la fiche en mode View.

### D. Visibilité des Statuts (Point [20260908-2330])
- **Règle générale** : Les statuts des entités ne doivent être visibles que pour les administrateurs (`!empty($data['isAdmin'])`), sauf exceptions métier dûment justifiées.
- **Pages List** : La colonne "Statut" du tableau ne s'affiche que si l'utilisateur possède les privilèges d'administrateur. Les visiteurs et utilisateurs non-administrateurs ne voient pas cette colonne.

### E. Mobile First (Point [20260908-2319])
- **Priorité absolue au mobile** : Tout écran (List, View, Form) doit être conçu, testé et utilisable en priorité sur écran mobile étroit (≤ 414px) avant le desktop.
- **Tableaux responsive en cartes** : Tous les tableaux de données utilisent la classe `.table-responsive-cards`. Chaque cellule `<td>` comporte obligatoirement l'attribut `data-label="..."` contenant le libellé traduit de la colonne, permettant un affichage en carte verticale sans tableau tronqué.
- **Aucun scroll horizontal parasite** : La page ne doit présenter aucun débordement horizontal (`overflow-x`) sur écran mobile.
- **Ergonomie tactile** : Les boutons, sélecteurs et liens doivent offrir une zone de frappe confortable (hauteur minimale de 44px).
- **Navigation mobile** : Le volet latéral NavView doit se replier en menu hamburger et se fermer automatiquement au clic en dehors.

