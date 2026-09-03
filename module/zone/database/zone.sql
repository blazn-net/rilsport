-- ============================================================
-- Module : zone
-- ============================================================
-- Ordre d'exécution :
--   1. Enregistrement module dans t_system_module
--   2. Schéma : t_zone_type, t_zone_zone, t_zone_zone_i18n, t_zone_city
--   3. Schéma : t_zone_text_key, t_zone_text
--   4. Données : types de zones et zones racines (Monde → Continents)
--   5. Données : traductions UI fixes (ZONE_)
--
-- Dépendances : lang.sql, system.sql, user.sql
-- ============================================================

-- Synchroniser les séquences si nécessaire
SELECT setval('t_system_module_id_seq', COALESCE((SELECT MAX(id) FROM t_system_module), 1));


-- ============================================================
-- ENREGISTREMENT MODULE DANS SYSTEM
-- ============================================================

INSERT INTO t_system_module (code, is_active, is_system) VALUES
('zone', TRUE, TRUE)
ON CONFLICT (code) DO UPDATE SET
    is_active = EXCLUDED.is_active,
    is_system = EXCLUDED.is_system;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'fr', 'Zone', 'Gestion des zones géographiques (continents, pays, régions)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'en', 'Zone', 'Geographic zones management (continents, countries, regions)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'es', 'Zona', 'Gestión de zonas geográficas (continentes, países, regiones)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;


-- ============================================================
-- SCHÉMA : t_zone_type (Types de zones)
-- ============================================================
-- Exemples : world, continent, country, admin1 (région/état), admin2 (département/province)

CREATE TABLE IF NOT EXISTS t_zone_type (
    code        VARCHAR(20)  PRIMARY KEY,
    level       SMALLINT     NOT NULL,   -- 0=world, 1=continent, 2=country, 3=admin1, 4=admin2
    name        VARCHAR(100) NOT NULL
);


-- ============================================================
-- SCHÉMA : t_zone_zone (Zones géographiques)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_zone (
    id              SERIAL       PRIMARY KEY,
    geonames_id     INT          DEFAULT NULL UNIQUE,    -- ID GeoNames (NULL pour "Monde")
    type_code       VARCHAR(20)  NOT NULL,
    parent_id       INT          DEFAULT NULL,           -- NULL = racine (Monde)
    name_default    VARCHAR(200) NOT NULL,               -- Nom de référence (EN de GeoNames)
    country_code    VARCHAR(2)   DEFAULT NULL,           -- ISO 3166-1 alpha-2 (pour pays et sous-niveaux)
    children_loaded BOOLEAN      NOT NULL DEFAULT FALSE, -- TRUE = enfants déjà récupérés de GeoNames
    status_id       INT          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by      INT          DEFAULT NULL,
    modified_at     TIMESTAMP    DEFAULT NULL,
    modified_by     INT          DEFAULT NULL,
    FOREIGN KEY (type_code)   REFERENCES t_zone_type(code)     ON DELETE RESTRICT,
    FOREIGN KEY (parent_id)   REFERENCES t_zone_zone(id)       ON DELETE SET NULL,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)       ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)       ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_zone_zone_parent    ON t_zone_zone(parent_id);
CREATE INDEX IF NOT EXISTS idx_zone_zone_type      ON t_zone_zone(type_code);
CREATE INDEX IF NOT EXISTS idx_zone_zone_geonames  ON t_zone_zone(geonames_id);
CREATE INDEX IF NOT EXISTS idx_zone_zone_country   ON t_zone_zone(country_code);


-- ============================================================
-- SCHÉMA : t_zone_zone_i18n (Traductions des zones)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_zone_i18n (
    zone_id     INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(200) NOT NULL,
    PRIMARY KEY (zone_id, lang_code),
    FOREIGN KEY (zone_id)   REFERENCES t_zone_zone(id)         ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)  ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : t_zone_city (Villes — référentiel léger)
