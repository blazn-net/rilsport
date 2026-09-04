-- ============================================================
-- Module : zone
-- ============================================================
-- Ordre d'exécution :
--   1. Enregistrement module dans t_system_module
--   2. Schéma : t_zone_type, t_zone_zone, t_zone_zone_i18n, t_zone_city
--   3. Schéma : t_zone_text_key, t_zone_text
--   4. Données : types, Monde, 7 continents (GeoNames), 193 pays membres ONU
--   5. Données : traductions UI (ZONE_)
--
-- Dépendances : lang.sql, system.sql, user.sql
--
-- Stratégie :
--   - Monde et Continents : pré-seedés avec geonames_id, children_loaded = TRUE
--   - Continents : Afrique, Amérique du Nord, Amérique du Sud, Antarctique, Asie, Europe, Océanie (GeoNames)
--   - Pays (193 membres ONU) : pré-seedés avec geonames_id, children_loaded = FALSE
--   - Admin1/Admin2 : lazy-loaded depuis GeoNames au premier clic utilisateur
--   - Chaque zone possède désormais un geonames_id officiel GeoNames
-- ============================================================

SELECT setval('t_system_module_id_seq', COALESCE((SELECT MAX(id) FROM t_system_module), 1));


-- ============================================================
-- ENREGISTREMENT MODULE DANS SYSTEM
-- ============================================================

INSERT INTO t_system_module (code, is_active, is_system) VALUES
('zone', TRUE, TRUE)
ON CONFLICT (code) DO UPDATE SET
    is_active = EXCLUDED.is_active,
    is_system = EXCLUDED.is_system;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'fr', 'Zone', 'Gestion des zones géographiques (continents, pays, régions)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'en', 'Zone', 'Geographic zones management (continents, countries, regions)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;

INSERT INTO t_system_module_i18n (module_id, lang_code, name, description)
SELECT id, 'es', 'Zona', 'Gestión de zonas geográficas (continentes, países, regiones)'
FROM t_system_module WHERE code = 'zone'
ON CONFLICT (module_id, lang_code) DO UPDATE SET name = EXCLUDED.name, description = EXCLUDED.description;


-- ============================================================
-- SCHÉMA : t_zone_type
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_type (
    code  VARCHAR(20) PRIMARY KEY,
    level SMALLINT    NOT NULL,
    name  VARCHAR(100) NOT NULL
);


-- ============================================================
-- SCHÉMA : t_zone_zone
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_zone (
    id              SERIAL       PRIMARY KEY,
    geonames_id     INT          DEFAULT NULL,
    type_code       VARCHAR(20)  NOT NULL,
    parent_id       INT          DEFAULT NULL,
    name_default    VARCHAR(200) NOT NULL,
    country_code    VARCHAR(2)   DEFAULT NULL,
    children_loaded BOOLEAN      NOT NULL DEFAULT FALSE,
    status_id       INT          NOT NULL DEFAULT 1,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by      INT          DEFAULT NULL,
    modified_at     TIMESTAMP    DEFAULT NULL,
    modified_by     INT          DEFAULT NULL,
    FOREIGN KEY (type_code)   REFERENCES t_zone_type(code)     ON DELETE RESTRICT,
    FOREIGN KEY (parent_id)   REFERENCES t_zone_zone(id)       ON DELETE SET NULL,
    FOREIGN KEY (created_by)  REFERENCES t_user_user(id)       ON DELETE SET NULL,
    FOREIGN KEY (modified_by) REFERENCES t_user_user(id)       ON DELETE SET NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_zone_geonames_id
    ON t_zone_zone(geonames_id);

CREATE INDEX IF NOT EXISTS idx_zone_zone_parent   ON t_zone_zone(parent_id);
CREATE INDEX IF NOT EXISTS idx_zone_zone_type     ON t_zone_zone(type_code);
CREATE INDEX IF NOT EXISTS idx_zone_zone_country  ON t_zone_zone(country_code);


-- ============================================================
-- SCHÉMA : t_zone_zone_i18n
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_zone_i18n (
    zone_id   INT         NOT NULL,
    lang_code VARCHAR(5)  NOT NULL,
    name      VARCHAR(200) NOT NULL,
    PRIMARY KEY (zone_id, lang_code),
    FOREIGN KEY (zone_id)   REFERENCES t_zone_zone(id)        ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code) ON DELETE CASCADE
);


