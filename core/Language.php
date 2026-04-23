<?php
namespace Core;

class Language {
    private static $db = null;

    /**
     * Charge le dictionnaire d'un module donné pour la langue courante.
     * Si la table n'existe pas ou en cas d'erreur, retourne un tableau vide sans faire planter l'application.
     * @param string $module Le nom du module (ex: 'main', 'user')
     * @return array Le dictionnaire associatif des traductions
     */
    public static function load($module) {
        if (self::$db === null) {
            self::$db = new Database();
        }

        // Déterminer la langue courante
        $lang = $_SESSION['lang'] ?? DEFAULT_LANG;

        $dict = [];
        $table = "t_" . strtolower($module) . "_text";

        try {
            self::$db->query("SELECT text_code, text_label FROM {$table} WHERE lang_code = :lang");
            self::$db->bind(':lang', $lang);
            $results = self::$db->resultSet();

            if ($results) {
                foreach ($results as $row) {
                    $dict[$row['text_code']] = $row['text_label'];
                }
            }
        } catch (\PDOException $e) {
            // Si la table n'existe pas, on passe discrètement
        }

        return $dict;
    }

    /**
     * Récupère la liste des langues disponibles dans le système.
     * @return array Tableau des langues configurées
     */
    public static function getSystemLanguages() {
        if (self::$db === null) {
            self::$db = new Database();
        }

        try {
            self::$db->query("SELECT lang_code, lang_name, lang_flag FROM t_main_lang WHERE status_id = 1 ORDER BY lang_code ASC");
            $results = self::$db->resultSet();
            if ($results) {
                // Ensure array shape if resultSet returns objects depending on PDO mode
                $langs = [];
                foreach ($results as $row) {
                    $langs[] = is_object($row) ? (array)$row : $row;
                }
                return $langs;
            }
        } catch (\PDOException $e) {
            // Si la table n'existe pas, on retourne une liste vide
        }

        return [];
    }
}
