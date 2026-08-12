# Dépendances — Module `system`

## Fichier SQL
`module/system/database/system.sql`

## Dépendances requises
| Ordre | Fichier    | Raison                                                    |
|-------|------------|-----------------------------------------------------------|
| 1     | `lang.sql` | `t_system_text.lang_code` et `_i18n.lang_code` référencent `t_lang_lang(lang_code)` |

> ⚠️ **Exécuter `lang.sql` avant `system.sql`**

## Tables créées par ce module

### 1. Registre des Modules et Métadonnées Métiers du Module System (`_i18n`) (#6.10)
| Table                  | Description                                                  |
|------------------------|--------------------------------------------------------------|
| `t_system_module`      | Registre central de tous les modules du système (#6.1)       |
| `t_system_module_i18n` | Traductions (nom, description) des modules                   |
| `t_system_object`      | Objets métier du module system (module, object, page…) (#6.2)|
| `t_system_object_i18n` | Traductions (nom, description) des objets system             |
| `t_system_page`        | Pages et routes du module system (#6.3)                      |
| `t_system_page_i18n`   | Traductions (titre, description) des pages system            |
| `t_system_table`       | Tables BDD du module system (#6.4)                           |
| `t_system_table_i18n`  | Traductions (description) des tables system                  |
| `t_system_column`      | Colonnes BDD des tables system (#6.5)                        |
| `t_system_column_i18n` | Traductions (libellé, description) des colonnes system       |

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
