-- ============================================================
-- Module : lang
-- ============================================================
-- Ordre d'exécution :
--   1. Schéma : t_lang_lang (table maître des langues du système)
--   2. Schéma : t_lang_text_key, t_lang_text
--   3. Données : t_lang_lang (langues initiales)
--   4. Données : t_lang_text_key, t_lang_text (traductions LANG_)
--
-- Note : Ce fichier doit être exécuté EN PREMIER.
--        Les autres modules (user, system) en dépendent via t_lang_lang.
-- ============================================================


-- ============================================================
-- SCHÉMA : t_lang_lang (langues disponibles dans le système)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_lang_lang (
    lang_code   VARCHAR(5)  PRIMARY KEY,
    lang_name   VARCHAR(50) NOT NULL,
    lang_flag   VARCHAR(20) DEFAULT '',
    status_id   INT         NOT NULL DEFAULT 1,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    created_by  INT         DEFAULT NULL,
    modified_at TIMESTAMP   DEFAULT NULL,
    modified_by INT         DEFAULT NULL
);


-- ============================================================
-- SCHÉMA : traductions du module lang
-- ============================================================

CREATE TABLE IF NOT EXISTS t_lang_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_lang_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_lang_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)     ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : métadonnées du module lang (point 6.10)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_lang_object (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 1,
    code        VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uk_lang_object_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_lang_object_i18n (
    object_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (object_id, lang_code),
    FOREIGN KEY (object_id) REFERENCES t_lang_object(id)    ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_lang_page (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 1,
    object_id   INT          DEFAULT NULL,
    code        VARCHAR(50)  NOT NULL,
    page_type   VARCHAR(20)  NOT NULL DEFAULT 'list',
    url_path    VARCHAR(255) NOT NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (object_id) REFERENCES t_lang_object(id) ON DELETE SET NULL,
    CONSTRAINT uk_lang_page_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_lang_page_i18n (
    page_id     INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (page_id, lang_code),
    FOREIGN KEY (page_id)   REFERENCES t_lang_page(id)    ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_lang_table (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 1,
    object_id   INT          DEFAULT NULL,
    table_name  VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (object_id) REFERENCES t_lang_object(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_lang_table_i18n (
    table_id    INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    description TEXT         NOT NULL,
    PRIMARY KEY (table_id, lang_code),
    FOREIGN KEY (table_id)  REFERENCES t_lang_table(id)   ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_lang_column (
    id               SERIAL       PRIMARY KEY,
    table_id         INT          NOT NULL,
    column_name      VARCHAR(100) NOT NULL,
    data_type        VARCHAR(50)  NOT NULL,
    is_nullable      BOOLEAN      NOT NULL DEFAULT TRUE,
    is_primary_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    is_foreign_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    fk_target_table  VARCHAR(100) DEFAULT NULL,
    fk_target_column VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (table_id) REFERENCES t_lang_table(id) ON DELETE CASCADE,
    CONSTRAINT uk_lang_column_table_col UNIQUE (table_id, column_name)
);

CREATE TABLE IF NOT EXISTS t_lang_column_i18n (
    column_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    label       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (column_id, lang_code),
    FOREIGN KEY (column_id) REFERENCES t_lang_column(id)  ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : langues initiales
-- ============================================================

INSERT INTO t_lang_lang (lang_code, lang_name, lang_flag) VALUES
('fr', 'Français', 'fi-fr'),
('en', 'English',  'fi-gb'),
('es', 'Español',  'fi-es')
ON CONFLICT (lang_code) DO NOTHING;


-- ============================================================
-- DONNÉES : métadonnées du module lang
-- ============================================================

INSERT INTO t_lang_object (id, module_id, code) VALUES
(1, 1, 'lang')
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_lang_object_i18n (object_id, lang_code, name) VALUES
(1, 'fr', 'Langue'), (1, 'en', 'Language'), (1, 'es', 'Idioma')
ON CONFLICT (object_id, lang_code) DO UPDATE SET
    name = EXCLUDED.name;

INSERT INTO t_lang_page (id, module_id, object_id, code, page_type, url_path) VALUES
(1, 1, 1, 'langs', 'list', '/lang/langs'),
(2, 1, 1, 'lang',  'form', '/lang/lang')
ON CONFLICT (module_id, code) DO UPDATE SET
    object_id = EXCLUDED.object_id,
    page_type = EXCLUDED.page_type,
    url_path  = EXCLUDED.url_path;

INSERT INTO t_lang_table (id, module_id, object_id, table_name) VALUES
(1, 1, 1, 't_lang_lang'),
(2, 1, 1, 't_lang_text_key'),
(3, 1, 1, 't_lang_text'),
(4, 1, 1, 't_lang_object'),
(5, 1, 1, 't_lang_object_i18n'),
(6, 1, 1, 't_lang_page'),
(7, 1, 1, 't_lang_page_i18n'),
(8, 1, 1, 't_lang_table'),
(9, 1, 1, 't_lang_table_i18n'),
(10, 1, 1, 't_lang_column'),
(11, 1, 1, 't_lang_column_i18n')
ON CONFLICT (table_name) DO UPDATE SET
    module_id = EXCLUDED.module_id,
    object_id = EXCLUDED.object_id;


-- ============================================================
-- DONNÉES : clés de traduction
-- ============================================================

INSERT INTO t_lang_text_key (text_code) VALUES
('LANG_LANGS_MGT'),
('LANG_ADD_LANG_BTN'),
('LANG_ADD_LANG_TITLE'),
('LANG_EDIT_LANG_TITLE'),
('LANG_ADD_NOTE'),
('LANG_CODE'),
('LANG_CODE_LABEL'),
('LANG_CODE_PH'),
('LANG_CODE_HELP'),
('LANG_NAME'),
('LANG_NAME_LABEL'),
('LANG_NAME_PH'),
('LANG_FLAG'),
('LANG_FLAG_LABEL'),
('LANG_FLAG_PH'),
('LANG_FLAG_HELP'),
('LANG_NO_FLAG'),
('LANG_STATUS'),
('LANG_ACTIVE'),
('LANG_DRAFT'),
('LANG_UNKNOWN'),
('LANG_INFO_PANEL'),
('LANG_CREATED_AT'),
('LANG_CREATED_BY'),
('LANG_MODIFIED_AT'),
('LANG_MODIFIED_BY'),
('LANG_ERR_MISSING_LANG_FIELDS'),
('LANG_MSG_LANG_UPDATED'),
('LANG_MSG_LANG_ADDED'),
('LANG_SUCCESS_DEACTIVATE_LANG'),
('LANG_ERR_DEACTIVATE_LANG'),
('LANG_SUCCESS_DELETE_LANG'),
('LANG_ERR_DELETE_LANG'),
('LANG_DELETE_LANG_CONFIRM'),
('LANG_FORCE_DELETE_LANG_CONFIRM')
ON CONFLICT (text_code) DO NOTHING;


-- ============================================================
-- DONNÉES : traductions (fr / en / es)
-- ============================================================

INSERT INTO t_lang_text (text_code, lang_code, text_label) VALUES

('LANG_LANGS_MGT',               'fr', 'Langues'),
('LANG_LANGS_MGT',               'en', 'Languages'),
('LANG_LANGS_MGT',               'es', 'Idiomas'),

('LANG_ADD_LANG_BTN',            'fr', 'Ajouter une langue'),
('LANG_ADD_LANG_BTN',            'en', 'Add Language'),
('LANG_ADD_LANG_BTN',            'es', 'Añadir Idioma'),

('LANG_EDIT_LANG_TITLE',         'fr', 'Modifier la langue'),
('LANG_EDIT_LANG_TITLE',         'en', 'Edit Language'),
('LANG_EDIT_LANG_TITLE',         'es', 'Editar Idioma'),

('LANG_ADD_NOTE',                'fr', 'Les traductions pour cette nouvelle langue seront créées automatiquement (vides) pour tous les modules existants.'),
('LANG_ADD_NOTE',                'en', 'Translations for this new language will be automatically created (empty) for all existing modules.'),
('LANG_ADD_NOTE',                'es', 'Las traducciones para este nuevo idioma se crearán automáticamente (vacías) para todos los módulos existentes.'),

('LANG_CODE',                    'fr', 'Code'),
('LANG_CODE',                    'en', 'Code'),
('LANG_CODE',                    'es', 'Código'),

('LANG_CODE_LABEL',              'fr', 'Code de la langue'),
('LANG_CODE_LABEL',              'en', 'Language Code'),
('LANG_CODE_LABEL',              'es', 'Código de idioma'),

('LANG_CODE_PH',                 'fr', 'ex : fr, en, es'),
('LANG_CODE_PH',                 'en', 'e.g.: fr, en, es'),
('LANG_CODE_PH',                 'es', 'ej.: fr, en, es'),

('LANG_CODE_HELP',               'fr', 'Le code de la langue ne peut pas être modifié.'),
('LANG_CODE_HELP',               'en', 'The language code cannot be changed.'),
('LANG_CODE_HELP',               'es', 'El código de idioma no se puede cambiar.'),

('LANG_NAME',                    'fr', 'Nom'),
('LANG_NAME',                    'en', 'Name'),
('LANG_NAME',                    'es', 'Nombre'),

('LANG_NAME_LABEL',              'fr', 'Nom de la langue'),
('LANG_NAME_LABEL',              'en', 'Language Name'),
('LANG_NAME_LABEL',              'es', 'Nombre del idioma'),

('LANG_NAME_PH',                 'fr', 'ex : Français, English'),
('LANG_NAME_PH',                 'en', 'e.g.: French, English'),
('LANG_NAME_PH',                 'es', 'ej.: Francés, Inglés'),

('LANG_FLAG',                    'fr', 'Drapeau'),
('LANG_FLAG',                    'en', 'Flag'),
('LANG_FLAG',                    'es', 'Bandera'),

('LANG_FLAG_LABEL',              'fr', 'Classe du drapeau'),
('LANG_FLAG_LABEL',              'en', 'Flag CSS Class'),
('LANG_FLAG_LABEL',              'es', 'Clase CSS de la bandera'),

('LANG_FLAG_PH',                 'fr', 'ex : fi-fr, fi-gb'),
('LANG_FLAG_PH',                 'en', 'e.g.: fi-fr, fi-gb'),
('LANG_FLAG_PH',                 'es', 'ej.: fi-fr, fi-gb'),

('LANG_FLAG_HELP',               'fr', 'Classe CSS flag-icons (ex : fi-fr). Laisser vide si aucun drapeau.'),
('LANG_FLAG_HELP',               'en', 'flag-icons CSS class (e.g.: fi-fr). Leave empty for no flag.'),
('LANG_FLAG_HELP',               'es', 'Clase CSS flag-icons (ej.: fi-fr). Dejar vacío si no hay bandera.'),

('LANG_NO_FLAG',                 'fr', 'Aucun drapeau'),
('LANG_NO_FLAG',                 'en', 'No flag'),
('LANG_NO_FLAG',                 'es', 'Sin bandera'),

('LANG_STATUS',                  'fr', 'Statut'),
('LANG_STATUS',                  'en', 'Status'),
('LANG_STATUS',                  'es', 'Estado'),

('LANG_ACTIVE',                  'fr', 'Actif'),
('LANG_ACTIVE',                  'en', 'Active'),
('LANG_ACTIVE',                  'es', 'Activo'),

('LANG_DRAFT',                   'fr', 'Brouillon'),
('LANG_DRAFT',                   'en', 'Draft'),
('LANG_DRAFT',                   'es', 'Borrador'),

('LANG_UNKNOWN',                 'fr', 'Inconnu'),
('LANG_UNKNOWN',                 'en', 'Unknown'),
('LANG_UNKNOWN',                 'es', 'Desconocido'),

('LANG_INFO_PANEL',              'fr', 'Informations'),
('LANG_INFO_PANEL',              'en', 'Information'),
('LANG_INFO_PANEL',              'es', 'Información'),

('LANG_CREATED_AT',              'fr', 'Créé le :'),
('LANG_CREATED_AT',              'en', 'Created at:'),
('LANG_CREATED_AT',              'es', 'Creado el:'),

('LANG_CREATED_BY',              'fr', 'Créé par :'),
('LANG_CREATED_BY',              'en', 'Created by:'),
('LANG_CREATED_BY',              'es', 'Creado por:'),

('LANG_MODIFIED_AT',             'fr', 'Modifié le :'),
('LANG_MODIFIED_AT',             'en', 'Modified at:'),
('LANG_MODIFIED_AT',             'es', 'Modificado el:'),

('LANG_MODIFIED_BY',             'fr', 'Modifié par :'),
('LANG_MODIFIED_BY',             'en', 'Modified by:'),
('LANG_MODIFIED_BY',             'es', 'Modificado por:'),

('LANG_ERR_MISSING_LANG_FIELDS', 'fr', 'Le code et le nom de la langue sont obligatoires.'),
('LANG_ERR_MISSING_LANG_FIELDS', 'en', 'The language code and name are required.'),
('LANG_ERR_MISSING_LANG_FIELDS', 'es', 'El código y el nombre del idioma son obligatorios.'),

('LANG_MSG_LANG_UPDATED',        'fr', 'Langue mise à jour avec succès.'),
('LANG_MSG_LANG_UPDATED',        'en', 'Language updated successfully.'),
('LANG_MSG_LANG_UPDATED',        'es', 'Idioma actualizado con éxito.'),

('LANG_MSG_LANG_ADDED',          'fr', 'Langue ajoutée avec succès.'),
('LANG_MSG_LANG_ADDED',          'en', 'Language added successfully.'),
('LANG_MSG_LANG_ADDED',          'es', 'Idioma añadido con éxito.'),

('LANG_SUCCESS_DEACTIVATE_LANG', 'fr', 'Langue désactivée avec succès.'),
('LANG_SUCCESS_DEACTIVATE_LANG', 'en', 'Language deactivated successfully.'),
('LANG_SUCCESS_DEACTIVATE_LANG', 'es', 'Idioma desactivado con éxito.'),

('LANG_ERR_DEACTIVATE_LANG',     'fr', 'Erreur lors de la désactivation de la langue : '),
('LANG_ERR_DEACTIVATE_LANG',     'en', 'Error deactivating the language: '),
('LANG_ERR_DEACTIVATE_LANG',     'es', 'Error al desactivar el idioma: '),

('LANG_SUCCESS_DELETE_LANG',     'fr', 'Langue supprimée définitivement.'),
('LANG_SUCCESS_DELETE_LANG',     'en', 'Language permanently deleted.'),
('LANG_SUCCESS_DELETE_LANG',     'es', 'Idioma eliminado permanentemente.'),

('LANG_ERR_DELETE_LANG',         'fr', 'Erreur lors de la suppression définitive de la langue : '),
('LANG_ERR_DELETE_LANG',         'en', 'Error permanently deleting the language: '),
('LANG_ERR_DELETE_LANG',         'es', 'Error al eliminar permanentemente el idioma: '),

('LANG_DELETE_LANG_CONFIRM',     'fr', 'Êtes-vous sûr de vouloir désactiver cette langue ?'),
('LANG_DELETE_LANG_CONFIRM',     'en', 'Are you sure you want to deactivate this language?'),
('LANG_DELETE_LANG_CONFIRM',     'es', '¿Está seguro de que desea desactivar este idioma?'),

('LANG_FORCE_DELETE_LANG_CONFIRM','fr', 'Êtes-vous sûr de vouloir supprimer définitivement cette langue ? Cette action est irréversible.'),
('LANG_FORCE_DELETE_LANG_CONFIRM','en', 'Are you sure you want to permanently delete this language? This action cannot be undone.'),
('LANG_FORCE_DELETE_LANG_CONFIRM','es', '¿Está seguro de que desea eliminar permanentemente este idioma? Esta acción es irreversible.')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
