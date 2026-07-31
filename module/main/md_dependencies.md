# Dépendances — Module `main`

## Fichier SQL
`module/main/database/main.sql`

## Dépendances requises
| Ordre | Fichier    | Raison                                                                |
|-------|------------|-----------------------------------------------------------------------|
| 1     | `lang.sql` | Fournit la table `t_lang_lang` utilisée par tous les modules          |
| 2     | `user.sql` | Fournit les tables `t_user_user`, `t_user_role`, `t_user_user_status` |

> ⚠️ **Ce module ne crée pas de tables propres.**
> Il s'appuie sur les tables définies dans les modules `lang` et `user`.

## Tables utilisées (créées par d'autres modules)
| Table                | Définie dans |
|----------------------|--------------|
| `t_lang_lang`        | `lang.sql`   |
| `t_user_user`        | `user.sql`   |
| `t_user_role`        | `user.sql`   |
| `t_user_user_status` | `user.sql`   |

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql
3. user.sql       ← dépendance obligatoire
4. main.sql       ← ce fichier (en dernier)
```
