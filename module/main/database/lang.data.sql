-- ============================================================
-- Données : lang
-- ============================================================

INSERT INTO t_main_lang (lang_code, lang_name, lang_flag) VALUES 
('fr', 'Français', 'fi-fr'), 
('en', 'English',  'fi-gb'), 
('es', 'Español',  'fi-es')
ON CONFLICT DO NOTHING;
