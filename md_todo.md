# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] #1 Modules obligatoires OU fonctionnel VS applicatif : 
    - system 
    - main
    - user
    - lang
    - zone
    - module
    - role
    - ...
- [ ] #2 Type de tables :
    - system
    - status
    - text
    - ...
- [ ] #3 Revoir les tables des langues : en cours
- [ ] #4 Dans la BDD, *lang* est dans *main* et *system* ? Le mettre dans *system* ou *lang*
    - [ ] #4.1 Le module *main* ne contient pour l'instant que la page *home*
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