-- ============================================================
-- SCHÉMA : t_zone_city
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_city (
    id           SERIAL       PRIMARY KEY,
    geonames_id  INT          DEFAULT NULL UNIQUE,
    zone_id      INT          DEFAULT NULL,
    name_default VARCHAR(200) NOT NULL,
    country_code VARCHAR(2)   DEFAULT NULL,
    status_id    INT          NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    created_by   INT          DEFAULT NULL,
    FOREIGN KEY (zone_id)    REFERENCES t_zone_zone(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES t_user_user(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_zone_city_zone    ON t_zone_city(zone_id);
CREATE INDEX IF NOT EXISTS idx_zone_city_country ON t_zone_city(country_code);


-- ============================================================
-- SCHÉMA : traductions UI
-- ============================================================

CREATE TABLE IF NOT EXISTS t_zone_text_key (
    text_code VARCHAR(100) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS t_zone_text (
    text_code  VARCHAR(100) NOT NULL,
    lang_code  VARCHAR(5)   NOT NULL,
    text_label TEXT         NOT NULL,
    PRIMARY KEY (text_code, lang_code),
    FOREIGN KEY (text_code) REFERENCES t_zone_text_key(text_code) ON DELETE CASCADE,
    FOREIGN KEY (lang_code) REFERENCES t_lang_lang(lang_code)     ON DELETE CASCADE
);


-- ============================================================
-- DONNÉES : Types de zones
-- ============================================================

INSERT INTO t_zone_type (code, level, name) VALUES
('world',     0, 'Monde'),
('continent', 1, 'Continent'),
('country',   2, 'Pays'),
('admin1',    3, 'Région / État / Province'),
('admin2',    4, 'Département / Comté / District')
ON CONFLICT (code) DO UPDATE SET level = EXCLUDED.level, name = EXCLUDED.name;


-- ============================================================
-- DONNÉES : Monde (root)
-- children_loaded = TRUE car les continents sont pré-seedés
-- ============================================================

INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, children_loaded)
VALUES (6295630, 'world', NULL, 'Earth', TRUE)
ON CONFLICT (geonames_id) DO UPDATE SET
    children_loaded = TRUE,
    name_default    = EXCLUDED.name_default;


-- ============================================================
-- DONNÉES : Continents (GeoNames IDs officiels)
-- children_loaded = TRUE car les pays sont pré-seedés
-- ============================================================

DELETE FROM t_zone_zone WHERE type_code = 'continent' AND geonames_id IS NULL;

INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, children_loaded)
SELECT c.geonames_id, 'continent', w.id, c.name_default, TRUE
FROM t_zone_zone w,
(VALUES
    (6255146, 'Africa'),
    (6255149, 'North America'),
    (6255150, 'South America'),
    (6255147, 'Asia'),
    (6255148, 'Europe'),
    (6255151, 'Oceania'),
    (6255152, 'Antarctica')
) AS c(geonames_id, name_default)
WHERE w.type_code = 'world'
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code       = EXCLUDED.type_code,
    parent_id       = EXCLUDED.parent_id,
    name_default    = EXCLUDED.name_default,
    children_loaded = TRUE;


-- ============================================================
-- DONNÉES : Pays membres ONU (193)
-- Groupés par continent — children_loaded = FALSE (admin1 lazy)
-- ============================================================

-- ---- AFRIQUE (54) -----------------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (2589581, 'Algeria',                            'DZ'),
    (3351879, 'Angola',                             'AO'),
    (2395170, 'Benin',                              'BJ'),
    (933860,  'Botswana',                           'BW'),
    (2361809, 'Burkina Faso',                       'BF'),
    (433561,  'Burundi',                            'BI'),
    (3374766, 'Cabo Verde',                         'CV'),
    (2233387, 'Cameroon',                           'CM'),
    (239880,  'Central African Republic',           'CF'),
    (2434508, 'Chad',                               'TD'),
    (921929,  'Comoros',                            'KM'),
    (2260494, 'Republic of the Congo',              'CG'),
    (203312,  'Democratic Republic of the Congo',   'CD'),
    (223816,  'Djibouti',                           'DJ'),
    (357994,  'Egypt',                              'EG'),
    (2309096, 'Equatorial Guinea',                  'GQ'),
    (338010,  'Eritrea',                            'ER'),
    (934841,  'Eswatini',                           'SZ'),
    (337996,  'Ethiopia',                           'ET'),
    (2400553, 'Gabon',                              'GA'),
    (2413451, 'Gambia',                             'GM'),
    (2300660, 'Ghana',                              'GH'),
    (2420477, 'Guinea',                             'GN'),
    (2372248, 'Guinea-Bissau',                      'GW'),
    (2287781, 'Ivory Coast',                        'CI'),
    (192950,  'Kenya',                              'KE'),
    (932692,  'Lesotho',                            'LS'),
    (2275384, 'Liberia',                            'LR'),
    (2215636, 'Libya',                              'LY'),
    (1062947, 'Madagascar',                         'MG'),
    (927384,  'Malawi',                             'MW'),
    (2453866, 'Mali',                               'ML'),
    (2378080, 'Mauritania',                         'MR'),
    (934292,  'Mauritius',                          'MU'),
    (2542007, 'Morocco',                            'MA'),
    (1036973, 'Mozambique',                         'MZ'),
    (3355338, 'Namibia',                            'NA'),
    (2440476, 'Niger',                              'NE'),
    (2328926, 'Nigeria',                            'NG'),
    (49518,   'Rwanda',                             'RW'),
    (2410758, 'São Tomé and Príncipe',              'ST'),
    (2245662, 'Senegal',                            'SN'),
    (241170,  'Seychelles',                         'SC'),
    (2403846, 'Sierra Leone',                       'SL'),
    (51537,   'Somalia',                            'SO'),
    (953987,  'South Africa',                       'ZA'),
    (7909807, 'South Sudan',                        'SS'),
    (366755,  'Sudan',                              'SD'),
    (149590,  'Tanzania',                           'TZ'),
    (2363686, 'Togo',                               'TG'),
    (2464461, 'Tunisia',                            'TN'),
    (226074,  'Uganda',                             'UG'),
    (895949,  'Zambia',                             'ZM'),
    (878675,  'Zimbabwe',                           'ZW')
) AS d(geonames_id, name_en, iso2)
WHERE c.type_code = 'continent' AND c.name_default = 'Africa'
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;

-- ---- AMÉRIQUE DU NORD (23) --------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (3576396, 'Antigua and Barbuda',                      'AG'),
    (3572887, 'Bahamas',                                  'BS'),
    (3374084, 'Barbados',                                 'BB'),
    (3582678, 'Belize',                                   'BZ'),
    (6251999, 'Canada',                                   'CA'),
    (3624060, 'Costa Rica',                               'CR'),
    (3562981, 'Cuba',                                     'CU'),
    (3575830, 'Dominica',                                 'DM'),
    (3508796, 'Dominican Republic',                       'DO'),
    (3585968, 'El Salvador',                              'SV'),
    (3580239, 'Grenada',                                  'GD'),
    (3595528, 'Guatemala',                                'GT'),
    (3723988, 'Haiti',                                    'HT'),
    (3608932, 'Honduras',                                 'HN'),
    (3489940, 'Jamaica',                                  'JM'),
    (3996063, 'Mexico',                                   'MX'),
    (3617476, 'Nicaragua',                                'NI'),
    (3703430, 'Panama',                                   'PA'),
    (3575174, 'Saint Kitts and Nevis',                    'KN'),
    (3576468, 'Saint Lucia',                              'LC'),
    (3577815, 'Saint Vincent and the Grenadines',         'VC'),
    (3573591, 'Trinidad and Tobago',                      'TT'),
    (6252001, 'United States',                            'US')
) AS d(geonames_id, name_en, iso2)
WHERE c.geonames_id = 6255149
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;

-- ---- AMÉRIQUE DU SUD (12) ---------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (3865483, 'Argentina',                                'AR'),
    (3923057, 'Bolivia',                                  'BO'),
    (3469034, 'Brazil',                                   'BR'),
    (3895114, 'Chile',                                    'CL'),
    (3686110, 'Colombia',                                 'CO'),
    (3658394, 'Ecuador',                                  'EC'),
    (3378535, 'Guyana',                                   'GY'),
    (3437598, 'Paraguay',                                 'PY'),
    (3932488, 'Peru',                                     'PE'),
    (3382998, 'Suriname',                                 'SR'),
    (3439705, 'Uruguay',                                  'UY'),
    (3625428, 'Venezuela',                                'VE')
) AS d(geonames_id, name_en, iso2)
WHERE c.geonames_id = 6255150
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;

-- Nettoyer l'ancien continent "America" virtuel s'il existait
DELETE FROM t_zone_zone WHERE type_code = 'continent' AND geonames_id IS NULL;

