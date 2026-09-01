-- ============================================================
-- Module : sport
-- ============================================================
-- Ordre d'exécution :
--   1. Enregistrement module dans t_system_module
--   2. Schéma : t_sport_sport, t_sport_season
--   3. Schéma : t_sport_text_key, t_sport_text
--   4. Schéma : Métadonnées du module sport
--   5. Données : Sports & Saisons par défaut
--   6. Données : traductions UI fixes (SPORT_)
--
-- Dépendances : lang.sql, system.sql, user.sql
-- ============================================================

-- Synchroniser les séquences si nécessaire
SELECT setval('t_system_module_id_seq', COALESCE((SELECT MAX(id) FROM t_system_module), 1));

-- ============================================================
-- ENREGISTREMENT MODULE DANS SYSTEM
-- ============================================================

INSERT INTO t_system_module (code, is_active, is_system) VALUES
('sport', TRUE, FALSE)
ON CONFLICT (code) DO UPDATE SET
    is_active = EXCLUDED.is_active,
    is_system = EXCLUDED.is_system;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'fr', 'Sport', 'Gestion des disciplines et activités sportives' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'en', 'Sport', 'Sports and athletic activities management' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'es', 'Deporte', 'Gestión de deportes y actividades deportivas' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;


-- ============================================================
-- SCHÉMA : t_sport_sport (Sports / Disciplines)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_sport (
    id          SERIAL       PRIMARY KEY,
    code        VARCHAR(50)  NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    icon        VARCHAR(50)  DEFAULT 'mif-trophy',
    status_id   INT          NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by  INT          DEFAULT NULL,
    modified_at TIMESTAMP    DEFAULT NULL,
    modified_by INT          DEFAULT NULL,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id) ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id) ON DELETE SET NULL
);


-- ============================================================
-- SCHÉMA : t_sport_season (Saisons sportives)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_season (
    id          SERIAL       PRIMARY KEY,
    code        VARCHAR(50)  NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    date_start  DATE         NOT NULL,
    date_end    DATE         NOT NULL,
    status_id   INT          NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by  INT          DEFAULT NULL,
    modified_at TIMESTAMP    DEFAULT NULL,
    modified_by INT          DEFAULT NULL,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id) ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id) ON DELETE SET NULL
);


