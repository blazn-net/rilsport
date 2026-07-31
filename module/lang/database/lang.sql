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
-- DONNÉES : langues initiales
-- ============================================================

INSERT INTO t_lang_lang (lang_code, lang_name, lang_flag) VALUES
('fr', 'Français', 'fi-fr'),
('en', 'English',  'fi-gb'),
('es', 'Español',  'fi-es')
ON CONFLICT (lang_code) DO NOTHING;


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
