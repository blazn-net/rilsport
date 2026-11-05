# 📋 RIL Sport — Suivi de Projet & Conventions

Bienvenue sur le tableau de suivi du projet **RIL Sport**.

---

## 🏷️ Légende & Labels des Issues

### Types d'anomalies / tâches
- `type: bug` : Dysfonctionnement ou anomalie à corriger
- `type: evolution` : Nouvelle fonctionnalité métier
- `type: test` : Plan de test, jeu d'essai ou scénario de validation
- `type: refactoring` : Amélioration technique, factorisation, nettoyage de code
- `type: question` : Question d'architecture, idée, réflexion

### Priorités
- `priority: p1` : Bloquant / Urgent (à traiter immédiatement)
- `priority: p2` : Majeur / Important
- `priority: p3` : Normal
- `priority: p4` : Mineur / Secondaire

### Localisation & Statuts
- `front` : Interface utilisateur, vues, CSS Metro UI, contrôleurs d'affichage
- `back` : Modèles de données, règles métier, scripts SQL, API
- **États** :
  - **Nouveau** : Issue ouverte (`open`)
  - **En cours** : Issue ouverte avec le label `in-progress`
  - **Réalisé** : Issue fermée (`closed`)

---

## ⚡ Commandes Utiles & Raccourcis

### Synchronisation Git (Push)
- **Bitbucket** :
  ```bash
  git push origin main
  ```
- **GitHub** :
  ```bash
  git push github main
  ```

### Assistant IA (Antigravity)
- `/learn` : À taper dans le chat de l'assistant après une décision ou une correction importante pour l'enregistrer durablement dans les règles projet (`.agents/AGENTS.md`).

---

## 🖥️ Vue Locale (WAMP)
Une interface web synthétique synchronisée avec les issues GitHub est accessible en local :
- **URL** : `http://localhost/rilsport/doc/generate_todo_view.php`
