# Plan de Test & Recette — Module `system`

Ce document constitue le cahier de recette et de test pour le module technique **System**.  
Il couvre l'ossature globale de l'application :
- Le layout principal (`header.php`, `footer.php`, conteneur `navview.php`)
- La mécanique de navigation globale et le volet latéral
- Les exigences **Mobile First** (`[20260908-2319]`) du conteneur d'interface
- Le registre des métadonnées système (`t_system_*`)

---

## 1. Profils d'Utilisateurs & Environnements de Test

### Profils de test
| Profil | Code | Visibilité attendue dans la navigation |
|---|---|---|
| **Visiteur anonyme** | `P1` | Liens publics (Accueil, Compétitions, Clubs, Équipes, Personnes). Sélecteur de langues. Pas de bloc « Mon Compte » ni « Administration ». |
| **Utilisateur standard** | `P2` | Liens publics + bloc « Mon Compte » (Mon Profil, Déconnexion). Pas de bloc « Administration ». |
| **Administrateur** | `P3` | Liens publics + bloc « Mon Compte » + bloc « Administration » (Sous-groupes System et Sports). |

### Formats d'écran
| Format | Viewport | Attentes ergonomiques |
|---|---|---|
| **Mobile (S / M)** | 360px – 414px | Volet `NavView` rétracté par défaut. Déclenchement via icône hamburger. Largeur plein écran ou tiroir avec overlay. Fermeture au tap extérieur. Cibles tactiles ≥ 44px. Zéro débordement horizontal (`overflow-x`). |
| **Tablette** | 768px – 1024px | Volet compact ou ouvert selon configuration Metro UI. |
| **Desktop** | ≥ 1200px | Volet `NavView` ouvert en colonne fixe à gauche du contenu principal. |

---

## 2. Matrice Mobile First du Conteneur Global (`[20260908-2319]`)

| ID | Thème | Test à exécuter sur mobile (≤ 414px) | Résultat attendu | Statut |
|---|---|---|---|:---:|
| **MOB-SYS-01** | **Bouton Hamburger** | Cliquer sur le bouton hamburger (`button.pull-button .mif-menu`). | Le volet latéral `NavView` s'ouvre avec fluidité au-dessus du contenu sans pousser ni déformer la page. | [ ] |
| **MOB-SYS-02** | **Fermeture du volet** | 1. Cliquer à nouveau sur le hamburger.<br>2. Ou taper en dehors du volet latéral sur la zone d'ombre (overlay). | Le volet se referme immédiatement sans rechargement de page. | [ ] |
| **MOB-SYS-03** | **Sélecteur de Langue** | Taper sur l'un des boutons de langue (`FR`, `EN`, `ES`) dans l'en-tête du volet. | Boutons confortables au toucher (hauteur/largeur ≥ 44px), changement de langue immédiat en session (`?lang=XX`). | [ ] |
| **MOB-SYS-04** | **Logo & Identité** | Vérifier l'en-tête du volet et la barre de titre. | Le logo circulaire et le nom du site (`SITENAME`) s'affichent proprement sans débordement de texte. | [ ] |
| **MOB-SYS-05** | **Sous-menus déroulants** | Taper sur les menus déroulants (ex: `System`, `Sports`). | Le sous-menu s'ouvre verticalement (`data-role="collapse"`), les sous-items s'affichent lisiblement sans chevauchement. | [ ] |
| **MOB-SYS-06** | **Défilement vertical** | Faire défiler le menu lorsque tous les sous-groupes sont ouverts. | Le menu dispose de son propre scroll interne vertical si nécessaire, aucun blocage tactile. | [ ] |

---

## 3. Matrice de Navigation et Contrôle d'Accès (`architecture_rules.md` § 4.A)

| ID | Périmètre | Test à exécuter | Résultat attendu | Statut |
|---|---|---|---|:---:|
| **NAV-01** | **Accueil** | Cliquer sur l'item Accueil. | Redirige vers `/`. L'icône `mif-home` et le libellé sont présents. État actif (`active`) surbrillé en rouge. | [ ] |
| **NAV-02** | **Règle 4.A — Libellés** | Examiner tous les libellés du menu. | Les noms sont tous au **pluriel direct** (`Utilisateurs`, `Langues`, `Zones géographiques`, `Sports`, `Saisons`, `Clubs`, `Équipes`, `Personnes / Acteurs`, `Compétitions`). **Aucune mention « Gestion [Objet] »**. | [ ] |
| **NAV-03** | **Bloc « Mon Compte »** | 1. En visiteur (`P1`) : vérifier absence.<br>2. En connecté (`P2`/`P3`) : vérifier présence. | 1. Bloc totalement invisible.<br>2. Affiche « Mon profil (username) » et « Déconnexion ». Liens fonctionnels. | [ ] |
| **NAV-04** | **Bloc « Administration »** | 1. En `P1` ou `P2` : vérifier absence.<br>2. En `P3` (Admin) : vérifier présence. | 1. Bloc totalement masqué dans le code HTML.<br>2. Titre de section « Administration » présent avec les sous-groupes `System` et `Sports`. | [ ] |
| **NAV-05** | **Sous-groupe System** | En Admin (`P3`), déplier `System`. | Contient : `Utilisateurs` (`/user/users`), `Langues` (`/lang/langs`), `Zones géographiques` (`/zone/zones`). Liens fonctionnels. | [ ] |
| **NAV-06** | **Sous-groupe Sports** | En Admin (`P3`), déplier `Sports`. | Contient : `Sports` (`/sport/sports`), `Saisons` (`/sport/seasons`). Liens fonctionnels. | [ ] |
| **NAV-07** | **Persistance de l'état ouvert** | Naviguer sur `/sport/sports` en Admin. | Le sous-menu `Sports` s'ouvre automatiquement (`data-collapsed="false"`), l'item `Sports` a la classe `active`. | [ ] |

---

## 4. Tests BDD et Métadonnées Système

| ID | Périmètre | Test à exécuter | Résultat attendu | Statut |
|---|---|---|---|:---:|
| **SYS-01** | **Ré-entrance SQL** | Exécuter le script `module/system/database/system.sql` deux fois consécutives. | 0 erreur SQL, pas de doublons (`CREATE TABLE IF NOT EXISTS`, `ON CONFLICT DO NOTHING`). | [ ] |
| **SYS-02** | **Registre des Modules** | Vérifier la table `t_system_module`. | Tous les modules installés (`lang`, `system`, `user`, `zone`, `sport`, `main`) sont déclarés avec leur statut actif (`status_id = 1`). | [ ] |
| **SYS-03** | **Traductions UI fixes** | Vérifier les tables `t_system_text_key` et `t_system_text`. | Clés `SYS_*` peuplées pour les 3 langues (`fr`, `en`, `es`). | [ ] |

---

## 5. Synthèse d'Exécution des Tests

- **Date de la recette :** `____ / ____ / 2026`
- **Testeur(s) :** `________________________`
- **Résultat global :** 
  - Total des tests : `15`
  - Tests réussis : `____`
  - Tests en échec / anomalies : `____`
- **Commentaires et anomalies constatées :**  
  *(Consigner ici les écarts relevés sur la navigation globale ou le layout)*
