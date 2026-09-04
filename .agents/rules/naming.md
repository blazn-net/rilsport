# Règles de Nommage — Projet RIL Sport

Ces règles s'appliquent à tous les fichiers, classes, routes et éléments BDD.

## 1. Vocabulaire Singulier / Pluriel
- **Pluriel = Page List (tableau récapitulatif)**
  - Exemple : `Users`, `Sports`, `Langs`.
  - Route : `/[module]/[objets]` (ex: `/user/users`, `/sport/sports`).
- **Singulier = Page Form (formulaire unifié)**
  - Exemple : `User`, `Sport`, `Lang`.
  - Route création : `/[module]/[objet]` (ex: `/user/user`).
  - Route édition : `/[module]/[objet]/[id]` (ex: `/user/user/1`).

## 2. Base de Données
- Tables : `t_[module]_[objet]` (ex: `t_sport_sport`, `t_sport_sport_status`).
- Tables i18n : `t_[module]_[objet]_i18n`.
- Tables UI fixes : `t_[module]_text_key` et `t_[module]_text`.
- Clés primaires : `id`.
- Clés étrangères : `[objet]_id`.
- Clés de traduction : `{OBJET}_{TYPE}_{NOM}` en majuscules (ex: `SPORT_LBL_NAME`, `SPORT_STATUS_ACTIVE`).
