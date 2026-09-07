<?php
namespace Module\Sport\Model;

use Core\Database;

class Club {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Récupère tous les clubs avec filtres optionnels
     */
    public function getAllClubs($search = '', $countryCode = '', $sportId = null) {
        $sql = "
            SELECT c.*,
                   cs.text_code AS status_text_code,
                   COALESCE(z.name_default, c.country_code) AS country_name,
                   (
                       SELECT STRING_AGG(s.name, ', ' ORDER BY s.name)
                       FROM t_sport_section sec
                       JOIN t_sport_sport s ON sec.sport_id = s.id
                       WHERE sec.club_id = c.id
                   ) AS sports_names,
                   (
                       SELECT COUNT(*)
                       FROM t_sport_section sec
                       WHERE sec.club_id = c.id
                   ) AS sections_count
            FROM t_sport_club c
            LEFT JOIN t_sport_club_status cs ON c.status_id = cs.id
            LEFT JOIN t_zone_zone z ON z.country_code = c.country_code AND z.type_code = 'country'
            WHERE 1=1
        ";

        if (!empty($search)) {
            $sql .= " AND (c.name ILIKE :search OR c.code ILIKE :search OR c.city_name ILIKE :search OR c.short_name ILIKE :search)";
        }
        if (!empty($countryCode)) {
            $sql .= " AND c.country_code = :country_code";
        }
        if (!empty($sportId)) {
            $sql .= " AND EXISTS (SELECT 1 FROM t_sport_section sec WHERE sec.club_id = c.id AND sec.sport_id = :sport_id)";
        }

        $sql .= " ORDER BY c.name ASC";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        if (!empty($countryCode)) {
            $this->db->bind(':country_code', $countryCode);
        }
        if (!empty($sportId)) {
            $this->db->bind(':sport_id', (int)$sportId);
        }

        return $this->db->resultSet();
    }

