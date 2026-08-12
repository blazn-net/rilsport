# Dépendances — Module `user`

## Fichier SQL
`module/user/database/user.sql`

## Dépendances requises
| Ordre | Fichier      | Raison                                                             |
|-------|--------------|--------------------------------------------------------------------|
| 1     | `lang.sql`   | `t_user_text.lang_code` référence `t_lang_lang(lang_code)`           |
| 2     | `system.sql` | `t_user_object.module_id` etc. référencent `t_system_module(id)`   |

> ⚠️ **Exécuter `lang.sql` puis `system.sql` avant `user.sql`**

## Tables créées par ce module

### 1. Structure métier et traductions UI
| Table                  | Description                                        |
|------------------------|----------------------------------------------------|
| `t_user_user_status`   | Statuts possibles d'un utilisateur                 |
| `t_user_role`          | Rôles du système (admin, user…)                    |
| `t_user_user`          | Comptes utilisateurs (id, username, email, password_hash, nom, prenom, status_id, audit) |
| `t_user_user_role`     | Association utilisateur ↔ rôle                    |
| `t_user_text_key`      | Clés de traduction du module user                  |
| `t_user_text`          | Traductions (fr / en / es) du module user          |

### 2. Métadonnées locales du module user (#6.10)
| Table                | Description                                        |
|----------------------|----------------------------------------------------|
| `t_user_object`      | Objets métier du module user (user, role)          |
| `t_user_object_i18n` | Traductions des objets user                        |
| `t_user_page`        | Pages et routes du module user (users, user…)      |
| `t_user_page_i18n`   | Traductions des pages user                         |
| `t_user_table`       | Tables BDD du module user                          |
| `t_user_table_i18n`  | Traductions des descriptions de tables user        |
| `t_user_column`      | Colonnes BDD des tables user                       |
| `t_user_column_i18n` | Traductions des libellés de colonnes user          |

## Données initiales insérées
- Statuts : `active` (1), `pending` (2)
- Rôles : `admin`, `user`
- Compte administrateur par défaut : `willbask` / `willbask@rilsport.com` (Nom: Baskerville, Prénom: William, Rôle: `admin` uniquement)
- Compte utilisateur par défaut : `ireneadler` / `ireneadler@rilsport.com` (Nom: Adler, Prénom: Irene, Rôle: `user`)
- Mots de passe de test documentés dans [pwd.md](file:///c:/wamp64/www/rilsport/module/user/pwd.md)

## Ordre d'exécution recommandé
```
1. lang.sql       ← dépendance obligatoire
2. system.sql
3. user.sql       ← ce fichier
4. main.sql
```
