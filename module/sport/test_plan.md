# Plan de Test & Recette — Module `sport`

Ce document constitue le cahier de recette et de test pour le module **Sport**.  
Il intègre les exigences impératives :
- **Mobile First** (Point `[20260908-2319]` de `doc/todo.md`)
- **Règles d'Interface et Navigation** (Paragraphe 4 de `doc/architecture_rules.md`)

---

## 1. Profils d'Utilisateurs & Environnements de Test

### Profils de test
| Profil | Code | Droits sur le module Sport |
|---|---|---|
| **Visiteur anonyme** | `P1` | Consultation uniquement (`View`). Pas d'accès formulaire (`Edit`/`Add`), colonne Actions masquée, colonne Statut masquée. |
| **Utilisateur standard** | `P2` | Consultation uniquement (`View`). Pas d'accès formulaire, colonne Actions masquée, colonne Statut masquée. |
| **Administrateur** | `P3` | Droits complets : Consultation (`View`), Création (`Add`), Modification (`Edit`), Suppression logique/physique, colonnes Actions et Statut visibles. |

### Définitions des formats d'écran (Mobile First)
| Format | Viewport | Attentes ergonomiques |
|---|---|---|
| **Mobile (S / M)** | 360px – 414px | Affichage 100% vertical, tableaux Metro UI convertis en cartes (`.table-responsive-cards`), menu latéral NavView replié (icône hamburger), cibles tactiles ≥ 44px. |
| **Tablette** | 768px – 1024px | Tiroir rétractable fluide, adaptation des grilles et colonnes. |
| **Desktop** | ≥ 1200px | Navigation latérale ouverte, tableaux complets en colonnes standard. |

---

## 2. Matrice de Conformité aux Règles d'Interface (`architecture_rules.md` § 4)

Ces 4 règles fondamentales s'appliquent à **toutes les entités** du module :

- [ ] **4.A — Liens de navigation (`navview.php`)**
  - [ ] Chaque page List possède son lien dans le menu de navigation.
  - [ ] Nommage au **pluriel direct** : `Sports`, `Saisons`, `Clubs`, `Équipes`, `Personnes / Acteurs`, `Compétitions`.
  - [ ] **Interdiction** des intitulés du type « Gestion [Objet] ».
  - [ ] Marqueur d'état actif (`class="active"`) fonctionnel sur l'URL correspondante.

- [ ] **4.B — Dualité Consultation (`View`) vs Formulaire (`Form` / `Edit`)**
  - [ ] **Mode View** : Affichage pur en libellés, badges, cartes HTML.
  - [ ] **Interdiction formelle** de balises `<input>`, `<select>`, `<textarea>` même avec attributs `readonly` ou `disabled`.
  - [ ] Aucun bouton de soumission (« Enregistrer ») affiché en consultation.
  - [ ] **Mode Edit / Add** : Éléments interactifs de saisie (Metro UI) et boutons de validation / annulation.

- [ ] **4.C — Colonne Actions et Accès aux Fiches (`[20260908-2304]`)**
  - [ ] Clic sur le nom de l'objet dans un tableau List : ouvre **toujours** la fiche en mode **Consultation (`View`)**, pour tous les utilisateurs (visiteurs et administrateurs).
  - [ ] Colonne **Actions** : visible **uniquement pour les administrateurs** (`!empty($data['isAdmin'])`), totalement masquée pour `P1` et `P2`.
  - [ ] Accès au mode **Édition (`Edit`)** : réservé aux administrateurs, accessible soit via le crayon (`mif-pencil`) de la colonne Actions, soit via le bouton « Modifier » situé en haut de la fiche en mode `View`.

- [ ] **4.D — Visibilité des Statuts (`[20260908-2330]`)**
  - [ ] Colonne « Statut » dans les tableaux List : affichée **uniquement pour les administrateurs**. Masquée aux visiteurs et utilisateurs non-admins.
  - [ ] Badge « Statut » sur les fiches : réservé aux administrateurs (sauf exception métier publique justifiée).

---

## 3. Matrice Mobile First (`[20260908-2319]`)

| ID | Thème | Test à exécuter sur mobile (≤ 414px) | Résultat attendu | Statut |
|---|---|---|---|:---:|
| **MOB-01** | **Volet NavView** | Cliquer sur le bouton hamburger (`mif-menu`). | Le volet s'ouvre au-dessus du contenu sans casser le layout. Un clic hors du volet le referme. | [ ] |
| **MOB-02** | **Sélecteur de Langue** | Cliquer sur les boutons de langues (`FR`, `EN`, `ES`) dans le volet mobile. | Boutons confortables au toucher (≥ 44px), rechargement immédiat dans la langue choisie. | [ ] |
| **MOB-03** | **Tableaux en cartes** | Ouvrir chaque page List (`/sport/sports`, `/sport/clubs`, etc.). | `thead` masqué, chaque ligne devient une carte avec ombre/bordure (`.table-responsive-cards`), libellés visibles via `td::before { content: attr(data-label) }`, **aucun scroll horizontal global**. | [ ] |
| **MOB-04** | **Recherche Metro UI** | Utiliser la barre de recherche au-dessus d'un tableau sur mobile. | Champ 100% largeur, filtrage réactif, clavier virtuel sans saut d'écran indésirable. | [ ] |
| **MOB-05** | **Formulaires (Edit/Add)** | Ouvrir un formulaire de création / édition sur mobile. | Champs (`input`, `select`, `textarea`) à 100% de largeur, lisibles, boutons d'action visibles et empilés proprement. | [ ] |
| **MOB-06** | **Fiches (View)** | Consulter une fiche d'objet (ex: `/sport/club/1`). | Badges, logos, coordonnées et accordéons d'audit s'adaptent verticalement sans troncature. | [ ] |