    /**
     * Récupère un club par son identifiant unique
     */
    public function getClubById($id) {
        $this->db->query("
            SELECT c.*,
                   cs.text_code AS status_text_code,
                   COALESCE(z.name_default, c.country_code) AS country_name,
                   u_c.username AS created_by_name,
                   u_m.username AS modified_by_name
            FROM t_sport_club c
            LEFT JOIN t_sport_club_status cs ON c.status_id = cs.id
            LEFT JOIN t_zone_zone z ON z.country_code = c.country_code AND z.type_code = 'country'
            LEFT JOIN t_user_user u_c ON c.created_by = u_c.id
            LEFT JOIN t_user_user u_m ON c.modified_by = u_m.id
            WHERE c.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Récupère un club par son code / slug
     */
    public function getClubByCode($code) {
        $this->db->query("SELECT * FROM t_sport_club WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    /**
     * Récupère les sections (sports) d'un club
     */
    public function getClubSections($clubId) {
        $this->db->query("
            SELECT sec.*,
                   s.name AS sport_name,
                   s.code AS sport_code,
                   s.icon AS sport_icon,
                   cs.text_code AS status_text_code
            FROM t_sport_section sec
            JOIN t_sport_sport s ON sec.sport_id = s.id
            LEFT JOIN t_sport_club_status cs ON sec.status_id = cs.id
            WHERE sec.club_id = :club_id
            ORDER BY s.name ASC
        ");
        $this->db->bind(':club_id', $clubId);
        return $this->db->resultSet();
    }

    /**
     * Récupère la liste des statuts possibles pour un club
     */
    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_club_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    /**
     * Récupère les pays disponibles depuis la base de données
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
                ['country_code' => 'BE', 'name' => 'Belgique'],
                ['country_code' => 'CH', 'name' => 'Suisse'],
                ['country_code' => 'CA', 'name' => 'Canada'],
                ['country_code' => 'US', 'name' => 'États-Unis']
            ];
        }
        return $countries;
    }

    /**
     * Ajoute un club
     */
    public function addClub($data) {
        $this->db->query("
            INSERT INTO t_sport_club (
                code, name, short_name, acronym, foundation_year, logo,
                primary_color, secondary_color, country_code, city_name,
                city_id, postal_code, address, website, email, phone,
                description, status_id, created_by, created_at
            ) VALUES (
                :code, :name, :short_name, :acronym, :foundation_year, :logo,
                :primary_color, :secondary_color, :country_code, :city_name,
                :city_id, :postal_code, :address, :website, :email, :phone,
                :description, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");

        $this->bindClubParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);

        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    /**
     * Met à jour un club existant
     */
    public function updateClub($data) {
        $sql = "
            UPDATE t_sport_club
            SET name = :name,
                short_name = :short_name,
                acronym = :acronym,
                foundation_year = :foundation_year,
                primary_color = :primary_color,
                secondary_color = :secondary_color,
                country_code = :country_code,
                city_name = :city_name,
                city_id = :city_id,
                postal_code = :postal_code,
                address = :address,
                website = :website,
                email = :email,
                phone = :phone,
                description = :description,
                status_id = :status_id,
                modified_by = :modified_by,
                modified_at = CURRENT_TIMESTAMP
        ";

        if (array_key_exists('logo', $data)) {
            $sql .= ", logo = :logo";
        }

        $sql .= " WHERE id = :id";

        $this->db->query($sql);
        $this->bindClubParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);

        if (array_key_exists('logo', $data)) {
            $this->db->bind(':logo', $data['logo']);
        }

        return $this->db->execute();
    }

    /**
     * Synchronise les sections d'un club avec une liste de sport IDs
     */
    public function syncSections($clubId, array $sportIds, $userId = null) {
        $club = $this->getClubById($clubId);
        if (!$club) return false;

        $existingSections = $this->getClubSections($clubId);
        $existingSportIds = array_column($existingSections, 'sport_id');

        // Ajout des nouvelles disciplines
        foreach ($sportIds as $sportId) {
            $sportId = (int)$sportId;
            if (!in_array($sportId, $existingSportIds)) {
                $this->db->query("SELECT * FROM t_sport_sport WHERE id = :id");
                $this->db->bind(':id', $sportId);
                $sport = $this->db->single();
                if ($sport) {
                    $secCode = $club['code'] . '-' . $sport['code'];
                    $secName = $club['name'] . ' ' . $sport['name'];

                    $this->db->query("
                        INSERT INTO t_sport_section (
                            club_id, sport_id, code, name, creation_year, status_id, created_by, created_at
                        ) VALUES (
                            :club_id, :sport_id, :code, :name, :creation_year, 1, :created_by, CURRENT_TIMESTAMP
                        ) ON CONFLICT (club_id, sport_id) DO NOTHING
                    ");
                    $this->db->bind(':club_id', $clubId);
                    $this->db->bind(':sport_id', $sportId);
                    $this->db->bind(':code', $secCode);
                    $this->db->bind(':name', $secName);
                    $this->db->bind(':creation_year', $club['foundation_year']);
                    $this->db->bind(':created_by', $userId);
                    $this->db->execute();
                }
            }
        }

        // Suppression des disciplines retirées
        foreach ($existingSections as $sec) {
            if (!in_array((int)$sec['sport_id'], $sportIds)) {
                $this->db->query("DELETE FROM t_sport_section WHERE id = :id");
                $this->db->bind(':id', $sec['id']);
                $this->db->execute();
            }
        }

        return true;
    }

    /**
     * Désactivation (soft delete) d'un club
     */
    public function softDeleteClub($id, $userId) {
        $this->db->query("
            UPDATE t_sport_club
            SET status_id = 3,
                modified_by = :user,
                modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    /**
     * Suppression physique
     */
    public function forceDeleteClub($id) {
        $this->db->query("DELETE FROM t_sport_club WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Bind des paramètres communs pour add/update
     */
    private function bindClubParams($data) {
        if (isset($data['code'])) {
            $this->db->bind(':code', strtolower(trim($data['code'])));
        }
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':short_name', !empty($data['short_name']) ? trim($data['short_name']) : null);
        $this->db->bind(':acronym', !empty($data['acronym']) ? strtoupper(trim($data['acronym'])) : null);
        $this->db->bind(':foundation_year', !empty($data['foundation_year']) ? (int)$data['foundation_year'] : null);
        if (array_key_exists('logo', $data)) {
            $this->db->bind(':logo', !empty($data['logo']) ? trim($data['logo']) : null);
        }
        $this->db->bind(':primary_color', !empty($data['primary_color']) ? trim($data['primary_color']) : null);
        $this->db->bind(':secondary_color', !empty($data['secondary_color']) ? trim($data['secondary_color']) : null);
        $this->db->bind(':country_code', strtoupper(trim($data['country_code'])));
        $this->db->bind(':city_name', trim($data['city_name']));
        $this->db->bind(':city_id', !empty($data['city_id']) ? (int)$data['city_id'] : null);
        $this->db->bind(':postal_code', !empty($data['postal_code']) ? trim($data['postal_code']) : null);
        $this->db->bind(':address', !empty($data['address']) ? trim($data['address']) : null);
        $this->db->bind(':website', !empty($data['website']) ? trim($data['website']) : null);
        $this->db->bind(':email', !empty($data['email']) ? trim($data['email']) : null);
        $this->db->bind(':phone', !empty($data['phone']) ? trim($data['phone']) : null);
        $this->db->bind(':description', !empty($data['description']) ? trim($data['description']) : null);
        $this->db->bind(':status_id', !empty($data['status_id']) ? (int)$data['status_id'] : 1);
    }
}
