# Dépendances — Module `system`

## Fichier SQL
`module/system/database/system.sql`

## Dépendances requises
| Ordre | Fichier    | Raison                                                    |
|-------|------------|-----------------------------------------------------------|
| 1     | `lang.sql` | `t_system_text.lang_code` référence `t_lang_lang(lang_code)` |

> ⚠️ **Exécuter `lang.sql` avant `system.sql`**

## Tables créées par ce module
| Table                 | Description                                   |
|-----------------------|-----------------------------------------------|
| `t_system_text_key`   | Clés de traduction du module system           |
| `t_system_text`       | Traductions (fr / en / es) du module system   |

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql     ← ce fichier
3. user.sql
4. main.sql
```
