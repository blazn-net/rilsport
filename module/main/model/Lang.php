<?php
namespace Module\Main\Model;

use Core\Database;

class Lang {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllLangs() {
        $this->db->query("SELECT * FROM t_main_lang ORDER BY lang_code ASC");
        return $this->db->resultSet();
    }

    public function getLangByCode($code) {
        $this->db->query("
            SELECT l.*, 
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_main_lang l
            LEFT JOIN t_user_user u_c ON l.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON l.modified_by = u_m.id
            WHERE l.lang_code = :code
        ");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function addLang($data) {
        $this->db->query("INSERT INTO t_main_lang (lang_code, lang_name, lang_flag, status_id, created_by, created_at) 
                          VALUES (:code, :name, :flag, :status, :user, CURRENT_TIMESTAMP)");
        $this->db->bind(':code', $data['lang_code']);
        $this->db->bind(':name', $data['lang_name']);
        $this->db->bind(':flag', $data['lang_flag']);
        $this->db->bind(':status', $data['status_id']);
        $this->db->bind(':user', $data['user_id']);
        
        if ($this->db->execute()) {
            // Création automatique des traductions
            $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name LIKE 't_%_text_key'");
            $keyTables = $this->db->resultSet();

            foreach ($keyTables as $tbl) {
                $keyTableName = $tbl['table_name'];
                $textTableName = str_replace('_key', '', $keyTableName);
                
                $this->db->query("SELECT text_code FROM {$keyTableName}");
                $keys = $this->db->resultSet();

                foreach ($keys as $k) {
                    $this->db->query("INSERT INTO {$textTableName} (text_code, lang_code, text_label) VALUES (:tcode, :lcode, '') ON CONFLICT DO NOTHING");
                    $this->db->bind(':tcode', $k['text_code']);
                    $this->db->bind(':lcode', $data['lang_code']);
                    $this->db->execute();
                }
            }
            return true;
        }
        return false;
    }

    public function updateLang($data) {
        $this->db->query("UPDATE t_main_lang SET lang_name = :name, lang_flag = :flag, status_id = :status, modified_by = :user, modified_at = CURRENT_TIMESTAMP WHERE lang_code = :code");
        $this->db->bind(':code', $data['lang_code']);
        $this->db->bind(':name', $data['lang_name']);
        $this->db->bind(':flag', $data['lang_flag']);
        $this->db->bind(':status', $data['status_id']);
        $this->db->bind(':user', $data['user_id']);
        return $this->db->execute();
    }

    public function softDeleteLang($code, $userId) {
        $this->db->query("UPDATE t_main_lang SET status_id = 2, modified_by = :user, modified_at = CURRENT_TIMESTAMP WHERE lang_code = :code");
        $this->db->bind(':code', $code);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeleteLang($code) {
        $this->db->query("DELETE FROM t_main_lang WHERE lang_code = :code");
        $this->db->bind(':code', $code);
        return $this->db->execute();
    }
}
