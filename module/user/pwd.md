# Mots de passe et Identifiants de Test — Module `user`

Ce document répertorie les comptes utilisateurs de démonstration créés par défaut via `user.sql` pour les tests en environnement local.

---

## 🔑 Comptes de test par défaut

| Profil / Rôle | Identifiant (`username`) | Prénom | Nom | Adresse E-mail | Mot de passe | Rôles attribués | Statut |
|---|---|---|---|---|---|---|---|
| **Administrateur** | `willbask` | William | Baskerville | `willbask@rilsport.com` | `admin123` | `admin` | Actif (`1`) |
| **Utilisateur** | `ireneadler` | Irene | Adler | `ireneadler@rilsport.com` | `user123` | `user` | Actif (`1`) |

---

> ℹ️ **Remarque sur le rôle Administrateur :**
> L'utilisateur ayant le rôle `admin` détient tous les privilèges système. Conformément aux règles d'architecture (B1-3.8.1), il n'a **pas** besoin d'avoir également le rôle `user`.