-- ---- ASIE (47) --------------------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (1149361, 'Afghanistan',          'AF'),
    (174982,  'Armenia',              'AM'),
    (587116,  'Azerbaijan',           'AZ'),
    (290291,  'Bahrain',              'BH'),
    (1210997, 'Bangladesh',           'BD'),
    (1252634, 'Bhutan',               'BT'),
    (1820814, 'Brunei',               'BN'),
    (1831722, 'Cambodia',             'KH'),
    (1814991, 'China',                'CN'),
    (146669,  'Cyprus',               'CY'),
    (614540,  'Georgia',              'GE'),
    (1269750, 'India',                'IN'),
    (1643084, 'Indonesia',            'ID'),
    (130758,  'Iran',                 'IR'),
    (99237,   'Iraq',                 'IQ'),
    (294640,  'Israel',               'IL'),
    (1861060, 'Japan',                'JP'),
    (248816,  'Jordan',               'JO'),
    (1522867, 'Kazakhstan',           'KZ'),
    (285570,  'Kuwait',               'KW'),
    (1527747, 'Kyrgyzstan',           'KG'),
    (1655842, 'Laos',                 'LA'),
    (272103,  'Lebanon',              'LB'),
    (1733045, 'Malaysia',             'MY'),
    (1282028, 'Maldives',             'MV'),
    (2029969, 'Mongolia',             'MN'),
    (1327865, 'Myanmar',              'MM'),
    (1282988, 'Nepal',                'NP'),
    (1873107, 'North Korea',          'KP'),
    (286963,  'Oman',                 'OM'),
    (1168579, 'Pakistan',             'PK'),
    (1694008, 'Philippines',          'PH'),
    (289688,  'Qatar',                'QA'),
    (102358,  'Saudi Arabia',         'SA'),
    (1880251, 'Singapore',            'SG'),
    (1835841, 'South Korea',          'KR'),
    (1227603, 'Sri Lanka',            'LK'),
    (163843,  'Syria',                'SY'),
    (1220409, 'Tajikistan',           'TJ'),
    (1605651, 'Thailand',             'TH'),
    (1966436, 'Timor-Leste',          'TL'),
    (298795,  'Turkey',               'TR'),
    (1220060, 'Turkmenistan',         'TM'),
    (290557,  'United Arab Emirates', 'AE'),
    (1512440, 'Uzbekistan',           'UZ'),
    (1562822, 'Vietnam',              'VN'),
    (69543,   'Yemen',                'YE')
) AS d(geonames_id, name_en, iso2)
WHERE c.type_code = 'continent' AND c.name_default = 'Asia'
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;

-- ---- EUROPE (43) ------------------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (783754,  'Albania',                'AL'),
    (3041565, 'Andorra',                'AD'),
    (2782113, 'Austria',                'AT'),
    (630336,  'Belarus',                'BY'),
    (2802361, 'Belgium',                'BE'),
    (3277605, 'Bosnia and Herzegovina', 'BA'),
    (732800,  'Bulgaria',               'BG'),
    (3202326, 'Croatia',                'HR'),
    (3077311, 'Czech Republic',         'CZ'),
    (2623032, 'Denmark',                'DK'),
    (453733,  'Estonia',                'EE'),
    (660013,  'Finland',                'FI'),
    (3017382, 'France',                 'FR'),
    (2921044, 'Germany',                'DE'),
    (390903,  'Greece',                 'GR'),
    (719819,  'Hungary',                'HU'),
    (2629691, 'Iceland',                'IS'),
    (2963597, 'Ireland',                'IE'),
    (3175395, 'Italy',                  'IT'),
    (458258,  'Latvia',                 'LV'),
    (3042058, 'Liechtenstein',          'LI'),
    (597427,  'Lithuania',              'LT'),
    (2960313, 'Luxembourg',             'LU'),
    (2562770, 'Malta',                  'MT'),
    (617790,  'Moldova',                'MD'),
    (2993457, 'Monaco',                 'MC'),
    (3194884, 'Montenegro',             'ME'),
    (2750405, 'Netherlands',            'NL'),
    (718075,  'North Macedonia',        'MK'),
    (607072,  'Norway',                 'NO'),
    (798544,  'Poland',                 'PL'),
    (2264397, 'Portugal',               'PT'),
    (798549,  'Romania',                'RO'),
    (2017370, 'Russia',                 'RU'),
    (3168068, 'San Marino',             'SM'),
    (6290252, 'Serbia',                 'RS'),
    (3057568, 'Slovakia',               'SK'),
    (3190538, 'Slovenia',               'SI'),
    (2510769, 'Spain',                  'ES'),
    (2661886, 'Sweden',                 'SE'),
    (2658434, 'Switzerland',            'CH'),
    (690791,  'Ukraine',                'UA'),
    (2635167, 'United Kingdom',         'GB')
) AS d(geonames_id, name_en, iso2)
WHERE c.type_code = 'continent' AND c.name_default = 'Europe'
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;

-- ---- OCÉANIE (14) -----------------------------------------
INSERT INTO t_zone_zone (geonames_id, type_code, parent_id, name_default, country_code, children_loaded)
SELECT d.geonames_id, 'country', c.id, d.name_en, d.iso2, FALSE
FROM t_zone_zone c,
(VALUES
    (2077456, 'Australia',        'AU'),
    (2205218, 'Fiji',             'FJ'),
    (4030945, 'Kiribati',         'KI'),
    (2080185, 'Marshall Islands', 'MH'),
    (2081918, 'Micronesia',       'FM'),
    (2110425, 'Nauru',            'NR'),
    (2186224, 'New Zealand',      'NZ'),
    (1559582, 'Palau',            'PW'),
    (2088628, 'Papua New Guinea', 'PG'),
    (4034894, 'Samoa',            'WS'),
    (2103350, 'Solomon Islands',  'SB'),
    (4032283, 'Tonga',            'TO'),
    (2110297, 'Tuvalu',           'TV'),
    (2134431, 'Vanuatu',          'VU')
) AS d(geonames_id, name_en, iso2)
WHERE c.type_code = 'continent' AND c.name_default = 'Oceania'
ON CONFLICT (geonames_id) DO UPDATE SET
    type_code    = EXCLUDED.type_code,
    parent_id    = EXCLUDED.parent_id,
    name_default = EXCLUDED.name_default,
    country_code = EXCLUDED.country_code;


-- ============================================================
-- TRADUCTIONS : Monde, Continents, Pays (via geonames_id)
-- Un seul INSERT bulk pour toutes les zones avec geonames_id
-- ============================================================

INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
SELECT z.id, t.lang_code, t.name
FROM t_zone_zone z
JOIN (VALUES
    -- Monde
    (6295630, 'fr', 'Monde'),
    (6295630, 'en', 'World'),
    (6295630, 'es', 'Mundo'),
    -- Afrique
    (2589581, 'fr', 'Algérie'),               (2589581, 'en', 'Algeria'),                         (2589581, 'es', 'Argelia'),
    (3351879, 'fr', 'Angola'),                (3351879, 'en', 'Angola'),                           (3351879, 'es', 'Angola'),
    (2395170, 'fr', 'Bénin'),                 (2395170, 'en', 'Benin'),                            (2395170, 'es', 'Benín'),
    (933860,  'fr', 'Botswana'),              (933860,  'en', 'Botswana'),                         (933860,  'es', 'Botsuana'),
    (2361809, 'fr', 'Burkina Faso'),          (2361809, 'en', 'Burkina Faso'),                     (2361809, 'es', 'Burkina Faso'),
    (433561,  'fr', 'Burundi'),               (433561,  'en', 'Burundi'),                          (433561,  'es', 'Burundi'),
    (3374766, 'fr', 'Cap-Vert'),              (3374766, 'en', 'Cape Verde'),                       (3374766, 'es', 'Cabo Verde'),
    (2233387, 'fr', 'Cameroun'),              (2233387, 'en', 'Cameroon'),                         (2233387, 'es', 'Camerún'),
    (239880,  'fr', 'République centrafricaine'), (239880, 'en', 'Central African Republic'),      (239880,  'es', 'República Centroafricana'),
    (2434508, 'fr', 'Tchad'),                 (2434508, 'en', 'Chad'),                             (2434508, 'es', 'Chad'),
    (921929,  'fr', 'Comores'),               (921929,  'en', 'Comoros'),                          (921929,  'es', 'Comoras'),
    (2260494, 'fr', 'République du Congo'),   (2260494, 'en', 'Republic of the Congo'),            (2260494, 'es', 'República del Congo'),
    (203312,  'fr', 'République démocratique du Congo'), (203312, 'en', 'Democratic Republic of the Congo'), (203312, 'es', 'República Democrática del Congo'),
    (223816,  'fr', 'Djibouti'),              (223816,  'en', 'Djibouti'),                         (223816,  'es', 'Yibuti'),
    (357994,  'fr', 'Égypte'),                (357994,  'en', 'Egypt'),                            (357994,  'es', 'Egipto'),
    (2309096, 'fr', 'Guinée équatoriale'),    (2309096, 'en', 'Equatorial Guinea'),                (2309096, 'es', 'Guinea Ecuatorial'),
    (338010,  'fr', 'Érythrée'),              (338010,  'en', 'Eritrea'),                          (338010,  'es', 'Eritrea'),
    (934841,  'fr', 'Eswatini'),              (934841,  'en', 'Eswatini'),                         (934841,  'es', 'Esuatini'),
    (337996,  'fr', 'Éthiopie'),              (337996,  'en', 'Ethiopia'),                         (337996,  'es', 'Etiopía'),
    (2400553, 'fr', 'Gabon'),                 (2400553, 'en', 'Gabon'),                            (2400553, 'es', 'Gabón'),
    (2413451, 'fr', 'Gambie'),                (2413451, 'en', 'Gambia'),                           (2413451, 'es', 'Gambia'),
    (2300660, 'fr', 'Ghana'),                 (2300660, 'en', 'Ghana'),                            (2300660, 'es', 'Ghana'),
    (2420477, 'fr', 'Guinée'),                (2420477, 'en', 'Guinea'),                           (2420477, 'es', 'Guinea'),
    (2372248, 'fr', 'Guinée-Bissau'),         (2372248, 'en', 'Guinea-Bissau'),                    (2372248, 'es', 'Guinea-Bisáu'),
    (2287781, 'fr', 'Côte d''Ivoire'),        (2287781, 'en', 'Ivory Coast'),                      (2287781, 'es', 'Costa de Marfil'),
    (192950,  'fr', 'Kenya'),                 (192950,  'en', 'Kenya'),                            (192950,  'es', 'Kenia'),
    (932692,  'fr', 'Lesotho'),               (932692,  'en', 'Lesotho'),                          (932692,  'es', 'Lesoto'),
    (2275384, 'fr', 'Liberia'),               (2275384, 'en', 'Liberia'),                          (2275384, 'es', 'Liberia'),
    (2215636, 'fr', 'Libye'),                 (2215636, 'en', 'Libya'),                            (2215636, 'es', 'Libia'),
    (1062947, 'fr', 'Madagascar'),            (1062947, 'en', 'Madagascar'),                       (1062947, 'es', 'Madagascar'),
    (927384,  'fr', 'Malawi'),                (927384,  'en', 'Malawi'),                           (927384,  'es', 'Malaui'),
    (2453866, 'fr', 'Mali'),                  (2453866, 'en', 'Mali'),                             (2453866, 'es', 'Malí'),
    (2378080, 'fr', 'Mauritanie'),            (2378080, 'en', 'Mauritania'),                       (2378080, 'es', 'Mauritania'),
    (934292,  'fr', 'Maurice'),               (934292,  'en', 'Mauritius'),                        (934292,  'es', 'Mauricio'),
    (2542007, 'fr', 'Maroc'),                 (2542007, 'en', 'Morocco'),                          (2542007, 'es', 'Marruecos'),
    (1036973, 'fr', 'Mozambique'),            (1036973, 'en', 'Mozambique'),                       (1036973, 'es', 'Mozambique'),
    (3355338, 'fr', 'Namibie'),               (3355338, 'en', 'Namibia'),                          (3355338, 'es', 'Namibia'),
    (2440476, 'fr', 'Niger'),                 (2440476, 'en', 'Niger'),                            (2440476, 'es', 'Níger'),
    (2328926, 'fr', 'Nigeria'),               (2328926, 'en', 'Nigeria'),                          (2328926, 'es', 'Nigeria'),
    (49518,   'fr', 'Rwanda'),                (49518,   'en', 'Rwanda'),                           (49518,   'es', 'Ruanda'),
    (2410758, 'fr', 'Sao Tomé-et-Príncipe'), (2410758, 'en', 'São Tomé and Príncipe'),             (2410758, 'es', 'Santo Tomé y Príncipe'),
    (2245662, 'fr', 'Sénégal'),               (2245662, 'en', 'Senegal'),                          (2245662, 'es', 'Senegal'),
    (241170,  'fr', 'Seychelles'),            (241170,  'en', 'Seychelles'),                       (241170,  'es', 'Seychelles'),
    (2403846, 'fr', 'Sierra Leone'),          (2403846, 'en', 'Sierra Leone'),                     (2403846, 'es', 'Sierra Leona'),
    (51537,   'fr', 'Somalie'),               (51537,   'en', 'Somalia'),                          (51537,   'es', 'Somalia'),
    (953987,  'fr', 'Afrique du Sud'),        (953987,  'en', 'South Africa'),                     (953987,  'es', 'Sudáfrica'),
    (7909807, 'fr', 'Soudan du Sud'),         (7909807, 'en', 'South Sudan'),                      (7909807, 'es', 'Sudán del Sur'),
    (366755,  'fr', 'Soudan'),                (366755,  'en', 'Sudan'),                            (366755,  'es', 'Sudán'),
    (149590,  'fr', 'Tanzanie'),              (149590,  'en', 'Tanzania'),                         (149590,  'es', 'Tanzania'),
    (2363686, 'fr', 'Togo'),                  (2363686, 'en', 'Togo'),                             (2363686, 'es', 'Togo'),
    (2464461, 'fr', 'Tunisie'),               (2464461, 'en', 'Tunisia'),                          (2464461, 'es', 'Túnez'),
    (226074,  'fr', 'Ouganda'),               (226074,  'en', 'Uganda'),                           (226074,  'es', 'Uganda'),
    (895949,  'fr', 'Zambie'),                (895949,  'en', 'Zambia'),                           (895949,  'es', 'Zambia'),
    (878675,  'fr', 'Zimbabwe'),              (878675,  'en', 'Zimbabwe'),                         (878675,  'es', 'Zimbabue'),
    -- Amérique
    (3576396, 'fr', 'Antigua-et-Barbuda'),    (3576396, 'en', 'Antigua and Barbuda'),              (3576396, 'es', 'Antigua y Barbuda'),
    (3865483, 'fr', 'Argentine'),             (3865483, 'en', 'Argentina'),                        (3865483, 'es', 'Argentina'),
    (3572887, 'fr', 'Bahamas'),               (3572887, 'en', 'Bahamas'),                          (3572887, 'es', 'Bahamas'),
    (3374084, 'fr', 'Barbade'),               (3374084, 'en', 'Barbados'),                         (3374084, 'es', 'Barbados'),
    (3582678, 'fr', 'Belize'),                (3582678, 'en', 'Belize'),                           (3582678, 'es', 'Belice'),
    (3923057, 'fr', 'Bolivie'),               (3923057, 'en', 'Bolivia'),                          (3923057, 'es', 'Bolivia'),
    (3469034, 'fr', 'Brésil'),                (3469034, 'en', 'Brazil'),                           (3469034, 'es', 'Brasil'),
    (6251999, 'fr', 'Canada'),                (6251999, 'en', 'Canada'),                           (6251999, 'es', 'Canadá'),
    (3895114, 'fr', 'Chili'),                 (3895114, 'en', 'Chile'),                            (3895114, 'es', 'Chile'),
    (3686110, 'fr', 'Colombie'),              (3686110, 'en', 'Colombia'),                         (3686110, 'es', 'Colombia'),
    (3624060, 'fr', 'Costa Rica'),            (3624060, 'en', 'Costa Rica'),                       (3624060, 'es', 'Costa Rica'),
    (3562981, 'fr', 'Cuba'),                  (3562981, 'en', 'Cuba'),                             (3562981, 'es', 'Cuba'),
    (3575830, 'fr', 'Dominique'),             (3575830, 'en', 'Dominica'),                         (3575830, 'es', 'Dominica'),
    (3508796, 'fr', 'République dominicaine'),(3508796, 'en', 'Dominican Republic'),               (3508796, 'es', 'República Dominicana'),
    (3658394, 'fr', 'Équateur'),              (3658394, 'en', 'Ecuador'),                          (3658394, 'es', 'Ecuador'),
    (3585968, 'fr', 'Salvador'),              (3585968, 'en', 'El Salvador'),                      (3585968, 'es', 'El Salvador'),
    (3580239, 'fr', 'Grenade'),               (3580239, 'en', 'Grenada'),                          (3580239, 'es', 'Granada'),
    (3595528, 'fr', 'Guatemala'),             (3595528, 'en', 'Guatemala'),                        (3595528, 'es', 'Guatemala'),
    (3378535, 'fr', 'Guyana'),                (3378535, 'en', 'Guyana'),                           (3378535, 'es', 'Guyana'),
    (3723988, 'fr', 'Haïti'),                 (3723988, 'en', 'Haiti'),                            (3723988, 'es', 'Haití'),
    (3608932, 'fr', 'Honduras'),              (3608932, 'en', 'Honduras'),                         (3608932, 'es', 'Honduras'),
    (3489940, 'fr', 'Jamaïque'),              (3489940, 'en', 'Jamaica'),                          (3489940, 'es', 'Jamaica'),
    (3996063, 'fr', 'Mexique'),               (3996063, 'en', 'Mexico'),                           (3996063, 'es', 'México'),
    (3617476, 'fr', 'Nicaragua'),             (3617476, 'en', 'Nicaragua'),                        (3617476, 'es', 'Nicaragua'),
    (3703430, 'fr', 'Panama'),                (3703430, 'en', 'Panama'),                           (3703430, 'es', 'Panamá'),
    (3437598, 'fr', 'Paraguay'),              (3437598, 'en', 'Paraguay'),                         (3437598, 'es', 'Paraguay'),
    (3932488, 'fr', 'Pérou'),                 (3932488, 'en', 'Peru'),                             (3932488, 'es', 'Perú'),
    (3575174, 'fr', 'Saint-Christophe-et-Niévès'), (3575174, 'en', 'Saint Kitts and Nevis'),       (3575174, 'es', 'San Cristóbal y Nieves'),
    (3576468, 'fr', 'Sainte-Lucie'),          (3576468, 'en', 'Saint Lucia'),                      (3576468, 'es', 'Santa Lucía'),
    (3577815, 'fr', 'Saint-Vincent-et-les-Grenadines'), (3577815, 'en', 'Saint Vincent and the Grenadines'), (3577815, 'es', 'San Vicente y las Granadinas'),
    (3382998, 'fr', 'Suriname'),              (3382998, 'en', 'Suriname'),                         (3382998, 'es', 'Surinam'),
    (3573591, 'fr', 'Trinité-et-Tobago'),     (3573591, 'en', 'Trinidad and Tobago'),              (3573591, 'es', 'Trinidad y Tobago'),
    (6252001, 'fr', 'États-Unis'),            (6252001, 'en', 'United States'),                    (6252001, 'es', 'Estados Unidos'),
    (3439705, 'fr', 'Uruguay'),               (3439705, 'en', 'Uruguay'),                          (3439705, 'es', 'Uruguay'),
    (3625428, 'fr', 'Venezuela'),             (3625428, 'en', 'Venezuela'),                        (3625428, 'es', 'Venezuela'),
    -- Asie
    (1149361, 'fr', 'Afghanistan'),           (1149361, 'en', 'Afghanistan'),                      (1149361, 'es', 'Afganistán'),
    (174982,  'fr', 'Arménie'),               (174982,  'en', 'Armenia'),                          (174982,  'es', 'Armenia'),
    (587116,  'fr', 'Azerbaïdjan'),           (587116,  'en', 'Azerbaijan'),                       (587116,  'es', 'Azerbaiyán'),
    (290291,  'fr', 'Bahreïn'),               (290291,  'en', 'Bahrain'),                          (290291,  'es', 'Bahréin'),
    (1210997, 'fr', 'Bangladesh'),            (1210997, 'en', 'Bangladesh'),                       (1210997, 'es', 'Bangladesh'),
    (1252634, 'fr', 'Bhoutan'),               (1252634, 'en', 'Bhutan'),                           (1252634, 'es', 'Bután'),
    (1820814, 'fr', 'Brunei'),                (1820814, 'en', 'Brunei'),                           (1820814, 'es', 'Brunéi'),
    (1831722, 'fr', 'Cambodge'),              (1831722, 'en', 'Cambodia'),                         (1831722, 'es', 'Camboya'),
    (1814991, 'fr', 'Chine'),                 (1814991, 'en', 'China'),                            (1814991, 'es', 'China'),
    (146669,  'fr', 'Chypre'),                (146669,  'en', 'Cyprus'),                           (146669,  'es', 'Chipre'),
    (614540,  'fr', 'Géorgie'),               (614540,  'en', 'Georgia'),                          (614540,  'es', 'Georgia'),
    (1269750, 'fr', 'Inde'),                  (1269750, 'en', 'India'),                            (1269750, 'es', 'India'),
    (1643084, 'fr', 'Indonésie'),             (1643084, 'en', 'Indonesia'),                        (1643084, 'es', 'Indonesia'),
    (130758,  'fr', 'Iran'),                  (130758,  'en', 'Iran'),                             (130758,  'es', 'Irán'),
    (99237,   'fr', 'Irak'),                  (99237,   'en', 'Iraq'),                             (99237,   'es', 'Irak'),
    (294640,  'fr', 'Israël'),                (294640,  'en', 'Israel'),                           (294640,  'es', 'Israel'),
    (1861060, 'fr', 'Japon'),                 (1861060, 'en', 'Japan'),                            (1861060, 'es', 'Japón'),
    (248816,  'fr', 'Jordanie'),              (248816,  'en', 'Jordan'),                           (248816,  'es', 'Jordania'),
    (1522867, 'fr', 'Kazakhstan'),            (1522867, 'en', 'Kazakhstan'),                       (1522867, 'es', 'Kazajistán'),
    (285570,  'fr', 'Koweït'),                (285570,  'en', 'Kuwait'),                           (285570,  'es', 'Kuwait'),
    (1527747, 'fr', 'Kirghizistan'),          (1527747, 'en', 'Kyrgyzstan'),                       (1527747, 'es', 'Kirguistán'),
    (1655842, 'fr', 'Laos'),                  (1655842, 'en', 'Laos'),                             (1655842, 'es', 'Laos'),
    (272103,  'fr', 'Liban'),                 (272103,  'en', 'Lebanon'),                          (272103,  'es', 'Líbano'),
    (1733045, 'fr', 'Malaisie'),              (1733045, 'en', 'Malaysia'),                         (1733045, 'es', 'Malasia'),
    (1282028, 'fr', 'Maldives'),              (1282028, 'en', 'Maldives'),                         (1282028, 'es', 'Maldivas'),
    (2029969, 'fr', 'Mongolie'),              (2029969, 'en', 'Mongolia'),                         (2029969, 'es', 'Mongolia'),
    (1327865, 'fr', 'Myanmar'),               (1327865, 'en', 'Myanmar'),                          (1327865, 'es', 'Birmania'),
    (1282988, 'fr', 'Népal'),                 (1282988, 'en', 'Nepal'),                            (1282988, 'es', 'Nepal'),
    (1873107, 'fr', 'Corée du Nord'),         (1873107, 'en', 'North Korea'),                      (1873107, 'es', 'Corea del Norte'),
    (286963,  'fr', 'Oman'),                  (286963,  'en', 'Oman'),                             (286963,  'es', 'Omán'),
    (1168579, 'fr', 'Pakistan'),              (1168579, 'en', 'Pakistan'),                         (1168579, 'es', 'Pakistán'),
    (1694008, 'fr', 'Philippines'),           (1694008, 'en', 'Philippines'),                      (1694008, 'es', 'Filipinas'),
    (289688,  'fr', 'Qatar'),                 (289688,  'en', 'Qatar'),                            (289688,  'es', 'Catar'),
    (102358,  'fr', 'Arabie saoudite'),       (102358,  'en', 'Saudi Arabia'),                     (102358,  'es', 'Arabia Saudita'),
    (1880251, 'fr', 'Singapour'),             (1880251, 'en', 'Singapore'),                        (1880251, 'es', 'Singapur'),
    (1835841, 'fr', 'Corée du Sud'),          (1835841, 'en', 'South Korea'),                      (1835841, 'es', 'Corea del Sur'),
    (1227603, 'fr', 'Sri Lanka'),             (1227603, 'en', 'Sri Lanka'),                        (1227603, 'es', 'Sri Lanka'),
    (163843,  'fr', 'Syrie'),                 (163843,  'en', 'Syria'),                            (163843,  'es', 'Siria'),
    (1220409, 'fr', 'Tadjikistan'),           (1220409, 'en', 'Tajikistan'),                       (1220409, 'es', 'Tayikistán'),
    (1605651, 'fr', 'Thaïlande'),             (1605651, 'en', 'Thailand'),                         (1605651, 'es', 'Tailandia'),
    (1966436, 'fr', 'Timor oriental'),        (1966436, 'en', 'Timor-Leste'),                      (1966436, 'es', 'Timor Oriental'),
    (298795,  'fr', 'Turquie'),               (298795,  'en', 'Turkey'),                           (298795,  'es', 'Turquía'),
    (1220060, 'fr', 'Turkménistan'),          (1220060, 'en', 'Turkmenistan'),                     (1220060, 'es', 'Turkmenistán'),
    (290557,  'fr', 'Émirats arabes unis'),   (290557,  'en', 'United Arab Emirates'),             (290557,  'es', 'Emiratos Árabes Unidos'),
    (1512440, 'fr', 'Ouzbékistan'),           (1512440, 'en', 'Uzbekistan'),                       (1512440, 'es', 'Uzbekistán'),
    (1562822, 'fr', 'Vietnam'),               (1562822, 'en', 'Vietnam'),                          (1562822, 'es', 'Vietnam'),
    (69543,   'fr', 'Yémen'),                 (69543,   'en', 'Yemen'),                            (69543,   'es', 'Yemen'),
    -- Europe
    (783754,  'fr', 'Albanie'),               (783754,  'en', 'Albania'),                          (783754,  'es', 'Albania'),
    (3041565, 'fr', 'Andorre'),               (3041565, 'en', 'Andorra'),                          (3041565, 'es', 'Andorra'),
    (2782113, 'fr', 'Autriche'),              (2782113, 'en', 'Austria'),                          (2782113, 'es', 'Austria'),
    (630336,  'fr', 'Biélorussie'),           (630336,  'en', 'Belarus'),                          (630336,  'es', 'Bielorrusia'),
    (2802361, 'fr', 'Belgique'),              (2802361, 'en', 'Belgium'),                          (2802361, 'es', 'Bélgica'),
    (3277605, 'fr', 'Bosnie-Herzégovine'),    (3277605, 'en', 'Bosnia and Herzegovina'),           (3277605, 'es', 'Bosnia y Herzegovina'),
    (732800,  'fr', 'Bulgarie'),              (732800,  'en', 'Bulgaria'),                         (732800,  'es', 'Bulgaria'),
    (3202326, 'fr', 'Croatie'),               (3202326, 'en', 'Croatia'),                          (3202326, 'es', 'Croacia'),
    (3077311, 'fr', 'Tchéquie'),              (3077311, 'en', 'Czech Republic'),                   (3077311, 'es', 'República Checa'),
    (2623032, 'fr', 'Danemark'),              (2623032, 'en', 'Denmark'),                          (2623032, 'es', 'Dinamarca'),
    (453733,  'fr', 'Estonie'),               (453733,  'en', 'Estonia'),                          (453733,  'es', 'Estonia'),
    (660013,  'fr', 'Finlande'),              (660013,  'en', 'Finland'),                          (660013,  'es', 'Finlandia'),
    (3017382, 'fr', 'France'),                (3017382, 'en', 'France'),                           (3017382, 'es', 'Francia'),
    (2921044, 'fr', 'Allemagne'),             (2921044, 'en', 'Germany'),                          (2921044, 'es', 'Alemania'),
    (390903,  'fr', 'Grèce'),                 (390903,  'en', 'Greece'),                           (390903,  'es', 'Grecia'),
    (719819,  'fr', 'Hongrie'),               (719819,  'en', 'Hungary'),                          (719819,  'es', 'Hungría'),
    (2629691, 'fr', 'Islande'),               (2629691, 'en', 'Iceland'),                          (2629691, 'es', 'Islandia'),
    (2963597, 'fr', 'Irlande'),               (2963597, 'en', 'Ireland'),                          (2963597, 'es', 'Irlanda'),
    (3175395, 'fr', 'Italie'),                (3175395, 'en', 'Italy'),                            (3175395, 'es', 'Italia'),
    (458258,  'fr', 'Lettonie'),              (458258,  'en', 'Latvia'),                           (458258,  'es', 'Letonia'),
    (3042058, 'fr', 'Liechtenstein'),         (3042058, 'en', 'Liechtenstein'),                    (3042058, 'es', 'Liechtenstein'),
    (597427,  'fr', 'Lituanie'),              (597427,  'en', 'Lithuania'),                        (597427,  'es', 'Lituania'),
    (2960313, 'fr', 'Luxembourg'),            (2960313, 'en', 'Luxembourg'),                       (2960313, 'es', 'Luxemburgo'),
    (2562770, 'fr', 'Malte'),                 (2562770, 'en', 'Malta'),                            (2562770, 'es', 'Malta'),
    (617790,  'fr', 'Moldavie'),              (617790,  'en', 'Moldova'),                          (617790,  'es', 'Moldavia'),
    (2993457, 'fr', 'Monaco'),                (2993457, 'en', 'Monaco'),                           (2993457, 'es', 'Mónaco'),
    (3194884, 'fr', 'Monténégro'),            (3194884, 'en', 'Montenegro'),                       (3194884, 'es', 'Montenegro'),
    (2750405, 'fr', 'Pays-Bas'),              (2750405, 'en', 'Netherlands'),                      (2750405, 'es', 'Países Bajos'),
    (718075,  'fr', 'Macédoine du Nord'),     (718075,  'en', 'North Macedonia'),                  (718075,  'es', 'Macedonia del Norte'),
    (607072,  'fr', 'Norvège'),               (607072,  'en', 'Norway'),                           (607072,  'es', 'Noruega'),
    (798544,  'fr', 'Pologne'),               (798544,  'en', 'Poland'),                           (798544,  'es', 'Polonia'),
    (2264397, 'fr', 'Portugal'),              (2264397, 'en', 'Portugal'),                         (2264397, 'es', 'Portugal'),
    (798549,  'fr', 'Roumanie'),              (798549,  'en', 'Romania'),                          (798549,  'es', 'Rumanía'),
    (2017370, 'fr', 'Russie'),                (2017370, 'en', 'Russia'),                           (2017370, 'es', 'Rusia'),
    (3168068, 'fr', 'Saint-Marin'),           (3168068, 'en', 'San Marino'),                       (3168068, 'es', 'San Marino'),
    (6290252, 'fr', 'Serbie'),                (6290252, 'en', 'Serbia'),                           (6290252, 'es', 'Serbia'),
    (3057568, 'fr', 'Slovaquie'),             (3057568, 'en', 'Slovakia'),                         (3057568, 'es', 'Eslovaquia'),
    (3190538, 'fr', 'Slovénie'),              (3190538, 'en', 'Slovenia'),                         (3190538, 'es', 'Eslovenia'),
    (2510769, 'fr', 'Espagne'),               (2510769, 'en', 'Spain'),                            (2510769, 'es', 'España'),
    (2661886, 'fr', 'Suède'),                 (2661886, 'en', 'Sweden'),                           (2661886, 'es', 'Suecia'),
    (2658434, 'fr', 'Suisse'),                (2658434, 'en', 'Switzerland'),                      (2658434, 'es', 'Suiza'),
    (690791,  'fr', 'Ukraine'),               (690791,  'en', 'Ukraine'),                          (690791,  'es', 'Ucrania'),
    (2635167, 'fr', 'Royaume-Uni'),           (2635167, 'en', 'United Kingdom'),                   (2635167, 'es', 'Reino Unido'),
    -- Océanie
    (2077456, 'fr', 'Australie'),             (2077456, 'en', 'Australia'),                        (2077456, 'es', 'Australia'),
    (2205218, 'fr', 'Fidji'),                 (2205218, 'en', 'Fiji'),                             (2205218, 'es', 'Fiyi'),
    (4030945, 'fr', 'Kiribati'),              (4030945, 'en', 'Kiribati'),                         (4030945, 'es', 'Kiribati'),
    (2080185, 'fr', 'Îles Marshall'),         (2080185, 'en', 'Marshall Islands'),                 (2080185, 'es', 'Islas Marshall'),
    (2081918, 'fr', 'Micronésie'),            (2081918, 'en', 'Micronesia'),                       (2081918, 'es', 'Micronesia'),
    (2110425, 'fr', 'Nauru'),                 (2110425, 'en', 'Nauru'),                            (2110425, 'es', 'Nauru'),
    (2186224, 'fr', 'Nouvelle-Zélande'),      (2186224, 'en', 'New Zealand'),                      (2186224, 'es', 'Nueva Zelanda'),
    (1559582, 'fr', 'Palaos'),                (1559582, 'en', 'Palau'),                            (1559582, 'es', 'Palaos'),
    (2088628, 'fr', 'Papouasie-Nouvelle-Guinée'), (2088628, 'en', 'Papua New Guinea'),             (2088628, 'es', 'Papúa Nueva Guinea'),
    (4034894, 'fr', 'Samoa'),                 (4034894, 'en', 'Samoa'),                            (4034894, 'es', 'Samoa'),
    (2103350, 'fr', 'Îles Salomon'),          (2103350, 'en', 'Solomon Islands'),                  (2103350, 'es', 'Islas Salomón'),
    (4032283, 'fr', 'Tonga'),                 (4032283, 'en', 'Tonga'),                            (4032283, 'es', 'Tonga'),
    (2110297, 'fr', 'Tuvalu'),                (2110297, 'en', 'Tuvalu'),                           (2110297, 'es', 'Tuvalu'),
    (2134431, 'fr', 'Vanuatu'),               (2134431, 'en', 'Vanuatu'),                          (2134431, 'es', 'Vanuatu')
) AS t(geonames_id, lang_code, name) ON z.geonames_id = t.geonames_id
ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name;

