# Dépendances — Module `lang`

## Fichier SQL
`module/lang/database/lang.sql`

## Dépendances requises
> Aucune — ce module est le **premier à exécuter**.

## Tables créées par ce module
| Table               | Description                                 |
|---------------------|---------------------------------------------|
| `t_lang_lang`       | Table maître des langues du système         |
| `t_lang_text_key`   | Clés de traduction du module lang           |
| `t_lang_text`       | Traductions (fr / en / es) du module lang   |

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
