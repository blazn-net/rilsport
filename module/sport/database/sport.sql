-- ============================================================
-- Module : sport
-- ============================================================
-- Ordre d'exécution :
--   1. Enregistrement module dans t_system_module
--   2. Schéma : t_sport_sport, t_sport_season, t_sport_person_role, t_sport_person
--   3. Schéma : t_sport_text_key, t_sport_text
--   4. Schéma : Métadonnées du module sport
--   5. Données : Sports, Saisons & Personnes par défaut
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
-- SCHÉMA : t_sport_person_role & t_sport_person (Acteurs / Personnes)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_person_role (
    code      VARCHAR(50)  PRIMARY KEY,
    name      VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS t_sport_person (
    id          SERIAL       PRIMARY KEY,
    code        VARCHAR(50)  NOT NULL UNIQUE,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    gender      VARCHAR(10)  DEFAULT 'M',
    birth_date  DATE         DEFAULT NULL,
    nationality VARCHAR(50)  DEFAULT NULL,
    role_code   VARCHAR(50)  NOT NULL DEFAULT 'player',
    status_id   INT          NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by  INT          DEFAULT NULL,
    modified_at TIMESTAMP    DEFAULT NULL,
    modified_by INT          DEFAULT NULL,
    FOREIGN KEY (role_code)   REFERENCES t_sport_person_role(code) ON DELETE RESTRICT,
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
    FOREIGN KEY (object_id) REFERENCES t_sport_object(id)  ON DELETE SET NULL
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
-- DONNÉES : Métadonnées et Objets
-- ============================================================

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'sport' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'season' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'person' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Sport' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Saison' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Personne / Acteur' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'person'
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

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'persons', 'list', '/sport/persons' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'person'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'person', 'form', '/sport/person' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'person'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_sport' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'sport'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_season' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'season'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_person' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'person'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;


-- ============================================================
-- DONNÉES : Rôles des personnes
-- ============================================================

INSERT INTO t_sport_person_role (code, name) VALUES
('player',   'Joueur / Joueuse'),
('coach',    'Entraîneur / Coach'),
('referee',  'Arbitre / Juge'),
('official', 'Officiel / Délégué'),
('staff',    'Staff médical / Technique')
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name;


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
-- DONNÉES : Personnes / Acteurs par défaut
-- ============================================================

INSERT INTO t_sport_person (code, first_name, last_name, gender, birth_date, nationality, role_code, status_id) VALUES
('p-mbappe',     'Kylian',    'Mbappé',    'M', '1998-12-20', 'Française', 'player',   1),
('p-wembanyama', 'Victor',    'Wembanyama','M', '2004-01-04', 'Française', 'player',   1),
('p-deschamps',  'Didier',    'Deschamps', 'M', '1968-10-15', 'Française', 'coach',    1),
('p-turpin',     'Clément',   'Turpin',    'M', '1982-03-16', 'Française', 'referee',  1)
ON CONFLICT (code) DO UPDATE SET
    first_name  = EXCLUDED.first_name,
    last_name   = EXCLUDED.last_name,
    gender      = EXCLUDED.gender,
    birth_date  = EXCLUDED.birth_date,
    nationality = EXCLUDED.nationality,
    role_code   = EXCLUDED.role_code,
    status_id   = EXCLUDED.status_id;


-- ============================================================
-- DONNÉES : clés de traduction UI (SPORT_)
-- ============================================================

INSERT INTO t_sport_text_key (text_code) VALUES
('SPORT_SPORTS_MGT'),
('SPORT_SEASONS_MGT'),
('SPORT_PERSONS_MGT'),
('SPORT_ADD_SPORT_BTN'),
('SPORT_ADD_SEASON_BTN'),
('SPORT_ADD_PERSON_BTN'),
('SPORT_EDIT_SPORT_TITLE'),
('SPORT_ADD_SPORT_TITLE'),
('SPORT_EDIT_SEASON_TITLE'),
('SPORT_ADD_SEASON_TITLE'),
('SPORT_EDIT_PERSON_TITLE'),
('SPORT_ADD_PERSON_TITLE'),
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
('SPORT_PERSON_CODE'),
('SPORT_PERSON_FIRSTNAME'),
('SPORT_PERSON_LASTNAME'),
('SPORT_PERSON_FULLNAME'),
('SPORT_PERSON_GENDER'),
('SPORT_PERSON_BIRTHDATE'),
('SPORT_PERSON_NATIONALITY'),
('SPORT_PERSON_ROLE'),
('SPORT_PERSON_SPORT'),
('SPORT_PERSON_CODE_LABEL'),
('SPORT_PERSON_FIRSTNAME_LABEL'),
('SPORT_PERSON_LASTNAME_LABEL'),
('SPORT_PERSON_GENDER_LABEL'),
('SPORT_PERSON_BIRTHDATE_LABEL'),
('SPORT_PERSON_NATIONALITY_LABEL'),
('SPORT_PERSON_ROLE_LABEL'),
('SPORT_PERSON_SPORT_LABEL'),
('SPORT_PERSON_ALL_SPORTS'),
('SPORT_PERSON_ROLE_PLAYER'),
('SPORT_PERSON_ROLE_COACH'),
('SPORT_PERSON_ROLE_REFEREE'),
('SPORT_PERSON_ROLE_OFFICIAL'),
('SPORT_PERSON_ROLE_STAFF'),
('SPORT_MSG_SPORT_ADDED'),
('SPORT_MSG_SPORT_UPDATED'),
('SPORT_MSG_SPORT_DEACTIVATED'),
('SPORT_MSG_SPORT_DELETED'),
('SPORT_MSG_SEASON_ADDED'),
('SPORT_MSG_SEASON_UPDATED'),
('SPORT_MSG_SEASON_DEACTIVATED'),
('SPORT_MSG_SEASON_DELETED'),
('SPORT_MSG_PERSON_ADDED'),
('SPORT_MSG_PERSON_UPDATED'),
('SPORT_MSG_PERSON_DEACTIVATED'),
('SPORT_MSG_PERSON_DELETED'),
('SPORT_ERR_MISSING_FIELDS'),
('SPORT_ERR_CODE_EXISTS'),
('SPORT_ERR_SEASON_MISSING_FIELDS'),
('SPORT_ERR_SEASON_CODE_EXISTS'),
('SPORT_ERR_PERSON_MISSING_FIELDS'),
('SPORT_ERR_PERSON_CODE_EXISTS'),
('SPORT_ERR_DATES_INVALID'),
('SPORT_DELETE_SPORT_CONFIRM'),
('SPORT_DELETE_SEASON_CONFIRM'),
('SPORT_DELETE_PERSON_CONFIRM'),
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

('SPORT_PERSONS_MGT',         'fr', 'Personnes / Acteurs'),
('SPORT_PERSONS_MGT',         'en', 'Persons / Actors'),
('SPORT_PERSONS_MGT',         'es', 'Personas / Actores'),

('SPORT_ADD_SPORT_BTN',       'fr', 'Ajouter un sport'),
('SPORT_ADD_SPORT_BTN',       'en', 'Add Sport'),
('SPORT_ADD_SPORT_BTN',       'es', 'Añadir deporte'),

('SPORT_ADD_SEASON_BTN',      'fr', 'Ajouter une saison'),
('SPORT_ADD_SEASON_BTN',      'en', 'Add Season'),
('SPORT_ADD_SEASON_BTN',      'es', 'Añadir temporada'),

('SPORT_ADD_PERSON_BTN',      'fr', 'Ajouter une personne'),
('SPORT_ADD_PERSON_BTN',      'en', 'Add Person'),
('SPORT_ADD_PERSON_BTN',      'es', 'Añadir persona'),

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

('SPORT_EDIT_PERSON_TITLE',   'fr', 'Modifier la personne'),
('SPORT_EDIT_PERSON_TITLE',   'en', 'Edit Person'),
('SPORT_EDIT_PERSON_TITLE',   'es', 'Editar persona'),

('SPORT_ADD_PERSON_TITLE',    'fr', 'Ajouter une personne'),
('SPORT_ADD_PERSON_TITLE',    'en', 'Add Person'),
('SPORT_ADD_PERSON_TITLE',    'es', 'Añadir persona'),

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

('SPORT_PERSON_CODE',         'fr', 'Code personne'),
('SPORT_PERSON_CODE',         'en', 'Person code'),
('SPORT_PERSON_CODE',         'es', 'Código de persona'),

('SPORT_PERSON_FIRSTNAME',    'fr', 'Prénom'),
('SPORT_PERSON_FIRSTNAME',    'en', 'First name'),
('SPORT_PERSON_FIRSTNAME',    'es', 'Nombre'),

('SPORT_PERSON_LASTNAME',     'fr', 'Nom'),
('SPORT_PERSON_LASTNAME',     'en', 'Last name'),
('SPORT_PERSON_LASTNAME',     'es', 'Apellido'),

('SPORT_PERSON_FULLNAME',     'fr', 'Nom complet'),
('SPORT_PERSON_FULLNAME',     'en', 'Full name'),
('SPORT_PERSON_FULLNAME',     'es', 'Nombre completo'),

('SPORT_PERSON_GENDER',       'fr', 'Genre'),
('SPORT_PERSON_GENDER',       'en', 'Gender'),
('SPORT_PERSON_GENDER',       'es', 'Género'),

('SPORT_PERSON_BIRTHDATE',    'fr', 'Date de naissance'),
('SPORT_PERSON_BIRTHDATE',    'en', 'Birth date'),
('SPORT_PERSON_BIRTHDATE',    'es', 'Fecha de nacimiento'),

('SPORT_PERSON_NATIONALITY',  'fr', 'Nationalité'),
('SPORT_PERSON_NATIONALITY',  'en', 'Nationality'),
('SPORT_PERSON_NATIONALITY',  'es', 'Nacionalidad'),

('SPORT_PERSON_ROLE',         'fr', 'Rôle / Fonction'),
('SPORT_PERSON_ROLE',         'en', 'Role / Function'),
('SPORT_PERSON_ROLE',         'es', 'Rol / Función'),

('SPORT_PERSON_SPORT',        'fr', 'Sport principal'),
('SPORT_PERSON_SPORT',        'en', 'Primary sport'),
('SPORT_PERSON_SPORT',        'es', 'Deporte principal'),

('SPORT_PERSON_CODE_LABEL',   'fr', 'Code unique (ex: p-mbappe)'),
('SPORT_PERSON_CODE_LABEL',   'en', 'Unique code (e.g. p-mbappe)'),
('SPORT_PERSON_CODE_LABEL',   'es', 'Código único (ej: p-mbappe)'),

('SPORT_PERSON_FIRSTNAME_LABEL', 'fr', 'Prénom'),
('SPORT_PERSON_FIRSTNAME_LABEL', 'en', 'First name'),
('SPORT_PERSON_FIRSTNAME_LABEL', 'es', 'Nombre'),

('SPORT_PERSON_LASTNAME_LABEL',  'fr', 'Nom de famille'),
('SPORT_PERSON_LASTNAME_LABEL',  'en', 'Last name'),
('SPORT_PERSON_LASTNAME_LABEL',  'es', 'Apellido'),

('SPORT_PERSON_GENDER_LABEL',    'fr', 'Genre (M: Masculin, F: Féminin)'),
('SPORT_PERSON_GENDER_LABEL',    'en', 'Gender (M: Male, F: Female)'),
('SPORT_PERSON_GENDER_LABEL',    'es', 'Género (M: Masculino, F: Femenino)'),

('SPORT_PERSON_BIRTHDATE_LABEL', 'fr', 'Date de naissance'),
('SPORT_PERSON_BIRTHDATE_LABEL', 'en', 'Birth date'),
('SPORT_PERSON_BIRTHDATE_LABEL', 'es', 'Fecha de nacimiento'),

('SPORT_PERSON_NATIONALITY_LABEL','fr', 'Nationalité'),
('SPORT_PERSON_NATIONALITY_LABEL','en', 'Nationality'),
('SPORT_PERSON_NATIONALITY_LABEL','es', 'Nacionalidad'),

('SPORT_PERSON_ROLE_LABEL',    'fr', 'Rôle principal'),
('SPORT_PERSON_ROLE_LABEL',    'en', 'Primary role'),
('SPORT_PERSON_ROLE_LABEL',    'es', 'Rol principal'),

('SPORT_PERSON_SPORT_LABEL',   'fr', 'Sport principal rattaché'),
('SPORT_PERSON_SPORT_LABEL',   'en', 'Associated primary sport'),
('SPORT_PERSON_SPORT_LABEL',   'es', 'Deporte principal asociado'),

('SPORT_PERSON_ALL_SPORTS',    'fr', 'Tous les sports / Aucun'),
('SPORT_PERSON_ALL_SPORTS',    'en', 'All sports / None'),
('SPORT_PERSON_ALL_SPORTS',    'es', 'Todos los deportes / Ninguno'),

('SPORT_PERSON_ROLE_PLAYER',   'fr', 'Joueur / Joueuse'),
('SPORT_PERSON_ROLE_PLAYER',   'en', 'Player'),
('SPORT_PERSON_ROLE_PLAYER',   'es', 'Jugador / Jugadora'),

('SPORT_PERSON_ROLE_COACH',    'fr', 'Entraîneur / Coach'),
('SPORT_PERSON_ROLE_COACH',    'en', 'Coach'),
('SPORT_PERSON_ROLE_COACH',    'es', 'Entrenador / Coach'),

('SPORT_PERSON_ROLE_REFEREE',  'fr', 'Arbitre / Juge'),
('SPORT_PERSON_ROLE_REFEREE',  'en', 'Referee / Judge'),
('SPORT_PERSON_ROLE_REFEREE',  'es', 'Árbitro / Juez'),

('SPORT_PERSON_ROLE_OFFICIAL', 'fr', 'Officiel / Délégué'),
('SPORT_PERSON_ROLE_OFFICIAL', 'en', 'Official'),
('SPORT_PERSON_ROLE_OFFICIAL', 'es', 'Oficial / Delegado'),

('SPORT_PERSON_ROLE_STAFF',    'fr', 'Staff médical / Technique'),
('SPORT_PERSON_ROLE_STAFF',    'en', 'Medical / Tech Staff'),
('SPORT_PERSON_ROLE_STAFF',    'es', 'Personal médico / Técnico'),

('SPORT_MSG_PERSON_ADDED',     'fr', 'Personne ajoutée avec succès.'),
('SPORT_MSG_PERSON_ADDED',     'en', 'Person added successfully.'),
('SPORT_MSG_PERSON_ADDED',     'es', 'Persona añadida con éxito.'),

('SPORT_MSG_PERSON_UPDATED',   'fr', 'Personne mise à jour avec succès.'),
('SPORT_MSG_PERSON_UPDATED',   'en', 'Person updated successfully.'),
('SPORT_MSG_PERSON_UPDATED',   'es', 'Persona actualizada con éxito.'),

('SPORT_MSG_PERSON_DEACTIVATED','fr', 'Personne désactivée.'),
('SPORT_MSG_PERSON_DEACTIVATED','en', 'Person deactivated.'),
('SPORT_MSG_PERSON_DEACTIVATED','es', 'Persona desactivada.'),

('SPORT_MSG_PERSON_DELETED',   'fr', 'Personne supprimée.'),
('SPORT_MSG_PERSON_DELETED',   'en', 'Person deleted.'),
('SPORT_MSG_PERSON_DELETED',   'es', 'Persona eliminada.'),

('SPORT_ERR_PERSON_MISSING_FIELDS', 'fr', 'Veuillez remplir tous les champs obligatoires (Code, Prénom, Nom).'),
('SPORT_ERR_PERSON_MISSING_FIELDS', 'en', 'Please fill in all required fields (Code, First name, Last name).'),
('SPORT_ERR_PERSON_MISSING_FIELDS', 'es', 'Por favor complete todos los campos obligatorios (Código, Nombre, Apellido).'),

('SPORT_ERR_PERSON_CODE_EXISTS', 'fr', 'Une personne avec ce code existe déjà.'),
('SPORT_ERR_PERSON_CODE_EXISTS', 'en', 'A person with this code already exists.'),
('SPORT_ERR_PERSON_CODE_EXISTS', 'es', 'Ya existe una persona con este código.'),

('SPORT_DELETE_PERSON_CONFIRM','fr', 'Voulez-vous vraiment désactiver cette personne ?'),
('SPORT_DELETE_PERSON_CONFIRM','en', 'Are you sure you want to deactivate this person?'),
('SPORT_DELETE_PERSON_CONFIRM','es', '¿Está seguro de que desea desactivar esta persona?')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
