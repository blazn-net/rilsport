# Directives et Règles Base de Données — Projet RIL Sport

Ce document définit les règles impératives applicables à la conception, la structuration et l'écriture des bases de données et des scripts SQL du projet RIL Sport.

---

## 1. Règle du Statut Obligatoire par Entité

Toute table représentant une entité de données ou un objet métier (ex: utilisateur, club, sport, zone, ville, rôle, etc.) **doit obligatoirement comporter une gestion de statut**.

### Spécifications techniques
1. **Colonne `status_id` :**
   - Nom de colonne : `status_id INT NOT NULL DEFAULT [valeur_par_defaut]`
   - Clé étrangère : Référence vers la table de statuts correspondante avec clause `ON DELETE RESTRICT`.

2. **Table de statuts dédiée :**
   - Chaque entité principale doit posséder sa table de statuts nommée : `t_[module]_[objet]_status`.
   - Schéma standard d'une table de statuts :
     ```sql
     CREATE TABLE IF NOT EXISTS t_[module]_[objet]_status (
         id        SERIAL       PRIMARY KEY,
         text_code VARCHAR(100) NOT NULL UNIQUE
     );
     ```
   - Le champ `text_code` pointe vers la clé de traduction UI correspondante dans `t_[module]_text_key` (ex: `USER_STATUS_ACTIVE`, `SPORT_STATUS_ARCHIVED`).

3. **Statuts standards recommandés :**
   - `1` : Actif / Active
   - `2` : En attente / Pending
   - `3` : Inactif / Suspendu / Disabled
   - `4` : Archivé / Archived

---

## 2. Règle des Traductions

Le projet distingue strictement **2 catégories de traductions**. Aucune chaîne de caractères visible par l'utilisateur ne doit être codée en dur dans les fichiers PHP ou HTML.

### A. Traductions d'Interface Fixes (Boutons, Libellés, Messages, En-têtes)
Chaque module dispose d'**une seule et unique paire de tables** pour l'intégralité de ses traductions d'interface :

```sql
CREATE TABLE IF NOT EXISTS t_[module]_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_[module]_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_[module]_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)         ON DELETE CASCADE
);
```

#### Conventions de nommage des codes de traduction (`text_code`) :
- Toujours en **MAJUSCULES**.
- Toujours préfixé par le nom de l'objet : `{OBJET}_...`.
- Préfixes sémantiques :
  - `{OBJET}_LBL_...` : Libellé de champ ou de colonne (ex: `USER_LBL_EMAIL`, `SPORT_LBL_NAME`).
  - `{OBJET}_BTN_...` : Bouton ou action (ex: `USER_BTN_INVITE`, `ZONE_BTN_IMPORT`).
  - `{OBJET}_ERR_...` : Message d'erreur (ex: `USER_ERR_NOT_FOUND`, `AUTH_ERR_PASSWORD`).
  - `{OBJET}_MSG_...` : Message d'information ou confirmation.
  - `{OBJET}_STATUS_...` : Libellé de statut (ex: `USER_STATUS_ACTIVE`).

