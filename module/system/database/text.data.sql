-- ============================================================
-- Données : traductions du module system
-- Dépend de : text.schema.sql, main/lang.data.sql
-- ============================================================

-- ------------------------------------------------------------
-- Clés de traduction
-- ------------------------------------------------------------

INSERT INTO t_system_text_key (text_code) VALUES
('HOME'), ('LOGIN'), ('REGISTER'), ('LOGOUT'), ('MVC_DESC'),
('LOGGED_IN_AS'), ('ROLE_LABEL'), ('NOT_LOGGED_IN'), ('MORE_FEATURES'),
('MY_PROFILE'), ('COPYRIGHT'), ('ERR_INVALID_MODEL_FORMAT'),
('ERR_VIEW_NOT_FOUND'), ('ERR_CONTROLLER_NOT_FOUND'), ('BTN_EDIT'),
('BTN_DISABLE'), ('BTN_DELETE'), ('MENU')
ON CONFLICT (text_code) DO NOTHING;

-- ------------------------------------------------------------
-- Traductions
-- ------------------------------------------------------------

INSERT INTO t_system_text (text_code, lang_code, text_label) VALUES

('HOME',                    'fr', 'Accueil'),
('HOME',                    'en', 'Home'),
('HOME',                    'es', 'Inicio'),
('LOGIN',                   'fr', 'Connexion'),
('LOGIN',                   'en', 'Login'),
('LOGIN',                   'es', 'Iniciar sesión'),
('REGISTER',                'fr', 'Inscription'),
('REGISTER',                'en', 'Register'),
('REGISTER',                'es', 'Registro'),
('LOGOUT',                  'fr', 'Déconnexion'),
('LOGOUT',                  'en', 'Logout'),
('LOGOUT',                  'es', 'Cerrar sesión'),
('MENU',                    'fr', 'Menu'),
('MENU',                    'en', 'Menu'),
('MENU',                    'es', 'Menú'),
('MVC_DESC',                'fr', 'Ce projet utilise désormais une architecture propre et moderne : le Modèle-Vue-Contrôleur (MVC).'),
('MVC_DESC',                'en', 'This project now uses a clean and modern architecture: Model-View-Controller (MVC).'),
('MVC_DESC',                'es', 'Este proyecto ahora utiliza una arquitectura limpia y moderna: Modelo-Vista-Controlador (MVC).'),
('LOGGED_IN_AS',            'fr', 'Vous êtes connecté en tant que'),
('LOGGED_IN_AS',            'en', 'You are logged in as'),
('LOGGED_IN_AS',            'es', 'Has iniciado sesión como'),
('ROLE_LABEL',              'fr', 'Rôle'),
('ROLE_LABEL',              'en', 'Role'),
('ROLE_LABEL',              'es', 'Rol'),
('NOT_LOGGED_IN',           'fr', 'Vous n''êtes pas connecté.'),
('NOT_LOGGED_IN',           'en', 'You are not logged in.'),
('NOT_LOGGED_IN',           'es', 'No has iniciado sesión.'),
('MORE_FEATURES',           'fr', 'pour accéder à plus de fonctionnalités.'),
('MORE_FEATURES',           'en', 'to access more features.'),
('MORE_FEATURES',           'es', 'para acceder a más funciones.'),
('MY_PROFILE',              'fr', 'Mon Profil'),
('MY_PROFILE',              'en', 'My Profile'),
('MY_PROFILE',              'es', 'Mi Perfil'),
('COPYRIGHT',               'fr', 'Tous droits réservés.'),
('COPYRIGHT',               'en', 'All rights reserved.'),
('COPYRIGHT',               'es', 'Todos los derechos reservados.'),
('ERR_INVALID_MODEL_FORMAT','fr', 'Format de modèle invalide. Utilisez "module/ModelName"'),
('ERR_INVALID_MODEL_FORMAT','en', 'Invalid model format. Use "module/ModelName"'),
('ERR_INVALID_MODEL_FORMAT','es', 'Formato de modelo no válido. Utilice "module/ModelName"'),
('ERR_VIEW_NOT_FOUND',      'fr', 'La vue n''existe pas : '),
('ERR_VIEW_NOT_FOUND',      'en', 'View does not exist: '),
('ERR_VIEW_NOT_FOUND',      'es', 'La vista no existe: '),
('ERR_CONTROLLER_NOT_FOUND','fr', 'Le contrôleur %s est introuvable.'),
('ERR_CONTROLLER_NOT_FOUND','en', 'The controller %s was not found.'),
('ERR_CONTROLLER_NOT_FOUND','es', 'El controlador %s no se encontró.'),
('BTN_EDIT',                'fr', 'Modifier'),
('BTN_EDIT',                'en', 'Edit'),
('BTN_EDIT',                'es', 'Editar'),
('BTN_DISABLE',             'fr', 'Désactiver'),
('BTN_DISABLE',             'en', 'Disable'),
('BTN_DISABLE',             'es', 'Desactivar'),
('BTN_DELETE',              'fr', 'Supprimer'),
('BTN_DELETE',              'en', 'Delete'),
('BTN_DELETE',              'es', 'Eliminar')

ON CONFLICT (text_code, lang_code) DO NOTHING;
