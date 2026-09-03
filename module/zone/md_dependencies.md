# Dépendances — Module `zone`

## Fichier SQL
`module/zone/database/zone.sql`

## Dépendances requises
> Ce module dépend de **lang.sql**, **system.sql** et **user.sql**.

## Tables créées par ce module

### 1. Référentiel géographique
| Table               | Description                                                            |
|---------------------|------------------------------------------------------------------------|
| `t_zone_type`       | Types de zones (world, continent, country, admin1, admin2)             |
| `t_zone_zone`       | Zones géographiques (lazy-loaded depuis GeoNames)                      |
| `t_zone_zone_i18n`  | Traductions des zones (fr / en / es, extensible)                       |
| `t_zone_city`       | Villes référencées à la demande (non incluses dans t_zone_zone)        |

### 2. Traductions UI du module zone
| Table               | Description                                                            |
|---------------------|------------------------------------------------------------------------|
| `t_zone_text_key`   | Clés de traduction du module zone                                      |
| `t_zone_text`       | Traductions (fr / en / es) du module zone                              |

## Stratégie de chargement (Lazy Tree)
- Au premier lancement, seule la zone racine "Monde" (geonames_id 6295630) est en base.
- Quand un utilisateur clique sur une zone dans le treeview, le backend vérifie `children_loaded`.
- Si `FALSE` → appel à l'API GeoNames (`/childrenJSON?geonameId=...`) → insertion en BDD → `children_loaded = TRUE`.
- Les traductions sont insérées pour toutes les langues actives de `t_lang_lang`.
- Si une nouvelle langue est ajoutée ultérieurement, une commande de re-synchronisation est disponible.

## Modules qui dépendent de `zone`
_(à compléter au fur et à mesure)_

## Ordre d'exécution recommandé
```
1. lang.sql       ← aucune dépendance
2. system.sql     ← dépend de lang
3. user.sql       ← dépend de lang, system
4. zone.sql       ← ce fichier (dépend de lang, system, user)
5. main.sql
6. sport.sql
```
