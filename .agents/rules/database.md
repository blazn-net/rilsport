# Règles BDD Impératives — Projet RIL Sport

Ces règles sont strictement obligatoires pour toute création, modification ou vérification de base de données PostgreSQL dans le projet.

## 1. Statut Obligatoire par Entité
- **Toute table métier/données DOIT posséder une colonne `status_id`** :
  ```sql
  status_id INT NOT NULL DEFAULT 1,
  FOREIGN KEY (status_id) REFERENCES t_[module]_[objet]_status(id) ON DELETE RESTRICT
  ```
- **Chaque objet métier DOIT avoir sa table de statuts** `t_[module]_[objet]_status` avec `(id SERIAL PRIMARY KEY, text_code VARCHAR(100) NOT NULL UNIQUE)`.
- Les libellés de statut sont traduits dans les tables de traductions fixes via `text_code`.

## 2. Traductions Fixes d'Interface UI
- Chaque module DOIT posséder sa paire de tables UI :
  - `t_[module]_text_key (text_code VARCHAR(100) PRIMARY KEY)`
  - `t_[module]_text (text_code VARCHAR(100), lang_code VARCHAR(5), text_label TEXT, PRIMARY KEY(text_code, lang_code))`
- Tout libellé de l'interface (boutons, colonnes, messages d'erreur, statuts, infobulles) DOIT être inséré dans `t_[module]_text` pour chaque langue active (`fr`, `en`, `es`).
- Aucun texte utilisateur ne doit être codé en dur dans les fichiers PHP/HTML.
- Le format des codes de traduction est impérativement : `{OBJET}_{TYPE}_{NOM}` en majuscules (ex: `USER_LBL_NAME`, `USER_STATUS_ACTIVE`).

## 3. Données Traduisibles Métier (`_i18n`)
- Pour les champs textuels dynamiques saisis par l'utilisateur (nom, description, bio...) :
  - Créer systématiquement une table `t_[module]_[objet]_i18n`.
  - Clé primaire composite : `([objet]_id, lang_code)`.
  - FK CASCADE vers la table principale et vers `t_lang_lang(lang_code)`.

## 4. Colonnes d'Audit
- Toute table principale DOIT comporter :
  ```sql
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_by  INT       DEFAULT NULL,
  modified_at TIMESTAMP DEFAULT NULL,
  modified_by INT       DEFAULT NULL,
  FOREIGN KEY (created_by)  REFERENCES t_user_user(id) ON DELETE SET NULL,
  FOREIGN KEY (modified_by) REFERENCES t_user_user(id) ON DELETE SET NULL
  ```

## 5. Ré-entrance des Scripts SQL
- Tout script SQL doit pouvoir s'exécuter 2 fois consécutives sans erreur :
  - `CREATE TABLE IF NOT EXISTS`
  - `CREATE INDEX IF NOT EXISTS`
  - `INSERT ... ON CONFLICT (...) DO NOTHING` pour les données de référence/statuts/rôles.
  - `INSERT ... ON CONFLICT (text_code, lang_code) DO UPDATE SET text_label = EXCLUDED.text_label` pour les traductions d'interface.
  - Toujours réaligner les séquences après les inserts avec `setval()`.

## 6. Documentation des Dépendances
- Tout module contenant un script SQL DOIT posséder et maintenir à jour son fichier `module/[module]/md_dependencies.md`.
