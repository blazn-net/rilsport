# TODO Liste

## Légende
- `[ ]` ou `[]` : Tâche à faire
- `[-]` : Tâche en cours
- `[x]` : Tâche réalisée

## Snippet Antigravity IDE
> Tape `todo` puis touche `Entrée` pour insérer une nouvelle tâche avec ID horodaté unique `[AAAAMMJJ-HHMM]`.

---

Liste des futures tâches à réaliser pour le projet rilsport :

## Bug

### P1 (Haute priorité)
- [ ] [20260908-2236] BDD
    - [x] [20260908-2237] Skill/lesson : structurer les fichiers .md pour prendre en compte toutes les règles (doc/architecture_rules.md, doc/naming_conventions.md, doc/database_rules.md et .agents/rules/)
    - [x] [20260908-2238] ‼️<strong><u>REVOIR LES SCRIPT SQL ET LES TESTER 2 FOIS (SCRIPT RÉ-ENTRANT)</u></strong>‼️ — sport.sql validé x2 ✅
- [ ] [20260908-2239] Module *Sport* : à placer dans un module *sport*.
    - [x] [20260908-2240] sport
    - [-] [20260908-2241] clubs, sections, équipes
        - [-] [20260908-2242] clubs
        - [-] [20260908-2243] sections
        - [-] [20260908-2244] équipes
    - [-] [20260908-2245] acteurs : joueurs/arbitres/officiels/autres...  
        - [ ] [20260908-2246] un acteur peut avoir plusieurs rôles. Les roles dépendent des matchs  
    - [-] [20260908-2247] compétition, multi compétition (sport event), championnats (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
        - [x] `[20260918-1800]` Modèle BDD : 9 tables `t_sport_competition*` (type, status, competition, i18n, edition, phase, group, round, entry)
        - [x] [20260918-1810] MVC Compétitions : Model + Controllers + Vues (list + fiche)
        - [x] [20260918-1820] MVC Éditions : Model + Controllers + Vues (list + fiche avec phases/équipes/sous-éditions)
        - [x] [20260918-1830] Navigation + fix routeur kebab-case (App.php)
        - [ ] MVC Phases, Groupes, Rounds (à faire)
        - [ ] MVC Inscriptions / Entries (à faire)
    - [ ] [20260908-2248] matches : triangulaires (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
    - [ ] [20260908-2249] classements (‼️<strong><u>AVEC CLAUDE</u></strong>‼️)
    - [ ] [20260908-2250] fédérations/ligues
    - [ ] [20260908-2251] organisateur : fédération/ligue/club/acteur
    - [ ] [20260908-2252] terrains
- [-] [20260908-2253] Module *Zone* : ‼️<strong><u>AVEC CLAUDE</u></strong>‼️.
    - [x] [20260908-2254] Monde
    - [x] [20260908-2255] Continent
    - [x] [20260908-2256] Pays
    - [ ] [20260908-2257] Région / Etat / Province / Land (selon les pays)
    - [ ] [20260908-2258] Département / District / Comté (selon les pays)
    - [-] [20260908-2259] Ville : *=> en cours...*
        - [ ] [20260908-2300] t_zone_city (table légère, peuplée à la demande)
        - [ ] [20260908-2301] champ city_name (texte libre) selon l'objet concerné
    - [ ] [20260908-2302] Lien avec club, équipe, user (personne?)

### P2
- [ ] [20260908-2303] Type de tables et/ou de modules et/ou objets :
    - [ ] [20260908-2304] system
    - [ ] [20260908-2305] status
    - [ ] [20260908-2306] text
    - ...
    - [ ] [20260908-2307] Où mettre ces modules ? main, system, param, config, séparé ?
- [ ] [20260908-2308] BDD (ref : B1-0)
    - [ ] [20260908-2309] Rechercher les textes en dur
    - [ ] [20260908-2310] Vérifier les traductions
    - [ ] [20260908-2311] Vérifier les statuts de chaque table
    - [ ] [20260908-2312] Remplacer les codes par des ids
- [ ] [20260908-2313] Version des modules et des *.sql

### P3
- [ ] [20260908-2314] Sous-sites :
    [SPORT_NAME].rilsport.net (ou rilsport.net/[SPORT_NAME])
    [CHAMPIONNAT_NAME].rilsport.net (ou rilsport.net/[CHAMPIONNAT_NAME])
    [TEAM_NAME].rilsport.net (ou rilsport.net/[TEAM_NAME])
    [CLUB_NAME].rilsport.net (ou rilsport.net/[CLUB_NAME])
    [VENUE_NAME].rilsport.net (ou rilsport.net/[VENUE_NAME])
    [PERSON_NAME].rilsport.net (ou rilsport.net/[PERSON_NAME])
    - [ ] [20260908-2315] Comment gérer les sous domaines avec planethoster ?

### P4 (Basse priorité)
- [ ] [20260908-2316] Sélecteurs (ou simulaire) : à concevoir
- [ ] [20260908-2317] Beaucoup de tables ont des noms non traduits en base de données
- [ ] [20260908-2318] Revoir les statuts par table
- [ ] [20260908-2312] Menu : ne pas surligner le texte au survol. Harmoniser couleur de survol entre menu principal et langue

## Évolution

### P1 (Haute priorité)
- [ ] [20260908-2319] Mobile first !
- [ ] [20260919-1349] Sécurité des textbox
- [ ] [20260908-2320] Gestion des permissions/rôles (rôle fonctionnel, applicatif, global, local)
- [ ] [20260908-2321] Gestion du menu : en cours (C:\Users\smusl\.gemini\antigravity-ide\brain\950e7931-d767-4c3d-9ac6-7b0398a37c92\implementation_plan.md)
    - [ ] [20260908-2322] Lister les modules, pages, tables, colonnes afin d'avoir une cohérence dans les libellés
    - [ ] [20260908-2323] Le
- [ ] [20260908-2324] Vérifier que chaque objet a ses pages "List" et "Form"
- [ ] [20260920-2202] Pages List : les champs de recherche doivent être dans l'url
- [-] [20260920-2318] Faire une classe/template parente pour les pages list et form (Pilote réalisé avec module/system/view/common/list_template.php sur Saisons)

### P2
- [ ] [20260908-2325] Module blog/news/forum
    - [ ] [20260908-2326] Forum = Discord ?
    - [ ] [20260908-2327] Blog/News = Facebook/Instagram
- [ ] [20260908-2328] Gestion des fichiers css
- [ ] [20260908-2329] Si un sélecteur n'a qu'une seule valeur, il faut afficher directement cette valeur
- [-] [20260908-2330] Les statuts ne doivent être visibles que pour les admins, sauf exceptions (appliqué sur Clubs et Équipes)
- [-] [20260908-2304] Colonne Action que pour les admins. Le lien sur le nom ouvre le mode view d'une page Form (appliqué sur Clubs)

### P3
- [ ] [20260908-2331] Couleur des rôles, status, ...
- [ ] [20260908-2332] Icones gratuites en plus de Metro UI
    Bibliothèques d'icônes intégrables (SVG / Font)Lucide Icons : La bibliothèque incontournable du moment, basée sur Feather Icons. Légère, très propre et utilisable en SVG, Web Font ou composants JS.  Tabler Icons : Plus de 5 000 icônes gratuites et open-source au format SVG. Son style épuré s'intègre très bien aux interfaces de type dashboard.  Font Awesome : Le grand classique. La version gratuite propose un large catalogue d'icônes en Police Web ou SVG.  Phosphor Icons : Une bibliothèque très flexible proposant 6 styles différents pour chaque icône (Thin, Light, Regular, Bold, Fill, Duotone).  Remix Icon : Une collection open-source avec des styles Line (contour) et Fill (rempli) très neutres et bien pensés pour le design d'application.  Google Material Symbols : Le système officiel de Google, hautement personnalisable via des variables CSS (épaisseur, remplissage, taille).  Moteurs de recherche et agrégateurs (Recherche & Export)Icônes / Iconify : Un moteur de recherche qui agrège des dizaines de bibliothèques open-source en un seul endroit. Idéal pour copier-coller rapidement le code SVG ou importer la bonne icône.SVG Repo : Des milliers d'icônes et d'illustrations vectorielles libres de droits téléchargeables directement en SVG.  The Noun Project : Une immense base de données d'icônes créées par des designers du monde entier. 

### P4 (Basse priorité)
- [ ] [20260908-2333] Créer un installateur (code + sql)
- [ ] [20260908-2334] Audit :
    Pour chaque modification d'objet, il faut stocker :
    - la date et l'heure
    - l'user
    - Les modifications : ancienne et nouvelle valeur
    - [IMPORTANT] Comment faire pour ne pas surcharger la base de données ?
    - [IMPORTANT] Comment vérifier la validité des données ?
- [ ] [20260908-2325] Tester les pages par l'IA
    - [ ] [20260920-2330] Audit statique automatique des vues (script PHP/CLI de validation des règles §4 architecture_rules et mobile first sur tous les fichiers PHP)
    - [ ] [20260920-2331] Tests E2E et visuels par l'IA via `browser_subagent` (navigation réelle, responsive mobile ≤ 414px, rôles visiteur vs admin)
    - [ ] [20260920-2332] Restitution et mise à jour automatique des cahiers de recette (mise à jour des cases [x] et relevé d'anomalies dans `test_plan.md`)

## Question / Idées

### P1 (Haute priorité)
- [ ] [20260908-2335] 

### P2
- [ ] [20260908-2336] 

### P3
- [ ] [20260908-2337] 

### P4 (Basse priorité)
- [ ] [20260908-2338]