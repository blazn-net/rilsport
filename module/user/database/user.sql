-- ============================================================
-- Module : user
-- ============================================================
-- Ordre d'exécution :
--   1. Schéma : t_user_user_status, t_user_role, t_user_user, t_user_user_role
--   2. Schéma : t_user_text_key, t_user_text
--   3. Données : t_user_user_status, t_user_role
--   4. Données : t_user_user (admin par défaut), t_user_user_role
--   5. Données : t_user_text_key, t_user_text (traductions)
--
-- Dépendances : lang.sql (t_lang_lang)
-- ============================================================


-- ============================================================
-- SCHÉMA : user (tables métier)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_user_user_status (
    id        SERIAL       PRIMARY KEY,
    text_code VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS t_user_role (
    role_id    VARCHAR(50)  PRIMARY KEY,
    text_code  VARCHAR(100) NOT NULL,
    badge_code VARCHAR(50)  NOT NULL
);

CREATE TABLE IF NOT EXISTS t_user_user (
    id            SERIAL       PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nom           VARCHAR(100) DEFAULT NULL,
    prenom        VARCHAR(100) DEFAULT NULL,
    status_id     INT          NOT NULL DEFAULT 2,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by    INT          DEFAULT NULL,
    modified_at   TIMESTAMP    DEFAULT NULL,
    modified_by   INT          DEFAULT NULL,
    FOREIGN KEY (status_id)   REFERENCES t_user_user_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)        ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_user_user_role (
    user_id INT         NOT NULL,
    role    VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES t_user_user(id)      ON DELETE CASCADE,
    FOREIGN KEY (role)    REFERENCES t_user_role(role_id) ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : traductions du module user
-- ============================================================

CREATE TABLE IF NOT EXISTS t_user_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_user_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_user_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)     ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : métadonnées du module user (point 6.10)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_user_object (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 3,
    code        VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    CONSTRAINT uk_user_object_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_user_object_i18n (
    object_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    name        VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (object_id, lang_code),
    FOREIGN KEY (object_id) REFERENCES t_user_object(id)    ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_user_page (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 3,
    object_id   INT          DEFAULT NULL,
    code        VARCHAR(50)  NOT NULL,
    page_type   VARCHAR(20)  NOT NULL DEFAULT 'list',
    url_path    VARCHAR(255) NOT NULL,
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_user_object(id)   ON DELETE SET NULL,
    CONSTRAINT uk_user_page_module_code UNIQUE (module_id, code)
);

CREATE TABLE IF NOT EXISTS t_user_page_i18n (
    page_id     INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (page_id, lang_code),
    FOREIGN KEY (page_id)   REFERENCES t_user_page(id)    ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_user_table (
    id          SERIAL       PRIMARY KEY,
    module_id   INT          NOT NULL DEFAULT 3,
    object_id   INT          DEFAULT NULL,
    table_name  VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES t_system_module(id) ON DELETE CASCADE,
    FOREIGN KEY (object_id) REFERENCES t_user_object(id)   ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_user_table_i18n (
    table_id    INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    description TEXT         NOT NULL,
    PRIMARY KEY (table_id, lang_code),
    FOREIGN KEY (table_id)  REFERENCES t_user_table(id)   ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS t_user_column (
    id               SERIAL       PRIMARY KEY,
    table_id         INT          NOT NULL,
    column_name      VARCHAR(100) NOT NULL,
    data_type        VARCHAR(50)  NOT NULL,
    is_nullable      BOOLEAN      NOT NULL DEFAULT TRUE,
    is_primary_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    is_foreign_key   BOOLEAN      NOT NULL DEFAULT FALSE,
    fk_target_table  VARCHAR(100) DEFAULT NULL,
    fk_target_column VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (table_id) REFERENCES t_user_table(id) ON DELETE CASCADE,
    CONSTRAINT uk_user_column_table_col UNIQUE (table_id, column_name)
);

CREATE TABLE IF NOT EXISTS t_user_column_i18n (
    column_id   INT          NOT NULL,
    lang_code   VARCHAR(5)   NOT NULL,
    label       VARCHAR(100) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (column_id, lang_code),
    FOREIGN KEY (column_id) REFERENCES t_user_column(id)  ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : statuts et rôles
-- ============================================================

INSERT INTO t_user_user_status (id, text_code) VALUES
(1, 'USER_STATUS_ACTIVE'),
(2, 'USER_STATUS_PENDING')
ON CONFLICT (id) DO NOTHING;

INSERT INTO t_user_role (role_id, text_code, badge_code) VALUES
('admin', 'USER_ROLE_ADMIN', 'alert'),
('user',  'USER_ROLE_USER',  'primary')
ON CONFLICT (role_id) DO NOTHING;


-- ============================================================
-- DONNÉES : métadonnées du module user
-- ============================================================

INSERT INTO t_user_object (id, module_id, code) VALUES
(7, 3, 'user'),
(8, 3, 'role')
ON CONFLICT (module_id, code) DO NOTHING;

INSERT INTO t_user_object_i18n (object_id, lang_code, name) VALUES
(7, 'fr', 'Utilisateur'),(7, 'en', 'User'),      (7, 'es', 'Usuario'),
(8, 'fr', 'Rôle'),       (8, 'en', 'Role'),      (8, 'es', 'Rol')
ON CONFLICT (object_id, lang_code) DO UPDATE SET
    name = EXCLUDED.name;

INSERT INTO t_user_page (id, module_id, object_id, code, page_type, url_path) VALUES
(13, 3, 7, 'users',   'list',   '/user/users'),
(14, 3, 7, 'user',    'form',   '/user/user'),
(15, 3, 7, 'login',   'custom', '/user/login'),
(16, 3, 7, 'profile', 'custom', '/user/profile')
ON CONFLICT (module_id, code) DO UPDATE SET
    object_id = EXCLUDED.object_id,
    page_type = EXCLUDED.page_type,
    url_path  = EXCLUDED.url_path;

INSERT INTO t_user_table (id, module_id, object_id, table_name) VALUES
(11, 3, 7, 't_user_user'),
(12, 3, 7, 't_user_user_status'),
(13, 3, 8, 't_user_role'),
(14, 3, 8, 't_user_user_role'),
(15, 3, NULL, 't_user_text_key'),
(16, 3, NULL, 't_user_text'),
(17, 3, 7, 't_user_object'),
(18, 3, 7, 't_user_object_i18n'),
(19, 3, 7, 't_user_page'),
(20, 3, 7, 't_user_page_i18n'),
(21, 3, 7, 't_user_table'),
(22, 3, 7, 't_user_table_i18n'),
(23, 3, 7, 't_user_column'),
(24, 3, 7, 't_user_column_i18n')
ON CONFLICT (table_name) DO UPDATE SET
    module_id = EXCLUDED.module_id,
    object_id = EXCLUDED.object_id;


-- ============================================================
-- DONNÉES : utilisateurs par défaut (admin & user)
-- ============================================================

-- Mot de passe admin : admin123
-- Mot de passe user  : user123
-- Note : L'utilisateur de type admin n'a besoin que du rôle 'admin' (rôle admin = tous les droits).
INSERT INTO t_user_user (username, email, password_hash, nom, prenom, status_id) VALUES
('willbask',   'willbask@rilsport.com',   '$2y$10$gFxOZ4d6l22xXRdhD8dHYO2Wgpt9eqyh8TeUqy07MlrcEcp2O1Sd.', 'Baskerville', 'William', 1),
('ireneadler', 'ireneadler@rilsport.com', '$2y$10$0qcpqf8Kgzh4FraeKCwkpu1QODtrcbCIG9q8Rsnaw7ZHJc21hFsH2', 'Adler',       'Irene',   1)
ON CONFLICT (username) DO UPDATE SET
    email         = EXCLUDED.email,
    password_hash = EXCLUDED.password_hash,
    nom           = EXCLUDED.nom,
    prenom        = EXCLUDED.prenom,
    status_id     = EXCLUDED.status_id;

-- Attribution du rôle 'admin' à l'utilisateur admin
INSERT INTO t_user_user_role (user_id, role)
SELECT id, 'admin' FROM t_user_user WHERE username = 'willbask'
ON CONFLICT (user_id, role) DO NOTHING;

-- Attribution du rôle 'user' à l'utilisateur user
INSERT INTO t_user_user_role (user_id, role)
SELECT id, 'user' FROM t_user_user WHERE username = 'ireneadler'
ON CONFLICT (user_id, role) DO NOTHING;


-- ============================================================
-- DONNÉES : clés de traduction
-- ============================================================

INSERT INTO t_user_text_key (text_code) VALUES
('USER_USERNAME'),
('USER_LASTNAME'),
('USER_FIRSTNAME'),
('USER_PASSWORD'),
('USER_EMAIL'),
('USER_ROLE'),
('USER_ACTIONS'),
('USER_DATE_REG'),
('USER_USERS_LIST'),
('USER_ADD_USER_BTN'),
('USER_EDIT_USER_TITLE'),
('USER_LEAVE_BLANK_NO_CHANGE'),
('USER_BTN_SAVE'),
('USER_BTN_UPDATE'),
('USER_BTN_BACK'),
('USER_SUBMIT'),
('USER_NO_ACCOUNT'),
('USER_ALREADY_ACCOUNT'),
('USER_ERR_INVALID_CREDS'),
('USER_ERR_PASSWORDS_MISMATCH'),
('USER_ERR_PASSWORD_LENGTH'),
('USER_ERR_USER_EXISTS'),
('USER_SUCCESS_REGISTER'),
('USER_ERR_REGISTER_FAIL'),
('USER_ERR_UPDATE_FAIL'),
('USER_ERR_ADD_USER_EXISTS'),
('USER_MSG_USER_DELETED'),
('USER_ERR_DELETE_FAIL'),
('USER_ERR_DELETE_SELF'),
('USER_ERR_UNAUTHORIZED'),
('USER_ROLE_ADMIN'),
('USER_ROLE_USER'),
('USER_DELETE_USER_CONFIRM'),
('USER_STATUS_ACTIVE'),
('USER_STATUS_PENDING'),
('USER_MY_PROFILE'),
('USER_SUCCESS_UPDATE_USER'),
('ERR_ACCOUNT_NOT_VALIDATED'),
('LANG_LANGS_MGT'),
('MAIN'),
('STATUS'),
('INFO'),
('CREATED_AT'),
('CREATED_BY'),
('MODIFIED_AT'),
('MODIFIED_BY')
ON CONFLICT (text_code) DO NOTHING;


-- ============================================================
-- DONNÉES : traductions (fr / en / es)
-- ============================================================

INSERT INTO t_user_text (text_code, lang_code, text_label) VALUES

('USER_USERNAME',              'fr', 'Nom d''utilisateur'),
('USER_USERNAME',              'en', 'Username'),
('USER_USERNAME',              'es', 'Nombre de usuario'),

('USER_LASTNAME',              'fr', 'Nom'),
('USER_LASTNAME',              'en', 'Last Name'),
('USER_LASTNAME',              'es', 'Apellido'),

('USER_FIRSTNAME',             'fr', 'Prénom'),
('USER_FIRSTNAME',             'en', 'First Name'),
('USER_FIRSTNAME',             'es', 'Nombre'),

('USER_PASSWORD',              'fr', 'Mot de passe'),
('USER_PASSWORD',              'en', 'Password'),
('USER_PASSWORD',              'es', 'Contraseña'),

('USER_EMAIL',                 'fr', 'Email'),
('USER_EMAIL',                 'en', 'Email Address'),
('USER_EMAIL',                 'es', 'Correo electrónico'),

('USER_ROLE',                  'fr', 'Rôle'),
('USER_ROLE',                  'en', 'Role'),
('USER_ROLE',                  'es', 'Rol'),

('USER_ACTIONS',               'fr', 'Actions'),
('USER_ACTIONS',               'en', 'Actions'),
('USER_ACTIONS',               'es', 'Acciones'),

('USER_DATE_REG',              'fr', 'Date d''inscription'),
('USER_DATE_REG',              'en', 'Registration Date'),
('USER_DATE_REG',              'es', 'Fecha de registro'),

('USER_USERS_LIST',            'fr', 'Gestion des Utilisateurs'),
('USER_USERS_LIST',            'en', 'User Management'),
('USER_USERS_LIST',            'es', 'Gestión de Usuarios'),

('USER_ADD_USER_BTN',          'fr', 'Ajouter un utilisateur'),
('USER_ADD_USER_BTN',          'en', 'Add User'),
('USER_ADD_USER_BTN',          'es', 'Añadir Usuario'),

('USER_EDIT_USER_TITLE',       'fr', 'Modifier l''utilisateur'),
('USER_EDIT_USER_TITLE',       'en', 'Edit User'),
('USER_EDIT_USER_TITLE',       'es', 'Editar Usuario'),

('USER_BTN_SAVE',              'fr', 'Enregistrer'),
('USER_BTN_SAVE',              'en', 'Save'),
('USER_BTN_SAVE',              'es', 'Guardar'),

('USER_BTN_UPDATE',            'fr', 'Mettre à jour'),
('USER_BTN_UPDATE',            'en', 'Update'),
('USER_BTN_UPDATE',            'es', 'Actualizar'),

('USER_BTN_BACK',              'fr', 'Retour à la liste'),
('USER_BTN_BACK',              'en', 'Back to list'),
('USER_BTN_BACK',              'es', 'Volver a la lista'),

('USER_SUBMIT',                'fr', 'Valider'),
('USER_SUBMIT',                'en', 'Submit'),
('USER_SUBMIT',                'es', 'Enviar'),

('USER_LEAVE_BLANK_NO_CHANGE', 'fr', '(laisser vide pour ne pas changer)'),
('USER_LEAVE_BLANK_NO_CHANGE', 'en', '(leave blank to keep unchanged)'),
('USER_LEAVE_BLANK_NO_CHANGE', 'es', '(dejar en blanco para no cambiar)'),

('USER_NO_ACCOUNT',            'fr', 'Pas de compte ?'),
('USER_NO_ACCOUNT',            'en', 'No account?'),
('USER_NO_ACCOUNT',            'es', '¿No tienes cuenta?'),

('USER_ALREADY_ACCOUNT',       'fr', 'Déjà inscrit ?'),
('USER_ALREADY_ACCOUNT',       'en', 'Already registered?'),
('USER_ALREADY_ACCOUNT',       'es', '¿Ya registrado?'),

('USER_ERR_INVALID_CREDS',     'fr', 'Identifiants incorrects.'),
('USER_ERR_INVALID_CREDS',     'en', 'Invalid credentials.'),
('USER_ERR_INVALID_CREDS',     'es', 'Credenciales incorrectas.'),

('USER_ERR_PASSWORDS_MISMATCH','fr', 'Les mots de passe ne correspondent pas.'),
('USER_ERR_PASSWORDS_MISMATCH','en', 'Passwords do not match.'),
('USER_ERR_PASSWORDS_MISMATCH','es', 'Las contraseñas no coinciden.'),

('USER_ERR_PASSWORD_LENGTH',   'fr', 'Le mot de passe doit contenir au moins 6 caractères.'),
('USER_ERR_PASSWORD_LENGTH',   'en', 'The password must be at least 6 characters.'),
('USER_ERR_PASSWORD_LENGTH',   'es', 'La contraseña debe tener al menos 6 caracteres.'),

('USER_ERR_USER_EXISTS',       'fr', 'Ce nom d''utilisateur ou cet email est déjà utilisé.'),
('USER_ERR_USER_EXISTS',       'en', 'This username or email is already taken.'),
('USER_ERR_USER_EXISTS',       'es', 'Este nombre de usuario o correo electrónico ya está en uso.'),

('USER_SUCCESS_REGISTER',      'fr', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.'),
('USER_SUCCESS_REGISTER',      'en', 'Account created successfully! You can now log in.'),
('USER_SUCCESS_REGISTER',      'es', '¡Cuenta creada con éxito! Ahora puede iniciar sesión.'),

('USER_ERR_REGISTER_FAIL',     'fr', 'Une erreur est survenue lors de l''inscription.'),
('USER_ERR_REGISTER_FAIL',     'en', 'An error occurred during registration.'),
('USER_ERR_REGISTER_FAIL',     'es', 'Se produjo un error durante el registro.'),

('USER_ERR_UPDATE_FAIL',       'fr', 'Erreur lors de la modification.'),
('USER_ERR_UPDATE_FAIL',       'en', 'Error during the update.'),
('USER_ERR_UPDATE_FAIL',       'es', 'Error durante la actualización.'),

('USER_ERR_ADD_USER_EXISTS',   'fr', 'Erreur lors de l''ajout (nom d''utilisateur ou email déjà utilisé).'),
('USER_ERR_ADD_USER_EXISTS',   'en', 'Error adding user (username or email already in use).'),
('USER_ERR_ADD_USER_EXISTS',   'es', 'Error al agregar (nombre de usuario o correo ya en uso).'),

('USER_MSG_USER_DELETED',      'fr', 'Utilisateur supprimé.'),
('USER_MSG_USER_DELETED',      'en', 'User deleted.'),
('USER_MSG_USER_DELETED',      'es', 'Usuario eliminado.'),

('USER_ERR_DELETE_FAIL',       'fr', 'Erreur lors de la suppression.'),
('USER_ERR_DELETE_FAIL',       'en', 'Error during deletion.'),
('USER_ERR_DELETE_FAIL',       'es', 'Error durante la eliminación.'),

('USER_ERR_DELETE_SELF',       'fr', 'Vous ne pouvez pas supprimer votre propre compte.'),
('USER_ERR_DELETE_SELF',       'en', 'You cannot delete your own account.'),
('USER_ERR_DELETE_SELF',       'es', 'No puede eliminar su propia cuenta.'),

('USER_ERR_UNAUTHORIZED',      'fr', 'Accès non autorisé. Vous devez être administrateur.'),
('USER_ERR_UNAUTHORIZED',      'en', 'Unauthorized access. You must be an administrator.'),
('USER_ERR_UNAUTHORIZED',      'es', 'Acceso no autorizado. Debe ser administrador.'),

('USER_ROLE_ADMIN',            'fr', 'Administrateur'),
('USER_ROLE_ADMIN',            'en', 'Administrator'),
('USER_ROLE_ADMIN',            'es', 'Administrador'),

('USER_ROLE_USER',             'fr', 'Utilisateur'),
('USER_ROLE_USER',             'en', 'User'),
('USER_ROLE_USER',             'es', 'Usuario'),

('USER_DELETE_USER_CONFIRM',   'fr', 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?'),
('USER_DELETE_USER_CONFIRM',   'en', 'Are you sure you want to delete this user?'),
('USER_DELETE_USER_CONFIRM',   'es', '¿Está seguro de que desea eliminar este usuario?'),

('USER_STATUS_ACTIVE',         'fr', 'Actif'),
('USER_STATUS_ACTIVE',         'en', 'Active'),
('USER_STATUS_ACTIVE',         'es', 'Activo'),

('USER_STATUS_PENDING',        'fr', 'En attente'),
('USER_STATUS_PENDING',        'en', 'Pending'),
('USER_STATUS_PENDING',        'es', 'Pendiente'),

('USER_MY_PROFILE',            'fr', 'Mon profil'),
('USER_MY_PROFILE',            'en', 'My profile'),
('USER_MY_PROFILE',            'es', 'Mi perfil'),

('USER_SUCCESS_UPDATE_USER',   'fr', 'Utilisateur modifié avec succès.'),
('USER_SUCCESS_UPDATE_USER',   'en', 'User updated successfully.'),
('USER_SUCCESS_UPDATE_USER',   'es', 'Usuario actualizado con éxito.'),

('ERR_ACCOUNT_NOT_VALIDATED',  'fr', 'Le compte n''est pas encore validé.'),
('ERR_ACCOUNT_NOT_VALIDATED',  'en', 'The account has not been validated yet.'),
('ERR_ACCOUNT_NOT_VALIDATED',  'es', 'La cuenta aún no ha sido validada.'),

('LANG_LANGS_MGT',             'fr', 'Gestion des Langues'),
('LANG_LANGS_MGT',             'en', 'Language Management'),
('LANG_LANGS_MGT',             'es', 'Gestión de Idiomas'),

('MAIN',                       'fr', 'Accueil'),
('MAIN',                       'en', 'Home'),
('MAIN',                       'es', 'Inicio'),

('STATUS',                     'fr', 'Statut'),
('STATUS',                     'en', 'Status'),
('STATUS',                     'es', 'Estado'),

('INFO',                       'fr', 'Informations'),
('INFO',                       'en', 'Information'),
('INFO',                       'es', 'Información'),

('CREATED_AT',                 'fr', 'Créé le'),
('CREATED_AT',                 'en', 'Created at'),
('CREATED_AT',                 'es', 'Creado el'),

('CREATED_BY',                 'fr', 'Créé par'),
('CREATED_BY',                 'en', 'Created by'),
('CREATED_BY',                 'es', 'Creado por'),

('MODIFIED_AT',                'fr', 'Modifié le'),
('MODIFIED_AT',                'en', 'Modified at'),
('MODIFIED_AT',                'es', 'Modificado el'),

('MODIFIED_BY',                'fr', 'Modifié par'),
('MODIFIED_BY',                'en', 'Modified by'),
('MODIFIED_BY',                'es', 'Modificado por')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
