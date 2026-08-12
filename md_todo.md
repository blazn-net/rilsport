# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [x] B1-1 md_naming_convention.md contient plus que des normes de convention de nommage. À splitter en 2 fichiers md.
- [ ] B1-2 Type de tables et/ou de modules et/ou objets :
    - system
    - status
    - text
    - ...
- [ ] B1-3 Fichiers .sql :
    - [x] B1-3.1 Faire un fichier sql par module. Il contient le schema, les données et les textes
    - [x] B1-3.5 t_[MODULE]_text avec préfixe obligatoire {OBJET}_ dans text_code.
    - [x] B1-3.6 Ajouter un IF EXISTS dans tous les inserts de données.
        - [x] B1-3.6.1 Utiliser "ON CONFLICT (...) DO UPDATE"
    - [x] B1-3.7 Documenter (dans md_naming_convention.md, paragraphe "Base de données") 
    - [ ] B1-3.8 Mettre à jour les user : un user pour chaque profil : login + password
        - [ ] Si un user a le role admin, il n'a pas besoin d'autres roles (rôle *user* par exemple). Le role admin a tous les droits => mettre à jour md_naming_convention.md.
    - [ ] B1-3.99 ‼️<strong><u>REVOIR LES SCRIPT SQL ET LES TESTER 2 FOIS (SCRIPT RÉ-ENTRANT)</u></strong>‼️
    
- [ ] B1-5 BDD 
    - [ ] B1-5.1 Rechercher les textes en dur
    - [ ] B1-5.2 Remplacer les codes par des ids
- [ ] B1-6 Dans le module *system*, ajouter les objets et tables de métadonnées :
    - [x] B1-6.1 *t_system_module*
    - [x] B1-6.2 *t_system_object*
    - [x] B1-6.3 *t_system_page*
    - [x] B1-6.4 *t_system_table*
    - [x] B1-6.5 *t_system_column*
    - [x] B1-6.10 on garde *t_system_module* dans *system*. Les autres tables sont dispatchées dans leurs modules respectifs : t_[MODULE]_object, t_[MODULE]_page, t_[MODULE]_table, t_[MODULE]_column, etc.
- [ ] B1-19 Version des modules et des *.sql 

### P2
- [ ] B2-17 Sous-sites :
    [SPORT_NAME].rilsport.net (ou rilsport.net/[SPORT_NAME])
    [CHAMPIONNAT_NAME].rilsport.net (ou rilsport.net/[CHAMPIONNAT_NAME])
    [TEAM_NAME].rilsport.net (ou rilsport.net/[TEAM_NAME])
    [CLUB_NAME].rilsport.net (ou rilsport.net/[CLUB_NAME])
    [VENUE_NAME].rilsport.net (ou rilsport.net/[VENUE_NAME])
    [PERSON_NAME].rilsport.net (ou rilsport.net/[PERSON_NAME])
- [ ] B2-20 Sélecteurs (ou simulaire) : à concevoir

### P3
- [ ] B3-18

### P4 (Basse priorité)
- [ ] B4-7 

## Évolution

### P1 (Haute priorité)
- [ ] E1-8 Gestion des permissions/rôles (rôle fonctionnel, applicatif, global, local)
- [ ] E1-9 Gestion du menu : en cours (C:\Users\smusl\.gemini\antigravity-ide\brain\950e7931-d767-4c3d-9ac6-7b0398a37c92\implementation_plan.md)
    - [x] E1-9.0 Sidebar ou Sidenav ? => NavView
    - [ ] E1-9.1 Lister les modules, pages, tables, colonnes afin d'avoir une cohérence dans les libellés
- [ ] E1-21 Vérifier que chaque objet a ses pages "List" et "Form"

### P2
- [ ] E2-10 Module blog/news/forum
    - [ ] E2-10.1 Forum = Discord ?
    - [ ] E2-10.2 Blog/News = Facebook/Instagram

### P3
- [ ] E3-11 Couleur des rôles, status, ...

### P4 (Basse priorité)
- [ ] E4-12 Créer un installateur (code + sql)

## Question / Idées

### P1 (Haute priorité)
- [ ] Q1-13 

### P2
- [ ] Q2-14 

### P3
- [ ] Q3-15 

### P4 (Basse priorité)
- [ ] Q4-16 