-- ============================================================
-- SCHÉMA : traductions UI du module sport
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_sport_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_sport_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)      ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : métadonnées du module sport
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_object (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    code        VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    CONSTRAINT uk_sport_object_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_sport_object_i18n (
    object_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (object_id, lang_code),
    FOREIGN KEY (object_id) REFERENCES t_sport_object(id)  ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_sport_page (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    object_id   INT          DEFAULT NULL,
    code        VARCHAR(50)  NOT NULL,
    page_type   VARCHAR(20)  NOT NULL DEFAULT 'list',
    url_path    VARCHAR(255) NOT NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_sport_object(id)  ON DELETE SET NULL,
    CONSTRAINT uk_sport_page_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_sport_page_i18n (
    page_id     INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (page_id, lang_code),
    FOREIGN KEY (page_id)   REFERENCES t_sport_page(id)   ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_sport_table (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    object_id   INT          DEFAULT NULL,
    table_name  VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_system_object(id)  ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_sport_table_i18n (
    table_id    INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    description TEXT         NOT NULL,
    PRIMARY KEY (table_id, lang_code),
    FOREIGN KEY (table_id)  REFERENCES t_sport_table(id)  ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_sport_column (
    id               SERIAL       PRIMARY KEY,
    table_id         INT          NOT NULL,
    column_name      VARCHAR(100) NOT NULL,
    data_type        VARCHAR(50)  NOT NULL,
    is_nullable      BOOLEAN      NOT NULL DEFAULT TRUE,
    is_primary_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    is_foreign_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    fk_target_table  VARCHAR(100) DEFAULT NULL,
    fk_target_column VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (table_id) REFERENCES t_sport_table(id) ON DELETE CASCADE,
    CONSTRAINT uk_sport_column_table_col UNIQUE (table_id, column_name)
);

CREATE TABLE IF NOT EXISTS t_sport_column_i18n (
    column_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    label       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (column_id, lang_code),
    FOREIGN KEY (column_id) REFERENCES t_sport_column(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : métadonnées du module sport (Objets & Pages)
-- ============================================================

-- Objets métier
INSERT INTO t_sport_object (module_id, code)
SELECT id, 'sport' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'season' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Sport' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Saison' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

-- Pages / Routes
INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'sports', 'list', '/sport/sports' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'sport', 'form', '/sport/sport' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'seasons', 'list', '/sport/seasons' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'season', 'form', '/sport/season' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_sport' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_season' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;


-- ============================================================
-- DONNÉES : Sports par défaut
-- ============================================================

INSERT INTO t_sport_sport (code, name, description, icon, status_id) VALUES
('football',   'Football',   'Sport collectif se jouant avec un ballon rond sur un terrain rectangulaire.', 'mif-trophy', 1),
('basketball', 'Basketball', 'Sport collectif opposant deux équipes de cinq joueurs sur un terrain rectangulaire.', 'mif-trophy', 1),
('tennis',     'Tennis',     'Sport de raquette qui oppose soit deux joueurs soit quatre joueurs.', 'mif-trophy', 1),
('rugby',      'Rugby',      'Sport d''équipe qui se joue avec un ballon ovale.', 'mif-trophy', 1),
('handball',   'Handball',   'Sport collectif où deux équipes de sept joueurs s''affrontent avec un ballon.', 'mif-trophy', 1),
('volleyball', 'Volleyball', 'Sport collectif opposant deux équipes de six joueurs séparées par un filet.', 'mif-trophy', 1)
ON CONFLICT (code) DO UPDATE SET
    name        = EXCLUDED.name,
    description = EXCLUDED.description,
    icon        = EXCLUDED.icon,
    status_id   = EXCLUDED.status_id;


-- ============================================================
-- DONNÉES : Saisons par défaut
-- ============================================================

INSERT INTO t_sport_season (code, name, date_start, date_end, status_id) VALUES
('2025-2026', 'Saison 2025-2026', '2025-08-01', '2026-07-31', 1),
('2026-2027', 'Saison 2026-2027', '2026-08-01', '2027-07-31', 1),
('2026',      'Saison 2026',      '2026-01-01', '2026-12-31', 1),
('2027',      'Saison 2027',      '2027-01-01', '2027-12-31', 1)
ON CONFLICT (code) DO UPDATE SET
    name       = EXCLUDED.name,
    date_start = EXCLUDED.date_start,
    date_end   = EXCLUDED.date_end,
    status_id  = EXCLUDED.status_id;


-- ============================================================
-- DONNÉES : clés de traduction UI (SPORT_)
-- ============================================================

INSERT INTO t_sport_text_key (text_code) VALUES
('SPORT_SPORTS_MGT'),
('SPORT_SEASONS_MGT'),
('SPORT_ADD_SPORT_BTN'),
('SPORT_ADD_SEASON_BTN'),
('SPORT_EDIT_SPORT_TITLE'),
('SPORT_ADD_SPORT_TITLE'),
('SPORT_EDIT_SEASON_TITLE'),
('SPORT_ADD_SEASON_TITLE'),
('SPORT_CODE'),
('SPORT_NAME'),
('SPORT_DESCRIPTION'),
('SPORT_ICON'),
('SPORT_STATUS'),
('SPORT_ACTIVE'),
('SPORT_INACTIVE'),
('SPORT_CODE_LABEL'),
('SPORT_NAME_LABEL'),
('SPORT_DESCRIPTION_LABEL'),
('SPORT_ICON_LABEL'),
('SPORT_STATUS_LABEL'),
('SPORT_CODE_HELP'),
('SPORT_ICON_HELP'),
('SPORT_SEASON_CODE'),
('SPORT_SEASON_NAME'),
('SPORT_SEASON_SPORT'),
('SPORT_SEASON_DATE_START'),
('SPORT_SEASON_DATE_END'),
('SPORT_SEASON_ALL_SPORTS'),
('SPORT_SEASON_CODE_LABEL'),
('SPORT_SEASON_NAME_LABEL'),
('SPORT_SEASON_SPORT_LABEL'),
('SPORT_SEASON_START_LABEL'),
('SPORT_SEASON_END_LABEL'),
('SPORT_SEASON_CODE_HELP'),
('SPORT_MSG_SPORT_ADDED'),
('SPORT_MSG_SPORT_UPDATED'),
('SPORT_MSG_SPORT_DEACTIVATED'),
('SPORT_MSG_SPORT_DELETED'),
('SPORT_MSG_SEASON_ADDED'),
('SPORT_MSG_SEASON_UPDATED'),
('SPORT_MSG_SEASON_DEACTIVATED'),
('SPORT_MSG_SEASON_DELETED'),
('SPORT_ERR_MISSING_FIELDS'),
('SPORT_ERR_CODE_EXISTS'),
('SPORT_ERR_SEASON_MISSING_FIELDS'),
('SPORT_ERR_SEASON_CODE_EXISTS'),
('SPORT_ERR_DATES_INVALID'),
('SPORT_DELETE_SPORT_CONFIRM'),
('SPORT_DELETE_SEASON_CONFIRM'),
('SPORT_FORCE_DELETE_CONFIRM'),
('SPORT_INFO_PANEL')
ON CONFLICT (text_code) DO NOTHING;


-- ============================================================
-- DONNÉES : traductions UI (fr / en / es)
-- ============================================================

INSERT INTO t_sport_text (text_code, lang_code, text_label) VALUES
('SPORT_SPORTS_MGT',          'fr', 'Sports'),
('SPORT_SPORTS_MGT',          'en', 'Sports'),
('SPORT_SPORTS_MGT',          'es', 'Deportes'),

('SPORT_SEASONS_MGT',         'fr', 'Saisons'),
('SPORT_SEASONS_MGT',         'en', 'Seasons'),
('SPORT_SEASONS_MGT',         'es', 'Temporadas'),

('SPORT_ADD_SPORT_BTN',       'fr', 'Ajouter un sport'),
('SPORT_ADD_SPORT_BTN',       'en', 'Add Sport'),
('SPORT_ADD_SPORT_BTN',       'es', 'Añadir deporte'),

('SPORT_ADD_SEASON_BTN',      'fr', 'Ajouter une saison'),
('SPORT_ADD_SEASON_BTN',      'en', 'Add Season'),
('SPORT_ADD_SEASON_BTN',      'es', 'Añadir temporada'),

('SPORT_EDIT_SPORT_TITLE',    'fr', 'Modifier le sport'),
('SPORT_EDIT_SPORT_TITLE',    'en', 'Edit Sport'),
('SPORT_EDIT_SPORT_TITLE',    'es', 'Editar deporte'),

('SPORT_ADD_SPORT_TITLE',     'fr', 'Ajouter un sport'),
('SPORT_ADD_SPORT_TITLE',     'en', 'Add Sport'),
('SPORT_ADD_SPORT_TITLE',     'es', 'Añadir deporte'),

('SPORT_EDIT_SEASON_TITLE',   'fr', 'Modifier la saison'),
('SPORT_EDIT_SEASON_TITLE',   'en', 'Edit Season'),
('SPORT_EDIT_SEASON_TITLE',   'es', 'Editar temporada'),

('SPORT_ADD_SEASON_TITLE',    'fr', 'Ajouter une saison'),
('SPORT_ADD_SEASON_TITLE',    'en', 'Add Season'),
('SPORT_ADD_SEASON_TITLE',    'es', 'Añadir temporada'),

('SPORT_CODE',                'fr', 'Code'),
('SPORT_CODE',                'en', 'Code'),
('SPORT_CODE',                'es', 'Código'),

('SPORT_NAME',                'fr', 'Nom'),
('SPORT_NAME',                'en', 'Name'),
('SPORT_NAME',                'es', 'Nombre'),

('SPORT_DESCRIPTION',         'fr', 'Description'),
('SPORT_DESCRIPTION',         'en', 'Description'),
('SPORT_DESCRIPTION',         'es', 'Descripción'),

('SPORT_ICON',                'fr', 'Icône'),
('SPORT_ICON',                'en', 'Icon'),
('SPORT_ICON',                'es', 'Icono'),

('SPORT_STATUS',              'fr', 'Statut'),
('SPORT_STATUS',              'en', 'Status'),
('SPORT_STATUS',              'es', 'Estado'),

('SPORT_ACTIVE',              'fr', 'Actif'),
('SPORT_ACTIVE',              'en', 'Active'),
('SPORT_ACTIVE',              'es', 'Activo'),

('SPORT_INACTIVE',            'fr', 'Inactif'),
('SPORT_INACTIVE',            'en', 'Inactive'),
('SPORT_INACTIVE',            'es', 'Inactivo'),

('SPORT_CODE_LABEL',          'fr', 'Code unique du sport'),
('SPORT_CODE_LABEL',          'en', 'Unique sport code'),
('SPORT_CODE_LABEL',          'es', 'Código único del deporte'),

('SPORT_NAME_LABEL',          'fr', 'Nom de la discipline'),
('SPORT_NAME_LABEL',          'en', 'Discipline name'),
('SPORT_NAME_LABEL',          'es', 'Nombre de la disciplina'),

('SPORT_DESCRIPTION_LABEL',   'fr', 'Description'),
('SPORT_DESCRIPTION_LABEL',   'en', 'Description'),
('SPORT_DESCRIPTION_LABEL',   'es', 'Descripción'),

('SPORT_ICON_LABEL',          'fr', 'Classe d''icône (ex: mif-trophy)'),
('SPORT_ICON_LABEL',          'en', 'Icon class (e.g. mif-trophy)'),
('SPORT_ICON_LABEL',          'es', 'Clase de icono (ej: mif-trophy)'),

('SPORT_STATUS_LABEL',        'fr', 'Statut'),
('SPORT_STATUS_LABEL',        'en', 'Status'),
('SPORT_STATUS_LABEL',        'es', 'Estado'),

('SPORT_CODE_HELP',           'fr', 'Le code ne peut plus être modifié après la création.'),
('SPORT_CODE_HELP',           'en', 'The code cannot be modified after creation.'),
('SPORT_CODE_HELP',           'es', 'El código no se puede modificar después de la creación.'),

('SPORT_ICON_HELP',           'fr', 'Nom de classe d''icône Metro UI (mif-*).'),
('SPORT_ICON_HELP',           'en', 'Metro UI icon class name (mif-*).'),
('SPORT_ICON_HELP',           'es', 'Nombre de clase de icono Metro UI (mif-*).'),

('SPORT_SEASON_CODE',         'fr', 'Code saison'),
('SPORT_SEASON_CODE',         'en', 'Season code'),
('SPORT_SEASON_CODE',         'es', 'Código de temporada'),

('SPORT_SEASON_NAME',         'fr', 'Nom de la saison'),
('SPORT_SEASON_NAME',         'en', 'Season name'),
('SPORT_SEASON_NAME',         'es', 'Nombre de temporada'),

('SPORT_SEASON_SPORT',        'fr', 'Sport rattaché'),
('SPORT_SEASON_SPORT',        'en', 'Associated sport'),
('SPORT_SEASON_SPORT',        'es', 'Deporte asociado'),

('SPORT_SEASON_DATE_START',   'fr', 'Date de début'),
('SPORT_SEASON_DATE_START',   'en', 'Start date'),
('SPORT_SEASON_DATE_START',   'es', 'Fecha de inicio'),

('SPORT_SEASON_DATE_END',     'fr', 'Date de fin'),
('SPORT_SEASON_DATE_END',     'en', 'End date'),
('SPORT_SEASON_DATE_END',     'es', 'Fecha de fin'),

('SPORT_SEASON_ALL_SPORTS',   'fr', 'Tous les sports (Globale)'),
('SPORT_SEASON_ALL_SPORTS',   'en', 'All sports (Global)'),
('SPORT_SEASON_ALL_SPORTS',   'es', 'Todos los deportes (Global)'),

('SPORT_SEASON_CODE_LABEL',   'fr', 'Code unique (ex: 2026-2027 ou 2027)'),
('SPORT_SEASON_CODE_LABEL',   'en', 'Unique code (e.g. 2026-2027 or 2027)'),
('SPORT_SEASON_CODE_LABEL',   'es', 'Código único (ej: 2026-2027 o 2027)'),

('SPORT_SEASON_NAME_LABEL',   'fr', 'Nom de la saison (ex: Saison 2026-2027)'),
('SPORT_SEASON_NAME_LABEL',   'en', 'Season name (e.g. Season 2026-2027)'),
('SPORT_SEASON_NAME_LABEL',   'es', 'Nombre de la temporada (ej: Temporada 2026-2027)'),

('SPORT_SEASON_SPORT_LABEL',  'fr', 'Sport concerné (laisser vide si globale)'),
('SPORT_SEASON_SPORT_LABEL',  'en', 'Associated sport (leave empty for global)'),
('SPORT_SEASON_SPORT_LABEL',  'es', 'Deporte correspondiente (dejar en blanco para global)'),

('SPORT_SEASON_START_LABEL',  'fr', 'Date de début de saison'),
('SPORT_SEASON_START_LABEL',  'en', 'Season start date'),
('SPORT_SEASON_START_LABEL',  'es', 'Fecha de inicio de temporada'),

('SPORT_SEASON_END_LABEL',    'fr', 'Date de fin de saison'),
('SPORT_SEASON_END_LABEL',    'en', 'Season end date'),
('SPORT_SEASON_END_LABEL',    'es', 'Fecha de fin de temporada'),

('SPORT_SEASON_CODE_HELP',    'fr', 'Identifiant de la saison.'),
('SPORT_SEASON_CODE_HELP',    'en', 'Season identifier.'),
('SPORT_SEASON_CODE_HELP',    'es', 'Identificador de la temporada.'),

('SPORT_MSG_SPORT_ADDED',     'fr', 'Sport ajouté avec succès.'),
('SPORT_MSG_SPORT_ADDED',     'en', 'Sport added successfully.'),
('SPORT_MSG_SPORT_ADDED',     'es', 'Deporte añadido con éxito.'),

('SPORT_MSG_SPORT_UPDATED',   'fr', 'Sport mis à jour avec succès.'),
('SPORT_MSG_SPORT_UPDATED',   'en', 'Sport updated successfully.'),
('SPORT_MSG_SPORT_UPDATED',   'es', 'Deporte actualizado con éxito.'),

('SPORT_MSG_SPORT_DEACTIVATED','fr', 'Sport désactivé.'),
('SPORT_MSG_SPORT_DEACTIVATED','en', 'Sport deactivated.'),
('SPORT_MSG_SPORT_DEACTIVATED','es', 'Deporte desactivado.'),

('SPORT_MSG_SPORT_DELETED',   'fr', 'Sport supprimé.'),
('SPORT_MSG_SPORT_DELETED',   'en', 'Sport deleted.'),
('SPORT_MSG_SPORT_DELETED',   'es', 'Deporte eliminado.'),

('SPORT_MSG_SEASON_ADDED',    'fr', 'Saison ajoutée avec succès.'),
('SPORT_MSG_SEASON_ADDED',    'en', 'Season added successfully.'),
('SPORT_MSG_SEASON_ADDED',    'es', 'Temporada añadida con éxito.'),

('SPORT_MSG_SEASON_UPDATED',  'fr', 'Saison mise à jour avec succès.'),
('SPORT_MSG_SEASON_UPDATED',  'en', 'Season updated successfully.'),
('SPORT_MSG_SEASON_UPDATED',  'es', 'Temporada actualizada con éxito.'),

('SPORT_MSG_SEASON_DEACTIVATED','fr', 'Saison désactivée.'),
('SPORT_MSG_SEASON_DEACTIVATED','en', 'Season deactivated.'),
('SPORT_MSG_SEASON_DEACTIVATED','es', 'Temporada desactivada.'),

('SPORT_MSG_SEASON_DELETED',  'fr', 'Saison supprimée.'),
('SPORT_MSG_SEASON_DELETED',  'en', 'Season deleted.'),
('SPORT_MSG_SEASON_DELETED',  'es', 'Temporada eliminada.'),

('SPORT_ERR_MISSING_FIELDS',  'fr', 'Veuillez remplir tous les champs obligatoires (Code et Nom).'),
('SPORT_ERR_MISSING_FIELDS',  'en', 'Please fill in all required fields (Code and Name).'),
('SPORT_ERR_MISSING_FIELDS',  'es', 'Por favor complete todos los campos obligatorios (Código y Nombre).'),

('SPORT_ERR_CODE_EXISTS',      'fr', 'Un sport avec ce code existe déjà.'),
('SPORT_ERR_CODE_EXISTS',      'en', 'A sport with this code already exists.'),
('SPORT_ERR_CODE_EXISTS',      'es', 'Ya existe un deporte con este código.'),

('SPORT_ERR_SEASON_MISSING_FIELDS', 'fr', 'Veuillez remplir tous les champs obligatoires (Code, Nom, Date de début, Date de fin).'),
('SPORT_ERR_SEASON_MISSING_FIELDS', 'en', 'Please fill in all required fields (Code, Name, Start date, End date).'),
('SPORT_ERR_SEASON_MISSING_FIELDS', 'es', 'Por favor complete todos los campos obligatorios (Código, Nombre, Fecha de inicio, Fecha de fin).'),

('SPORT_ERR_SEASON_CODE_EXISTS', 'fr', 'Une saison avec ce code existe déjà.'),
('SPORT_ERR_SEASON_CODE_EXISTS', 'en', 'A season with this code already exists.'),
('SPORT_ERR_SEASON_CODE_EXISTS', 'es', 'Ya existe una temporada con este código.'),

('SPORT_ERR_DATES_INVALID',    'fr', 'La date de début doit être antérieure à la date de fin.'),
('SPORT_ERR_DATES_INVALID',    'en', 'The start date must be before the end date.'),
('SPORT_ERR_DATES_INVALID',    'es', 'La fecha de inicio debe ser anterior a la fecha de fin.'),

('SPORT_DELETE_SPORT_CONFIRM','fr', 'Voulez-vous vraiment désactiver ce sport ?'),
('SPORT_DELETE_SPORT_CONFIRM','en', 'Are you sure you want to deactivate this sport?'),
('SPORT_DELETE_SPORT_CONFIRM','es', '¿Está seguro de que desea desactivar este deporte?'),

('SPORT_DELETE_SEASON_CONFIRM','fr', 'Voulez-vous vraiment désactiver cette saison ?'),
('SPORT_DELETE_SEASON_CONFIRM','en', 'Are you sure you want to deactivate this season?'),
('SPORT_DELETE_SEASON_CONFIRM','es', '¿Está seguro de que desea desactivar esta temporada?'),

('SPORT_FORCE_DELETE_CONFIRM','fr', 'Voulez-vous vraiment supprimer définitivement cet élément ?'),
('SPORT_FORCE_DELETE_CONFIRM','en', 'Are you sure you want to permanently delete this item?'),
('SPORT_FORCE_DELETE_CONFIRM','es', '¿Está seguro de que desea eliminar permanentemente este elemento?'),

('SPORT_INFO_PANEL',          'fr', 'Informations d''audit'),
('SPORT_INFO_PANEL',          'en', 'Audit information'),
('SPORT_INFO_PANEL',          'es', 'Información de auditoría')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