---

## 4. Scénarios de Test par Entité Métier

### 4.1. Disciplines Sportives (`Sport` / `Sports`)
- **Routes :** `/sport/sports` (List), `/sport/sport` (Add), `/sport/sport/{id}` (View), `/sport/sport/{id}/edit` (Edit)

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **SPT-01** | Navigation Menu | Tous | Cliquer sur `Sports` dans le menu. | Redirection `/sport/sports`. Libellé exact « Sports ». Lien actif en surbrillance. | [ ] |
| **SPT-02** | Liste — Visiteur / User | P1 / P2 | Accéder à `/sport/sports`. | Pas de bouton « Ajouter un sport ». **Colonne Statut masquée**. **Colonne Actions masquée**. Clic sur le nom d'un sport mène vers `View`. | [ ] |
| **SPT-03** | Liste — Admin | P3 | Accéder à `/sport/sports`. | Bouton « Ajouter un sport » présent. Colonnes Statut et Actions visibles avec icônes (crayon, désactivation, corbeille). | [ ] |
| **SPT-04** | Fiche Consultation (`View`) | P1 / P2 / P3 | Cliquer sur le nom d'un sport (ex: Football). | Fiche pure : icône, nom, code, description. **Zéro balise `<input>` / `<select>`**. Pas de bouton « Enregistrer ». Bouton « Modifier » visible uniquement pour Admin (P3). | [ ] |
| **SPT-05** | Création (`Add`) | P3 | Cliquer sur « Ajouter un sport ». | Formulaire interactif complet : code, nom, description, classe icône. Soumission réussie -> message flash vert en liste. | [ ] |
| **SPT-06** | Modification (`Edit`) | P3 | Cliquer sur le bouton crayon ou « Modifier » sur la fiche. | Formulaire d'édition : code verrouillé, sélecteur statut actif/inactif, panel audit visible. Mise à jour fonctionnelle. | [ ] |
| **SPT-07** | Protection accès direct | P1 / P2 | Taper `/sport/sport` dans l'URL ou envoyer un `POST`. | Blocage immédiat avec message d'erreur d'autorisation (403 / redirection). | [ ] |

---

### 4.2. Saisons (`Season` / `Seasons`)
- **Routes :** `/sport/seasons` (List), `/sport/season` (Add), `/sport/season/{id}` (View/Edit)

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **SEA-01** | Menu NavView | P3 | Menu Administration > Sports > `Saisons`. | Lien présent avec l'icône `mif-calendar`, libellé au pluriel « Saisons ». | [ ] |
| **SEA-02** | Filtrage des colonnes | P1 vs P3 | Comparer `/sport/seasons` entre P1 et P3. | P1 : Pas de colonne Statut ni Actions. P3 : Colonnes complètes affichées. | [ ] |
| **SEA-03** | Consultation vs Édition | P1 / P3 | Ouvrir une saison (ex: 2025/2026). | P1 : Mode `View` sans aucun input. P3 : Accès au mode `Edit` avec sélecteurs de dates. | [ ] |
| **SEA-04** | Formatage des dates | Tous | Vérifier l'affichage des dates de début et fin. | Dates formatées selon la langue (`d/m/Y`), aucun format brut non formaté. | [ ] |

---

### 4.3. Clubs & Sections (`Club` / `Clubs`)
- **Routes :** `/sport/clubs` (List), `/sport/club` (Add), `/sport/club/{id}` (View), `/sport/club/{id}/edit` (Edit)

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **CLB-01** | Menu NavView | Tous | Vérifier le menu de niveau 1. | Lien `Clubs` présent avec l'icône `mif-security`. | [ ] |
| **CLB-02** | Liste des clubs | P1 vs P3 | Consulter `/sport/clubs`. | Tableau avec logo, code, nom, ville, pays. Non-admin : pas de colonne Statut ni Actions. Clic nom -> `View`. | [ ] |
| **CLB-03** | Fiche Club (`View`) | P1 / P3 | Consulter la fiche d'un club. | Affichage complet : informations, logo, liste des sections rattachées sous forme de badges. Pas d'input éditable. Bouton « Modifier » pour Admin seul. | [ ] |
| **CLB-04** | Rapprochement Sections | P3 | Éditer un club et cocher/décocher des sections sportives. | Mise à jour correcte de `t_sport_section` sans orphelins. Affichage mis à jour en consultation. | [ ] |
| **CLB-05** | Upload Logo Club | P3 | Téléverser une image de logo (< 2 Mo). | Fichier enregistré, aperçu immédiat, responsive sur mobile. | [ ] |

