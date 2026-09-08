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
-- SCHÉMA : t_sport_club_status (Statuts des clubs et sections)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_club_status (
    id        SERIAL       PRIMARY KEY,
    text_code VARCHAR(100) NOT NULL UNIQUE
);


-- ============================================================
-- SCHÉMA : t_sport_club (Clubs sportifs)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_club (
    id              SERIAL       PRIMARY KEY,
    code            VARCHAR(50)  NOT NULL UNIQUE,
    name            VARCHAR(150) NOT NULL,
    short_name      VARCHAR(50)  DEFAULT NULL,
    acronym         VARCHAR(20)  DEFAULT NULL,
    foundation_year INT          DEFAULT NULL,
    logo            VARCHAR(255) DEFAULT NULL,
    primary_color   VARCHAR(20)  DEFAULT NULL,
    secondary_color VARCHAR(20)  DEFAULT NULL,
    country_code    VARCHAR(2)   NOT NULL,
    city_name       VARCHAR(150) NOT NULL,
    city_id         INT          DEFAULT NULL,
    postal_code     VARCHAR(20)  DEFAULT NULL,
    address         TEXT         DEFAULT NULL,
    website         VARCHAR(255) DEFAULT NULL,
    email           VARCHAR(100) DEFAULT NULL,
    phone           VARCHAR(30)  DEFAULT NULL,
    description     TEXT         DEFAULT NULL,
    status_id       INT          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by      INT          DEFAULT NULL,
    modified_at     TIMESTAMP    DEFAULT NULL,
    modified_by     INT          DEFAULT NULL,
    FOREIGN KEY (status_id)   REFERENCES t_sport_club_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)        ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)        ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_sport_club_country ON t_sport_club(country_code);
CREATE INDEX IF NOT EXISTS idx_sport_club_status  ON t_sport_club(status_id);


-- ============================================================
-- SCHÉMA : t_sport_section (Sections sportives par discipline)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_section (
    id            SERIAL       PRIMARY KEY,
    club_id       INT          NOT NULL,
    sport_id      INT          NOT NULL,
    code          VARCHAR(50)  NOT NULL UNIQUE,
    name          VARCHAR(150) NOT NULL,
    logo          VARCHAR(255) DEFAULT NULL,
    creation_year INT          DEFAULT NULL,
    status_id     INT          NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by    INT          DEFAULT NULL,
    modified_at   TIMESTAMP    DEFAULT NULL,
    modified_by   INT          DEFAULT NULL,
    FOREIGN KEY (club_id)     REFERENCES t_sport_club(id)        ON DELETE CASCADE,
    FOREIGN KEY (sport_id)    REFERENCES t_sport_sport(id)       ON DELETE RESTRICT,
    FOREIGN KEY (status_id)   REFERENCES t_sport_club_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)        ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)        ON DELETE SET NULL,
    CONSTRAINT uk_sport_section_club_sport UNIQUE (club_id, sport_id)
);

CREATE INDEX IF NOT EXISTS idx_sport_section_club  ON t_sport_section(club_id);
CREATE INDEX IF NOT EXISTS idx_sport_section_sport ON t_sport_section(sport_id);


-- ============================================================
-- SCHÉMA : t_sport_team_status (Statuts des équipes)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_team_status (
    id        SERIAL       PRIMARY KEY,
    text_code VARCHAR(100) NOT NULL UNIQUE
);


-- ============================================================
-- SCHÉMA : t_sport_team (Équipes sportives)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_sport_team (
    id          SERIAL       PRIMARY KEY,
    section_id  INT          NOT NULL,
    code        VARCHAR(50)  NOT NULL UNIQUE,
    name        VARCHAR(150) NOT NULL,
    short_name  VARCHAR(50)  DEFAULT NULL,
    gender      VARCHAR(10)  DEFAULT 'M',
    category    VARCHAR(50)  DEFAULT 'Senior',
    level       VARCHAR(50)  DEFAULT 'National',
    status_id   INT          NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by  INT          DEFAULT NULL,
    modified_at TIMESTAMP    DEFAULT NULL,
    modified_by INT          DEFAULT NULL,
    FOREIGN KEY (section_id)  REFERENCES t_sport_section(id)     ON DELETE CASCADE,
    FOREIGN KEY (status_id)   REFERENCES t_sport_team_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)         ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)         ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_sport_team_section ON t_sport_team(section_id);