### B. Traductions des Données Métiers Dynamiques (`_i18n`)
Pour les objets dont le contenu textuel est éditable par les utilisateurs et traduisible dans plusieurs langues (ex: nom et description d'un sport, désignation d'un continent, titre d'une règle) :

- Table principale : stocke les attributs neutres/techniques (`id`, `code`, `status_id`, dates, audit).
- Table fille de traduction : suffixée obligatoirement par **`_i18n`** :
  ```sql
  CREATE TABLE IF NOT EXISTS t_[module]_[objet]_i18n (
      [objet]_id  INT          NOT NULL,
      lang_code   VARCHAR(5)   NOT NULL,
      name        VARCHAR(200) NOT NULL,
      description TEXT         DEFAULT NULL,
      PRIMARY KEY ([objet]_id, lang_code),
      FOREIGN KEY ([objet]_id) REFERENCES t_[module]_[objet](id) ON DELETE CASCADE,
      FOREIGN KEY (lang_code)  REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
  );
  ```

---

## 3. Règle des Colonnes d'Audit

Toute table principale d'entité métier doit inclure les 4 champs d'audit standard :

```sql
created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
created_by  INT       DEFAULT NULL,
modified_at TIMESTAMP DEFAULT NULL,
modified_by INT       DEFAULT NULL,
FOREIGN KEY (created_by)  REFERENCES t_user_user(id) ON DELETE SET NULL,
FOREIGN KEY (modified_by) REFERENCES t_user_user(id) ON DELETE SET NULL
```

---

## 4. Règle de Ré-entrance Absolue des Scripts SQL

Tous les scripts `.sql` doivent être **entièrement ré-entrants** : ils doivent pouvoir être exécutés 2 fois consécutives (ou plus) sur une base sans provoquer la moindre erreur ni dédoubler des enregistrements.

1. **Création de structure :**
   - Toujours `CREATE TABLE IF NOT EXISTS`.
   - Toujours `CREATE INDEX IF NOT EXISTS` ou `CREATE UNIQUE INDEX IF NOT EXISTS`.
2. **Insertion de données :**
   - Données de référence / constantes (rôles, statuts, données par défaut) :
     ```sql
     INSERT INTO t_table (col1, col2) VALUES (...)
     ON CONFLICT (col1) DO NOTHING;
     ```
   - Traductions UI (`t_[module]_text`) :
     ```sql
     INSERT INTO t_[module]_text (text_code, lang_code, text_label) VALUES (...)
     ON CONFLICT (text_code, lang_code) DO UPDATE SET
         text_label = EXCLUDED.text_label;
     ```
   - Enregistrement des modules dans `t_system_module` / `t_system_module_i18n` :
     ```sql
     ON CONFLICT (code) DO UPDATE SET is_active = EXCLUDED.is_active;
     ```
3. **Mise à jour des séquences d'ID :**
   - En cas d'insertions avec des IDs explicites, toujours réaligner la séquence PostgreSQL à la fin du bloc d'inserts :
     ```sql
     SELECT setval('t_[module]_[objet]_id_seq', COALESCE((SELECT MAX(id) FROM t_[module]_[objet]), 1));
     ```

---

## 5. Convention de Nommage des Tables et Clés

- **Nom des tables :** `t_[module]_[objet]` en minuscules avec underscore.
  - Exemple : `t_user_user`, `t_user_role`, `t_sport_sport`, `t_zone_zone`.
- **Clé primaire :** `id SERIAL PRIMARY KEY` (ou clé métier textuelle spécifique justifiée, ex: `lang_code` pour `t_lang_lang`).
- **Clés étrangères :** `[objet_cible]_id` (ex: `status_id`, `parent_id`, `module_id`).
- **Index :** `idx_[module]_[objet]_[colonne]` pour un index simple, `uq_[module]_[objet]_[colonne]` pour une contrainte unique.

---

## 6. Ordre d'Exécution et Fichier `md_dependencies.md`

Chaque module contient un fichier `module/[module]/md_dependencies.md` qui liste les prérequis et l'ordre d'exécution.

L'ordre standard dans un fichier `[module].sql` est :
1. Enregistrement éventuel dans `t_system_module` et `t_system_module_i18n`.
2. Schéma des tables de statuts (`t_[module]_[objet]_status`).
3. Schéma des tables d'interface (`t_[module]_text_key`, `t_[module]_text`).
4. Schéma des tables métier principales (`t_[module]_[objet]`).
5. Schéma des tables de traduction métier (`t_[module]_[objet]_i18n`).
6. Schéma des tables de liaison (`t_[module]_[objetA]_[objetB]`).
7. Insertion des statuts et données de référence (`ON CONFLICT DO NOTHING`).
8. Insertion des traductions UI (`ON CONFLICT DO UPDATE`).
