# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] #2 Type de tables et/ou de modules et/ou objets :
    - system
    - status
    - text
    - ...
- [ ] #3 Fichiers .sql :
    - [x] #3.1 Faire un fichier sql par module. Il contient le schema, les données et les textes
    - [x] #3.5 t_[MODULE]_text avec préfixe obligatoire {OBJET}_ dans text_code.
    - [x] #3.6 Ajouter un IF EXISTS dans tous les inserts de données.
        - [x] #3.6.1 Utiliser "ON CONFLICT (...) DO UPDATE"
    - [x] #3.7 Documenter (dans md_naming_convention.md, paragraphe "Base de données") 
    - [x] #3.8 Tester les sql 2 fois (script ré-entrant) ✅ aucune erreur, NOTICE uniquement   
    - [ ] #3.9 Revoir les script sql !
    
- [ ] #5 BDD 
    - [ ] #5.1 Rechercher les textes en dur
    - [ ] #5.2 Remplacer les codes par des ids
- [ ] #6 Dans le module *system*, ajouter les objets et tables de métadonnées :
    - [x] #6.1 *t_system_module*
    - [x] #6.2 *t_system_object*
    - [x] #6.3 *t_system_page*
    - [x] #6.4 *t_system_table*
    - [x] #6.5 *t_system_column*
    - [ ] #6.9 *system* sera le seul module qui contiendra des données des autres modules (à l'insertion/suppression d'un module, il faudra mettre à jour les tables du module *system*)
    - [ ] #6.10 on garde *t_system_module* dans *system*. Les autres tables sont dispatchés dans leurs modules respectifs : t_[MODULE]_system_object, t_[MODULE]_system_page, etc...
- [ ] #19 Version des modules et des *.sql 

### P2
- [ ] #17 Sous-sites :
    [SPORT_NAME].rilsport.net (ou rilsport.net/[SPORT_NAME])
    [CHAMPIONNAT_NAME].rilsport.net (ou rilsport.net/[CHAMPIONNAT_NAME])
    [TEAM_NAME].rilsport.net (ou rilsport.net/[TEAM_NAME])
    [CLUB_NAME].rilsport.net (ou rilsport.net/[CLUB_NAME])
    [VENUE_NAME].rilsport.net (ou rilsport.net/[VENUE_NAME])
    [PERSON_NAME].rilsport.net (ou rilsport.net/[PERSON_NAME])
- [ ] #20 Sélecteurs (ou simulaire) : à concevoir

### P3
- [ ] #18

### P4 (Basse priorité)
- [ ] #7 

## Évolution

### P1 (Haute priorité)
- [ ] #8 Gestion des permissions/rôles (rôle fonctionnel, applicatif, global, local)
- [ ] #9 Gestion du menu
    - [ ] #9.1 Lister les modules, pages, tables, colonnes afin d'avoir une cohérence dans les libellés

### P2
- [ ] #10 Module blog/news/forum
    - [ ] #10.1 Forum = Discord ?
    - [ ] #10.2 Blog/News = Facebook/Instagram

### P3
- [ ] #11 Couleur des rôles, status, ...

### P4 (Basse priorité)
- [ ] #12 Créer un installateur (code + sql)

## Question / Idées

### P1 (Haute priorité)
- [ ] #13 

### P2
- [ ] #14 

### P3
- [ ] #15 

### P4 (Basse priorité)
- [ ] #16 