-- Traductions des continents (GeoNames IDs officiels)
INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
SELECT z.id, t.lang_code, t.name
FROM t_zone_zone z
JOIN (VALUES
    (6255146, 'fr', 'Afrique'),            (6255146, 'en', 'Africa'),            (6255146, 'es', 'África'),
    (6255149, 'fr', 'Amérique du Nord'),   (6255149, 'en', 'North America'),     (6255149, 'es', 'América del Norte'),
    (6255150, 'fr', 'Amérique du Sud'),    (6255150, 'en', 'South America'),     (6255150, 'es', 'América del Sur'),
    (6255147, 'fr', 'Asie'),               (6255147, 'en', 'Asia'),              (6255147, 'es', 'Asia'),
    (6255148, 'fr', 'Europe'),             (6255148, 'en', 'Europe'),            (6255148, 'es', 'Europa'),
    (6255151, 'fr', 'Océanie'),            (6255151, 'en', 'Oceania'),           (6255151, 'es', 'Oceanía'),
    (6255152, 'fr', 'Antarctique'),        (6255152, 'en', 'Antarctica'),        (6255152, 'es', 'Antártida')
) AS t(geonames_id, lang_code, name)
  ON z.geonames_id = t.geonames_id
ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name;


-- ============================================================
-- DONNÉES : Traductions UI (ZONE_)
-- ============================================================

