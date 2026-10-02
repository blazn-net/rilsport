# Conventions de Nommage et Vocabulaire — Projet RIL Sport

Ce document recense les règles impératives de vocabulaire, de nommage de fichiers, de classes, de routes URL et de base de données.

---

## 1. Règle du Singulier / Pluriel

### A. Pages List (Tableaux) = Pluriel
- Fait référence au tableau récapitulatif listant tous les objets.
- Terme employé : **Nom de l'objet au pluriel** (ex: `Users`, `Sports`, `Langs`, `Clubs`).
- Route URL : `/[module]/[objets]` (ex: `/user/users`, `/sport/sports`).

### B. Pages Form (Fiche / Formulaire) = Singulier
- Fait référence au formulaire d'un élément unique, utilisé à la fois pour la création et l'édition.
- Terme employé : **Nom de l'objet au singulier** (ex: `User`, `Sport`, `Lang`, `Club`).
- Route URL Ajout (sans ID) : `/[module]/[objet]` (ex: `/user/user`).
- Route URL Édition (avec ID) : `/[module]/[objet]/[id]` (ex: `/user/user/1`).

---

## 2. Conventions en Base de Données

| Élément | Règle | Exemple |
|---|---|---|
| **Table principale** | `t_[module]_[objet]` | `t_user_user`, `t_sport_sport`, `t_lang_lang` |
| **Table de statut** | `t_[module]_[objet]_status` | `t_user_user_status`, `t_sport_sport_status` |
| **Table de traduction i18n** | `t_[module]_[objet]_i18n` | `t_sport_sport_i18n`, `t_zone_zone_i18n` |
| **Table de liaison N-N** | `t_[module]_[objetA]_[objetB]` | `t_user_user_role`, `t_sport_club_team` |
| **Clés de traduction UI** | `t_[module]_text_key` | `t_user_text_key`, `t_sport_text_key` |
| **Traductions UI** | `t_[module]_text` | `t_user_text`, `t_sport_text` |
| **Clé primaire simple** | `id SERIAL PRIMARY KEY` | `id` |
| **Clé étrangère** | `[objet_cible]_id` | `status_id`, `created_by`, `sport_id` |
| **Index standard** | `idx_[module]_[objet]_[colonne]` | `idx_zone_city_country` |
| **Contrainte unique** | `uq_[module]_[objet]_[colonne]` | `uq_zone_geonames_id` |

### Nommage des clés de traduction d'interface (`text_code`) :
Format obligatoire : `{OBJET}_{TYPE}_{ACTION_OU_NOM}` en **MAJUSCULES**.
- `USER_LBL_USERNAME`
- `USER_STATUS_ACTIVE`
- `SPORT_BTN_CREATE`
- `SYS_NAV_DASHBOARD`

---

## 3. Conventions de Code PHP

### A. Fichiers et Dossiers
- Fichiers de Contrôleurs : PascalCase au singulier dans `controller/` (ex: `module/sport/controller/Sport.php`).
- Fichiers de Modèles : PascalCase au singulier dans `model/` (ex: `module/sport/model/Sport.php`).
- Répertoire de Vues : Minuscules au singulier sous `view/[objet]/` (ex: `view/sport/list.php`, `view/sport/form.php`).
- Fichier SQL : Minuscules au singulier ou nom de module (ex: `database/sport.sql`).

### B. Classes et Méthodes
- Noms de classes : PascalCase (ex: `class SportController extends Controller`).
- Noms de méthodes : camelCase (ex: `public function getActiveSports()`).
- Propriétés : camelCase (ex: `protected $dbConnection`).
- Constantes : UPPER_SNAKE_CASE (ex: `const STATUS_ACTIVE = 1;`).

---

## 4. Conventions d'URL

Format standardisé du routage :
```
/[module]/[objet_ou_page]/[methode]/[id]
```

- Consultation de la liste : `/sport/sports` (ou `/sport/sports/index`)
- Formulaire d'ajout : `/sport/sport`
- Formulaire d'édition : `/sport/sport/edit/12` (ou `/sport/sport/12`)
- Action spécifique : `/sport/sport/delete/12`

---

## 5. Conventions d'Interface Utilisateur (UI) — Pages List

### A. Bouton « + Ajouter »
- **Couleur obligatoire : `info` (bleu)** — classe Metro UI `button info`
- Visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`)
- Placé en haut à droite de la page, aligné avec le titre `<h2>`
- **Libellé affiché : toujours « Ajouter »** (clé `SYS_BTN_ADD`) — jamais un libellé objet-spécifique
- `addBtnText` (config) = infobulle `title` uniquement (ex : « Ajouter un sport »)
- Icône : `mif-plus` + texte dans `<span class="btn-text">` (masqué sur mobile ≤ 414px)
- Exemple HTML : `<a href="..." class="button info" title="Ajouter un sport"><span class="mif-plus"></span> <span class="btn-text">Ajouter</span></a>`

### B. Boutons d'action dans la colonne « Actions »
| Action | Classe | Icône |
|---|---|---|
| Modifier | `button small info` | `mif-pencil` |
| Désactiver | `button small warning` | `mif-cancel` |
| Réactiver | `button small success` | `mif-checkmark` |
| Supprimer | `button small alert` | `mif-bin` |

### C. Colonnes admin-only
- Les colonnes **Statut** et **Actions** sont visibles **uniquement pour les admins** (`!empty($data['isAdmin'])`).
- Les visiteurs non-admins ne voient ni statut ni actions.

### D. Badges de statut
| Statut | Classe badge |
|---|---|
| Actif (`status_id = 1`) | `badge success` |
| Inactif / Désactivé (`status_id = 2`) | `badge secondary` |
| Brouillon (`status_id = 3`) | `badge warning` |

### E. Boutons Filtrer / Réinitialiser
- **Filtrer** : `button primary` avec icône `mif-filter`
- **Réinitialiser** : `button secondary` avec icône `mif-reload`
- Ces boutons sont générés automatiquement par `list_template.php`

### F. Héritage obligatoire
> Toute page de type List **doit** utiliser `module/system/view/common/list_template.php`.  
> Ce template centralise : en-tête, alertes flash, barre de recherche GET, tableau responsive Metro UI, colonnes admin-only, pagination et contrôles de défilement horizontal.
