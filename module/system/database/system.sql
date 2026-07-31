-- ============================================================
-- Module : system
-- ============================================================
-- Ordre d'exécution :
--   1. Schéma : t_system_text_key, t_system_text
--   2. Données : t_system_text_key, t_system_text (traductions SYS_)
--
-- Dépendances : lang.sql (t_lang_lang)
-- ============================================================


-- ============================================================
-- SCHÉMA : text (tables de traduction du module system)
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
-- DONNÉES : traductions
-- ============================================================

INSERT INTO t_system_text_key (text_code) VALUES
('SYS_HOME'), ('SYS_LOGIN'), ('SYS_REGISTER'), ('SYS_LOGOUT'), ('SYS_MVC_DESC'),
('SYS_LOGGED_IN_AS'), ('SYS_ROLE_LABEL'), ('SYS_NOT_LOGGED_IN'), ('SYS_MORE_FEATURES'),
('SYS_MY_PROFILE'), ('SYS_COPYRIGHT'), ('SYS_ERR_INVALID_MODEL_FORMAT'),
('SYS_ERR_VIEW_NOT_FOUND'), ('SYS_ERR_CONTROLLER_NOT_FOUND'), ('SYS_BTN_EDIT'),
('SYS_BTN_DISABLE'), ('SYS_BTN_DELETE'), ('SYS_MENU')
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
('SYS_BTN_DELETE',              'es', 'Eliminar')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
