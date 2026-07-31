# Rules — Projet RIL Sport

## Maintenance de md_dependencies.md

Chaque module possède un fichier `module/[module]/md_dependencies.md` qui documente :
- le fichier SQL du module
- ses dépendances (quels autres `.sql` doivent être exécutés avant)
- les tables qu'il crée
- l'ordre global d'exécution recommandé

**Ce fichier doit être mis à jour systématiquement** dans les situations suivantes :

- Ajout, suppression ou renommage d'une table dans un fichier `.sql`
- Ajout ou suppression d'une `FOREIGN KEY` entre modules
- Création d'un nouveau module (créer le `md_dependencies.md` correspondant)
- Suppression d'un module (supprimer son `md_dependencies.md`)
- Modification des références croisées entre modules dans les fichiers `.php`

**Convention de nommage des tables** : toujours vérifier que les tables respectent `t_[module]_[objet]` (ex: `t_user_role`, `t_user_user`, `t_lang_lang`). Si une table ne respecte pas cette convention, la renommer dans le `.sql`, les fichiers `.php` concernés, **et** mettre à jour `md_dependencies.md`.