INSERT INTO t_zone_text_key (text_code) VALUES
('ZONE_TITLE_ZONES'),
('ZONE_TITLE_ZONE_ADD'),
('ZONE_TITLE_ZONE_EDIT'),
('ZONE_LABEL_NAME'),
('ZONE_LABEL_TYPE'),
('ZONE_LABEL_PARENT'),
('ZONE_LABEL_COUNTRY_CODE'),
('ZONE_LABEL_GEONAMES_ID'),
('ZONE_LABEL_CHILDREN_LOADED'),
('ZONE_LABEL_STATUS'),
('ZONE_MSG_ZONE_ADDED'),
('ZONE_MSG_ZONE_UPDATED'),
('ZONE_MSG_ZONE_DELETED'),
('ZONE_ERR_LOAD_CHILDREN'),
('ZONE_ERR_GEONAMES_UNAVAILABLE'),
('ZONE_ERR_MISSING_FIELDS'),
('ZONE_BTN_LOAD_CHILDREN'),
('ZONE_BTN_SELECT'),
('ZONE_PLACEHOLDER_SEARCH')
ON CONFLICT (text_code) DO NOTHING;

INSERT INTO t_zone_text (text_code, lang_code, text_label)
SELECT t.text_code, t.lang_code, t.text_label
FROM (VALUES
    ('ZONE_TITLE_ZONES',              'fr', 'Zones géographiques'),
    ('ZONE_TITLE_ZONE_ADD',           'fr', 'Ajouter une zone'),
    ('ZONE_TITLE_ZONE_EDIT',          'fr', 'Modifier la zone'),
    ('ZONE_LABEL_NAME',               'fr', 'Nom'),
    ('ZONE_LABEL_TYPE',               'fr', 'Type'),
    ('ZONE_LABEL_PARENT',             'fr', 'Zone parente'),
    ('ZONE_LABEL_COUNTRY_CODE',       'fr', 'Code pays'),
    ('ZONE_LABEL_GEONAMES_ID',        'fr', 'ID GeoNames'),
    ('ZONE_LABEL_CHILDREN_LOADED',    'fr', 'Sous-zones chargées'),
    ('ZONE_LABEL_STATUS',             'fr', 'Statut'),
    ('ZONE_MSG_ZONE_ADDED',           'fr', 'Zone ajoutée avec succès.'),
    ('ZONE_MSG_ZONE_UPDATED',         'fr', 'Zone mise à jour avec succès.'),
    ('ZONE_MSG_ZONE_DELETED',         'fr', 'Zone supprimée.'),
    ('ZONE_ERR_LOAD_CHILDREN',        'fr', 'Impossible de charger les sous-zones.'),
    ('ZONE_ERR_GEONAMES_UNAVAILABLE', 'fr', 'Le service GeoNames est temporairement indisponible.'),
    ('ZONE_ERR_MISSING_FIELDS',       'fr', 'Champs obligatoires manquants.'),
    ('ZONE_BTN_LOAD_CHILDREN',        'fr', 'Charger les sous-zones'),
    ('ZONE_BTN_SELECT',               'fr', 'Sélectionner'),
    ('ZONE_PLACEHOLDER_SEARCH',       'fr', 'Rechercher une zone...'),
    ('ZONE_TITLE_ZONES',              'en', 'Geographic zones'),
    ('ZONE_TITLE_ZONE_ADD',           'en', 'Add a zone'),
    ('ZONE_TITLE_ZONE_EDIT',          'en', 'Edit zone'),
    ('ZONE_LABEL_NAME',               'en', 'Name'),
    ('ZONE_LABEL_TYPE',               'en', 'Type'),
    ('ZONE_LABEL_PARENT',             'en', 'Parent zone'),
    ('ZONE_LABEL_COUNTRY_CODE',       'en', 'Country code'),
    ('ZONE_LABEL_GEONAMES_ID',        'en', 'GeoNames ID'),
    ('ZONE_LABEL_CHILDREN_LOADED',    'en', 'Sub-zones loaded'),
    ('ZONE_LABEL_STATUS',             'en', 'Status'),
    ('ZONE_MSG_ZONE_ADDED',           'en', 'Zone added successfully.'),
    ('ZONE_MSG_ZONE_UPDATED',         'en', 'Zone updated successfully.'),
    ('ZONE_MSG_ZONE_DELETED',         'en', 'Zone deleted.'),
    ('ZONE_ERR_LOAD_CHILDREN',        'en', 'Unable to load sub-zones.'),
    ('ZONE_ERR_GEONAMES_UNAVAILABLE', 'en', 'GeoNames service is temporarily unavailable.'),
    ('ZONE_ERR_MISSING_FIELDS',       'en', 'Required fields are missing.'),
    ('ZONE_BTN_LOAD_CHILDREN',        'en', 'Load sub-zones'),
    ('ZONE_BTN_SELECT',               'en', 'Select'),
    ('ZONE_PLACEHOLDER_SEARCH',       'en', 'Search a zone...'),
    ('ZONE_TITLE_ZONES',              'es', 'Zonas geográficas'),
    ('ZONE_TITLE_ZONE_ADD',           'es', 'Añadir una zona'),
    ('ZONE_TITLE_ZONE_EDIT',          'es', 'Editar zona'),
    ('ZONE_LABEL_NAME',               'es', 'Nombre'),
    ('ZONE_LABEL_TYPE',               'es', 'Tipo'),
    ('ZONE_LABEL_PARENT',             'es', 'Zona padre'),
    ('ZONE_LABEL_COUNTRY_CODE',       'es', 'Código de país'),
    ('ZONE_LABEL_GEONAMES_ID',        'es', 'ID GeoNames'),
    ('ZONE_LABEL_CHILDREN_LOADED',    'es', 'Sub-zonas cargadas'),
    ('ZONE_LABEL_STATUS',             'es', 'Estado'),
    ('ZONE_MSG_ZONE_ADDED',           'es', 'Zona añadida con éxito.'),
    ('ZONE_MSG_ZONE_UPDATED',         'es', 'Zona actualizada con éxito.'),
    ('ZONE_MSG_ZONE_DELETED',         'es', 'Zona eliminada.'),
    ('ZONE_ERR_LOAD_CHILDREN',        'es', 'No se pueden cargar las sub-zonas.'),
    ('ZONE_ERR_GEONAMES_UNAVAILABLE', 'es', 'El servicio GeoNames no está disponible temporalmente.'),
    ('ZONE_ERR_MISSING_FIELDS',       'es', 'Faltan campos obligatorios.'),
    ('ZONE_BTN_LOAD_CHILDREN',        'es', 'Cargar sub-zonas'),
    ('ZONE_BTN_SELECT',               'es', 'Seleccionar'),
    ('ZONE_PLACEHOLDER_SEARCH',       'es', 'Buscar una zona...')
) AS t(text_code, lang_code, text_label)
ON CONFLICT (text_code, lang_code) DO UPDATE SET text_label = EXCLUDED.text_label;
