<?php
namespace Module\Sport\Model;

use Core\Database;

class CompetitionEdition {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Toutes les editions avec filtres
     */
    public function getAllEditions($search = '', $competitionId = null, $seasonId = null) {
        $sql = "
            SELECT e.*,
                   cs.text_code  AS status_text_code,
                   se.name       AS season_name,
                   c.name        AS competition_name,
                   c.code        AS competition_code,
                   c.type_code   AS competition_type_code,
                   sp.name       AS sport_name,
                   pe.name       AS parent_edition_name,
                   (SELECT COUNT(*) FROM t_sport_competition_phase ph WHERE ph.edition_id = e.id) AS phases_count,
                   (SELECT COUNT(*) FROM t_sport_competition_entry en WHERE en.edition_id = e.id) AS entries_count
            FROM t_sport_competition_edition e
            LEFT JOIN t_sport_competition_status cs ON e.status_id      = cs.id
            LEFT JOIN t_sport_season             se ON e.season_id      = se.id
            LEFT JOIN t_sport_competition         c ON e.competition_id  = c.id
            LEFT JOIN t_sport_sport              sp ON c.sport_id        = sp.id
            LEFT JOIN t_sport_competition_edition pe ON e.parent_edition_id = pe.id
            WHERE 1=1
        ";
        if (!empty($search))        { $sql .= " AND (e.name ILIKE :search OR e.code ILIKE :search OR c.name ILIKE :search)"; }
        if (!empty($competitionId)) { $sql .= " AND e.competition_id = :competition_id"; }
        if (!empty($seasonId))      { $sql .= " AND e.season_id = :season_id"; }
        $sql .= " ORDER BY e.date_start DESC, e.name ASC";

        $this->db->query($sql);
        if (!empty($search))        { $this->db->bind(':search', '%' . $search . '%'); }
        if (!empty($competitionId)) { $this->db->bind(':competition_id', (int)$competitionId); }
        if (!empty($seasonId))      { $this->db->bind(':season_id', (int)$seasonId); }
        return $this->db->resultSet();
    }

