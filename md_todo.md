# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] #2 Type de tables :
    - system
    - status
    - text
    - ...
- [ ] #3 Fichiers .sql :
    - [x] #3.1 Faire un fichier pour la structure et un pour les données. Comment les nommer ?
    - [x] #3.2 Faire un .sql par module ou par objet ? 
    - [ ] #3.3 Renommer text.schema.sql en main.text_schema.sql
    - [ ] #3.4 Séparer le texte des données : user.text.sql (insert key + text)
    - [ ] #3.5 t_main_text avec préfixe obligatoire {OBJET}_ dans text_code.
    - [ ] #3.6 Ajouter un IF EXISTS dans tous les inserts de données.
        - [ ] #3.6.1 Utiliser "ON CONFLICT (...) DO UPDATE" ?
        - [ ] #3.6.2 Procédure stockée : p_text_upsert
    - [ ] #3.7 Documenter (dans md_naming_convention.md, paragraphe "Base de données") 
    - [ ] #3.8 Tester les sql 2 fois (script ré-entrant)    
- [ ] #5 BDD : remplacer les codes par des ids
- [ ] #6 Créer un module *module* avec les tables :
    - [ ] #6.1 *t_module_module*
    - [ ] #6.2 *t_module_page*
    - [ ] #6.3 *t_module_table*
    - [ ] #6.4 *t_module_column*
    - [ ] #6.5 *module* sera le seul module qui contiendra des données des autres modules (à l'insertion/suppression d'un module, il faudra mettre à jour les tables du module *module*)

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
