-- ============================================================
-- Objet : user
-- Tables : t_main_user_status, t_main_role, t_main_user, t_main_user_role
-- Dépend de : lang.schema.sql (t_main_lang)
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
    FOREIGN KEY (user_id) REFERENCES t_main_user(id)    ON DELETE CASCADE,
    FOREIGN KEY (role)    REFERENCES t_main_role(role_id) ON DELETE CASCADE
);