    /**
     * Une edition par ID
     */
    public function getEditionById($id) {
        $this->db->query("
            SELECT e.*,
                   cs.text_code  AS status_text_code,
                   se.name       AS season_name,
                   c.name        AS competition_name,
                   c.code        AS competition_code,
                   c.type_code   AS competition_type_code,
                   c.sport_id    AS competition_sport_id,
                   sp.name       AS sport_name,
                   pe.name       AS parent_edition_name,
                   u_c.username  AS created_by_name,
                   u_m.username  AS modified_by_name
            FROM t_sport_competition_edition e
            LEFT JOIN t_sport_competition_status  cs ON e.status_id        = cs.id
            LEFT JOIN t_sport_season              se ON e.season_id         = se.id
            LEFT JOIN t_sport_competition          c ON e.competition_id    = c.id
            LEFT JOIN t_sport_sport               sp ON c.sport_id          = sp.id
            LEFT JOIN t_sport_competition_edition pe ON e.parent_edition_id = pe.id
            LEFT JOIN t_user_user                u_c ON e.created_by        = u_c.id
            LEFT JOIN t_user_user                u_m ON e.modified_by       = u_m.id
            WHERE e.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Une edition par code
     */
    public function getEditionByCode($code) {
        $this->db->query("SELECT * FROM t_sport_competition_edition WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    /**
     * Phases d une edition
     */
    public function getPhasesByEdition($editionId) {
        $this->db->query("
            SELECT ph.*,
                   cs.text_code AS status_text_code,
                   (SELECT COUNT(*) FROM t_sport_competition_group g WHERE g.phase_id = ph.id) AS groups_count,
                   (SELECT COUNT(*) FROM t_sport_competition_round r WHERE r.phase_id = ph.id) AS rounds_count
            FROM t_sport_competition_phase ph
            LEFT JOIN t_sport_competition_status cs ON ph.status_id = cs.id
            WHERE ph.edition_id = :edition_id
            ORDER BY ph.phase_order ASC
        ");
        $this->db->bind(':edition_id', $editionId);
        return $this->db->resultSet();
    }

    /**
     * Inscriptions d une edition (avec groupe)
     */
    public function getEntriesByEdition($editionId) {
        $this->db->query("
            SELECT en.*,
                   t.name        AS team_name,
                   t.short_name  AS team_short_name,
                   sec.sport_id  AS sport_id,
                   g.name        AS group_name,
                   g.code        AS group_code
            FROM t_sport_competition_entry en
            LEFT JOIN t_sport_team                 t ON en.team_id  = t.id
            LEFT JOIN t_sport_section            sec ON t.section_id = sec.id
            LEFT JOIN t_sport_competition_group    g ON en.group_id  = g.id
            WHERE en.edition_id = :edition_id
            ORDER BY g.group_order ASC, en.entry_order ASC, t.name ASC
        ");
        $this->db->bind(':edition_id', $editionId);
        return $this->db->resultSet();
    }

    /**
     * Sous-editions (categories) d une edition parente
     */
    public function getSubEditions($parentEditionId) {
        $this->db->query("
            SELECT e.*,
                   cs.text_code AS status_text_code
            FROM t_sport_competition_edition e
            LEFT JOIN t_sport_competition_status cs ON e.status_id = cs.id
            WHERE e.parent_edition_id = :parent_id
            ORDER BY e.name ASC
        ");
        $this->db->bind(':parent_id', $parentEditionId);
        return $this->db->resultSet();
    }

    /**
     * Statuts
     */
    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_competition_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    /**
     * Saisons disponibles
     */
    public function getAllSeasons() {
        $this->db->query("SELECT * FROM t_sport_season ORDER BY date_start DESC");
        return $this->db->resultSet();
    }

    /**
     * Editions parentes possibles (excluant la courante)
     */
    public function getParentEditionCandidates($competitionId, $excludeId = null) {
        $sql = "
            SELECT id, name, code
            FROM t_sport_competition_edition
            WHERE competition_id = :competition_id
              AND parent_edition_id IS NULL
        ";
        if ($excludeId) { $sql .= " AND id != :exclude_id"; }
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        $this->db->bind(':competition_id', (int)$competitionId);
        if ($excludeId) { $this->db->bind(':exclude_id', (int)$excludeId); }
        return $this->db->resultSet();
    }

    /**
     * Cree une edition
     */
    public function addEdition($data) {
        $this->db->query("
            INSERT INTO t_sport_competition_edition (
                competition_id, parent_edition_id, season_id, code, name,
                edition_number, date_start, date_end, status_id, created_by, created_at
            ) VALUES (
                :competition_id, :parent_edition_id, :season_id, :code, :name,
                :edition_number, :date_start, :date_end, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");
        $this->bindParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    /**
     * Met a jour une edition
     */
    public function updateEdition($data) {
        $this->db->query("
            UPDATE t_sport_competition_edition
            SET competition_id    = :competition_id,
                parent_edition_id = :parent_edition_id,
                season_id         = :season_id,
                name              = :name,
                edition_number    = :edition_number,
                date_start        = :date_start,
                date_end          = :date_end,
                status_id         = :status_id,
                modified_by       = :modified_by,
                modified_at       = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->bindParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    /**
     * Soft delete
     */
    public function softDeleteEdition($id, $userId) {
        $this->db->query("
            UPDATE t_sport_competition_edition
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
    public function forceDeleteEdition($id) {
        $this->db->query("DELETE FROM t_sport_competition_edition WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Bind parametres communs
     */
    private function bindParams($data) {
        if (isset($data['code'])) { $this->db->bind(':code', strtolower(trim($data['code']))); }
        $this->db->bind(':competition_id',    (int)$data['competition_id']);
        $this->db->bind(':parent_edition_id', !empty($data['parent_edition_id']) ? (int)$data['parent_edition_id'] : null);
        $this->db->bind(':season_id',         !empty($data['season_id'])         ? (int)$data['season_id']         : null);
        $this->db->bind(':name',              trim($data['name']));
        $this->db->bind(':edition_number',    !empty($data['edition_number'])     ? (int)$data['edition_number']    : null);
        $this->db->bind(':date_start',        !empty($data['date_start'])         ? $data['date_start']             : null);
        $this->db->bind(':date_end',          !empty($data['date_end'])           ? $data['date_end']               : null);
        $this->db->bind(':status_id',         !empty($data['status_id'])          ? (int)$data['status_id']         : 1);
    }
}
