<?php
namespace Module\Sport\Model;

use Core\Database;

class Competition {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Récupère toutes les compétitions avec filtres optionnels
     */
    public function getAllCompetitions($search = '', $sportId = null, $typeCode = '') {
        $sql = "
            SELECT c.*,
                   ct.text_code AS type_text_code,
                   cs.text_code AS status_text_code,
                   s.name       AS sport_name,
                   s.code       AS sport_code,
                   COALESCE(z.name_default, c.country_code) AS country_name,
                   (SELECT COUNT(*) FROM t_sport_competition_edition e WHERE e.competition_id = c.id) AS editions_count
            FROM t_sport_competition c
            LEFT JOIN t_sport_competition_type   ct ON c.type_code  = ct.code
            LEFT JOIN t_sport_competition_status cs ON c.status_id  = cs.id
            LEFT JOIN t_sport_sport              s  ON c.sport_id   = s.id
            LEFT JOIN t_zone_zone                z  ON z.country_code = c.country_code AND z.type_code = 'country'
            WHERE 1=1
        ";

        if (!empty($search))   { $sql .= " AND (c.name ILIKE :search OR c.code ILIKE :search OR c.short_name ILIKE :search OR c.acronym ILIKE :search)"; }
        if (!empty($sportId))  { $sql .= " AND c.sport_id = :sport_id"; }
        if (!empty($typeCode)) { $sql .= " AND c.type_code = :type_code"; }

        $sql .= " ORDER BY s.name ASC, c.name ASC";

        $this->db->query($sql);

        if (!empty($search))   { $this->db->bind(':search', '%' . $search . '%'); }
        if (!empty($sportId))  { $this->db->bind(':sport_id', (int)$sportId); }
        if (!empty($typeCode)) { $this->db->bind(':type_code', $typeCode); }

        return $this->db->resultSet();
    }

    /**
     * Récupère une compétition par son ID
     */
    public function getCompetitionById($id) {
        $this->db->query("
            SELECT c.*,
                   ct.text_code AS type_text_code,
                   cs.text_code AS status_text_code,
                   s.name       AS sport_name,
                   s.icon       AS sport_icon,
                   COALESCE(z.name_default, c.country_code) AS country_name,
                   u_c.username AS created_by_name,
                   u_m.username AS modified_by_name
            FROM t_sport_competition c
            LEFT JOIN t_sport_competition_type   ct ON c.type_code  = ct.code
            LEFT JOIN t_sport_competition_status cs ON c.status_id  = cs.id
            LEFT JOIN t_sport_sport              s  ON c.sport_id   = s.id
            LEFT JOIN t_zone_zone                z  ON z.country_code = c.country_code AND z.type_code = 'country'
            LEFT JOIN t_user_user               u_c ON c.created_by  = u_c.id
            LEFT JOIN t_user_user               u_m ON c.modified_by = u_m.id
            WHERE c.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Récupère une compétition par son code
     */
    public function getCompetitionByCode($code) {
        $this->db->query("SELECT * FROM t_sport_competition WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    /**
     * Récupère les éditions principales d'une compétition
     */
    public function getEditionsByCompetition($competitionId) {
        $this->db->query("
            SELECT e.*,
                   cs.text_code AS status_text_code,
                   se.name      AS season_name
            FROM t_sport_competition_edition e
            LEFT JOIN t_sport_competition_status cs ON e.status_id = cs.id
            LEFT JOIN t_sport_season             se ON e.season_id = se.id
            WHERE e.competition_id = :competition_id
              AND e.parent_edition_id IS NULL
            ORDER BY e.date_start DESC, e.name ASC
        ");
        $this->db->bind(':competition_id', $competitionId);
        return $this->db->resultSet();
    }

    /**
     * Récupère tous les types de compétitions
     */
    public function getAllTypes() {
        $this->db->query("SELECT * FROM t_sport_competition_type ORDER BY code ASC");
        return $this->db->resultSet();
    }

    /**
     * Récupère tous les statuts
     */
    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_competition_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    /**
     * Récupère les pays depuis la zone
     */
    public function getAllCountries() {
        $this->db->query("
            SELECT DISTINCT country_code, name_default AS name
            FROM t_zone_zone
            WHERE type_code = 'country' AND country_code IS NOT NULL
            ORDER BY name_default ASC
        ");
        $countries = $this->db->resultSet();
        if (empty($countries)) {
            return [
                ['country_code' => 'FR', 'name' => 'France'],
                ['country_code' => 'ES', 'name' => 'Espagne'],
                ['country_code' => 'IT', 'name' => 'Italie'],
                ['country_code' => 'DE', 'name' => 'Allemagne'],
                ['country_code' => 'GB', 'name' => 'Royaume-Uni'],
            ];
        }
        return $countries;
    }

    /**
     * Crée une compétition
     */
    public function addCompetition($data) {
        $this->db->query("
            INSERT INTO t_sport_competition (
                code, name, short_name, acronym, sport_id, type_code,
                logo, country_code, description, status_id, created_by, created_at
            ) VALUES (
                :code, :name, :short_name, :acronym, :sport_id, :type_code,
                :logo, :country_code, :description, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");
        $this->bindParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    /**
     * Met à jour une compétition
     */
    public function updateCompetition($data) {
        $sql = "
            UPDATE t_sport_competition
            SET name         = :name,
                short_name   = :short_name,
                acronym      = :acronym,
                sport_id     = :sport_id,
                type_code    = :type_code,
                country_code = :country_code,
                description  = :description,
                status_id    = :status_id,
                modified_by  = :modified_by,
                modified_at  = CURRENT_TIMESTAMP
        ";
        if (array_key_exists('logo', $data)) { $sql .= ", logo = :logo"; }
        $sql .= " WHERE id = :id";

        $this->db->query($sql);
        $this->bindParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        if (array_key_exists('logo', $data)) { $this->db->bind(':logo', $data['logo']); }

        return $this->db->execute();
    }

    /**
     * Désactivation (soft delete)
     */
    public function softDeleteCompetition($id, $userId) {
        $this->db->query("
            UPDATE t_sport_competition
            SET status_id = 2, modified_by = :user, modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    /**
     * Suppression physique
     */
    public function forceDeleteCompetition($id) {
        $this->db->query("DELETE FROM t_sport_competition WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Bind des paramètres communs add/update
     */
    private function bindParams($data) {
        if (isset($data['code'])) { $this->db->bind(':code', strtolower(trim($data['code']))); }
        $this->db->bind(':name',         trim($data['name']));
        $this->db->bind(':short_name',   !empty($data['short_name'])   ? trim($data['short_name'])                  : null);
        $this->db->bind(':acronym',      !empty($data['acronym'])      ? strtoupper(trim($data['acronym']))          : null);
        $this->db->bind(':sport_id',     (int)$data['sport_id']);
        $this->db->bind(':type_code',    trim($data['type_code']));
        $this->db->bind(':country_code', !empty($data['country_code']) ? strtoupper(trim($data['country_code']))    : null);
        $this->db->bind(':description',  !empty($data['description'])  ? trim($data['description'])                 : null);
        $this->db->bind(':status_id',    !empty($data['status_id'])    ? (int)$data['status_id']                    : 1);
        if (array_key_exists('logo', $data)) {
            $this->db->bind(':logo', !empty($data['logo']) ? trim($data['logo']) : null);
        }
    }
}
