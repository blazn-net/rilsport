-- ============================================================
-- Tables de traduction du module main
-- Partagées par tous les objets du module
-- Tables : t_main_text_key, t_main_text
-- Dépend de : lang.schema.sql (t_main_lang)
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