---

### 4.4. Équipes (`Team` / `Teams`)
- **Routes :** `/sport/teams` (List), `/sport/team` (Add), `/sport/team/{id}` (View/Edit)

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **TEM-01** | Menu NavView | Tous | Vérifier le lien au menu. | Libellé au pluriel « Équipes », icône `mif-groups`. | [ ] |
| **TEM-02** | Données et Badges | Tous | Consulter `/sport/teams`. | Affichage clair de la section de rattachement, genre (M/F/Mixte), catégorie d'âge. Colonnes Statut et Actions visibles uniquement pour P3. | [ ] |
| **TEM-03** | Consultation vs Formulaire | P1 vs P3 | Ouvrir une équipe. | P1 : Fiche en mode `View` propre. P3 : Accès au formulaire de saisie pour rattacher au club/section. | [ ] |

---

### 4.5. Personnes / Acteurs (`Person` / `Persons`)
- **Routes :** `/sport/persons` (List), `/sport/person` (Add), `/sport/person/{id}` (View/Edit)

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **PER-01** | Menu NavView | Tous | Vérifier le lien dans le menu. | Libellé « Personnes / Acteurs », icône `mif-contacts`. | [ ] |
| **PER-02** | Rôles multiples | Tous | Consulter un acteur ayant plusieurs fonctions (joueur, arbitre, dirigeant). | Les rôles s'affichent correctement sous forme de badges en mode `View`. | [ ] |
| **PER-03** | Règle 4.C | P1 vs P3 | Clic sur le nom d'une personne dans la liste. | Ouvre systématiquement la fiche en mode `View`. Colonne Actions masquée pour non-admin. | [ ] |

---

### 4.6. Compétitions & Éditions (`Competition`, `CompetitionEdition`)
- **Routes :** `/sport/competitions`, `/sport/competition/{id}`, `/sport/competition-editions`, `/sport/competition-edition/{id}`

| ID | Test | Profil | Étapes | Résultat attendu | Statut |
|---|---|---|---|---|:---:|
| **CPT-01** | Menu NavView | Tous | Cliquer sur `Compétitions`. | Redirection `/sport/competitions`, icône `mif-trophy`. | [ ] |
| **CPT-02** | Arborescence Métier | Tous | Naviguer : Compétition → Éditions → Phases → Groupes / Poules. | Enchaînement fluide sans perte de contexte ni erreur 404. | [ ] |
| **CPT-03** | Inscriptions (`Entries`) | P3 | Inscrire une équipe dans un groupe/poule. | Données enregistrées dans `t_sport_competition_entry`, visibilité immédiate dans l'édition. | [ ] |
| **CPT-04** | Responsive Poules | Mobile | Consulter une édition / phase sur mobile. | Affichage lisible des classements et poules sans débordement horizontal tronqué. | [ ] |

---

## 5. Tests Transverses : i18n, BDD et Audit

| ID | Périmètre | Test à exécuter | Résultat attendu | Statut |
|---|---|---|---|:---:|
| **TRV-01** | **Traductions UI (i18n)** | Basculer la langue du site en Anglais (`EN`) puis en Espagnol (`ES`). | Tous les libellés, en-têtes de colonnes, boutons et messages flash sont traduits. **Zéro chaîne en dur** en français dans le code. | [ ] |
| **TRV-02** | **Champs d'Audit** | Créer un enregistrement puis le modifier avec un compte admin. | `created_at` et `created_by` initialisés ; `modified_at` et `modified_by` mis à jour après édition. Le panneau d'audit affiche les noms d'utilisateurs correspondants. | [ ] |
| **TRV-03** | **Désactivation vs Suppression** | 1. Cliquer sur désactiver (`delete`).<br>2. Cliquer sur suppression définitive (`forcedelete`). | 1. Le statut passe à `2` (Inactif).<br>2. L'enregistrement est définitivement supprimé de la BDD si aucune contrainte de clé étrangère ne s'y oppose. | [ ] |
| **TRV-04** | **Ré-entrance BDD (`[20260908-2238]`)** | Exécuter le script `sport.sql` une 2e fois consécutive. | 0 erreur SQL, aucune régression, conservation des données (`CREATE TABLE IF NOT EXISTS`, `ON CONFLICT DO NOTHING`). | [ ] |

---

## 6. Synthèse d'Exécution des Tests

- **Date de la recette :** `____ / ____ / 2026`
- **Testeur(s) :** `________________________`
- **Résultat global :** 
  - Total des tests : `32`
  - Tests réussis : `____`
  - Tests en échec / anomalies : `____`
- **Commentaires et anomalies constatées :**  
  *(Consigner ici les écarts relevés pour correction ultérieure)*
