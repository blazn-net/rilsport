# TODO Liste

## Légende
- `[ ]` ou `[]` : Tâche à faire
- `[-]` : Tâche en cours
- `[x]` : Tâche réalisée

---

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] B1-0 BDD
    - [x] B1-0.1 Skill/lesson : structurer les fichiers .md pour prendre en compte toutes les règles (doc/architecture_rules.md, doc/naming_conventions.md, doc/database_rules.md et .agents/rules/)
    - [ ] B1-0.2 ‼️<strong><u>REVOIR LES SCRIPT SQL ET LES TESTER 2 FOIS (SCRIPT RÉ-ENTRANT)</u></strong>‼️
- [ ] B1-1 Module *Sport* : à placer dans un module *sport*. 
    - [x] sport
    - [-] clubs, sections, équipes
        - [-] clubs
        - [-] sections
        - [-] équipes
    - [x] acteurs : joueurs/arbitres/officiels/autres...   
        - [ ] un acteur peut avoir plusieurs rôles. Les roles dépendent des matchs  
    - [ ] compétition, multi compétition (sport event), championnats (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
    - [ ] matches : triangulaires (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
    - [ ] classements (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
    - [ ] fédérations/ligues
    - [ ] organisateur : fédération/ligue/club/acteur
    - [ ] terrains
- [-] B1-2 Module *Zone* : ‼️<strong><u>AVEC CLAUDE</u></strong>‼️.
    - [x] B1-2.0 Monde
    - [x] B1-2.1 Continent
    - [x] B1-2.2 Pays
    - [ ] B1-2.3 Région / Etat / Province / Land (selon les pays)
    - [ ] B1-2.4 Département / District / Comté (selon les pays)
    - [-] B1-2.5 Ville : *=> en cours...*
        - t_zone_city (table légère, peuplée à la demande)
        - champ city_name (texte libre) selon l'objet concerné
    - [ ] B1-2.6 Lien avec club, équipe, user (personne?)

### P2
- [ ] B2-0 Type de tables et/ou de modules et/ou objets :
    - system
    - status
    - text
    - ...
    - [ ] B2-0.1 Où mettre ces modules ? main, system, param, config, séparé ?
- [ ] B2-1 BDD (ref : B1-0)
    - [ ] B2-1.1 Rechercher les textes en dur
    - [ ] B2-1.2 Vérifier les traductions
    - [ ] B2-1.3 Vérifier les statuts de chaque table
    - [ ] B2-1.4 Remplacer les codes par des ids
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
- [ ] B4-3 Revoir les statuts par table

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
- [ ] E2-2 Si un sélecteur n'a qu'une seule valeur, il faut afficher directement cette valeur

### P3
- [ ] E3-0 Couleur des rôles, status, ...
- [ ] E3-1 Icones gratuites en plus de Metro UI
    Bibliothèques d'icônes intégrables (SVG / Font)Lucide Icons : La bibliothèque incontournable du moment, basée sur Feather Icons. Légère, très propre et utilisable en SVG, Web Font ou composants JS.  Tabler Icons : Plus de 5 000 icônes gratuites et open-source au format SVG. Son style épuré s'intègre très bien aux interfaces de type dashboard.  Font Awesome : Le grand classique. La version gratuite propose un large catalogue d'icônes en Police Web ou SVG.  Phosphor Icons : Une bibliothèque très flexible proposant 6 styles différents pour chaque icône (Thin, Light, Regular, Bold, Fill, Duotone).  Remix Icon : Une collection open-source avec des styles Line (contour) et Fill (rempli) très neutres et bien pensés pour le design d'application.  Google Material Symbols : Le système officiel de Google, hautement personnalisable via des variables CSS (épaisseur, remplissage, taille).  Moteurs de recherche et agrégateurs (Recherche & Export)Icônes / Iconify : Un moteur de recherche qui agrège des dizaines de bibliothèques open-source en un seul endroit. Idéal pour copier-coller rapidement le code SVG ou importer la bonne icône.SVG Repo : Des milliers d'icônes et d'illustrations vectorielles libres de droits téléchargeables directement en SVG.  The Noun Project : Une immense base de données d'icônes créées par des designers du monde entier.  

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
