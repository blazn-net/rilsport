-- ============================================================
-- Module : main
-- ============================================================
-- Ordre d'exécution :
--   1. Schéma : t_main_lang
--   2. Schéma : t_main_text_key, t_main_text
--   3. Schéma : t_main_user_status, t_main_role, t_main_user, t_main_user_role
--   4. Données : t_main_lang
--   5. Données : t_main_user_status, t_main_role, t_main_user, t_main_user_role
--   6. Données : t_main_text_key, t_main_text (traductions objet user)
-- ============================================================


-- ============================================================
-- SCHÉMA : lang
-- ============================================================

CREATE TABLE IF NOT EXISTS t_main_lang (
    lang_code VARCHAR(5)  PRIMARY KEY,
    lang_name VARCHAR(50) NOT NULL,
    lang_flag VARCHAR(20) DEFAULT ''
);


-- ============================================================
-- SCHÉMA : text (tables de traduction partagées du module)
-- ============================================================

CREATE TABLE IF NOT EXISTS t_main_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_main_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_main_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_main_lang(lang_code)     ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : user
-- ============================================================

CREATE TABLE IF NOT EXISTS t_main_user_status (
    id          SERIAL       PRIMARY KEY,
    text_code   VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS t_main_role (
    role_id    VARCHAR(50)  PRIMARY KEY,
    text_code  VARCHAR(100) NOT NULL,
    badge_code VARCHAR(50)  NOT NULL
);

CREATE TABLE IF NOT EXISTS t_main_user (
    id            SERIAL       PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status_id     INT          NOT NULL DEFAULT 2,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by    INT          DEFAULT NULL,
    modified_at   TIMESTAMP    DEFAULT NULL,
    modified_by   INT          DEFAULT NULL,
    FOREIGN KEY (status_id)   REFERENCES t_main_user_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_main_user(id)        ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_main_user(id)        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_main_user_role (
    user_id INT         NOT NULL,
    role    VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES t_main_user(id)      ON DELETE CASCADE,
    FOREIGN KEY (role)    REFERENCES t_main_role(role_id) ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : lang
-- ============================================================

INSERT INTO t_main_lang (lang_code, lang_name, lang_flag) VALUES
('fr', 'Français', 'fi-fr'),
('en', 'English',  'fi-gb'),
('es', 'Español',  'fi-es')
ON CONFLICT (lang_code) DO NOTHING;


-- ============================================================
-- DONNÉES : user
-- ============================================================

INSERT INTO t_main_user_status (id, text_code) VALUES
(1, 'USER_STATUS_ACTIVE'),
(2, 'USER_STATUS_PENDING')
ON CONFLICT (id) DO NOTHING;

INSERT INTO t_main_role (role_id, text_code, badge_code) VALUES
('admin', 'USER_ROLE_ADMIN', 'alert'),
('user',  'USER_ROLE_USER',  'primary')
ON CONFLICT (role_id) DO NOTHING;

-- Administrateur par défaut (mot de passe : admin123)
-- Hash généré avec password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO t_main_user (username, email, password_hash, status_id)
VALUES ('admin', 'admin@rilsport.com', '$2y$10$O0FfK0uRY0D0X8A3u4fK/eFfTf3nZ9aH1P8U2/R5m7xYhL3/K2wI.', 1)
ON CONFLICT (username) DO NOTHING;

INSERT INTO t_main_user_role (user_id, role)
SELECT id, 'admin' FROM t_main_user WHERE username = 'admin'
ON CONFLICT (user_id, role) DO NOTHING;

INSERT INTO t_main_user_role (user_id, role)
SELECT id, 'user' FROM t_main_user WHERE username = 'admin'
ON CONFLICT (user_id, role) DO NOTHING;


-- ============================================================
-- DONNÉES : traductions (objet user)
-- ============================================================

INSERT INTO t_main_text_key (text_code) VALUES
('USER_USERNAME'), ('USER_PASSWORD'), ('USER_EMAIL'), ('USER_ROLE'), ('USER_ACTIONS'),
('USER_DATE_REG'), ('USER_USERS_LIST'), ('USER_ADD_USER_BTN'), ('USER_EDIT_USER_TITLE'), ('USER_LEAVE_BLANK_NO_CHANGE'),
('USER_BTN_SAVE'), ('USER_BTN_UPDATE'), ('USER_BTN_BACK'), ('USER_SUBMIT'),
('USER_NO_ACCOUNT'), ('USER_ALREADY_ACCOUNT'), ('USER_ERR_INVALID_CREDS'),
('USER_ERR_PASSWORDS_MISMATCH'), ('USER_ERR_PASSWORD_LENGTH'), ('USER_ERR_USER_EXISTS'),
('USER_SUCCESS_REGISTER'), ('USER_ERR_REGISTER_FAIL'), ('USER_ERR_UPDATE_FAIL'),
('USER_ERR_ADD_USER_EXISTS'), ('USER_MSG_USER_DELETED'), ('USER_ERR_DELETE_FAIL'),
('USER_ERR_DELETE_SELF'), ('USER_ERR_UNAUTHORIZED'), ('USER_ROLE_ADMIN'), ('USER_ROLE_USER'),
('USER_DELETE_USER_CONFIRM'), ('USER_STATUS_ACTIVE'), ('USER_STATUS_PENDING'),
('USER_MY_PROFILE'), ('USER_SUCCESS_UPDATE_USER')
ON CONFLICT (text_code) DO NOTHING;

INSERT INTO t_main_text (text_code, lang_code, text_label) VALUES

('USER_USERNAME',              'fr', 'Nom d''utilisateur'),
('USER_USERNAME',              'en', 'Username'),
('USER_USERNAME',              'es', 'Nombre de usuario'),
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
('USER_SUCCESS_UPDATE_USER',   'es', 'Usuario actualizado con éxito.')

ON CONFLICT (text_code, lang_code) DO UPDATE
    SET text_label = EXCLUDED.text_label;
