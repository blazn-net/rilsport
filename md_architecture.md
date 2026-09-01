# Architecture et Directives Techniques du Projet RIL Sport

Ce document définit la structure générale de l'application, l'organisation des modules et les directives techniques (base de données, interface utilisateur et conventions d'exécution).

---

## Architecture des Modules

### Principes généraux

> **1 module = N objets**  
> **1 objet = 1 page List + 1 page Form**  
> **1 page List = 1 lien dans `module/system/view/navview.php`**

Un module regroupe plusieurs objets métier liés entre eux. Chaque objet métier défini dans un module doit **obligatoirement** posséder ses deux pages dédiées :
* **Page List** : affichage des éléments en tableau.
* **Page Form** : création et édition d'un élément unique.

> ℹ️ **Gestion de la navigation :** Pour l'instant, les menus sont gérés en dur dans `module/system/view/navview.php`. Toute création d'une nouvelle page **List** pour un objet implique obligatoirement l'ajout de son lien de navigation dans `navview.php`.

Chaque objet dispose de ses propres fichiers `controller`, `model` et `view` au sein du même module.

---

### Répertoire racine et typologie des modules

Tous les modules sont placés sous le répertoire `module/` :

```
module/
├── main/     ← module obligatoire (objets système et transversaux)
├── system/   ← module technique interne
└── [xxx]/    ← modules optionnels / métier
```

| Type | Répertoire | Description |
|------|-----------|-------------|
| **Obligatoire** | `module/main/` | Contient tous les objets indispensables au fonctionnement du système (`user`, `lang`, `zone`, `module`, `role`, ...) |
| **Technique** | `module/system/` | Gestion interne bas niveau (logs, config système, métadonnées, ...) |
| **Optionnel** | `module/[nom]/` | Tout module métier ajouté selon les besoins (`blog`, `shop`, `forum`, ...) |

---

### Structure interne d'un module

Chaque module suit la même organisation, quel que soit le nombre d'objets qu'il contient :

```
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
└── database/
    └── [nom].sql
```

*Exemple concret — module `blog` avec 2 objets (`Article` et `Comment`) :*

```
module/blog/
├── controller/
│   ├── Article.php
│   └── Comment.php
├── model/
│   ├── Article.php
│   └── Comment.php
├── view/
│   ├── article/
│   │   ├── list.php
│   │   └── form.php
│   └── comment/
│       ├── list.php
│       └── form.php
└── database/
    └── blog.sql
```

---

## Directives Interface Utilisateur et Menus

### Règle d'ajout des liens de navigation

> **Règle :** Pour l'instant, les menus de navigation sont gérés en dur dans `module/system/view/navview.php`. Toute création d'une nouvelle page **List** pour un objet métier doit **obligatoirement** s'accompagner de l'ajout de son lien de navigation correspondant dans `navview.php`.

### Nommage des éléments du menu sous Administration

> **Règle :** Les entrées de menu regroupées sous le groupe ou en-tête **"Administration"** ne doivent **jamais** commencer par *"Gestion [objet]"*, mais porter directement le nom de l'objet au pluriel (ex: `Utilisateurs`, `Langues`, `Menus`, `Modules`, etc.).

### Modes d'affichage des Fiches (View vs Form/Edit)

> **Règle :** Pour la vue d'un objet unique :
> - **Mode `View` (lecture seule)** : Affiche les informations uniquement sous forme de **libellés/textes HTML, cartes et badges** sans aucun élément de formulaire (`input`, `select`) désactivé ou en `readonly`.
> - **Mode `Form` / `Edit` (modification)** : Affiche les composants de formulaire interactifs (`textbox`, `dropdownlist`, `checkbox`, etc.) avec le bouton d'enregistrement.
> - **Adaptation des liens depuis `List`** : Pour un administrateur, l'action sur le tableau pointe vers l'édition ("Modifier" / crayon). Pour un utilisateur simple, l'action pointe vers la consultation ("Consulter" / œil).

---

## Directives Base de Données

### Emplacement des fichiers SQL

Chaque module dispose d'**un seul fichier `.sql`**, nommé `[module].sql`, placé dans `module/[module]/database/` :

```
module/main/database/main.sql
module/system/database/system.sql
module/blog/database/blog.sql
```

---

### Structure du système de traduction en BDD

Le projet distingue **2 types de traductions** :

#### 1. Traductions d'Interface Fixes (Boutons, Libellés, Messages d'erreur)
Chaque module dispose d'**une seule paire de tables de traduction UI** :

| Table | Rôle |
|---|---|
| `t_[MODULE]_text_key` | Registre de toutes les clés de traduction d'interface |
| `t_[MODULE]_text` | Traductions : `(text_code, lang_code)` → `text_label` |

#### 2. Traductions des Données Métiers Dynamiques (`_i18n`)
Pour les objets dont le contenu métier est rédigé et traduisible dans plusieurs langues (ex: nom et description d'un module, titre et contenu d'un article), on utilise une table fille dédiée avec le suffixe **`_i18n`** :

| Table | Rôle |
|---|---|
| `t_[MODULE]_[OBJET]` | Données neutres/techniques (`id`, `code`, dates, booléens) |
| `t_[MODULE]_[OBJET]_i18n` | Champs traduisibles : `(objet_id, lang_code)` → `name`, `description`, etc. |

*Exemples : `t_system_module_i18n`, `t_system_object_i18n`, `t_system_page_i18n`.*

---

### Politique d'insertion et scripts ré-entrants (`ON CONFLICT`)

Les scripts `.sql` doivent pouvoir être rejoués à tout moment sans provoquer d'erreur ni corrompre les données.

| Type de données | Clause SQL | Raison |
|---|---|---|
| Données de référence (langues, rôles, statuts, utilisateurs par défaut) | `ON CONFLICT (...) DO NOTHING` | Ne pas écraser des données potentiellement modifiées en production |
| Traductions d'interface (`t_[MODULE]_text`) | `ON CONFLICT (...) DO UPDATE SET text_label = EXCLUDED.text_label` | Permet de corriger ou mettre à jour une traduction en rejouant le script |

> **Règle :** Les colonnes de conflit doivent **toujours être explicites** dans la clause `ON CONFLICT (...)`.

---

### Ordre d'exécution recommandé dans un fichier `.sql`

Pour éviter les erreurs de clés étrangères (`FOREIGN KEY`), chaque fichier `.sql` doit respecter l'ordre suivant :

1. `CREATE TABLE` des tables sans dépendances FK (ex: `t_lang_lang`)
2. `CREATE TABLE` des tables de traduction UI (`t_[MODULE]_text_key`, `t_[MODULE]_text`)
3. `CREATE TABLE` des tables métier avec FK (ex: `t_user_user`, `t_user_role`, ...)
4. `INSERT` des données de référence (dans l'ordre des dépendances)
5. `INSERT` des traductions d'interface (`t_[MODULE]_text_key` puis `t_[MODULE]_text`)
