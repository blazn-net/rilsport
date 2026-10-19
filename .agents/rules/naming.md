# Règles de Nommage — Projet RIL Sport

Ces règles s'appliquent à tous les fichiers, classes, routes et éléments BDD.

## 1. Vocabulaire Singulier / Pluriel
- **Pluriel = Page List (tableau récapitulatif)**
  - Exemple : `Users`, `Sports`, `Langs`.
  - Route : `/[module]/[objets]` (ex: `/user/users`, `/sport/sports`).
- **Singulier = Page Entity (fiche individuelle : View / Edit / Add)**
  - Exemple : `User`, `Sport`, `Lang`.
  - Contrôleur : `[Objet] extends Core\EntityController`.
  - Route consultation : `/[module]/[objet]/[code]` (ex: `/sport/club/psg`, `/sport/season/2027`).
  - Route édition : `/[module]/[objet]/edit/[code]` (ex: `/sport/club/edit/psg`).
  - Route création : `/[module]/[objet]` (ex: `/sport/club`).

## 2. Base de Données
- Tables : `t_[module]_[objet]` (ex: `t_sport_sport`, `t_sport_sport_status`).
- Tables i18n : `t_[module]_[objet]_i18n`.
- Tables UI fixes : `t_[module]_text_key` et `t_[module]_text`.
- Clés primaires : `id`.
- Clés étrangères : `[objet]_id`.
- Clés de traduction : `{OBJET}_{TYPE}_{NOM}` en majuscules (ex: `SPORT_LBL_NAME`, `SPORT_STATUS_ACTIVE`).
