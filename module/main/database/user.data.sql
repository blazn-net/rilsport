-- ============================================================
-- Données : user
-- Inclut : données métier + traductions (t_main_text_key, t_main_text)
-- Dépend de : text.schema.sql, user.schema.sql, lang.data.sql
-- ============================================================

-- ------------------------------------------------------------
-- Statuts utilisateur
-- ------------------------------------------------------------

INSERT INTO t_main_user_status (id, text_code) VALUES
(1, 'STATUS_ACTIVE'),
(2, 'STATUS_PENDING')
ON CONFLICT (id) DO NOTHING;

-- ------------------------------------------------------------
-- Rôles
-- ------------------------------------------------------------

INSERT INTO t_main_role (role_id, text_code, badge_code) VALUES
('admin', 'ROLE_ADMIN', 'alert'),
('user',  'ROLE_USER',  'primary')
ON CONFLICT (role_id) DO NOTHING;

-- ------------------------------------------------------------
-- Administrateur par défaut (mot de passe : admin123)
-- Hash généré avec password_hash('admin123', PASSWORD_DEFAULT)
-- ------------------------------------------------------------

INSERT INTO t_main_user (username, email, password_hash, status_id)
VALUES ('admin', 'admin@rilsport.com', '$2y$10$O0FfK0uRY0D0X8A3u4fK/eFfTf3nZ9aH1P8U2/R5m7xYhL3/K2wI.', 1)
ON CONFLICT (username) DO NOTHING;

INSERT INTO t_main_user_role (user_id, role)
SELECT id, 'admin' FROM t_main_user WHERE username = 'admin'
ON CONFLICT DO NOTHING;

INSERT INTO t_main_user_role (user_id, role)
SELECT id, 'user' FROM t_main_user WHERE username = 'admin'
ON CONFLICT DO NOTHING;

-- ------------------------------------------------------------
-- Clés de traduction (objet user)
-- ------------------------------------------------------------

INSERT INTO t_main_text_key (text_code) VALUES
('USERNAME'), ('PASSWORD'), ('EMAIL'), ('ROLE'), ('ACTIONS'),
('DATE_REG'), ('USERS_LIST'), ('ADD_USER_BTN'), ('EDIT_USER_TITLE'), ('LEAVE_BLANK_NO_CHANGE'),
('BTN_SAVE'), ('BTN_UPDATE'), ('BTN_BACK'), ('SUBMIT'),
('NO_ACCOUNT'), ('ALREADY_ACCOUNT'), ('ERR_INVALID_CREDS'),
('ERR_PASSWORDS_MISMATCH'), ('ERR_PASSWORD_LENGTH'), ('ERR_USER_EXISTS'),
('SUCCESS_REGISTER'), ('ERR_REGISTER_FAIL'), ('ERR_UPDATE_FAIL'),
('ERR_ADD_USER_EXISTS'), ('MSG_USER_DELETED'), ('ERR_DELETE_FAIL'),
('ERR_DELETE_SELF'), ('ERR_UNAUTHORIZED'), ('ROLE_ADMIN'), ('ROLE_USER'),
('DELETE_USER_CONFIRM'), ('STATUS_ACTIVE'), ('STATUS_PENDING'),
('MY_PROFILE'), ('SUCCESS_UPDATE_USER')
ON CONFLICT (text_code) DO NOTHING;

-- ------------------------------------------------------------
-- Traductions (objet user)
-- ------------------------------------------------------------

INSERT INTO t_main_text (text_code, lang_code, text_label) VALUES

