<?php
namespace Module\Sport\Model;

use Core\Database;

class SportMatch {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Récupère tous les matchs avec filtres optionnels
     */
    public function getAllMatches(string $search = '', $competitionId = null, $roundId = null, $statusId = null) {
        $sql = "
            SELECT m.*,
                   r.name             AS round_name,
                   r.code             AS round_code,
                   r.round_order,
                   r.group_id,
                   r.phase_id,
                   g.name             AS group_name,
                   ph.name            AS phase_name,
                   ed.id              AS edition_id,
                   ed.name            AS edition_name,
                   comp.id            AS competition_id,
                   comp.name          AS competition_name,
                   ht.name            AS home_team_name,
                   ht.code            AS home_team_code,
                   hc.logo            AS home_club_logo,
                   at.name            AS away_team_name,
                   at.code            AS away_team_code,
                   ac.logo            AS away_club_logo,
                   ms.text_code       AS status_text_code,
                   u_c.username       AS created_by_name,
                   u_m.username       AS modified_by_name
            FROM t_sport_match m
            INNER JOIN t_sport_competition_round   r    ON m.round_id     = r.id
            LEFT  JOIN t_sport_competition_group   g    ON r.group_id     = g.id
            LEFT  JOIN t_sport_competition_phase   ph   ON r.phase_id     = ph.id
            LEFT  JOIN t_sport_competition_edition ed   ON ph.edition_id  = ed.id
            LEFT  JOIN t_sport_competition         comp ON ed.competition_id = comp.id
            LEFT  JOIN t_sport_team                ht   ON m.home_team_id = ht.id
            LEFT  JOIN t_sport_section             hsec ON ht.section_id  = hsec.id
            LEFT  JOIN t_sport_club                hc   ON hsec.club_id   = hc.id
            LEFT  JOIN t_sport_team                at   ON m.away_team_id = at.id
            LEFT  JOIN t_sport_section             asec ON at.section_id  = asec.id
            LEFT  JOIN t_sport_club                ac   ON asec.club_id   = ac.id
            LEFT  JOIN t_sport_match_status        ms   ON m.status_id    = ms.id
            LEFT  JOIN t_user_user                 u_c  ON m.created_by   = u_c.id
            LEFT  JOIN t_user_user                 u_m  ON m.modified_by  = u_m.id
            WHERE 1=1
        ";

        $params = [];

        if ($search !== '') {
            $sql .= " AND (ht.name ILIKE :s1 OR at.name ILIKE :s2 OR m.venue_name ILIKE :s3 OR comp.name ILIKE :s4)";
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
            $params[':s3'] = '%' . $search . '%';
            $params[':s4'] = '%' . $search . '%';
        }

        if (!empty($competitionId)) {
            $sql .= " AND comp.id = :comp_id";
            $params[':comp_id'] = (int)$competitionId;
        }

        if (!empty($roundId)) {
            $sql .= " AND m.round_id = :round_id";
            $params[':round_id'] = (int)$roundId;
        }

        if (!empty($statusId)) {
            $sql .= " AND m.status_id = :status_id";
            $params[':status_id'] = (int)$statusId;
        }

        $sql .= " ORDER BY m.match_date DESC NULLS LAST, r.round_order ASC, m.match_order ASC, m.id DESC";

        $this->db->query($sql);
        foreach ($params as $param => $val) {
            $this->db->bind($param, $val);
        }

        return $this->db->resultSet();
    }