CREATE INDEX IF NOT EXISTS idx_sport_team_status  ON t_sport_team(status_id);


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

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'club' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'section' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Club' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'club'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Section' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'section'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'clubs', 'list', '/sport/clubs' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'club'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'club', 'form', '/sport/club' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'club'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_club_status' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'club'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_club' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'club'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_section' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'section'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_object (module_id, code)
SELECT id, 'team' FROM t_system_module WHERE code = 'sport'
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_sport_object_i18n (object_id, lang_code, name)
SELECT o.id, 'fr', 'Équipe' FROM t_sport_object o JOIN t_system_module m ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'team'
ON CONFLICT (object_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'teams', 'list', '/sport/teams' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'team'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_page (module_id, object_id, code, page_type, url_path)
SELECT m.id, o.id, 'team', 'form', '/sport/team' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'team'
ON CONFLICT (module_id, code) DO UPDATE SET page_type = EXCLUDED.page_type, url_path = EXCLUDED.url_path;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_team_status' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'team'
ON CONFLICT (table_name) DO UPDATE SET module_id = EXCLUDED.module_id;

INSERT INTO t_sport_table (module_id, object_id, table_name)
SELECT m.id, o.id, 't_sport_team' FROM t_system_module m JOIN t_sport_object o ON o.module_id = m.id WHERE m.code = 'sport' AND o.code = 'team'
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
-- DONNÉES : Statuts des clubs et sections
-- ============================================================

INSERT INTO t_sport_club_status (id, text_code) VALUES
(1, 'CLUB_STATUS_ACTIVE'),
(2, 'CLUB_STATUS_PENDING'),
(3, 'CLUB_STATUS_INACTIVE'),
(4, 'CLUB_STATUS_DISSOLVED')
ON CONFLICT (id) DO UPDATE SET text_code = EXCLUDED.text_code;


-- ============================================================
-- DONNÉES : Clubs et Sections par défaut
-- ============================================================

INSERT INTO t_sport_club (id, code, name, short_name, acronym, foundation_year, logo, primary_color, secondary_color, country_code, city_name, postal_code, address, website, email, phone, description, status_id) VALUES
(1, 'psg', 'Paris Saint-Germain', 'Paris SG', 'PSG', 1970, 'public/uploads/clubs/psg.png', '#001C58', '#DA291C', 'FR', 'Paris', '75016', '24 Rue du Commandant Guilbaud', 'https://www.psg.fr', 'contact@psg.fr', '+33 1 47 43 71 71', 'Club omnisports français fondé en 1970.', 1),
(2, 'real-madrid', 'Real Madrid Club de Fútbol', 'Real Madrid', 'RMA', 1902, 'public/uploads/clubs/real-madrid.png', '#FFFFFF', '#00529F', 'ES', 'Madrid', '28036', 'Avenida de Concha Espina 1', 'https://www.realmadrid.com', 'contact@realmadrid.com', '+34 91 398 43 00', 'Club de football espagnol fondé en 1902 à Madrid.', 1),
(3, 'as-rillieux', 'Avenir Sportif Rillieux', 'AS Rillieux', 'ASR', 1985, NULL, '#008000', '#FFFFFF', 'FR', 'Rillieux-la-Pape', '69140', 'Stade municipal, Rue du Sport', 'https://as-rillieux.fr', 'contact@as-rillieux.fr', '+33 4 78 88 00 00', 'Club omnisports local amateur de la région lyonnaise.', 1)
ON CONFLICT (code) DO UPDATE SET
    name            = EXCLUDED.name,
    short_name      = EXCLUDED.short_name,
    acronym         = EXCLUDED.acronym,
    foundation_year = EXCLUDED.foundation_year,
    logo            = EXCLUDED.logo,
    primary_color   = EXCLUDED.primary_color,
    secondary_color = EXCLUDED.secondary_color,
    country_code    = EXCLUDED.country_code,
    city_name       = EXCLUDED.city_name,
    postal_code     = EXCLUDED.postal_code,
    address         = EXCLUDED.address,
    website         = EXCLUDED.website,
    email           = EXCLUDED.email,
    phone           = EXCLUDED.phone,
    description     = EXCLUDED.description,
    status_id       = EXCLUDED.status_id;

-- Sections pour PSG : Football (sport code 'football') et Handball (sport code 'handball')
INSERT INTO t_sport_section (club_id, sport_id, code, name, creation_year, status_id)
SELECT c.id, s.id, 'psg-football', 'PSG Football', 1970, 1
FROM t_sport_club c, t_sport_sport s
WHERE c.code = 'psg' AND s.code = 'football'
ON CONFLICT (club_id, sport_id) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_section (club_id, sport_id, code, name, creation_year, status_id)
SELECT c.id, s.id, 'psg-handball', 'PSG Handball', 1941, 1
FROM t_sport_club c, t_sport_sport s
WHERE c.code = 'psg' AND s.code = 'handball'
ON CONFLICT (club_id, sport_id) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

-- Section pour Real Madrid : Football
INSERT INTO t_sport_section (club_id, sport_id, code, name, creation_year, status_id)
SELECT c.id, s.id, 'real-madrid-football', 'Real Madrid Football', 1902, 1
FROM t_sport_club c, t_sport_sport s
WHERE c.code = 'real-madrid' AND s.code = 'football'
ON CONFLICT (club_id, sport_id) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

-- Section pour AS Rillieux : Rugby
INSERT INTO t_sport_section (club_id, sport_id, code, name, creation_year, status_id)
SELECT c.id, s.id, 'as-rillieux-rugby', 'AS Rillieux Rugby', 1985, 1
FROM t_sport_club c, t_sport_sport s
WHERE c.code = 'as-rillieux' AND s.code = 'rugby'
ON CONFLICT (club_id, sport_id) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;


-- ============================================================
-- DONNÉES : Statuts des équipes
-- ============================================================

INSERT INTO t_sport_team_status (id, text_code) VALUES
(1, 'TEAM_STATUS_ACTIVE'),
(2, 'TEAM_STATUS_INACTIVE'),
(3, 'TEAM_STATUS_DISSOLVED')
ON CONFLICT (id) DO UPDATE SET text_code = EXCLUDED.text_code;


-- ============================================================
-- DONNÉES : Équipes par défaut
-- ============================================================

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'psg-foot-pro', 'Équipe Première (Pro)', 'PSG Pro', 'M', 'Senior', 'Professionnel', 1
FROM t_sport_section WHERE code = 'psg-football'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'psg-foot-reserve', 'Équipe Réserve (National 3)', 'PSG Réserve', 'M', 'Senior', 'National', 1
FROM t_sport_section WHERE code = 'psg-football'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'psg-foot-u19', 'U19 National', 'PSG U19', 'M', 'U19', 'National', 1
FROM t_sport_section WHERE code = 'psg-football'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'psg-hand-pro', 'Équipe Pro Handball (StarLigue)', 'PSG Hand Pro', 'M', 'Senior', 'Professionnel', 1
FROM t_sport_section WHERE code = 'psg-handball'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'real-madrid-foot-pro', 'Primer Equipo (La Liga)', 'Real Madrid Pro', 'M', 'Senior', 'Professionnel', 1
FROM t_sport_section WHERE code = 'real-madrid-football'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;

INSERT INTO t_sport_team (section_id, code, name, short_name, gender, category, level, status_id)
SELECT id, 'as-rillieux-rugby-seniors', 'Séniors A', 'AS Rillieux 1', 'M', 'Senior', 'Régional', 1
FROM t_sport_section WHERE code = 'as-rillieux-rugby'
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name, status_id = EXCLUDED.status_id;


-- ============================================================
-- DONNÉES : clés de traduction UI (SPORT_)
-- ============================================================

INSERT INTO t_sport_text_key (text_code) VALUES
('SPORT_SPORTS_MGT'),
('SPORT_SEASONS_MGT'),
('SPORT_PERSONS_MGT'),
('SPORT_CLUBS_MGT'),
('SPORT_TEAMS_MGT'),
('SPORT_ADD_SPORT_BTN'),
('SPORT_ADD_SEASON_BTN'),
('SPORT_ADD_PERSON_BTN'),
('SPORT_ADD_CLUB_BTN'),
('SPORT_ADD_TEAM_BTN'),
('SPORT_EDIT_SPORT_TITLE'),
('SPORT_ADD_SPORT_TITLE'),
('SPORT_EDIT_SEASON_TITLE'),
('SPORT_ADD_SEASON_TITLE'),
('SPORT_EDIT_PERSON_TITLE'),
('SPORT_ADD_PERSON_TITLE'),
('SPORT_EDIT_CLUB_TITLE'),
('SPORT_ADD_CLUB_TITLE'),
('SPORT_VIEW_CLUB_TITLE'),
('SPORT_EDIT_TEAM_TITLE'),
('SPORT_ADD_TEAM_TITLE'),
('SPORT_VIEW_TEAM_TITLE'),
('SPORT_NAV_CLUBS'),
('SPORT_NAV_TEAMS'),
('TEAM_CODE'),
('TEAM_NAME'),
('TEAM_SHORT_NAME'),
('TEAM_SECTION'),
('TEAM_CLUB'),
('TEAM_SPORT'),
('TEAM_GENDER'),
('TEAM_GENDER_M'),
('TEAM_GENDER_F'),
('TEAM_GENDER_MIXED'),
('TEAM_CATEGORY'),
('TEAM_LEVEL'),
('TEAM_STATUS'),
('TEAM_STATUS_ACTIVE'),
('TEAM_STATUS_INACTIVE'),
('TEAM_STATUS_DISSOLVED'),
('TEAM_MSG_ADDED'),
('TEAM_MSG_UPDATED'),
('TEAM_MSG_DEACTIVATED'),
('TEAM_MSG_DELETED'),
('TEAM_ERR_MISSING_FIELDS'),
('TEAM_ERR_CODE_EXISTS'),
('TEAM_DELETE_CONFIRM'),
('SPORT_EDIT_SEASON_TITLE'),
('SPORT_ADD_SEASON_TITLE'),
('SPORT_EDIT_PERSON_TITLE'),
('SPORT_ADD_PERSON_TITLE'),
('SPORT_EDIT_CLUB_TITLE'),
('SPORT_ADD_CLUB_TITLE'),
('SPORT_VIEW_CLUB_TITLE'),
('SPORT_NAV_CLUBS'),
('CLUB_CODE'),
('CLUB_NAME'),
('CLUB_TABLE_NAME'),
('CLUB_SHORT_NAME'),
('CLUB_ACRONYM'),
('CLUB_FOUNDATION'),
('CLUB_LOGO'),
('CLUB_TABLE_LOGO'),
('CLUB_COLORS'),
('CLUB_COUNTRY'),
('CLUB_CITY'),
('CLUB_TABLE_LOCATION'),
('CLUB_POSTAL_CODE'),
('CLUB_ADDRESS'),
('CLUB_CONTACT'),
('CLUB_WEBSITE'),
('CLUB_EMAIL'),
('CLUB_PHONE'),
('CLUB_DESCRIPTION'),
('CLUB_STATUS'),
('CLUB_SECTIONS'),
('CLUB_FILTER_SPORT'),
('CLUB_PRIMARY_COLOR'),
('CLUB_SECONDARY_COLOR'),
('CLUB_STATUS_ACTIVE'),
('CLUB_STATUS_PENDING'),
('CLUB_STATUS_INACTIVE'),
('CLUB_STATUS_DISSOLVED'),
('CLUB_MSG_ADDED'),
('CLUB_MSG_UPDATED'),
('CLUB_MSG_DEACTIVATED'),
('CLUB_MSG_DELETED'),
('CLUB_ERR_MISSING_FIELDS'),
('CLUB_ERR_CODE_EXISTS'),
('CLUB_ERR_FILE_TOO_LARGE'),
('CLUB_ERR_INVALID_IMAGE'),
('CLUB_DELETE_CONFIRM'),
('SECTION_LBL_SPORT'),
('SECTION_LBL_NAME'),
('SECTION_LBL_CREATION_YEAR'),
('SECTION_BTN_ADD'),
('SECTION_BTN_DELETE'),
('SECTION_CONFIRM_DELETE'),
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
('SPORT_DELETE_PERSON_CONFIRM','es', '¿Está seguro de que desea desactivar esta persona?'),

-- Traductions Clubs & Sections
('SPORT_CLUBS_MGT',            'fr', 'Clubs'),
('SPORT_CLUBS_MGT',            'en', 'Clubs'),
('SPORT_CLUBS_MGT',            'es', 'Clubes'),

('SPORT_NAV_CLUBS',            'fr', 'Clubs'),
('SPORT_NAV_CLUBS',            'en', 'Clubs'),
('SPORT_NAV_CLUBS',            'es', 'Clubes'),

('SPORT_ADD_CLUB_BTN',         'fr', 'Nouveau club'),
('SPORT_ADD_CLUB_BTN',         'en', 'New Club'),
('SPORT_ADD_CLUB_BTN',         'es', 'Nuevo Club'),

('SPORT_EDIT_CLUB_TITLE',      'fr', 'Modifier le club'),
('SPORT_EDIT_CLUB_TITLE',      'en', 'Edit Club'),
('SPORT_EDIT_CLUB_TITLE',      'es', 'Editar Club'),

('SPORT_ADD_CLUB_TITLE',       'fr', 'Créer un club'),
('SPORT_ADD_CLUB_TITLE',       'en', 'Create Club'),
('SPORT_ADD_CLUB_TITLE',       'es', 'Crear Club'),

('SPORT_VIEW_CLUB_TITLE',      'fr', 'Fiche du club'),
('SPORT_VIEW_CLUB_TITLE',      'en', 'Club details'),
('SPORT_VIEW_CLUB_TITLE',      'es', 'Ficha del club'),

('CLUB_CODE',                  'fr', 'Code / Identifiant'),
('CLUB_CODE',                  'en', 'Code / Slug'),
('CLUB_CODE',                  'es', 'Código / Slug'),

('CLUB_NAME',                  'fr', 'Nom officiel'),
('CLUB_NAME',                  'en', 'Official Name'),
('CLUB_NAME',                  'es', 'Nombre oficial'),

('CLUB_TABLE_NAME',            'fr', 'Nom'),
('CLUB_TABLE_NAME',            'en', 'Name'),
('CLUB_TABLE_NAME',            'es', 'Nombre'),

('CLUB_SHORT_NAME',            'fr', 'Nom court'),
('CLUB_SHORT_NAME',            'en', 'Short Name'),
('CLUB_SHORT_NAME',            'es', 'Nombre corto'),

('CLUB_ACRONYM',               'fr', 'Sigle / Abréviation'),
('CLUB_ACRONYM',               'en', 'Acronym'),
('CLUB_ACRONYM',               'es', 'Sigla'),

('CLUB_FOUNDATION',            'fr', 'Année de fondation'),
('CLUB_FOUNDATION',            'en', 'Foundation Year'),
('CLUB_FOUNDATION',            'es', 'Año de fundación'),

('CLUB_LOGO',                  'fr', 'Logo'),
('CLUB_LOGO',                  'en', 'Logo'),
('CLUB_LOGO',                  'es', 'Logo'),

('CLUB_TABLE_LOGO',            'fr', 'Logo'),
('CLUB_TABLE_LOGO',            'en', 'Logo'),
('CLUB_TABLE_LOGO',            'es', 'Logo'),

('CLUB_COLORS',                'fr', 'Couleurs du club'),
('CLUB_COLORS',                'en', 'Club Colors'),
('CLUB_COLORS',                'es', 'Colores del club'),

('CLUB_PRIMARY_COLOR',         'fr', 'Couleur principale'),
('CLUB_PRIMARY_COLOR',         'en', 'Primary Color'),
('CLUB_PRIMARY_COLOR',         'es', 'Color principal'),

('CLUB_SECONDARY_COLOR',       'fr', 'Couleur secondaire'),
('CLUB_SECONDARY_COLOR',       'en', 'Secondary Color'),
('CLUB_SECONDARY_COLOR',       'es', 'Color secundario'),

('CLUB_COUNTRY',               'fr', 'Pays'),
('CLUB_COUNTRY',               'en', 'Country'),
('CLUB_COUNTRY',               'es', 'País'),

('CLUB_CITY',                  'fr', 'Ville'),
('CLUB_CITY',                  'en', 'City'),
('CLUB_CITY',                  'es', 'Ciudad'),

('CLUB_TABLE_LOCATION',        'fr', 'Localisation'),
('CLUB_TABLE_LOCATION',        'en', 'Location'),
('CLUB_TABLE_LOCATION',        'es', 'Ubicación'),

('CLUB_POSTAL_CODE',           'fr', 'Code postal'),
('CLUB_POSTAL_CODE',           'en', 'Postal Code'),
('CLUB_POSTAL_CODE',           'es', 'Código postal'),

('CLUB_ADDRESS',               'fr', 'Adresse'),
('CLUB_ADDRESS',               'en', 'Address'),
('CLUB_ADDRESS',               'es', 'Dirección'),

('CLUB_CONTACT',               'fr', 'Contact & Liens'),
('CLUB_CONTACT',               'en', 'Contact & Links'),
('CLUB_CONTACT',               'es', 'Contacto y Enlaces'),

('CLUB_WEBSITE',               'fr', 'Site Web'),
('CLUB_WEBSITE',               'en', 'Website'),
('CLUB_WEBSITE',               'es', 'Sitio Web'),

('CLUB_EMAIL',                 'fr', 'Email'),
('CLUB_EMAIL',                 'en', 'Email'),
('CLUB_EMAIL',                 'es', 'Correo electrónico'),

('CLUB_PHONE',                 'fr', 'Téléphone'),
('CLUB_PHONE',                 'en', 'Phone'),
('CLUB_PHONE',                 'es', 'Teléfono'),

('CLUB_DESCRIPTION',           'fr', 'Description / Histoire'),
('CLUB_DESCRIPTION',           'en', 'Description / History'),
('CLUB_DESCRIPTION',           'es', 'Descripción / Historia'),

('CLUB_STATUS',                'fr', 'Statut'),
('CLUB_STATUS',                'en', 'Status'),
('CLUB_STATUS',                'es', 'Estado'),

('CLUB_STATUS_ACTIVE',         'fr', 'Actif'),
('CLUB_STATUS_ACTIVE',         'en', 'Active'),
('CLUB_STATUS_ACTIVE',         'es', 'Activo'),

('CLUB_STATUS_PENDING',        'fr', 'En attente'),
('CLUB_STATUS_PENDING',        'en', 'Pending'),
('CLUB_STATUS_PENDING',        'es', 'Pendiente'),

('CLUB_STATUS_INACTIVE',       'fr', 'Inactif'),
('CLUB_STATUS_INACTIVE',       'en', 'Inactive'),
('CLUB_STATUS_INACTIVE',       'es', 'Inactivo'),

('CLUB_STATUS_DISSOLVED',      'fr', 'Dissous / Radié'),
('CLUB_STATUS_DISSOLVED',      'en', 'Dissolved'),
('CLUB_STATUS_DISSOLVED',      'es', 'Disuelto'),

('CLUB_SECTIONS',              'fr', 'Sport'),
('CLUB_SECTIONS',              'en', 'Sport'),
('CLUB_SECTIONS',              'es', 'Deporte'),

('CLUB_FILTER_SPORT',         'fr', 'Sport'),
('CLUB_FILTER_SPORT',         'en', 'Sport'),
('CLUB_FILTER_SPORT',         'es', 'Deporte'),

('CLUB_MSG_ADDED',             'fr', 'Le club a été créé avec succès.'),
('CLUB_MSG_ADDED',             'en', 'Club created successfully.'),
('CLUB_MSG_ADDED',             'es', 'Club creado con éxito.'),

('CLUB_MSG_UPDATED',           'fr', 'Le club a été mis à jour avec succès.'),
('CLUB_MSG_UPDATED',           'en', 'Club updated successfully.'),
('CLUB_MSG_UPDATED',           'es', 'Club actualizado con éxito.'),

('CLUB_MSG_DEACTIVATED',       'fr', 'Le club a été désactivé.'),
('CLUB_MSG_DEACTIVATED',       'en', 'Club deactivated.'),
('CLUB_MSG_DEACTIVATED',       'es', 'Club desactivado.'),

('CLUB_MSG_DELETED',           'fr', 'Le club a été supprimé.'),
('CLUB_MSG_DELETED',           'en', 'Club deleted.'),
('CLUB_MSG_DELETED',           'es', 'Club eliminado.'),

('CLUB_ERR_MISSING_FIELDS',    'fr', 'Veuillez remplir les champs obligatoires (Code, Nom, Pays, Ville).'),
('CLUB_ERR_MISSING_FIELDS',    'en', 'Please fill in all required fields (Code, Name, Country, City).'),
('CLUB_ERR_MISSING_FIELDS',    'es', 'Por favor complete los campos obligatorios (Código, Nombre, País, Ciudad).'),

('CLUB_ERR_CODE_EXISTS',       'fr', 'Un club avec cet identifiant/code existe déjà.'),
('CLUB_ERR_CODE_EXISTS',       'en', 'A club with this code already exists.'),
('CLUB_ERR_CODE_EXISTS',       'es', 'Ya existe un club con este código.'),

('CLUB_ERR_FILE_TOO_LARGE',    'fr', 'Le fichier est trop volumineux. La taille maximale autorisée est de 2 Mo.'),
('CLUB_ERR_FILE_TOO_LARGE',    'en', 'The file is too large. Maximum allowed size is 2 MB.'),
('CLUB_ERR_FILE_TOO_LARGE',    'es', 'El archivo es demasiado grande. El tamaño máximo permitido es de 2 MB.'),

('CLUB_ERR_INVALID_IMAGE',     'fr', 'Format d''image non valide (formats acceptés : PNG, JPG, JPEG, WEBP, SVG).'),
('CLUB_ERR_INVALID_IMAGE',     'en', 'Invalid image format (accepted formats: PNG, JPG, JPEG, WEBP, SVG).'),
('CLUB_ERR_INVALID_IMAGE',     'es', 'Formato de imagen no válido (formatos acceptados: PNG, JPG, JPEG, WEBP, SVG).'),

('CLUB_DELETE_CONFIRM',        'fr', 'Voulez-vous vraiment désactiver ce club ?'),
('CLUB_DELETE_CONFIRM',        'en', 'Are you sure you want to deactivate this club?'),
('CLUB_DELETE_CONFIRM',        'es', '¿Está seguro de que desea desactivar este club?'),

('SECTION_LBL_SPORT',          'fr', 'Sport'),
('SECTION_LBL_SPORT',          'en', 'Sport'),
('SECTION_LBL_SPORT',          'es', 'Deporte'),

('SECTION_LBL_NAME',           'fr', 'Nom de la section'),
('SECTION_LBL_NAME',           'en', 'Section Name'),
('SECTION_LBL_NAME',           'es', 'Nombre de la sección'),

('SECTION_LBL_CREATION_YEAR',  'fr', 'Année de création de la section'),
('SECTION_LBL_CREATION_YEAR',  'en', 'Section creation year'),
('SECTION_LBL_CREATION_YEAR',  'es', 'Año de creación de la sección'),

('SECTION_BTN_ADD',            'fr', 'Ajouter une section'),
('SECTION_BTN_ADD',            'en', 'Add Section'),
('SECTION_BTN_ADD',            'es', 'Añadir sección'),

('SECTION_BTN_DELETE',         'fr', 'Supprimer'),
('SECTION_BTN_DELETE',         'en', 'Delete'),
('SECTION_BTN_DELETE',         'es', 'Eliminar'),

('SECTION_CONFIRM_DELETE',     'fr', 'Voulez-vous vraiment retirer cette section ?'),
('SECTION_CONFIRM_DELETE',     'en', 'Are you sure you want to remove this section?'),
('SECTION_CONFIRM_DELETE',     'es', '¿Está seguro de que desea eliminar esta sección?'),

-- Traductions Équipes
('SPORT_TEAMS_MGT',            'fr', 'Équipes'),
('SPORT_TEAMS_MGT',            'en', 'Teams'),
('SPORT_TEAMS_MGT',            'es', 'Equipos'),

('SPORT_NAV_TEAMS',            'fr', 'Équipes'),
('SPORT_NAV_TEAMS',            'en', 'Teams'),
('SPORT_NAV_TEAMS',            'es', 'Equipos'),

('SPORT_ADD_TEAM_BTN',         'fr', 'Nouvelle équipe'),
('SPORT_ADD_TEAM_BTN',         'en', 'New Team'),
('SPORT_ADD_TEAM_BTN',         'es', 'Nuevo Equipo'),

('SPORT_EDIT_TEAM_TITLE',      'fr', 'Modifier l''équipe'),
('SPORT_EDIT_TEAM_TITLE',      'en', 'Edit Team'),
('SPORT_EDIT_TEAM_TITLE',      'es', 'Editar Equipo'),

('SPORT_ADD_TEAM_TITLE',       'fr', 'Créer une équipe'),
('SPORT_ADD_TEAM_TITLE',       'en', 'Create Team'),
('SPORT_ADD_TEAM_TITLE',       'es', 'Crear Equipo'),

('SPORT_VIEW_TEAM_TITLE',      'fr', 'Fiche de l''équipe'),
('SPORT_VIEW_TEAM_TITLE',      'en', 'Team Details'),
('SPORT_VIEW_TEAM_TITLE',      'es', 'Ficha del Equipo'),

('TEAM_CODE',                  'fr', 'Code / Identifiant'),
('TEAM_CODE',                  'en', 'Code / Slug'),
('TEAM_CODE',                  'es', 'Código / Slug'),

('TEAM_NAME',                  'fr', 'Nom de l''équipe'),
('TEAM_NAME',                  'en', 'Team Name'),
('TEAM_NAME',                  'es', 'Nombre del equipo'),

('TEAM_SHORT_NAME',            'fr', 'Nom court'),
('TEAM_SHORT_NAME',            'en', 'Short Name'),
('TEAM_SHORT_NAME',            'es', 'Nombre corto'),

('TEAM_SECTION',               'fr', 'Section rattachée'),
('TEAM_SECTION',               'en', 'Associated Section'),
('TEAM_SECTION',               'es', 'Sección asociada'),

('TEAM_CLUB',                  'fr', 'Club'),
('TEAM_CLUB',                  'en', 'Club'),
('TEAM_CLUB',                  'es', 'Club'),

('TEAM_SPORT',                 'fr', 'Discipline / Sport'),
('TEAM_SPORT',                 'en', 'Sport'),
('TEAM_SPORT',                 'es', 'Deporte'),

('TEAM_GENDER',                'fr', 'Genre'),
('TEAM_GENDER',                'en', 'Gender'),
('TEAM_GENDER',                'es', 'Género'),

('TEAM_GENDER_M',              'fr', 'Masculin'),
('TEAM_GENDER_M',              'en', 'Male'),
('TEAM_GENDER_M',              'es', 'Masculino'),

('TEAM_GENDER_F',              'fr', 'Féminin'),
('TEAM_GENDER_F',              'en', 'Female'),
('TEAM_GENDER_F',              'es', 'Femenino'),

('TEAM_GENDER_MIXED',          'fr', 'Mixte'),
('TEAM_GENDER_MIXED',          'en', 'Mixed'),
('TEAM_GENDER_MIXED',          'es', 'Mixto'),

('TEAM_CATEGORY',              'fr', 'Catégorie d''âge'),
('TEAM_CATEGORY',              'en', 'Age Category'),
('TEAM_CATEGORY',              'es', 'Categoría de edad'),

('TEAM_LEVEL',                 'fr', 'Niveau / Rang'),
('TEAM_LEVEL',                 'en', 'Level / Rank'),
('TEAM_LEVEL',                 'es', 'Nivel / Rango'),

('TEAM_STATUS',                'fr', 'Statut'),
('TEAM_STATUS',                'en', 'Status'),
('TEAM_STATUS',                'es', 'Estado'),

('TEAM_STATUS_ACTIVE',         'fr', 'Active'),
('TEAM_STATUS_ACTIVE',         'en', 'Active'),
('TEAM_STATUS_ACTIVE',         'es', 'Activo'),

('TEAM_STATUS_INACTIVE',       'fr', 'Inactive'),
('TEAM_STATUS_INACTIVE',       'en', 'Inactive'),
('TEAM_STATUS_INACTIVE',       'es', 'Inactivo'),

('TEAM_STATUS_DISSOLVED',      'fr', 'Dissoute'),
('TEAM_STATUS_DISSOLVED',      'en', 'Dissolved'),
('TEAM_STATUS_DISSOLVED',      'es', 'Disuelto'),

('TEAM_MSG_ADDED',             'fr', 'L''équipe a été créée avec succès.'),
('TEAM_MSG_ADDED',             'en', 'Team created successfully.'),
('TEAM_MSG_ADDED',             'es', 'Equipo creado con éxito.'),

('TEAM_MSG_UPDATED',           'fr', 'L''équipe a été mise à jour avec succès.'),
('TEAM_MSG_UPDATED',           'en', 'Team updated successfully.'),
('TEAM_MSG_UPDATED',           'es', 'Equipo actualizado con éxito.'),

('TEAM_MSG_DEACTIVATED',       'fr', 'L''équipe a été désactivée.'),
('TEAM_MSG_DEACTIVATED',       'en', 'Team deactivated.'),
('TEAM_MSG_DEACTIVATED',       'es', 'Equipo desactivado.'),

('TEAM_MSG_DELETED',           'fr', 'L''équipe a été supprimée.'),
('TEAM_MSG_DELETED',           'en', 'Team deleted.'),
('TEAM_MSG_DELETED',           'es', 'Equipo eliminado.'),

('TEAM_ERR_MISSING_FIELDS',    'fr', 'Veuillez remplir tous les champs obligatoires (Section, Code, Nom).'),
('TEAM_ERR_MISSING_FIELDS',    'en', 'Please fill in all required fields (Section, Code, Name).'),
('TEAM_ERR_MISSING_FIELDS',    'es', 'Por favor complete todos los campos obligatorios (Sección, Código, Nombre).'),

('TEAM_ERR_CODE_EXISTS',       'fr', 'Une équipe avec cet identifiant/code existe déjà.'),
('TEAM_ERR_CODE_EXISTS',       'en', 'A team with this code already exists.'),
('TEAM_ERR_CODE_EXISTS',       'es', 'Ya existe un equipo con este código.'),

('TEAM_DELETE_CONFIRM',        'fr', 'Voulez-vous vraiment désactiver cette équipe ?'),
('TEAM_DELETE_CONFIRM',        'en', 'Are you sure you want to deactivate this team?'),
('TEAM_DELETE_CONFIRM',        'es', '¿Está seguro de que desea desactivar este equipo?')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;


-- ============================================================
-- SYNCHRONISATION DES SÉQUENCES
-- ============================================================

SELECT setval('t_sport_sport_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_sport), 1));
SELECT setval('t_sport_season_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_season), 1));
SELECT setval('t_sport_person_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_person), 1));
SELECT setval('t_sport_club_status_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_club_status), 1));
SELECT setval('t_sport_club_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_club), 1));
SELECT setval('t_sport_section_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_section), 1));
SELECT setval('t_sport_team_status_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_team_status), 1));
SELECT setval('t_sport_team_id_seq', COALESCE((SELECT MAX(id) FROM t_sport_team), 1));
