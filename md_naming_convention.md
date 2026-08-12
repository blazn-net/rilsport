# Conventions de Nommage et Vocabulaire du Projet RIL Sport

Ce document recense l'ensemble des règles de nommage et de vocabulaire adoptées pour le projet RIL Sport afin d'assurer l'homogénéité du code, des URL et de la base de données.

> *Note : Pour les règles de structure des modules et l'architecture BDD, se référer au document [md_architecture.md](file:///c:/wamp64/www/rilsport/md_architecture.md).*

---

## Vocabulaire & Nommage des Pages ("List" et "Form")

Pour distinguer facilement les pages d'affichage en tableau de celles de création/édition, nous appliquons une règle basée sur le **Pluriel / Singulier** et des termes génériques associés.

### 1. Noms spécifiques (Pluriel vs Singulier)

Lorsque l'on fait référence à un objet métier spécifique du projet, on utilise son nom en anglais au pluriel ou au singulier :

* **Nom au Pluriel (ex: `Users`, `Langs`) = La Liste**
  * Il s'agit de la page qui affiche le tableau récapitulatif de tous les objets.
  * *Exemple : "Ajoute une colonne statut dans `Users`."*

* **Nom au Singulier (ex: `User`, `Lang`) = Le Formulaire unique**
  * Il s'agit de la page contenant le formulaire (utilisée **à la fois pour la création et la modification** selon la présence d'un ID).
    * **Sans ID** (`/user`) ➔ Mode Ajout (création).
    * **Avec ID** (`/user?id=1` ou `/user/1`) ➔ Mode Modification (champs pré-remplis).
  * *Exemple : "Mets à jour le champ e-mail dans `User`."*

### 2. Noms globaux (List vs Form)

Lorsque l'on formule une directive générale s'appliquant à l'ensemble de l'application :

* **Les pages "List" (ou les Listes)** : Terme générique pour désigner l'ensemble des pages récapitulatives au pluriel.
  * *Exemple : "Ajoute une infobulle sur les boutons d'action de toutes les pages List."*

* **Les pages "Form" (ou les Formulaires)** : Terme générique pour désigner l'ensemble des pages de saisie au singulier.
  * *Exemple : "Faisons en sorte qu'après une mise à jour, on reste sur la page Form."*

---

## Conventions d'URL

### Format standard

```
/[module]/[objet_ou_page]/[méthode]/[id]
```

| Segment | Description | Exemple |
|---------|------------|---------|
| `[module]` | Nom du module (répertoire sous `module/`) | `user`, `blog`, `main` |
| `[objet_ou_page]` | Nom de l'objet (singulier = Form, pluriel = List) ou nom de page spécifique | `user`, `users`, `login` |
| `[méthode]` | Action optionnelle du contrôleur | `index`, `edit` |
| `[id]` | Identifiant optionnel de la ressource | `1`, `42` |

### Application du Singulier / Pluriel dans l'URL

La règle Singulier/Pluriel s'applique directement dans les routes URL :

* **`/user/users`** ➔ Page List (tableau de tous les utilisateurs)
* **`/user/user`** ➔ Page Form en mode Ajout
* **`/user/user/1`** ➔ Page Form en mode Modification (ID = 1)
* **`/user/login`** ➔ Page spécifique (connexion)

---

## Nommage en Base de Données

### Nommage des tables

Toutes les tables doivent obligatoirement respecter le format : **`t_[MODULE]_[OBJET]`**

| Table | Module | Objet | Nature |
|---|---|---|---|
| `t_lang_lang` | `lang` | `lang` | Table principale |
| `t_user_user` | `user` | `user` | Table principale |
| `t_user_role` | `user` | `role` | Table principale |
| `t_user_user_role` | `user` | `user_role` | Table de liaison |
| `t_user_user_status` | `user` | `user_status` | Table de statut |
| `t_main_text_key` | `main` | `text_key` | Registre de clés de traduction UI |
| `t_main_text` | `main` | `text` | Table de traduction UI |

---

### Rôles et privilèges utilisateurs

* **Rôle `admin`** : Le rôle `admin` détient par définition l'intégralité des privilèges du système. Un utilisateur possédant le rôle `admin` n'a **pas** besoin de cumuler d'autres rôles (comme le rôle `user`).

---

### Nommage des clés de traduction d'interface (`text_code`)

Dans les tables `t_[MODULE]_text_key` et `t_[MODULE]_text`, les codes de traduction (`text_code`) doivent **obligatoirement inclure le préfixe de l'objet** auquel ils se rapportent, sous le format `{OBJET}_` (en majuscules) :

```
USER_USERNAME           → objet user, libellé du champ nom d'utilisateur
USER_ERR_UNAUTHORIZED   → objet user, message d'erreur d'accès refusé
SYS_HOME                → objet/page system, lien accueil
SYS_BTN_EDIT            → objet/page system, bouton modifier
```
