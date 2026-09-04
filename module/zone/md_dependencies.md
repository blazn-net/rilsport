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

## Stratégie de chargement
- **Monde → Continents → Pays** : **pré-seedés** dans ce fichier SQL. `children_loaded = TRUE` pour Monde et Continents.
- **Continents** : Continents officiels GeoNames avec leur `geonames_id` (Afrique, Amérique du Nord, Amérique du Sud, Antarctique, Asie, Europe, Océanie). Chaque zone possède désormais un `geonames_id` officiel GeoNames.
- **Pays** : 193 membres ONU pré-seedés. Chaque pays possède son `geonames_id` GeoNames officiel pour le lazy-loading des sous-niveaux (Amérique du Nord : 23, Amérique du Sud : 12, Afrique : 54, Asie : 47, Europe : 44, Océanie : 13).
- **Admin1 (régions/états)** : lazy-loaded depuis l'API GeoNames au premier clic.
- **Admin2 (départements/provinces)** : lazy-loaded depuis l'API GeoNames au premier clic.
- **Villes** : chargées à la demande dans `t_zone_city` (hors treeview principal).


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