    /**
     * Récupère un match par son identifiant
     */
    public function getMatchById(int $id) {
        $this->db->query("
            SELECT m.*,
                   r.name             AS round_name,
                   r.code             AS round_code,
                   r.round_order,
                   r.group_id,
                   r.phase_id,
                   g.name             AS group_name,
                   ph.name            AS phase_name,
                   ed.id              AS edition_id,
                   ed.name            AS edition_name,
                   comp.id            AS competition_id,
                   comp.name          AS competition_name,
                   ht.name            AS home_team_name,
                   ht.code            AS home_team_code,
                   hc.logo            AS home_club_logo,
                   at.name            AS away_team_name,
                   at.code            AS away_team_code,
                   ac.logo            AS away_club_logo,
                   ms.text_code       AS status_text_code,
                   u_c.username       AS created_by_name,
                   u_m.username       AS modified_by_name
            FROM t_sport_match m
            INNER JOIN t_sport_competition_round   r    ON m.round_id     = r.id
            LEFT  JOIN t_sport_competition_group   g    ON r.group_id     = g.id
            LEFT  JOIN t_sport_competition_phase   ph   ON r.phase_id     = ph.id
            LEFT  JOIN t_sport_competition_edition ed   ON ph.edition_id  = ed.id
            LEFT  JOIN t_sport_competition         comp ON ed.competition_id = comp.id
            LEFT  JOIN t_sport_team                ht   ON m.home_team_id = ht.id
            LEFT  JOIN t_sport_section             hsec ON ht.section_id  = hsec.id
            LEFT  JOIN t_sport_club                hc   ON hsec.club_id   = hc.id
            LEFT  JOIN t_sport_team                at   ON m.away_team_id = at.id
            LEFT  JOIN t_sport_section             asec ON at.section_id  = asec.id
            LEFT  JOIN t_sport_club                ac   ON asec.club_id   = ac.id
            LEFT  JOIN t_sport_match_status        ms   ON m.status_id    = ms.id
            LEFT  JOIN t_user_user                 u_c  ON m.created_by   = u_c.id
            LEFT  JOIN t_user_user                 u_m  ON m.modified_by  = u_m.id
            WHERE m.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Crée un nouveau match
     */
    public function addMatch(array $data, ?int $userId = null): int {
        $this->db->query("
            INSERT INTO t_sport_match (
                round_id, home_team_id, away_team_id, match_order,
                match_date, venue_name, score_home, score_away, score_detail,
                note, status_id, created_by
            ) VALUES (
                :round_id, :home_team_id, :away_team_id, :match_order,
                :match_date, :venue_name, :score_home, :score_away, :score_detail,
                :note, :status_id, :created_by
            ) RETURNING id
        ");

        $scoreDetail = !empty($data['score_detail']) ? (is_string($data['score_detail']) ? $data['score_detail'] : json_encode($data['score_detail'])) : null;
        $matchDate   = !empty($data['match_date']) ? $data['match_date'] : null;

        $this->db->bind(':round_id',     (int)$data['round_id']);
        $this->db->bind(':home_team_id', !empty($data['home_team_id']) ? (int)$data['home_team_id'] : null);
        $this->db->bind(':away_team_id', !empty($data['away_team_id']) ? (int)$data['away_team_id'] : null);
        $this->db->bind(':match_order',  !empty($data['match_order']) ? (int)$data['match_order'] : 1);
        $this->db->bind(':match_date',   $matchDate);
        $this->db->bind(':venue_name',   !empty($data['venue_name']) ? trim($data['venue_name']) : null);
        $this->db->bind(':score_home',   ($data['score_home'] !== '' && $data['score_home'] !== null) ? (int)$data['score_home'] : null);
        $this->db->bind(':score_away',   ($data['score_away'] !== '' && $data['score_away'] !== null) ? (int)$data['score_away'] : null);
        $this->db->bind(':score_detail', $scoreDetail);
        $this->db->bind(':note',         !empty($data['note']) ? trim($data['note']) : null);
        $this->db->bind(':status_id',    !empty($data['status_id']) ? (int)$data['status_id'] : 1);
        $this->db->bind(':created_by',   $userId);

        $row = $this->db->single();
        return (int)($row['id'] ?? 0);
    }

    /**
     * Met à jour un match
     */
    public function updateMatch(array $data, ?int $userId = null): bool {
        $scoreDetail = !empty($data['score_detail']) ? (is_string($data['score_detail']) ? $data['score_detail'] : json_encode($data['score_detail'])) : null;
        $matchDate   = !empty($data['match_date']) ? $data['match_date'] : null;

        $this->db->query("
            UPDATE t_sport_match SET
                round_id     = :round_id,
                home_team_id = :home_team_id,
                away_team_id = :away_team_id,
                match_order  = :match_order,
                match_date   = :match_date,
                venue_name   = :venue_name,
                score_home   = :score_home,
                score_away   = :score_away,
                score_detail = :score_detail,
                note         = :note,
                status_id    = :status_id,
                modified_at  = CURRENT_TIMESTAMP,
                modified_by  = :modified_by
            WHERE id = :id
        ");

        $this->db->bind(':id',           (int)$data['id']);
        $this->db->bind(':round_id',     (int)$data['round_id']);
        $this->db->bind(':home_team_id', !empty($data['home_team_id']) ? (int)$data['home_team_id'] : null);
        $this->db->bind(':away_team_id', !empty($data['away_team_id']) ? (int)$data['away_team_id'] : null);
        $this->db->bind(':match_order',  !empty($data['match_order']) ? (int)$data['match_order'] : 1);
        $this->db->bind(':match_date',   $matchDate);
        $this->db->bind(':venue_name',   !empty($data['venue_name']) ? trim($data['venue_name']) : null);
        $this->db->bind(':score_home',   ($data['score_home'] !== '' && $data['score_home'] !== null) ? (int)$data['score_home'] : null);
        $this->db->bind(':score_away',   ($data['score_away'] !== '' && $data['score_away'] !== null) ? (int)$data['score_away'] : null);
        $this->db->bind(':score_detail', $scoreDetail);
        $this->db->bind(':note',         !empty($data['note']) ? trim($data['note']) : null);
        $this->db->bind(':status_id',    !empty($data['status_id']) ? (int)$data['status_id'] : 1);
        $this->db->bind(':modified_by',  $userId);

        return $this->db->execute();
    }

    /**
     * Saisie rapide d'un score
     */
    public function updateScore(int $id, ?int $scoreHome, ?int $scoreAway, $scoreDetail = null, int $statusId = 3, ?int $userId = null): bool {
        $detail = !empty($scoreDetail) ? (is_string($scoreDetail) ? $scoreDetail : json_encode($scoreDetail)) : null;

        $this->db->query("
            UPDATE t_sport_match SET
                score_home   = :score_home,
                score_away   = :score_away,
                score_detail = :score_detail,
                status_id    = :status_id,
                modified_at  = CURRENT_TIMESTAMP,
                modified_by  = :modified_by
            WHERE id = :id
        ");

        $this->db->bind(':id',           $id);
        $this->db->bind(':score_home',   $scoreHome);
        $this->db->bind(':score_away',   $scoreAway);
        $this->db->bind(':score_detail', $detail);
        $this->db->bind(':status_id',    $statusId);
        $this->db->bind(':modified_by',  $userId);

        return $this->db->execute();
    }

    /**
     * Supprime ou annule un match
     */
    public function deleteMatch(int $id, bool $force = false): bool {
        if ($force) {
            $this->db->query("DELETE FROM t_sport_match WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } else {
            // Statut 5 = MATCH_STATUS_CANCELLED
            $this->db->query("UPDATE t_sport_match SET status_id = 5, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        }
    }

    /**
     * Réactive un match
     */
    public function activateMatch(int $id): bool {
        // Statut 1 = MATCH_STATUS_SCHEDULED
        $this->db->query("UPDATE t_sport_match SET status_id = 1, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Liste des statuts de match
     */
    public function getAllStatuses(): array {
        $this->db->query("SELECT * FROM t_sport_match_status ORDER BY id ASC");
        return $this->db->resultSet();
    }

    /**
     * Liste des journées / rounds avec contexte compétition
     */
    public function getRoundsList(): array {
        $this->db->query("
            SELECT r.id, r.name, r.round_order, r.group_id, r.phase_id,
                   g.name    AS group_name,
                   ph.name   AS phase_name,
                   ed.name   AS edition_name,
                   comp.name AS competition_name
            FROM t_sport_competition_round r
            LEFT JOIN t_sport_competition_group   g    ON r.group_id    = g.id
            LEFT JOIN t_sport_competition_phase   ph   ON r.phase_id    = ph.id
            LEFT JOIN t_sport_competition_edition ed   ON ph.edition_id = ed.id
            LEFT JOIN t_sport_competition         comp ON ed.competition_id = comp.id
            ORDER BY comp.name ASC, ed.name ASC, ph.phase_order ASC, r.round_order ASC, r.name ASC
        ");
        return $this->db->resultSet();
    }

    /**
     * Liste des équipes pour les sélecteurs
     */
    public function getTeamsList(): array {
        $this->db->query("
            SELECT t.id, t.name, t.code, c.name AS club_name, c.logo AS club_logo
            FROM t_sport_team t
            LEFT JOIN t_sport_section sec ON t.section_id = sec.id
            LEFT JOIN t_sport_club c ON sec.club_id = c.id
            ORDER BY t.name ASC
        ");
        return $this->db->resultSet();
    }
}
