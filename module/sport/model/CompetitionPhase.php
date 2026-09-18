<?php
namespace Module\Sport\Model;

use Core\Database;

class CompetitionPhase {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getPhaseById($id) {
        $this->db->query("
            SELECT ph.*,
                   cs.text_code  AS status_text_code,
                   e.name        AS edition_name,
                   e.competition_id,
                   c.name        AS competition_name,
                   u_c.username  AS created_by_name,
                   u_m.username  AS modified_by_name
            FROM t_sport_competition_phase ph
            LEFT JOIN t_sport_competition_status  cs ON ph.status_id   = cs.id
            LEFT JOIN t_sport_competition_edition  e ON ph.edition_id  = e.id
            LEFT JOIN t_sport_competition          c ON e.competition_id = c.id
            LEFT JOIN t_user_user                u_c ON ph.created_by   = u_c.id
            LEFT JOIN t_user_user                u_m ON ph.modified_by  = u_m.id
            WHERE ph.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

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

    public function getGroupsByPhase($phaseId) {
        $this->db->query("
            SELECT g.*,
                   pg.name AS parent_group_name,
                   (SELECT COUNT(*) FROM t_sport_competition_round r WHERE r.phase_id = g.phase_id AND r.group_id = g.id) AS rounds_count,
                   (SELECT COUNT(*) FROM t_sport_competition_entry en
                    JOIN t_sport_competition_edition ed ON en.edition_id = ed.id
                    JOIN t_sport_competition_phase ph ON ph.edition_id = ed.id AND ph.id = g.phase_id
                    WHERE en.group_id = g.id) AS entries_count
            FROM t_sport_competition_group g
            LEFT JOIN t_sport_competition_group pg ON g.parent_group_id = pg.id
            WHERE g.phase_id = :phase_id
            ORDER BY g.group_order ASC, g.name ASC
        ");
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->resultSet();
    }

    public function getRoundsByPhase($phaseId) {
        $this->db->query("
            SELECT r.*,
                   g.name AS group_name
            FROM t_sport_competition_round r
            LEFT JOIN t_sport_competition_group g ON r.group_id = g.id
            WHERE r.phase_id = :phase_id
            ORDER BY r.round_order ASC
        ");
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->resultSet();
    }

    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_competition_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    public function getNextOrder($editionId) {
        $this->db->query("SELECT COALESCE(MAX(phase_order), 0) + 1 AS next_order FROM t_sport_competition_phase WHERE edition_id = :edition_id");
        $this->db->bind(':edition_id', $editionId);
        $row = $this->db->single();
        return $row['next_order'] ?? 1;
    }

    public function addPhase($data) {
        $this->db->query("
            INSERT INTO t_sport_competition_phase (
                edition_id, code, name, phase_type, phase_order,
                date_start, date_end, status_id, created_by, created_at
            ) VALUES (
                :edition_id, :code, :name, :phase_type, :phase_order,
                :date_start, :date_end, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");
        $this->bindParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    public function updatePhase($data) {
        $this->db->query("
            UPDATE t_sport_competition_phase
            SET name         = :name,
                phase_type   = :phase_type,
                phase_order  = :phase_order,
                date_start   = :date_start,
                date_end     = :date_end,
                status_id    = :status_id,
                modified_by  = :modified_by,
                modified_at  = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->bindParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    public function softDeletePhase($id, $userId) {
        $this->db->query("UPDATE t_sport_competition_phase SET status_id = 2, modified_by = :user, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeletePhase($id) {
        $this->db->query("DELETE FROM t_sport_competition_phase WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    private function bindParams($data) {
        if (isset($data['code'])) { $this->db->bind(':code', strtolower(trim($data['code']))); }
        $this->db->bind(':edition_id',  (int)$data['edition_id']);
        $this->db->bind(':name',        trim($data['name']));
        $this->db->bind(':phase_type',  trim($data['phase_type'] ?? 'league'));
        $this->db->bind(':phase_order', !empty($data['phase_order']) ? (int)$data['phase_order'] : 1);
        $this->db->bind(':date_start',  !empty($data['date_start']) ? $data['date_start'] : null);
        $this->db->bind(':date_end',    !empty($data['date_end'])   ? $data['date_end']   : null);
        $this->db->bind(':status_id',   !empty($data['status_id'])  ? (int)$data['status_id'] : 1);
    }
}
