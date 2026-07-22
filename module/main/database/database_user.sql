-- ============================================================
-- Objet : user
-- Tables : t_user_user, t_user_role, t_user_user_role,
--          t_user_user_status
-- ============================================================

CREATE TABLE IF NOT EXISTS t_user_user_status (
    id          SERIAL PRIMARY KEY,
    text_code   VARCHAR(100) NOT NULL
);

INSERT INTO t_user_user_status (id, text_code) VALUES
(1, 'STATUS_ACTIVE'),
(2, 'STATUS_PENDING')
ON CONFLICT (id) DO NOTHING;

CREATE TABLE IF NOT EXISTS t_user_role (
    role_id    VARCHAR(50)  PRIMARY KEY,
    text_code  VARCHAR(100) NOT NULL,
    badge_code VARCHAR(50)  NOT NULL
);

INSERT INTO t_user_role (role_id, text_code, badge_code) VALUES
('admin', 'ROLE_ADMIN', 'alert'),
('user',  'ROLE_USER',  'primary')
ON CONFLICT (role_id) DO NOTHING;

CREATE TABLE IF NOT EXISTS t_user_user (
    id            SERIAL       PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status_id     INT          NOT NULL DEFAULT 2,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by    INT          DEFAULT NULL,
    modified_at   TIMESTAMP    DEFAULT NULL,
    modified_by   INT          DEFAULT NULL,
    FOREIGN KEY (status_id)   REFERENCES t_user_user_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id) ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS t_user_user_role (
    user_id INT         NOT NULL,
    role    VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, role),
    FOREIGN KEY (user_id) REFERENCES t_user_user(id) ON DELETE CASCADE,
    FOREIGN KEY (role)    REFERENCES t_user_role(role_id) ON DELETE CASCADE
);

-- Administrateur par défaut (mot de passe : admin123)
-- Hash généré avec password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO t_user_user (username, email, password_hash, status_id)
VALUES ('admin', 'admin@rilsport.com', '$2y$10$O0FfK0uRY0D0X8A3u4fK/eFfTf3nZ9aH1P8U2/R5m7xYhL3/K2wI.', 1)
ON CONFLICT (username) DO NOTHING;

INSERT INTO t_user_user_role (user_id, role)
SELECT id, 'admin' FROM t_user_user WHERE username = 'admin'
ON CONFLICT DO NOTHING;

INSERT INTO t_user_user_role (user_id, role)
SELECT id, 'user' FROM t_user_user WHERE username = 'admin'
ON CONFLICT DO NOTHING;
