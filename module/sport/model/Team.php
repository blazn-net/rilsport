<?php
namespace Module\Sport\Model;

use Core\Database;

class Team {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Récupère toutes les équipes avec filtres optionnels
     */
    public function getAllTeams($search = '', $clubId = null, $sportId = null, $gender = null) {
        $sql = "
            SELECT t.*,
                   sec.name AS section_name,
                   c.id AS club_id,
                   c.name AS club_name,
                   c.code AS club_code,
                   c.primary_color AS club_primary_color,
                   c.logo AS club_logo,
                   s.id AS sport_id,
                   s.name AS sport_name,
                   s.icon AS sport_icon,
                   ts.text_code AS status_text_code
            FROM t_sport_team t
            JOIN t_sport_section sec ON t.section_id = sec.id
            JOIN t_sport_club c ON sec.club_id = c.id
            JOIN t_sport_sport s ON sec.sport_id = s.id
            LEFT JOIN t_sport_team_status ts ON t.status_id = ts.id
            WHERE 1=1
        ";

        if (!empty($search)) {
            $sql .= " AND (t.name ILIKE :search OR t.code ILIKE :search OR c.name ILIKE :search OR sec.name ILIKE :search)";
        }
        if (!empty($clubId)) {
            $sql .= " AND c.id = :club_id";
        }
        if (!empty($sportId)) {
            $sql .= " AND s.id = :sport_id";
        }
        if (!empty($gender)) {
            $sql .= " AND t.gender = :gender";
        }

        $sql .= " ORDER BY c.name ASC, s.name ASC, t.name ASC";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        if (!empty($clubId)) {
            $this->db->bind(':club_id', (int)$clubId);
        }
        if (!empty($sportId)) {
            $this->db->bind(':sport_id', (int)$sportId);
        }
        if (!empty($gender)) {
            $this->db->bind(':gender', $gender);
        }

        return $this->db->resultSet();
    }

    /**
     * Récupère une équipe par son ID
     */
    public function getTeamById($id) {
        $this->db->query("
            SELECT t.*,
                   sec.name AS section_name,
                   sec.sport_id,
                   c.id AS club_id,
                   c.name AS club_name,
                   c.code AS club_code,
                   c.logo AS club_logo,
                   c.primary_color AS club_primary_color,
                   c.city_name AS club_city,
                   c.country_code AS club_country,
                   s.name AS sport_name,
                   s.icon AS sport_icon,
                   ts.text_code AS status_text_code,
                   u_c.username AS created_by_name,
                   u_m.username AS modified_by_name
            FROM t_sport_team t
            JOIN t_sport_section sec ON t.section_id = sec.id
            JOIN t_sport_club c ON sec.club_id = c.id
            JOIN t_sport_sport s ON sec.sport_id = s.id
            LEFT JOIN t_sport_team_status ts ON t.status_id = ts.id
            LEFT JOIN t_user_user u_c ON t.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON t.modified_by = u_m.id
            WHERE t.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Récupère une équipe par son code unique
     */
    public function getTeamByCode($code) {
        $this->db->query("SELECT * FROM t_sport_team WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    /**
     * Récupère toutes les sections actives pour le choix déroulant dans le formulaire
     */
    public function getAllSections() {
        $this->db->query("
            SELECT sec.id,
                   sec.name AS section_name,
                   sec.code AS section_code,
                   c.id AS club_id,
                   c.name AS club_name,
                   s.id AS sport_id,
                   s.name AS sport_name,
                   s.icon AS sport_icon
            FROM t_sport_section sec
            JOIN t_sport_club c ON sec.club_id = c.id
            JOIN t_sport_sport s ON sec.sport_id = s.id
            ORDER BY c.name ASC, s.name ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Récupère la liste des statuts d'équipe
     */
    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_team_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    /**
     * Ajoute une nouvelle équipe
     */
    public function addTeam($data) {
        $this->db->query("
            INSERT INTO t_sport_team (
                section_id, code, name, short_name, gender, category, level,
                status_id, created_by, created_at
            ) VALUES (
                :section_id, :code, :name, :short_name, :gender, :category, :level,
                :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");

        $this->bindTeamParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);

        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    /**
     * Met à jour une équipe existante
     */
    public function updateTeam($data) {
        $this->db->query("
            UPDATE t_sport_team
            SET section_id = :section_id,
                name = :name,
                short_name = :short_name,
                gender = :gender,
                category = :category,
                level = :level,
                status_id = :status_id,
                modified_by = :modified_by,
                modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $this->bindTeamParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);

        return $this->db->execute();
    }

    /**
     * Désactive une équipe (soft delete)
     */
    public function softDeleteTeam($id, $userId) {
        $this->db->query("
            UPDATE t_sport_team
            SET status_id = 2,
                modified_by = :user,
                modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    /**
     * Supprime définitivement une équipe
     */
    public function forceDeleteTeam($id) {
        $this->db->query("DELETE FROM t_sport_team WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Bind des paramètres d'équipe
     */
    private function bindTeamParams($data) {
        $this->db->bind(':section_id', (int)$data['section_id']);
        if (isset($data['code'])) {
            $this->db->bind(':code', strtolower(trim($data['code'])));
        }
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':short_name', !empty($data['short_name']) ? trim($data['short_name']) : null);
        $this->db->bind(':gender', !empty($data['gender']) ? trim($data['gender']) : 'M');
        $this->db->bind(':category', !empty($data['category']) ? trim($data['category']) : 'Senior');
        $this->db->bind(':level', !empty($data['level']) ? trim($data['level']) : 'National');
        $this->db->bind(':status_id', !empty($data['status_id']) ? (int)$data['status_id'] : 1);
    }
}
