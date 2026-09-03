<?php
namespace Module\Sport\Model;

use Core\Database;

class Person {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllPersons() {
        $this->db->query("
            SELECT p.*, 
                   pr.name AS role_name
            FROM t_sport_person p
            LEFT JOIN t_sport_person_role pr ON p.role_code = pr.code
            ORDER BY p.last_name ASC, p.first_name ASC
        ");
        return $this->db->resultSet();
    }

    public function getAllRoles() {
        $this->db->query("SELECT * FROM t_sport_person_role ORDER BY code ASC");
        return $this->db->resultSet();
    }

    public function getPersonById($id) {
        $this->db->query("
            SELECT p.*, 
                   pr.name AS role_name,
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_person p
            LEFT JOIN t_sport_person_role pr ON p.role_code = pr.code
            LEFT JOIN t_user_user u_c ON p.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON p.modified_by = u_m.id
            WHERE p.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getPersonByCode($code) {
        $this->db->query("
            SELECT p.*, 
                   pr.name AS role_name,
                   u_c.username AS created_by_name, 
                   u_m.username AS modified_by_name 
            FROM t_sport_person p
            LEFT JOIN t_sport_person_role pr ON p.role_code = pr.code
            LEFT JOIN t_user_user u_c ON p.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON p.modified_by = u_m.id
            WHERE p.code = :code
        ");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function addPerson($data) {
        $this->db->query("
            INSERT INTO t_sport_person (code, first_name, last_name, gender, birth_date, nationality, role_code, status_id, created_by, created_at) 
            VALUES (:code, :first_name, :last_name, :gender, :birth_date, :nationality, :role_code, :status_id, :created_by, CURRENT_TIMESTAMP)
        ");
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':gender', $data['gender'] ?? 'M');
        $this->db->bind(':birth_date', !empty($data['birth_date']) ? $data['birth_date'] : null);
        $this->db->bind(':nationality', !empty($data['nationality']) ? $data['nationality'] : null);
        $this->db->bind(':role_code', $data['role_code'] ?? 'player');
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        return $this->db->execute();
    }

    public function updatePerson($data) {
        $this->db->query("
            UPDATE t_sport_person 
            SET first_name = :first_name, 
                last_name = :last_name, 
                gender = :gender, 
                birth_date = :birth_date, 
                nationality = :nationality, 
                role_code = :role_code, 
                status_id = :status_id, 
                modified_by = :modified_by, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':gender', $data['gender'] ?? 'M');
        $this->db->bind(':birth_date', !empty($data['birth_date']) ? $data['birth_date'] : null);
        $this->db->bind(':nationality', !empty($data['nationality']) ? $data['nationality'] : null);
        $this->db->bind(':role_code', $data['role_code'] ?? 'player');
        $this->db->bind(':status_id', $data['status_id'] ?? 1);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    public function softDeletePerson($id, $userId) {
        $this->db->query("
            UPDATE t_sport_person 
            SET status_id = 2, 
                modified_by = :user, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeletePerson($id) {
        $this->db->query("DELETE FROM t_sport_person WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
