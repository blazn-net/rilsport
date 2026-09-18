<?php
namespace Module\Sport\Model;

use Core\Database;

class CompetitionEntry {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /** Toutes les inscriptions d'une edition */
    public function getEntriesByEdition($editionId) {
        $this->db->query("
            SELECT en.*,
                   t.name        AS team_name,
                   t.short_name  AS team_short_name,
                   t.code        AS team_code,
                   g.name        AS group_name,
                   g.code        AS group_code,
                   ph.name       AS phase_name
            FROM t_sport_competition_entry en
            LEFT JOIN t_sport_team                 t ON en.team_id  = t.id
            LEFT JOIN t_sport_competition_group    g ON en.group_id  = g.id
            LEFT JOIN t_sport_competition_phase   ph ON g.phase_id   = ph.id
            WHERE en.edition_id = :edition_id
            ORDER BY ph.phase_order ASC, g.group_order ASC, en.entry_order ASC, t.name ASC
        ");
        $this->db->bind(':edition_id', $editionId);
        return $this->db->resultSet();
    }

    /** Equipes disponibles (section correspondant au sport de la competition) */
    public function getAvailableTeams($editionId) {
        $this->db->query("
            SELECT t.id, t.name, t.short_name, t.code,
                   cl.name AS club_name
            FROM t_sport_team t
            JOIN t_sport_section  sec ON t.section_id = sec.id
            JOIN t_sport_club      cl ON sec.club_id   = cl.id
            JOIN t_sport_competition_edition e ON e.id = :edition_id
            JOIN t_sport_competition         c ON c.id = e.competition_id AND c.sport_id = sec.sport_id
            WHERE t.id NOT IN (
                SELECT team_id FROM t_sport_competition_entry WHERE edition_id = :edition_id2
            )
            ORDER BY cl.name ASC, t.name ASC
        ");
        $this->db->bind(':edition_id',  (int)$editionId);
        $this->db->bind(':edition_id2', (int)$editionId);
        return $this->db->resultSet();
    }

    /** Groupes disponibles pour une edition (tous groupes de toutes phases) */
    public function getGroupsForEdition($editionId) {
        $this->db->query("
            SELECT g.id, g.name, g.code, ph.name AS phase_name, ph.phase_order
            FROM t_sport_competition_group g
            JOIN t_sport_competition_phase ph ON g.phase_id = ph.id
            WHERE ph.edition_id = :edition_id
            ORDER BY ph.phase_order ASC, g.group_order ASC
        ");
        $this->db->bind(':edition_id', (int)$editionId);
        return $this->db->resultSet();
    }

    public function getEntryById($id) {
        $this->db->query("
            SELECT en.*,
                   t.name AS team_name,
                   g.name AS group_name
            FROM t_sport_competition_entry en
            LEFT JOIN t_sport_team               t ON en.team_id  = t.id
            LEFT JOIN t_sport_competition_group  g ON en.group_id = g.id
            WHERE en.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function entryExists($editionId, $teamId) {
        $this->db->query("SELECT id FROM t_sport_competition_entry WHERE edition_id = :edition_id AND team_id = :team_id");
        $this->db->bind(':edition_id', (int)$editionId);
        $this->db->bind(':team_id',    (int)$teamId);
        return $this->db->single();
    }

    public function getNextOrder($editionId) {
        $this->db->query("SELECT COALESCE(MAX(entry_order), 0) + 1 AS next_order FROM t_sport_competition_entry WHERE edition_id = :edition_id");
        $this->db->bind(':edition_id', (int)$editionId);
        $row = $this->db->single();
        return $row['next_order'] ?? 1;
    }

    public function addEntry($data) {
        $this->db->query("
            INSERT INTO t_sport_competition_entry (
                edition_id, team_id, group_id, entry_order, status_id, created_by, created_at
            ) VALUES (
                :edition_id, :team_id, :group_id, :entry_order, :status_id, :created_by, CURRENT_TIMESTAMP
            ) RETURNING id
        ");
        $this->db->bind(':edition_id',  (int)$data['edition_id']);
        $this->db->bind(':team_id',     (int)$data['team_id']);
        $this->db->bind(':group_id',    !empty($data['group_id'])    ? (int)$data['group_id']    : null);
        $this->db->bind(':entry_order', !empty($data['entry_order']) ? (int)$data['entry_order'] : 1);
        $this->db->bind(':status_id',   !empty($data['status_id'])   ? (int)$data['status_id']   : 1);
        $this->db->bind(':created_by',  $data['created_by'] ?? null);
        $row = $this->db->single();
        return $row['id'] ?? false;
    }

    /** Met a jour l affectation de groupe */
    public function updateEntry($id, $groupId, $entryOrder, $userId) {
        $this->db->query("
            UPDATE t_sport_competition_entry
            SET group_id    = :group_id,
                entry_order = :entry_order,
                modified_by = :modified_by,
                modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id',          (int)$id);
        $this->db->bind(':group_id',    !empty($groupId)    ? (int)$groupId    : null);
        $this->db->bind(':entry_order', !empty($entryOrder) ? (int)$entryOrder : 1);
        $this->db->bind(':modified_by', $userId);
        return $this->db->execute();
    }

    public function deleteEntry($id) {
        $this->db->query("DELETE FROM t_sport_competition_entry WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /** Batch : inscrit plusieurs equipes d'un coup */
    public function addEntriesBatch(array $teamIds, $editionId, $groupId = null, $userId = null) {
        $nextOrder = $this->getNextOrder($editionId);
        $added = 0;
        foreach ($teamIds as $teamId) {
            if (!$this->entryExists($editionId, $teamId)) {
                $this->addEntry([
                    'edition_id'  => $editionId,
                    'team_id'     => $teamId,
                    'group_id'    => $groupId,
                    'entry_order' => $nextOrder++,
                    'status_id'   => 1,
                    'created_by'  => $userId,
                ]);
                $added++;
            }
        }
        return $added;
    }
}
