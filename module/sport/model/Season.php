<?php
namespace Module\Sport\Model;

use Core\Database;

class Season {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllSeasons() {
        $this->db->query("
            SELECT se.*
            FROM t_sport_season se
            ORDER BY se.date_start DESC, se.id DESC
        ");
        return $this->db->resultSet();
    }

    public function getSeasonById($id) {
        $this->db->query("
            SELECT se.*, 
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_season se
            LEFT JOIN t_user_user u_c ON se.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON se.modified_by = u_m.id
            WHERE se.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getSeasonByCode($code) {
        $this->db->query("
            SELECT se.*, 
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_season se
            LEFT JOIN t_user_user u_c ON se.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON se.modified_by = u_m.id
            WHERE se.code = :code
        ");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function addSeason($data) {
        $this->db->query("
            INSERT INTO t_sport_season (code, name, date_start, date_end, status_id, created_by, created_at) 
            VALUES (:code, :name, :date_start, :date_end, :status_id, :created_by, CURRENT_TIMESTAMP)
        ");
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':date_start', $data['date_start']);
        $this->db->bind(':date_end', $data['date_end']);
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        return $this->db->execute();
    }

    public function updateSeason($data) {
        $this->db->query("
            UPDATE t_sport_season 
            SET name = :name, 
                date_start = :date_start, 
                date_end = :date_end, 
                status_id = :status_id, 
                modified_by = :modified_by, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':date_start', $data['date_start']);
        $this->db->bind(':date_end', $data['date_end']);
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    public function softDeleteSeason($id, $userId) {
        $this->db->query("
            UPDATE t_sport_season 
            SET status_id = 2, 
                modified_by = :user, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function activateSeason($id, $userId) {
        $this->db->query("
            UPDATE t_sport_season 
            SET status_id = 1, 
                modified_by = :user, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeleteSeason($id) {
        $this->db->query("DELETE FROM t_sport_season WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
