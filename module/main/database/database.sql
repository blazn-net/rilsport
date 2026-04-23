CREATE TABLE IF NOT EXISTS t_main_lang (
    lang_code VARCHAR(5) PRIMARY KEY,
    lang_name VARCHAR(50) NOT NULL,
    lang_flag VARCHAR(20) DEFAULT ''
);

INSERT INTO t_main_lang (lang_code, lang_name, lang_flag) VALUES 
('fr', 'Français', 'fi-fr'), 
('en', 'English', 'fi-gb'), 
('es', 'Español', 'fi-es')
ON CONFLICT DO NOTHING;
