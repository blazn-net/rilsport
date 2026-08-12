# Conventions de Nommage du Projet RIL Sport

Ce document recense les règles de nommage que nous avons adoptées pour ce projet, notamment pour simplifier nos échanges et unifier la structure du code.

## Les Pages "List" et "Form"

Pour distinguer facilement les pages qui affichent des tableaux de celles qui permettent d'ajouter ou de modifier un élément, nous utilisons une règle stricte basée sur le **Pluriel/Singulier** et des termes génériques pour en parler au global.

> **Règle fondamentale :** Tout objet métier défini dans un module doit **obligatoirement** posséder ses deux pages dédiées : une page **List** (affichage en tableau) et une page **Form** (création / édition).

### Règle 1 : Noms spécifiques (Pluriel vs Singulier)

Lorsque l'on parle d'un objet spécifique dans le projet, on utilise son nom en anglais et on joue sur le pluriel et le singulier :

*   **Nom au Pluriel (ex: `Users`, `Langs`) = La Liste**
    Il s'agit de la page qui affiche le tableau de tous les objets. Cette page sert de point d'entrée pour visualiser et lister les données.
    *Exemple : "Ajoute une colonne statut dans `Users`."*

*   **Nom au Singulier (ex: `User`, `Lang`) = Le Formulaire unique**
    Il s'agit de la page contenant le formulaire. Cette page sert **à la fois à la création et à la modification**, grâce à une vérification sur l'ID dans l'URL.
    *   **Si l'URL n'a pas d'ID** (`/user`) ➔ Mode Ajout (création pure).
    *   **Si l'URL a un ID** (`/user?id=1` ou `/user/1`) ➔ Mode Modification (les champs sont pré-remplis en base pour édition).
    *Exemple : "Mets à jour le champ e-mail dans `User`."*

### Règle 2 : Noms globaux (List vs Form)

Lorsque l'on veut définir une règle technique ou un changement d'interface qui s'applique à l'ensemble du projet sans cibler un objet particulier, nous utilisons des termes globaux :

*   **Les pages "List" (ou les Listes)** : Terme générique pour désigner toutes les pages au pluriel confondues.
    *Exemple : "Mets une infobulle sur les boutons d'action de toutes les pages List."*

*   **Les pages "Form" (ou les Formulaires)** : Terme générique pour désigner toutes les pages au singulier confondues.
    *Exemple : "Faisons en sorte qu'après une mise à jour, on reste sur la page Form."*

---

## Architecture des Modules

### Principe général

> **1 module = N objets**  
> **1 objet = 1 page List + 1 page Form**

Un module regroupe plusieurs objets métier liés entre eux. Chaque objet dispose **obligatoirement** de ses deux pages (**List** et **Form**) ainsi que de ses propres fichiers `controller`, `model` et `view` au sein du même module.

### Répertoire racine

Tous les modules sont placés sous `module/` :

```
module/
├── main/     ← module obligatoire (objets système)
├── system/   ← module technique interne
└── [xxx]/    ← modules optionnels/métier
```

### Types de modules

| Type | Répertoire | Description |
|------|-----------|-------------|
| **Obligatoire** | `module/main/` | Contient tous les objets indispensables au fonctionnement du système (`user`, `lang`, `zone`, `module`, `role`, ...) |
| **Technique** | `module/system/` | Gestion interne bas niveau (logs, config système, ...) |
| **Optionnel** | `module/[nom]/` | Tout module métier ajouté selon les besoins (`blog`, `shop`, `forum`, ...) |

### Structure interne d'un module

Chaque module suit la même organisation, quel que soit le nombre d'objets :

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
```

*Exemple concret — module `blog` avec 2 objets :*

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
│   └── comment/
└── database/
```

---

## Convention d'URL

### Format

```
/[module]/[objet_ou_page]/[méthode]/[id]
```

| Segment | Description | Exemple |
|---------|------------|---------|
| `[module]` | Nom du module (répertoire sous `module/`) | `main`, `blog` |
| `[objet_ou_page]` | Nom de l'objet (singulier = Form, pluriel = List) ou nom de page | `user`, `users`, `login` |
| `[méthode]` | Action optionnelle du contrôleur | `index`, `edit` |
| `[id]` | Identifiant optionnel de la ressource | `1`, `42` |

### Exemples

| URL | Module | Objet | Page |
|-----|--------|-------|------|
| `/user/users` | `user` | `user` | Liste des utilisateurs |
| `/user/user` | `user` | `user` | Formulaire (ajout) |
| `/user/user/1` | `user` | `user` | Formulaire (modification id=1) |
| `/user/login` | `user` | — | Page de connexion |
| `/lang/langs` | `lang` | `lang` | Liste des langues |
| `/blog/articles` | `blog` | `article` | Liste des articles |
| `/blog/article/5` | `blog` | `article` | Formulaire (modification id=5) |

