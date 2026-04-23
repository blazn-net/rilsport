-- CREATE DATABASE rilsport_db;
-- \c rilsport_db;

DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE t_user_user (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE t_user_role (
    role_id VARCHAR(50) PRIMARY KEY,
    text_code VARCHAR(100) NOT NULL,
    badge_code VARCHAR(50) NOT NULL,
    FOREIGN KEY (text_code) REFERENCES t_user_text_key(text_code) ON DELETE RESTRICT
);

CREATE TABLE t_user_user_role (
    user_id INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES t_user_user(id) ON DELETE CASCADE,
    FOREIGN KEY (role) REFERENCES t_user_role(role_id) ON DELETE CASCADE
);

-- Insérer un administrateur par défaut (mot de passe : admin123)
-- Le hash a été généré avec password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO t_user_user (username, email, password_hash) 
VALUES ('admin', 'admin@rilsport.com', '$2y$10$O0FfK0uRY0D0X8A3u4fK/eFfTf3nZ9aH1P8U2/R5m7xYhL3/K2wI.');

-- Assigner les rôles
INSERT INTO t_user_role (role_id, text_code, badge_code) VALUES 
('admin', 'ROLE_ADMIN', 'alert'), 
('user', 'ROLE_USER', 'primary');

INSERT INTO t_user_user_role (user_id, role) VALUES (1, 'admin'), (1, 'user');
