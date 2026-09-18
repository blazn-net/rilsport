<?php
namespace Module\Sport\Model;

use Core\Database;

class CompetitionGroup {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getGroupById($id) {
        $this->db->query("
            SELECT g.*,
                   ph.name        AS phase_name,
                   ph.edition_id,
                   ph.phase_type,
                   e.name         AS edition_name,
                   e.competition_id,
                   c.name         AS competition_name,
                   pg.name        AS parent_group_name,
                   u_c.username   AS created_by_name,
                   u_m.username   AS modified_by_name
            FROM t_sport_competition_group g
            LEFT JOIN t_sport_competition_phase   ph ON g.phase_id        = ph.id
            LEFT JOIN t_sport_competition_edition  e ON ph.edition_id      = e.id
            LEFT JOIN t_sport_competition          c ON e.competition_id   = c.id
            LEFT JOIN t_sport_competition_group   pg ON g.parent_group_id  = pg.id
            LEFT JOIN t_user_user                u_c ON g.created_by       = u_c.id
            LEFT JOIN t_user_user                u_m ON g.modified_by      = u_m.id
            WHERE g.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getGroupsByPhase($phaseId) {
        $this->db->query("
            SELECT g.*,
                   pg.name AS parent_group_name
            FROM t_sport_competition_group g
            LEFT JOIN t_sport_competition_group pg ON g.parent_group_id = pg.id
            WHERE g.phase_id = :phase_id
            ORDER BY g.group_order ASC, g.name ASC
        ");
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->resultSet();
    }

    /** Entries (teams) d'un groupe */
    public function getEntriesByGroup($groupId) {
        $this->db->query("
            SELECT en.*,
                   t.name       AS team_name,
                   t.short_name AS team_short_name
            FROM t_sport_competition_entry en
            LEFT JOIN t_sport_team t ON en.team_id = t.id
            WHERE en.group_id = :group_id
            ORDER BY en.entry_order ASC, t.name ASC
        ");
        $this->db->bind(':group_id', $groupId);
        return $this->db->resultSet();
    }

    /** Candidats pour parent_group (même phase, pas soi-même) */
    public function getParentGroupCandidates($phaseId, $excludeId = null) {
        $sql = "SELECT id, name, code FROM t_sport_competition_group WHERE phase_id = :phase_id AND parent_group_id IS NULL";
        if ($excludeId) { $sql .= " AND id != :exclude_id"; }
        $sql .= " ORDER BY group_order ASC, name ASC";
        $this->db->query($sql);
        $this->db->bind(':phase_id', (int)$phaseId);
        if ($excludeId) { $this->db->bind(':exclude_id', (int)$excludeId); }
        return $this->db->resultSet();
    }

    public function getNextOrder($phaseId) {
        $this->db->query("SELECT COALESCE(MAX(group_order), 0) + 1 AS next_order FROM t_sport_competition_group WHERE phase_id = :phase_id");
        $this->db->bind(':phase_id', $phaseId);
        $row = $this->db->single();
        return $row['next_order'] ?? 1;
    }

    public function getAllStatuses() {
        $this->db->query("SELECT * FROM t_sport_competition_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    public function addGroup($data) {
        $this->db->query("
            INSERT INTO t_sport_competition_group (
                phase_id, parent_group_id, code, name, group_order, status_id, created_by, created_at
            ) VALUES (
                :phase_id, :parent_group_id, :code, :name, :group_order, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");
        $this->bindParams($data);
        $this->db->bind(':created_by', $data['created_by'] ?? null);
        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    public function updateGroup($data) {
        $this->db->query("
            UPDATE t_sport_competition_group
            SET parent_group_id = :parent_group_id,
                name            = :name,
                group_order     = :group_order,
                status_id       = :status_id,
                modified_by     = :modified_by,
                modified_at     = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->bindParams($data);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':modified_by', $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    public function softDeleteGroup($id, $userId) {
        $this->db->query("UPDATE t_sport_competition_group SET status_id = 2, modified_by = :user, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':user', $userId);
        return $this->db->execute();
    }

    public function forceDeleteGroup($id) {
        $this->db->query("DELETE FROM t_sport_competition_group WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    private function bindParams($data) {
        if (isset($data['code'])) { $this->db->bind(':code', strtolower(trim($data['code']))); }
        $this->db->bind(':phase_id',        (int)$data['phase_id']);
        $this->db->bind(':parent_group_id', !empty($data['parent_group_id']) ? (int)$data['parent_group_id'] : null);
        $this->db->bind(':name',            trim($data['name']));
        $this->db->bind(':group_order',     !empty($data['group_order']) ? (int)$data['group_order'] : 1);
        $this->db->bind(':status_id',       !empty($data['status_id'])   ? (int)$data['status_id']   : 1);
    }
}