### Règle Singulier / Pluriel dans l'URL

La convention Singulier/Pluriel des pages **List** et **Form** (voir section ci-dessus) s'applique directement dans l'URL :

*   **`/user/users`** ➔ page List (tableau de tous les utilisateurs)
*   **`/user/user`** ➔ page Form en mode Ajout
*   **`/user/user/1`** ➔ page Form en mode Modification

---

## Base de données

### Fichiers SQL

Un seul fichier `.sql` par module, nommé `[module].sql`, placé dans `module/[module]/database/` :

```
module/main/database/main.sql
module/system/database/system.sql
module/blog/database/blog.sql
```

Chaque fichier contient dans l'ordre :
1. **Schémas** (`CREATE TABLE IF NOT EXISTS`) — dans l'ordre des dépendances FK
2. **Données** (`INSERT ... ON CONFLICT`) — dans l'ordre des dépendances FK

---

### Nommage des tables

Convention : **`t_[MODULE]_[OBJET]`**

| Exemple | Module | Objet |
|---|---|---|
| `t_lang_lang` | `lang` | `lang` |
| `t_user_user` | `user` | `user` |
| `t_user_role` | `user` | `role` |
| `t_user_user_role` | `user` | `user_role` (table de liaison) |
| `t_user_user_status` | `user` | `user_status` |
| `t_main_text_key` | `main` | registre des clés de traduction |
| `t_main_text` | `main` | traductions |
| `t_system_text_key` | `system` | registre des clés de traduction |
| `t_system_text` | `system` | traductions |

---

### Tables de traduction

Le projet distingue **2 types de traductions** :

#### 1. Traductions d'Interface Fixes (Boutons, Libellés, Messages d'erreur)
Chaque module dispose d'**une seule paire de tables de traduction UI** :

| Table | Rôle |
|---|---|
| `t_[MODULE]_text_key` | Registre de toutes les clés de traduction d'interface |
| `t_[MODULE]_text` | Traductions : `(text_code, lang_code)` → `text_label` |

**Préfixe obligatoire dans `text_code` : `{OBJET}_`**

#### 2. Traductions des Données Métiers Dynamiques (`_i18n`)
Pour les objets dont le contenu métier est rédigé et traduisible dans plusieurs langues (ex: nom et description d'un module, titre et contenu d'un article), on utilise une table fille dédiée avec le suffixe **`_i18n`** :

| Table | Rôle |
|---|---|
| `t_[MODULE]_[OBJET]` | Données neutres/technique (`id`, `code`, dates, booléens) |
| `t_[MODULE]_[OBJET]_i18n` | Champs traduisibles : `(objet_id, lang_code)` → `name`, `description`, etc. |

*Exemples : `t_system_module_i18n`, `t_system_object_i18n`, `t_system_page_i18n`.*

Chaque clé doit être préfixée par le nom de l'objet métier auquel elle appartient, en majuscules :

```
USER_USERNAME           → objet user, libellé champ nom d'utilisateur
USER_ERR_UNAUTHORIZED   → objet user, message erreur accès refusé
SYS_HOME                → module system, lien accueil
SYS_BTN_EDIT            → module system, bouton modifier
```

---

### Politique d'insertion (`ON CONFLICT`)

| Type de données | Clause | Raison |
|---|---|---|
| Données de référence (langues, rôles, statuts, utilisateurs par défaut) | `ON CONFLICT (...) DO NOTHING` | Ne pas écraser des données potentiellement modifiées en production |
| Traductions (`t_[MODULE]_text`) | `ON CONFLICT (...) DO UPDATE SET text_label = EXCLUDED.text_label` | Permet de corriger une traduction en relançant simplement le script |

> Les colonnes de conflit doivent **toujours être explicites** — ne jamais omettre `(...)`.

---

### Ordre d'exécution dans un fichier `.sql`

Pour chaque module, respecter cet ordre :

1. `CREATE TABLE` sans dépendances FK (ex: `t_lang_lang`)
2. `CREATE TABLE` des tables de traduction (ex: `t_main_text_key`, `t_main_text`)
3. `CREATE TABLE` des tables métier avec FK (ex: `t_user_user`, `t_user_role`, ...)
4. `INSERT` données de référence (dans le même ordre que les schémas)
5. `INSERT` traductions (`t_[MODULE]_text_key` puis `t_[MODULE]_text`)
