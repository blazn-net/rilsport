<?php
namespace Module\Sport\Model;

use Core\Database;

class Sport {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllSports() {
        $this->db->query("SELECT * FROM t_sport_sport ORDER BY id ASC");
        return $this->db->resultSet();
    }

    public function getSportById($id) {
        $this->db->query("
            SELECT s.*, 
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_sport s
            LEFT JOIN t_user_user u_c ON s.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON s.modified_by = u_m.id
            WHERE s.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getSportByCode($code) {
        $this->db->query("
            SELECT s.*, 
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_sport s
            LEFT JOIN t_user_user u_c ON s.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON s.modified_by = u_m.id
            WHERE s.code = :code
        ");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function addSport($data) {
        $this->db->query("
            INSERT INTO t_sport_sport (code, name, description, icon, status_id, created_by, created_at) 
            VALUES (:code, :name, :description, :icon, :status_id, :created_by, CURRENT_TIMESTAMP)
        ");
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':icon', (!empty($data['icon']) && $data['icon'] !== 'mif-dribbble') ? $data['icon'] : 'mif-trophy');
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        return $this->db->execute();
    }

    public function updateSport($data) {
        $this->db->query("
            UPDATE t_sport_sport 
            SET name = :name, 
                description = :description, 
                icon = :icon, 
                status_id = :status_id, 
                modified_by = :modified_by, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':icon', (!empty($data['icon']) && $data['icon'] !== 'mif-dribbble') ? $data['icon'] : 'mif-trophy');
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    public function softDeleteSport($id, $userId) {
        $this->db->query("
            UPDATE t_sport_sport 
            SET status_id = 2, 
                modified_by = :user, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeleteSport($id) {
        $this->db->query("DELETE FROM t_sport_sport WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