('USERNAME',              'fr', 'Nom d''utilisateur'),
('USERNAME',              'en', 'Username'),
('USERNAME',              'es', 'Nombre de usuario'),
('PASSWORD',              'fr', 'Mot de passe'),
('PASSWORD',              'en', 'Password'),
('PASSWORD',              'es', 'Contraseña'),
('EMAIL',                 'fr', 'Email'),
('EMAIL',                 'en', 'Email Address'),
('EMAIL',                 'es', 'Correo electrónico'),
('ROLE',                  'fr', 'Rôle'),
('ROLE',                  'en', 'Role'),
('ROLE',                  'es', 'Rol'),
('ACTIONS',               'fr', 'Actions'),
('ACTIONS',               'en', 'Actions'),
('ACTIONS',               'es', 'Acciones'),
('DATE_REG',              'fr', 'Date d''inscription'),
('DATE_REG',              'en', 'Registration Date'),
('DATE_REG',              'es', 'Fecha de registro'),
('USERS_LIST',            'fr', 'Gestion des Utilisateurs'),
('USERS_LIST',            'en', 'User Management'),
('USERS_LIST',            'es', 'Gestión de Usuarios'),
('ADD_USER_BTN',          'fr', 'Ajouter un utilisateur'),
('ADD_USER_BTN',          'en', 'Add User'),
('ADD_USER_BTN',          'es', 'Añadir Usuario'),
('EDIT_USER_TITLE',       'fr', 'Modifier l''utilisateur'),
('EDIT_USER_TITLE',       'en', 'Edit User'),
('EDIT_USER_TITLE',       'es', 'Editar Usuario'),
('BTN_SAVE',              'fr', 'Enregistrer'),
('BTN_SAVE',              'en', 'Save'),
('BTN_SAVE',              'es', 'Guardar'),
('BTN_UPDATE',            'fr', 'Mettre à jour'),
('BTN_UPDATE',            'en', 'Update'),
('BTN_UPDATE',            'es', 'Actualizar'),
('BTN_BACK',              'fr', 'Retour à la liste'),
('BTN_BACK',              'en', 'Back to list'),
('BTN_BACK',              'es', 'Volver a la lista'),
('SUBMIT',                'fr', 'Valider'),
('SUBMIT',                'en', 'Submit'),
('SUBMIT',                'es', 'Enviar'),
('LEAVE_BLANK_NO_CHANGE', 'fr', '(laisser vide pour ne pas changer)'),
('LEAVE_BLANK_NO_CHANGE', 'en', '(leave blank to keep unchanged)'),
('LEAVE_BLANK_NO_CHANGE', 'es', '(dejar en blanco para no cambiar)'),
('NO_ACCOUNT',            'fr', 'Pas de compte ?'),
('NO_ACCOUNT',            'en', 'No account?'),
('NO_ACCOUNT',            'es', '¿No tienes cuenta?'),
('ALREADY_ACCOUNT',       'fr', 'Déjà inscrit ?'),
('ALREADY_ACCOUNT',       'en', 'Already registered?'),
('ALREADY_ACCOUNT',       'es', '¿Ya registrado?'),
('ERR_INVALID_CREDS',     'fr', 'Identifiants incorrects.'),
('ERR_INVALID_CREDS',     'en', 'Invalid credentials.'),
('ERR_INVALID_CREDS',     'es', 'Credenciales incorrectas.'),
('ERR_PASSWORDS_MISMATCH','fr', 'Les mots de passe ne correspondent pas.'),
('ERR_PASSWORDS_MISMATCH','en', 'Passwords do not match.'),
('ERR_PASSWORDS_MISMATCH','es', 'Las contraseñas no coinciden.'),
('ERR_PASSWORD_LENGTH',   'fr', 'Le mot de passe doit contenir au moins 6 caractères.'),
('ERR_PASSWORD_LENGTH',   'en', 'The password must be at least 6 characters.'),
('ERR_PASSWORD_LENGTH',   'es', 'La contraseña debe tener al menos 6 caracteres.'),
('ERR_USER_EXISTS',       'fr', 'Ce nom d''utilisateur ou cet email est déjà utilisé.'),
('ERR_USER_EXISTS',       'en', 'This username or email is already taken.'),
('ERR_USER_EXISTS',       'es', 'Este nombre de usuario o correo electrónico ya está en uso.'),
('SUCCESS_REGISTER',      'fr', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.'),
('SUCCESS_REGISTER',      'en', 'Account created successfully! You can now log in.'),
('SUCCESS_REGISTER',      'es', '¡Cuenta creada con éxito! Ahora puede iniciar sesión.'),
('ERR_REGISTER_FAIL',     'fr', 'Une erreur est survenue lors de l''inscription.'),
('ERR_REGISTER_FAIL',     'en', 'An error occurred during registration.'),
('ERR_REGISTER_FAIL',     'es', 'Se produjo un error durante el registro.'),
('ERR_UPDATE_FAIL',       'fr', 'Erreur lors de la modification.'),
('ERR_UPDATE_FAIL',       'en', 'Error during the update.'),
('ERR_UPDATE_FAIL',       'es', 'Error durante la actualización.'),
('ERR_ADD_USER_EXISTS',   'fr', 'Erreur lors de l''ajout (nom d''utilisateur ou email déjà utilisé).'),
('ERR_ADD_USER_EXISTS',   'en', 'Error adding user (username or email already in use).'),
('ERR_ADD_USER_EXISTS',   'es', 'Error al agregar (nombre de usuario o correo ya en uso).'),
('MSG_USER_DELETED',      'fr', 'Utilisateur supprimé.'),
('MSG_USER_DELETED',      'en', 'User deleted.'),
('MSG_USER_DELETED',      'es', 'Usuario eliminado.'),
('ERR_DELETE_FAIL',       'fr', 'Erreur lors de la suppression.'),
('ERR_DELETE_FAIL',       'en', 'Error during deletion.'),
('ERR_DELETE_FAIL',       'es', 'Error durante la eliminación.'),
('ERR_DELETE_SELF',       'fr', 'Vous ne pouvez pas supprimer votre propre compte.'),
('ERR_DELETE_SELF',       'en', 'You cannot delete your own account.'),
('ERR_DELETE_SELF',       'es', 'No puede eliminar su propia cuenta.'),
('ERR_UNAUTHORIZED',      'fr', 'Accès non autorisé. Vous devez être administrateur.'),
('ERR_UNAUTHORIZED',      'en', 'Unauthorized access. You must be an administrator.'),
('ERR_UNAUTHORIZED',      'es', 'Acceso no autorizado. Debe ser administrador.'),
('ROLE_ADMIN',            'fr', 'Administrateur'),
('ROLE_ADMIN',            'en', 'Administrator'),
('ROLE_ADMIN',            'es', 'Administrador'),
('ROLE_USER',             'fr', 'Utilisateur'),
('ROLE_USER',             'en', 'User'),
('ROLE_USER',             'es', 'Usuario'),
('DELETE_USER_CONFIRM',   'fr', 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?'),
('DELETE_USER_CONFIRM',   'en', 'Are you sure you want to delete this user?'),
('DELETE_USER_CONFIRM',   'es', '¿Está seguro de que desea eliminar este usuario?'),
('STATUS_ACTIVE',         'fr', 'Actif'),
('STATUS_ACTIVE',         'en', 'Active'),
('STATUS_ACTIVE',         'es', 'Activo'),
('STATUS_PENDING',        'fr', 'En attente'),
('STATUS_PENDING',        'en', 'Pending'),
('STATUS_PENDING',        'es', 'Pendiente'),
('MY_PROFILE',            'fr', 'Mon profil'),
('MY_PROFILE',            'en', 'My profile'),
('MY_PROFILE',            'es', 'Mi perfil'),
('SUCCESS_UPDATE_USER',   'fr', 'Utilisateur modifié avec succès.'),
('SUCCESS_UPDATE_USER',   'en', 'User updated successfully.'),
('SUCCESS_UPDATE_USER',   'es', 'Usuario actualizado con éxito.')

ON CONFLICT (text_code, lang_code) DO NOTHING;
