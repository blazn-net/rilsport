# Dépendances — Module `user`

## Fichier SQL
`module/user/database/user.sql`

## Dépendances requises
| Ordre | Fichier    | Raison                                                   |
|-------|------------|----------------------------------------------------------|
| 1     | `lang.sql` | `t_user_text.lang_code` référence `t_lang_lang(lang_code)` |

> ⚠️ **Exécuter `lang.sql` avant `user.sql`**

## Tables créées par ce module
| Table                  | Description                                        |
|------------------------|----------------------------------------------------|
| `t_user_user_status`   | Statuts possibles d'un utilisateur                 |
| `t_user_role`          | Rôles du système (admin, user…)                    |
| `t_user_user`          | Comptes utilisateurs                               |
| `t_user_user_role`     | Association utilisateur ↔ rôle                    |
| `t_user_text_key`      | Clés de traduction du module user                  |
| `t_user_text`          | Traductions (fr / en / es) du module user          |

## Données initiales insérées
- Statuts : `active`, `pending`
- Rôles : `admin`, `user`
- Compte administrateur par défaut : `admin / admin@rilsport.com`

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql
3. user.sql       ← ce fichier
4. main.sql
```
