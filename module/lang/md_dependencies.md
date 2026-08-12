# Dépendances — Module `lang`

## Fichier SQL
`module/lang/database/lang.sql`

## Dépendances requises
> Aucune — ce module est le **premier à exécuter**.

## Tables créées par ce module

### 1. Structure métier et traductions UI
| Table               | Description                                 |
|---------------------|---------------------------------------------|
| `t_lang_lang`       | Table maître des langues du système         |
| `t_lang_text_key`   | Clés de traduction du module lang           |
| `t_lang_text`       | Traductions (fr / en / es) du module lang   |

### 2. Métadonnées locales du module lang (#6.10)
| Table                | Description                                        |
|----------------------|----------------------------------------------------|
| `t_lang_object`      | Objets métier du module lang (code: lang)          |
| `t_lang_object_i18n` | Traductions des objets lang                        |
| `t_lang_page`        | Pages et routes du module lang (langs, lang)       |
| `t_lang_page_i18n`   | Traductions des pages lang                         |
| `t_lang_table`       | Tables BDD du module lang                          |
| `t_lang_table_i18n`  | Traductions des descriptions de tables lang        |
| `t_lang_column`      | Colonnes BDD des tables lang                       |
| `t_lang_column_i18n` | Traductions des libellés de colonnes lang          |

## Modules qui dépendent de `lang`
- `system` → `module/system/database/system.sql`
- `user`   → `module/user/database/user.sql`

## Ordre d'exécution recommandé
```
1. lang.sql       ← ce fichier (aucune dépendance)
2. system.sql
3. user.sql
4. main.sql
```
