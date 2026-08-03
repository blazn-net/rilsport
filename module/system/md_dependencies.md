# Dépendances — Module `system`

## Fichier SQL
`module/system/database/system.sql`

## Dépendances requises
| Ordre | Fichier    | Raison                                                    |
|-------|------------|-----------------------------------------------------------|
| 1     | `lang.sql` | `t_system_text.lang_code` et `_i18n.lang_code` référencent `t_lang_lang(lang_code)` |

> ⚠️ **Exécuter `lang.sql` avant `system.sql`**

## Tables créées par ce module

### 1. Métadonnées et Traductions Métiers (`_i18n`)
| Table                  | Description                                            |
|------------------------|--------------------------------------------------------|
| `t_system_module`      | Registre des modules (données neutres) (#6.1)          |
| `t_system_module_i18n` | Traductions (nom, description) par langue pour module  |
| `t_system_object`      | Registre des objets métier (#6.2)                      |
| `t_system_object_i18n` | Traductions (nom, description) par langue pour objet   |
| `t_system_page`        | Registre des pages et routes (#6.3)                    |
| `t_system_page_i18n`   | Traductions (titre, description) par langue pour page  |
| `t_system_table`       | Registre des tables BDD (#6.4)                         |
| `t_system_table_i18n`  | Traductions (description) par langue pour table        |
| `t_system_column`      | Registre des colonnes BDD (#6.5)                       |
| `t_system_column_i18n` | Traductions (libellé, description) pour colonne       |

### 2. Traductions d'Interface Fixes (`text`)
| Table                  | Description                                            |
|------------------------|--------------------------------------------------------|
| `t_system_text_key`    | Clés de traduction UI du module system                 |
| `t_system_text`        | Traductions UI (fr / en / es) du module system         |

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql     ← ce fichier
3. user.sql
4. main.sql
```
