# Dépendances — Module `sport`

## Fichier SQL
`module/sport/database/sport.sql`

## Dépendances requises
| Ordre | Fichier      | Raison                                                             |
|-------|--------------|--------------------------------------------------------------------|
| 1     | `lang.sql`   | `t_sport_text.lang_code` référence `t_lang_lang(lang_code)`         |
| 2     | `system.sql` | `t_sport_object.module_id` etc. référencent `t_system_module(id)`  |
| 3     | `user.sql`   | `t_sport_sport.created_by` référence `t_user_user(id)`             |

> ⚠️ **Exécuter `lang.sql`, `system.sql` puis `user.sql` avant `sport.sql`**

## Tables créées par ce module

### 1. Structure métier et traductions UI
| Table               | Description                                        |
|---------------------|----------------------------------------------------|
| `t_sport_sport`       | Liste des disciplines sportives (id, code, name, description, icon, status_id, audit) |
| `t_sport_season`      | Saisons sportives (id, code, name, date_start, date_end, status_id, audit) |
| `t_sport_person_role` | Rôles / Fonctions des personnes (code, name)       |
| `t_sport_person`      | Personnes / Acteurs du sport (id, code, first_name, last_name, gender, birth_date, nationality, role_code, sport_id, status_id, audit) |
| `t_sport_club_status` | Statuts possibles d'un club et section (actif, en attente, inactif, dissous) |
| `t_sport_club`        | Clubs sportifs (id, code, name, short_name, acronym, foundation_year, logo, colors, country_code, city_name, city_id, postal_code, address, website, email, phone, description, status_id, audit) |
| `t_sport_section`     | Sections sportives rattachées aux clubs (id, club_id, sport_id, code, name, logo, creation_year, status_id, audit) |
| `t_sport_team_status` | Statuts possibles d'une équipe (active, inactive, dissoute) |
| `t_sport_team`        | Équipes sportives (id, section_id, code, name, short_name, gender, category, level, status_id, audit) |
| `t_sport_text_key`    | Clés de traduction du module sport                 |
| `t_sport_text`        | Traductions (fr / en / es) du module sport         |

### 2. Métadonnées locales du module sport
| Table                 | Description                                        |
|-----------------------|----------------------------------------------------|
| `t_sport_object`      | Objets métier du module sport (sport)              |
| `t_sport_object_i18n` | Traductions des objets sport                       |
| `t_sport_page`        | Pages et routes du module sport (sports, sport)    |
| `t_sport_page_i18n`   | Traductions des pages sport                        |
| `t_sport_table`       | Tables BDD du module sport                         |
| `t_sport_table_i18n`  | Traductions des descriptions de tables sport       |
| `t_sport_column`      | Colonnes BDD des tables sport                      |
| `t_sport_column_i18n` | Traductions des libellés de colonnes sport         |

## Données initiales insérées
- Module ID : `5` (`sport`)
- Sports par défaut : Football, Basketball, Tennis, Rugby, Handball, Volleyball
- Clés de traduction UI `SPORT_*`

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql
3. user.sql
4. main.sql
5. sport.sql      ← ce fichier
```
