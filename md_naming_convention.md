# Conventions de Nommage du Projet RIL Sport

Ce document recense les règles de nommage que nous avons adoptées pour ce projet, notamment pour simplifier nos échanges et unifier la structure du code.

## Les Pages "List" et "Form"

Pour distinguer facilement les pages qui affichent des tableaux de celles qui permettent d'ajouter ou de modifier un élément, nous utilisons une règle stricte basée sur le **Pluriel/Singulier** et des termes génériques pour en parler au global.

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

Un module regroupe plusieurs objets métier liés entre eux. Chaque objet dispose de ses propres fichiers `controller`, `model` et `view` au sein du même module.

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
| `/main/users` | `main` | `user` | Liste des utilisateurs |
| `/main/user` | `main` | `user` | Formulaire (ajout) |
| `/main/user/1` | `main` | `user` | Formulaire (modification id=1) |
| `/main/login` | `main` | — | Page de connexion |
| `/main/langs` | `main` | `lang` | Liste des langues |
| `/blog/articles` | `blog` | `article` | Liste des articles |
| `/blog/article/5` | `blog` | `article` | Formulaire (modification id=5) |

### Règle Singulier / Pluriel dans l'URL

La convention Singulier/Pluriel des pages **List** et **Form** (voir section ci-dessus) s'applique directement dans l'URL :

*   **`/main/users`** ➔ page List (tableau de tous les utilisateurs)
*   **`/main/user`** ➔ page Form en mode Ajout
*   **`/main/user/1`** ➔ page Form en mode Modification