-- ============================================================
-- Les villes ne sont PAS dans t_zone_zone.
-- Cette table stocke uniquement les villes référencées par d'autres modules
-- (ex: lieu d'un événement), alimentée à la demande depuis GeoNames.

CREATE TABLE IF NOT EXISTS t_zone_city (
    id              SERIAL       PRIMARY KEY,
    geonames_id     INT          DEFAULT NULL UNIQUE,
    zone_id         INT          DEFAULT NULL,           -- zone admin2 (ou admin1) parente
    name_default    VARCHAR(200) NOT NULL,               -- Nom EN de référence
    country_code    VARCHAR(2)   DEFAULT NULL,
    status_id       INT          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by      INT          DEFAULT NULL,
    FOREIGN KEY (zone_id)    REFERENCES t_zone_zone(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES t_user_user(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_zone_city_zone    ON t_zone_city(zone_id);
CREATE INDEX IF NOT EXISTS idx_zone_city_country ON t_zone_city(country_code);


-- ============================================================
-- SCHÉMA : traductions UI du module zone
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_zone_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_zone_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)     ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : Types de zones
-- ============================================================

INSERT INTO t_zone_type (code, level, name) VALUES
('world',     0, 'Monde'),
('continent', 1, 'Continent'),
('country',   2, 'Pays'),
('admin1',    3, 'Région / État / Province'),
('admin2',    4, 'Département / Comté / District')
ON CONFLICT (code) DO UPDATE SET
    level = EXCLUDED.level,
    name  = EXCLUDED.name;


-- ============================================================
-- DONNÉES : Zone racine — Monde (geonames_id 6295630)
-- ============================================================

INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, children_loaded)
VALUES (6295630, 'world', NULL, 'Earth', FALSE)
ON CONFLICT (geonames_id) DO UPDATE SET
    name_default    = EXCLUDED.name_default,
    children_loaded = t_zone_zone.children_loaded;

-- Traductions du Monde (les continents seront chargés depuis GeoNames au 1er clic)
INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
SELECT id, 'fr', 'Monde'  FROM t_zone_zone WHERE geonames_id = 6295630
ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
SELECT id, 'en', 'World'  FROM t_zone_zone WHERE geonames_id = 6295630
ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
SELECT id, 'es', 'Mundo'  FROM t_zone_zone WHERE geonames_id = 6295630
ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name;


-- ============================================================
-- DONNÉES : Traductions UI (ZONE_)
-- ============================================================

INSERT INTO t_zone_text_key (text_code) VALUES
('ZONE_TITLE_ZONES'),
('ZONE_TITLE_ZONE_ADD'),
('ZONE_TITLE_ZONE_EDIT'),
('ZONE_LABEL_NAME'),
('ZONE_LABEL_TYPE'),
('ZONE_LABEL_PARENT'),
('ZONE_LABEL_COUNTRY_CODE'),
('ZONE_LABEL_GEONAMES_ID'),
('ZONE_LABEL_CHILDREN_LOADED'),
('ZONE_LABEL_STATUS'),
('ZONE_MSG_ZONE_ADDED'),
('ZONE_MSG_ZONE_UPDATED'),
('ZONE_MSG_ZONE_DELETED'),
('ZONE_ERR_LOAD_CHILDREN'),
('ZONE_ERR_GEONAMES_UNAVAILABLE'),
('ZONE_ERR_MISSING_FIELDS'),
('ZONE_BTN_LOAD_CHILDREN'),
('ZONE_BTN_SELECT'),
('ZONE_PLACEHOLDER_SEARCH')
ON CONFLICT (text_code) DO NOTHING;

-- Traductions FR
INSERT INTO t_zone_text (text_code, lang_code, text_label) VALUES
('ZONE_TITLE_ZONES',             'fr', 'Zones géographiques'),
('ZONE_TITLE_ZONE_ADD',          'fr', 'Ajouter une zone'),
('ZONE_TITLE_ZONE_EDIT',         'fr', 'Modifier la zone'),
('ZONE_LABEL_NAME',              'fr', 'Nom'),
('ZONE_LABEL_TYPE',              'fr', 'Type'),
('ZONE_LABEL_PARENT',            'fr', 'Zone parente'),
('ZONE_LABEL_COUNTRY_CODE',      'fr', 'Code pays'),
('ZONE_LABEL_GEONAMES_ID',       'fr', 'ID GeoNames'),
('ZONE_LABEL_CHILDREN_LOADED',   'fr', 'Sous-zones chargées'),
('ZONE_LABEL_STATUS',            'fr', 'Statut'),
('ZONE_MSG_ZONE_ADDED',          'fr', 'Zone ajoutée avec succès.'),
('ZONE_MSG_ZONE_UPDATED',        'fr', 'Zone mise à jour avec succès.'),
('ZONE_MSG_ZONE_DELETED',        'fr', 'Zone supprimée.'),
('ZONE_ERR_LOAD_CHILDREN',       'fr', 'Impossible de charger les sous-zones.'),
('ZONE_ERR_GEONAMES_UNAVAILABLE','fr', 'Le service GeoNames est temporairement indisponible.'),
('ZONE_ERR_MISSING_FIELDS',      'fr', 'Champs obligatoires manquants.'),
('ZONE_BTN_LOAD_CHILDREN',       'fr', 'Charger les sous-zones'),
('ZONE_BTN_SELECT',              'fr', 'Sélectionner'),
('ZONE_PLACEHOLDER_SEARCH',      'fr', 'Rechercher une zone...')
ON CONFLICT (text_code, lang_code) DO UPDATE SET text_label = EXCLUDED.text_label;

-- Traductions EN
INSERT INTO t_zone_text (text_code, lang_code, text_label) VALUES
('ZONE_TITLE_ZONES',             'en', 'Geographic zones'),
('ZONE_TITLE_ZONE_ADD',          'en', 'Add a zone'),
('ZONE_TITLE_ZONE_EDIT',         'en', 'Edit zone'),
('ZONE_LABEL_NAME',              'en', 'Name'),
('ZONE_LABEL_TYPE',              'en', 'Type'),
('ZONE_LABEL_PARENT',            'en', 'Parent zone'),
('ZONE_LABEL_COUNTRY_CODE',      'en', 'Country code'),
('ZONE_LABEL_GEONAMES_ID',       'en', 'GeoNames ID'),
('ZONE_LABEL_CHILDREN_LOADED',   'en', 'Sub-zones loaded'),
('ZONE_LABEL_STATUS',            'en', 'Status'),
('ZONE_MSG_ZONE_ADDED',          'en', 'Zone added successfully.'),
('ZONE_MSG_ZONE_UPDATED',        'en', 'Zone updated successfully.'),
('ZONE_MSG_ZONE_DELETED',        'en', 'Zone deleted.'),
('ZONE_ERR_LOAD_CHILDREN',       'en', 'Unable to load sub-zones.'),
('ZONE_ERR_GEONAMES_UNAVAILABLE','en', 'GeoNames service is temporarily unavailable.'),
('ZONE_ERR_MISSING_FIELDS',      'en', 'Required fields are missing.'),
('ZONE_BTN_LOAD_CHILDREN',       'en', 'Load sub-zones'),
('ZONE_BTN_SELECT',              'en', 'Select'),
('ZONE_PLACEHOLDER_SEARCH',      'en', 'Search a zone...')
ON CONFLICT (text_code, lang_code) DO UPDATE SET text_label = EXCLUDED.text_label;

-- Traductions ES
INSERT INTO t_zone_text (text_code, lang_code, text_label) VALUES
('ZONE_TITLE_ZONES',             'es', 'Zonas geográficas'),
('ZONE_TITLE_ZONE_ADD',          'es', 'Añadir una zona'),
('ZONE_TITLE_ZONE_EDIT',         'es', 'Editar zona'),
('ZONE_LABEL_NAME',              'es', 'Nombre'),
('ZONE_LABEL_TYPE',              'es', 'Tipo'),
('ZONE_LABEL_PARENT',            'es', 'Zona padre'),
('ZONE_LABEL_COUNTRY_CODE',      'es', 'Código de país'),
('ZONE_LABEL_GEONAMES_ID',       'es', 'ID GeoNames'),
('ZONE_LABEL_CHILDREN_LOADED',   'es', 'Sub-zonas cargadas'),
('ZONE_LABEL_STATUS',            'es', 'Estado'),
('ZONE_MSG_ZONE_ADDED',          'es', 'Zona añadida con éxito.'),
('ZONE_MSG_ZONE_UPDATED',        'es', 'Zona actualizada con éxito.'),
('ZONE_MSG_ZONE_DELETED',        'es', 'Zona eliminada.'),
('ZONE_ERR_LOAD_CHILDREN',       'es', 'No se pueden cargar las sub-zonas.'),
('ZONE_ERR_GEONAMES_UNAVAILABLE','es', 'El servicio GeoNames no está disponible temporalmente.'),
('ZONE_ERR_MISSING_FIELDS',      'es', 'Faltan campos obligatorios.'),
('ZONE_BTN_LOAD_CHILDREN',       'es', 'Cargar sub-zonas'),
('ZONE_BTN_SELECT',              'es', 'Seleccionar'),
('ZONE_PLACEHOLDER_SEARCH',      'es', 'Buscar una zona...')
ON CONFLICT (text_code, lang_code) DO UPDATE SET text_label = EXCLUDED.text_label;
