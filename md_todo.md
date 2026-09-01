# TODO Liste

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] B1-0 ‼️<strong><u>REVOIR LES SCRIPT SQL ET LES TESTER 2 FOIS (SCRIPT RÉ-ENTRANT)</u></strong>‼️
- [ ] B1-1 Module *Sport* : à placer dans un module *sport*. 
    sport
    saisons, 
    championnats, 
    ligues,
    organisateur : fédération/ligue/club/personne
    compétition, multi compétition (sport event)
    clubs
    équipes
    matches
    classements
    terrains
    personne : joueurs/arbitres/officiels/autres...

### P2
- [ ] B2-0 Type de tables et/ou de modules et/ou objets :
    - system
    - status
    - text
    - ...
    - [ ] B2-0.1 Où mettre ces modules ? main, system, param, config, séparé ?
- [ ] B2-1 BDD 
    - [ ] B2-1.1 Rechercher les textes en dur
    - [ ] B2-1.2 Remplacer les codes par des ids
- [ ] B2-99 Version des modules et des *.sql 

### P3
- [ ] B3-1 Sous-sites :
    [SPORT_NAME].rilsport.net (ou rilsport.net/[SPORT_NAME])
    [CHAMPIONNAT_NAME].rilsport.net (ou rilsport.net/[CHAMPIONNAT_NAME])
    [TEAM_NAME].rilsport.net (ou rilsport.net/[TEAM_NAME])
    [CLUB_NAME].rilsport.net (ou rilsport.net/[CLUB_NAME])
    [VENUE_NAME].rilsport.net (ou rilsport.net/[VENUE_NAME])
    [PERSON_NAME].rilsport.net (ou rilsport.net/[PERSON_NAME])
    - [ ] B3-1.1 Comment gérer les sous domaines avec planethoster ?

### P4 (Basse priorité)
- [ ] B4-1 Sélecteurs (ou simulaire) : à concevoir
- [ ] B4-2 Beaucoup de tables ont des noms non traduits en base de données

## Évolution

### P1 (Haute priorité)
- [ ] E1-1 Gestion des permissions/rôles (rôle fonctionnel, applicatif, global, local)
- [ ] E1-2 Gestion du menu : en cours (C:\Users\smusl\.gemini\antigravity-ide\brain\950e7931-d767-4c3d-9ac6-7b0398a37c92\implementation_plan.md)
    - [ ] E1-2.1 Lister les modules, pages, tables, colonnes afin d'avoir une cohérence dans les libellés
    - [ ] E1-2.2 Le
- [ ] E1-3 Vérifier que chaque objet a ses pages "List" et "Form"

### P2
- [ ] E2-0 Module blog/news/forum
    - [ ] E2-0.1 Forum = Discord ?
    - [ ] E2-0.2 Blog/News = Facebook/Instagram
- [ ] E2-1 Gestion des fichiers css

### P3
- [ ] E3-0 Couleur des rôles, status, ...
- [ ] E3-1 Icones gratuites en plus de Metro UI

### P4 (Basse priorité)
- [ ] E4-0 Créer un installateur (code + sql)
- [ ] E4-1 Audit :
    Pour chaque modification d'objet, il faut stocker :
    - la date et l'heure
    - l'user
    - Les modifications : ancienne et nouvelle valeur
    - [IMPORTANT] Comment faire pour ne pas surcharger la base de données ?
    - [IMPORTANT] Comment vérifier la validité des données ?

## Question / Idées

### P1 (Haute priorité)
- [ ] Q1-10 

### P2
- [ ] Q2-0 

### P3
- [ ] Q3-0

### P4 (Basse priorité)
- [ ] Q4-0
