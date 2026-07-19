# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] Modules obligatoires : 
    - system 
    - main
    - user
    - lang
    - zone
    - module
    - role
    - ...
- [ ] Type de tables :
    - system
    - status
    - text
    - ...
- [ ] Revoir les tables des langues : en cours
- [ ] Dans la BDD, *lang* est dans *main* et *system* ? Le mettre dans *system* ou *lang*
    - [ ] Le module *main* ne contient pour l'instant que la page *home*
- [ ] BDD : remplacer les codes par des ids
- [ ] Créer un module *module* avec les tables :
    - [ ] *t_module_module*
    - [ ] *t_module_page*
    - [ ] *t_module_table*
    - [ ] *t_module_column*
    - [ ] *module* sera le seul module qui contiendra des données des autres modules (à l'insertion/suppression d'un module, il faudra mettre à jour les tables du module *module*)

### P2
- [x] Lang : Problème d'encodage : Langue modifiée avec succès, Infos, Bouton, etc...
- [x] User : pas de message après MAJ

### P3
- [x] après mise à jour, rester sur la page Form

### P4 (Basse priorité)
- [ ] 

## Évolution

### P1 (Haute priorité)
- [ ] Gestion des permissions/rôles (rôle fonctionnel, applicatif, global, local)
- [ ] gestion du menu
    - [ ] Lister les modules, pages, tables, colonnes afin d'avoir une cohérence dans les libellés

### P2
- [ ] Module blog/news/forum
    - [ ] Forum = Discord ?
    - [ ] Blog/News = Facebook/Instagram
    

### P3
- [x] Infobulle sur les boutons
- [ ] Couleur des rôles, status, ...

### P4 (Basse priorité)
- [ ] Créer un installateur (code + sql)

## Question / Idées

### P1 (Haute priorité)
- [ ] 

### P2
- [ ] 

### P3
- [ ] 

### P4 (Basse priorité)
- [ ] 
