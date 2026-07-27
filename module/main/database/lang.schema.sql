-- ============================================================
-- Objet : lang
-- Tables : t_main_lang
-- ============================================================

CREATE TABLE IF NOT EXISTS t_main_lang (
    lang_code VARCHAR(5)  PRIMARY KEY,
    lang_name VARCHAR(50) NOT NULL,
    lang_flag VARCHAR(20) DEFAULT ''
);
