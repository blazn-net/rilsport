# Conventions de Nommage du Projet RIL Sport

Ce document recense les règles de nommage que nous avons adoptées pour ce projet, notamment pour simplifier nos échanges et unifier la structure du code.

## Les Pages "List" et "Form"

Pour distinguer facilement les pages qui affichent des tableaux de celles qui permettent d'ajouter ou de modifier un élément, nous utilisons une règle stricte basée sur le **Pluriel/Singulier** et des termes génériques pour en parler au global.

### Règle 1 : Noms spécifiques (Pluriel vs Singulier)

Lorsque l'on parle d'un objet spécifique dans le projet, on utilise son nom en anglais et on joue sur le pluriel et le singulier :

*   **Nom au Pluriel (ex: `Users`, `Langs`) = La Liste**
    Il s'agit de la page qui affiche le tableau de tous les objets. Cette page sert de point d'entrée pour visualiser et lister les données.
    *Exemple : "Ajoute une colonne statut dans `Users`."*

*   **Nom au Singulier (ex: `User`, `Lang`) = Le Formulaire unique**
    Il s'agit de la page contenant le formulaire. Cette page sert **à la fois à la création et à la modification**, grâce à une vérification sur l'ID dans l'URL.
    *   **Si l'URL n'a pas d'ID** (`/user`) ➔ Mode Ajout (création pure).
    *   **Si l'URL a un ID** (`/user?id=1` ou `/user/1`) ➔ Mode Modification (les champs sont pré-remplis en base pour édition).
    *Exemple : "Mets à jour le champ e-mail dans `User`."*

### Règle 2 : Noms globaux (List vs Form)

Lorsque l'on veut définir une règle technique ou un changement d'interface qui s'applique à l'ensemble du projet sans cibler un objet particulier, nous utilisons des termes globaux :

*   **Les pages "List" (ou les Listes)** : Terme générique pour désigner toutes les pages au pluriel confondues.
    *Exemple : "Mets une infobulle sur les boutons d'action de toutes les pages List."*

*   **Les pages "Form" (ou les Formulaires)** : Terme générique pour désigner toutes les pages au singulier confondues.
    *Exemple : "Faisons en sorte qu'après une mise à jour, on reste sur la page Form."*
