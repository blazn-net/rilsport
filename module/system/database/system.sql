-- ============================================================
-- Module : system
-- ============================================================
-- Ordre d'exécution :
--   1. Schéma : t_system_module, t_system_module_i18n
--   2. Schéma : t_system_object, t_system_object_i18n
--   3. Schéma : t_system_page, t_system_page_i18n
--   4. Schéma : t_system_table, t_system_table_i18n
--   5. Schéma : t_system_column, t_system_column_i18n
--   6. Schéma : t_system_text_key, t_system_text (traductions UI fixes)
--   7. Données : métadonnées et leurs traductions i18n
--   8. Données : traductions UI fixes (SYS_)
--
-- Dépendances : lang.sql (t_lang_lang)
-- ============================================================


-- ============================================================
-- SCHÉMA : métadonnées système et traductions métier (_i18n)
-- ============================================================

-- 1. Modules
CREATE TABLE IF NOT EXISTS t_system_module (
    id          SERIAL       PRIMARY KEY,
    code        VARCHAR(50)  NOT NULL UNIQUE,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    is_system   BOOLEAN      NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS t_system_module_i18n (
    module_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (module_id, lang_code),
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)   ON DELETE CASCADE
);

-- 2. Objets métier
CREATE TABLE IF NOT EXISTS t_system_object (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    code        VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    CONSTRAINT uk_system_object_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_system_object_i18n (
    object_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (object_id, lang_code),
    FOREIGN KEY (object_id) REFERENCES t_system_object(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)   ON DELETE CASCADE
);

-- 3. Pages / Routes
CREATE TABLE IF NOT EXISTS t_system_page (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    object_id   INT          DEFAULT NULL,
    code        VARCHAR(50)  NOT NULL,
    page_type   VARCHAR(20)  NOT NULL DEFAULT 'list',
    url_path    VARCHAR(255) NOT NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_system_object(id) ON DELETE SET NULL,
    CONSTRAINT uk_system_page_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_system_page_i18n (
    page_id     INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (page_id, lang_code),
    FOREIGN KEY (page_id)   REFERENCES t_system_page(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

-- 4. Tables BDD
CREATE TABLE IF NOT EXISTS t_system_table (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL,
    object_id   INT          DEFAULT NULL,
    table_name  VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_system_object(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_system_table_i18n (
    table_id    INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    description TEXT         NOT NULL,
    PRIMARY KEY (table_id, lang_code),
    FOREIGN KEY (table_id)  REFERENCES t_system_table(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)  ON DELETE CASCADE
);

-- 5. Colonnes BDD
CREATE TABLE IF NOT EXISTS t_system_column (
    id               SERIAL       PRIMARY KEY,
    table_id         INT          NOT NULL,
    column_name      VARCHAR(100) NOT NULL,
    data_type        VARCHAR(50)  NOT NULL,
    is_nullable      BOOLEAN      NOT NULL DEFAULT TRUE,
    is_primary_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    is_foreign_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    fk_target_table  VARCHAR(100) DEFAULT NULL,
    fk_target_column VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (table_id) REFERENCES t_system_table(id) ON DELETE CASCADE,
    CONSTRAINT uk_system_column_table_col UNIQUE (table_id, column_name)
);

CREATE TABLE IF NOT EXISTS t_system_column_i18n (
    column_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    label       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (column_id, lang_code),
    FOREIGN KEY (column_id) REFERENCES t_system_column(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)   ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : text (tables de traduction UI fixes du module system)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_system_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_system_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_system_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)       ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : métadonnées et traductions métier (_i18n)
-- ============================================================

-- Modules
INSERT INTO t_system_module (id, code, is_active, is_system) VALUES
(1, 'lang',   TRUE, TRUE),
(2, 'system', TRUE, TRUE),
(3, 'user',   TRUE, TRUE),
(4, 'main',   TRUE, TRUE)
ON CONFLICT (code) DO UPDATE SET
    is_active = EXCLUDED.is_active,
    is_system = EXCLUDED.is_system;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description) VALUES
(1, 'fr', 'Langue',      'Gestion des langues et traductions'),
(1, 'en', 'Language',    'Language and translation management'),
(1, 'es', 'Idioma',      'Gestión de idiomas y traducciones'),

(2, 'fr', 'Système',     'Gestion système, métadonnées et logs'),
(2, 'en', 'System',      'System management, metadata and logs'),
(2, 'es', 'Sistema',     'Gestión de sistema, metadatos y registros'),

(3, 'fr', 'Utilisateur', 'Gestion des utilisateurs et des rôles'),
(3, 'en', 'User',        'User and role management'),
(3, 'es', 'Usuario',     'Gestión de usuarios y roles'),

(4, 'fr', 'Main',        'Module principal applicatif'),
(4, 'en', 'Main',        'Main application module'),
(4, 'es', 'Main',        'Módulo principal de la aplicación')
ON CONFLICT (module_id, lang_code) DO UPDATE SET
    name        = EXCLUDED.name,
    description = EXCLUDED.description;

-- Objets métier
INSERT INTO t_system_object (id, module_id, code) VALUES
(1, 1, 'lang'),
(2, 2, 'module'),
(3, 2, 'object'),
(4, 2, 'page'),
(5, 2, 'table'),
(6, 2, 'column'),
(7, 3, 'user'),
(8, 3, 'role')
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_system_object_i18n (object_id, lang_code, name) VALUES
(1, 'fr', 'Langue'),     (1, 'en', 'Language'),  (1, 'es', 'Idioma'),
(2, 'fr', 'Module'),     (2, 'en', 'Module'),    (2, 'es', 'Módulo'),
(3, 'fr', 'Objet'),      (3, 'en', 'Object'),    (3, 'es', 'Objeto'),
(4, 'fr', 'Page'),       (4, 'en', 'Page'),      (4, 'es', 'Página'),
(5, 'fr', 'Table BDD'),  (5, 'en', 'DB Table'),  (5, 'es', 'Tabla BD'),
(6, 'fr', 'Colonne BDD'),(6, 'en', 'DB Column'), (6, 'es', 'Columna BD'),
(7, 'fr', 'Utilisateur'),(7, 'en', 'User'),      (7, 'es', 'Usuario'),
(8, 'fr', 'Rôle'),       (8, 'en', 'Role'),      (8, 'es', 'Rol')
ON CONFLICT (object_id, lang_code) DO UPDATE SET
    name = EXCLUDED.name;

-- Pages / Routes
INSERT INTO t_system_page (id, module_id, object_id, code, page_type, url_path) VALUES
(1, 1, 1, 'langs',   'list',   '/lang/langs'),
(2, 1, 1, 'lang',    'form',   '/lang/lang'),
(3, 2, 2, 'modules', 'list',   '/system/modules'),
(4, 2, 2, 'module',  'form',   '/system/module'),
(5, 2, 3, 'objects', 'list',   '/system/objects'),
(6, 2, 3, 'object',  'form',   '/system/object'),
(7, 2, 4, 'pages',   'list',   '/system/pages'),
(8, 2, 4, 'page',    'form',   '/system/page'),
(9, 2, 5, 'tables',  'list',   '/system/tables'),
(10, 2, 5, 'table',  'form',   '/system/table'),
(11, 2, 6, 'columns','list',   '/system/columns'),
(12, 2, 6, 'column', 'form',   '/system/column'),
(13, 3, 7, 'users',   'list',   '/user/users'),
(14, 3, 7, 'user',    'form',   '/user/user'),
(15, 3, 7, 'login',   'custom', '/user/login'),
(16, 3, 7, 'profile', 'custom', '/user/profile')
ON CONFLICT (module_id, code) DO UPDATE SET
    object_id = EXCLUDED.object_id,
    page_type = EXCLUDED.page_type,
    url_path  = EXCLUDED.url_path;

-- Tables BDD
INSERT INTO t_system_table (id, module_id, object_id, table_name) VALUES
(1, 1, 1, 't_lang_lang'),
(2, 1, 1, 't_lang_text_key'),
(3, 1, 1, 't_lang_text'),
(4, 2, 2, 't_system_module'),
(5, 2, 3, 't_system_object'),
(6, 2, 4, 't_system_page'),
(7, 2, 5, 't_system_table'),
(8, 2, 6, 't_system_column'),
(9, 2, NULL, 't_system_text_key'),
(10, 2, NULL, 't_system_text'),
(11, 3, 7, 't_user_user'),
(12, 3, 7, 't_user_user_status'),
(13, 3, 8, 't_user_role'),
(14, 3, 8, 't_user_user_role'),
(15, 3, NULL, 't_user_text_key'),
(16, 3, NULL, 't_user_text')
ON CONFLICT (table_name) DO UPDATE SET
    module_id = EXCLUDED.module_id,
    object_id = EXCLUDED.object_id;


-- ============================================================
-- DONNÉES : traductions UI fixes (SYS_)
-- ============================================================

INSERT INTO t_system_text_key (text_code) VALUES
('SYS_HOME'), ('SYS_LOGIN'), ('SYS_REGISTER'), ('SYS_LOGOUT'), ('SYS_MVC_DESC'),
('SYS_LOGGED_IN_AS'), ('SYS_ROLE_LABEL'), ('SYS_NOT_LOGGED_IN'), ('SYS_MORE_FEATURES'),
('SYS_MY_PROFILE'), ('SYS_COPYRIGHT'), ('SYS_ERR_INVALID_MODEL_FORMAT'),
('SYS_ERR_VIEW_NOT_FOUND'), ('SYS_ERR_CONTROLLER_NOT_FOUND'), ('SYS_BTN_EDIT'),
('SYS_BTN_DISABLE'), ('SYS_BTN_DELETE'), ('SYS_MENU'),
('SYS_MODULE_TITLE'), ('SYS_OBJECT_TITLE'), ('SYS_PAGE_TITLE'), ('SYS_TABLE_TITLE'), ('SYS_COLUMN_TITLE')
ON CONFLICT (text_code) DO NOTHING;

INSERT INTO t_system_text (text_code, lang_code, text_label) VALUES

('SYS_HOME',                    'fr', 'Accueil'),
('SYS_HOME',                    'en', 'Home'),
('SYS_HOME',                    'es', 'Inicio'),
('SYS_LOGIN',                   'fr', 'Connexion'),
('SYS_LOGIN',                   'en', 'Login'),
('SYS_LOGIN',                   'es', 'Iniciar sesión'),
('SYS_REGISTER',                'fr', 'Inscription'),
('SYS_REGISTER',                'en', 'Register'),
('SYS_REGISTER',                'es', 'Registro'),
('SYS_LOGOUT',                  'fr', 'Déconnexion'),
('SYS_LOGOUT',                  'en', 'Logout'),
('SYS_LOGOUT',                  'es', 'Cerrar sesión'),
('SYS_MENU',                    'fr', 'Menu'),
('SYS_MENU',                    'en', 'Menu'),
('SYS_MENU',                    'es', 'Menú'),
('SYS_MVC_DESC',                'fr', 'Ce projet utilise désormais une architecture propre et moderne : le Modèle-Vue-Contrôleur (MVC).'),
('SYS_MVC_DESC',                'en', 'This project now uses a clean and modern architecture: Model-View-Controller (MVC).'),
('SYS_MVC_DESC',                'es', 'Este proyecto ahora utiliza una arquitectura limpia y moderna: Modelo-Vista-Controlador (MVC).'),
('SYS_LOGGED_IN_AS',            'fr', 'Vous êtes connecté en tant que'),
('SYS_LOGGED_IN_AS',            'en', 'You are logged in as'),
('SYS_LOGGED_IN_AS',            'es', 'Has iniciado sesión como'),
('SYS_ROLE_LABEL',              'fr', 'Rôle'),
('SYS_ROLE_LABEL',              'en', 'Role'),
('SYS_ROLE_LABEL',              'es', 'Rol'),
('SYS_NOT_LOGGED_IN',           'fr', 'Vous n''êtes pas connecté.'),
('SYS_NOT_LOGGED_IN',           'en', 'You are not logged in.'),
('SYS_NOT_LOGGED_IN',           'es', 'No has iniciado sesión.'),
('SYS_MORE_FEATURES',           'fr', 'pour accéder à plus de fonctionnalités.'),
('SYS_MORE_FEATURES',           'en', 'to access more features.'),
('SYS_MORE_FEATURES',           'es', 'para acceder a más funciones.'),
('SYS_MY_PROFILE',              'fr', 'Mon Profil'),
('SYS_MY_PROFILE',              'en', 'My Profile'),
('SYS_MY_PROFILE',              'es', 'Mi Perfil'),
('SYS_COPYRIGHT',               'fr', 'Tous droits réservés.'),
('SYS_COPYRIGHT',               'en', 'All rights reserved.'),
('SYS_COPYRIGHT',               'es', 'Todos los derechos reservados.'),
('SYS_ERR_INVALID_MODEL_FORMAT','fr', 'Format de modèle invalide. Utilisez "module/ModelName"'),
('SYS_ERR_INVALID_MODEL_FORMAT','en', 'Invalid model format. Use "module/ModelName"'),
('SYS_ERR_INVALID_MODEL_FORMAT','es', 'Formato de modelo no válido. Utilice "module/ModelName"'),
('SYS_ERR_VIEW_NOT_FOUND',      'fr', 'La vue n''existe pas : '),
('SYS_ERR_VIEW_NOT_FOUND',      'en', 'View does not exist: '),
('SYS_ERR_VIEW_NOT_FOUND',      'es', 'La vista no existe: '),
('SYS_ERR_CONTROLLER_NOT_FOUND','fr', 'Le contrôleur %s est introuvable.'),
('SYS_ERR_CONTROLLER_NOT_FOUND','en', 'The controller %s was not found.'),
('SYS_ERR_CONTROLLER_NOT_FOUND','es', 'El controlador %s no se encontró.'),
('SYS_BTN_EDIT',                'fr', 'Modifier'),
('SYS_BTN_EDIT',                'en', 'Edit'),
('SYS_BTN_EDIT',                'es', 'Editar'),
('SYS_BTN_DISABLE',             'fr', 'Désactiver'),
('SYS_BTN_DISABLE',             'en', 'Disable'),
('SYS_BTN_DISABLE',             'es', 'Desactivar'),
('SYS_BTN_DELETE',              'fr', 'Supprimer'),
('SYS_BTN_DELETE',              'en', 'Delete'),
('SYS_BTN_DELETE',              'es', 'Eliminar'),
('SYS_MODULE_TITLE',            'fr', 'Gestion des Modules'),
('SYS_MODULE_TITLE',            'en', 'Module Management'),
('SYS_MODULE_TITLE',            'es', 'Gestión de Módulos'),
('SYS_OBJECT_TITLE',            'fr', 'Gestion des Objets'),
('SYS_OBJECT_TITLE',            'en', 'Object Management'),
('SYS_OBJECT_TITLE',            'es', 'Gestión de Objetos'),
('SYS_PAGE_TITLE',              'fr', 'Gestion des Pages'),
('SYS_PAGE_TITLE',              'en', 'Page Management'),
('SYS_PAGE_TITLE',              'es', 'Gestión de Páginas'),
('SYS_TABLE_TITLE',             'fr', 'Gestion des Tables'),
('SYS_TABLE_TITLE',             'en', 'Table Management'),
('SYS_TABLE_TITLE',             'es', 'Gestión de Tablas'),
('SYS_COLUMN_TITLE',            'fr', 'Gestion des Colonnes'),
('SYS_COLUMN_TITLE',            'en', 'Column Management'),
('SYS_COLUMN_TITLE',            'es', 'Gestión de Columnas')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
