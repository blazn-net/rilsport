<?php
namespace Module\Sport\Model;

use Core\Database;

class Standing {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Récupère le classement complet d'un groupe ordonné par rang
     */
    public function getStandingsByGroup(int $groupId): array {
        $this->db->query("
            SELECT st.*,
                   t.name       AS team_name,
                   t.code       AS team_code,
                   c.name       AS club_name,
                   c.logo       AS club_logo,
                   g.name       AS group_name,
                   ph.name      AS phase_name,
                   ed.id        AS edition_id,
                   ed.name      AS edition_name,
                   comp.id      AS competition_id,
                   comp.name    AS competition_name,
                   u_m.username AS modified_by_name
            FROM t_sport_standing st
            INNER JOIN t_sport_team               t    ON st.team_id   = t.id
            LEFT  JOIN t_sport_section            sec  ON t.section_id = sec.id
            LEFT  JOIN t_sport_club               c    ON sec.club_id  = c.id
            INNER JOIN t_sport_competition_group  g    ON st.group_id  = g.id
            LEFT  JOIN t_sport_competition_phase  ph   ON g.phase_id   = ph.id
            LEFT  JOIN t_sport_competition_edition ed ON ph.edition_id = ed.id
            LEFT  JOIN t_sport_competition        comp ON ed.competition_id = comp.id
            LEFT  JOIN t_user_user                u_m  ON st.modified_by = u_m.id
            WHERE st.group_id = :group_id
            ORDER BY COALESCE(st.rank, 999) ASC, st.points DESC, st.goal_diff DESC, st.goals_for DESC, t.name ASC
        ");
        $this->db->bind(':group_id', $groupId);
        return $this->db->resultSet();
    }

    /**
     * Récupère une ligne de classement par ID
     */
    public function getStandingById(int $id) {
        $this->db->query("
            SELECT st.*,
                   t.name       AS team_name,
                   t.code       AS team_code,
                   c.name       AS club_name,
                   c.logo       AS club_logo,
                   g.name       AS group_name,
                   ph.name      AS phase_name,
                   ed.id        AS edition_id,
                   ed.name      AS edition_name,
                   comp.id      AS competition_id,
                   comp.name    AS competition_name,
                   u_c.username AS created_by_name,
                   u_m.username AS modified_by_name
            FROM t_sport_standing st
            INNER JOIN t_sport_team               t    ON st.team_id   = t.id
            LEFT  JOIN t_sport_section            sec  ON t.section_id = sec.id
            LEFT  JOIN t_sport_club               c    ON sec.club_id  = c.id
            INNER JOIN t_sport_competition_group  g    ON st.group_id = g.id
            LEFT  JOIN t_sport_competition_phase  ph   ON g.phase_id  = ph.id
            LEFT  JOIN t_sport_competition_edition ed ON ph.edition_id = ed.id
            LEFT  JOIN t_sport_competition        comp ON ed.competition_id = comp.id
            LEFT  JOIN t_user_user                u_c  ON st.created_by = u_c.id
            LEFT  JOIN t_user_user                u_m  ON st.modified_by = u_m.id
            WHERE st.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Récupère la règle de calcul applicable
     */
    public function getStandingRule(int $editionId, ?int $phaseId = null): array {
        $this->db->query("
            SELECT * FROM t_sport_standing_rule
            WHERE edition_id = :edition_id
              AND (phase_id = :phase_id OR phase_id IS NULL)
            ORDER BY phase_id DESC NULLS LAST
            LIMIT 1
        ");
        $this->db->bind(':edition_id', $editionId);
        $this->db->bind(':phase_id', $phaseId);
        $rule = $this->db->single();

        if (!$rule) {
            return [
                'pts_win'      => 3,
                'pts_draw'     => 1,
                'pts_loss'     => 0,
                'count_goals'  => true,
                'tri_criteria' => ['points', 'goal_diff', 'goals_for']
            ];
        }

        return $rule;
    }

    /**
     * Recalcule automatiquement le classement complet d'un groupe
     */
    public function recalculateGroupStandings(int $groupId, ?int $userId = null): bool {
        // 1. Récupère le groupe et son édition/phase
        $this->db->query("
            SELECT g.id, g.phase_id, ph.edition_id
            FROM t_sport_competition_group g
            LEFT JOIN t_sport_competition_phase ph ON g.phase_id = ph.id
            WHERE g.id = :group_id
        ");
        $this->db->bind(':group_id', $groupId);
        $groupInfo = $this->db->single();
        if (!$groupInfo) {
            return false;
        }

        $editionId = (int)($groupInfo['edition_id'] ?? 0);
        $phaseId   = !empty($groupInfo['phase_id']) ? (int)$groupInfo['phase_id'] : null;
        $rule      = $this->getStandingRule($editionId, $phaseId);

        $ptsWin  = (int)($rule['pts_win'] ?? 3);
        $ptsDraw = (int)($rule['pts_draw'] ?? 1);
        $ptsLoss = (int)($rule['pts_loss'] ?? 0);

        // 2. Récupère toutes les équipes inscrites ou associées à ce groupe
        $this->db->query("
            SELECT DISTINCT t.id, t.name
            FROM t_sport_team t
            WHERE t.id IN (
                SELECT team_id FROM t_sport_competition_entry WHERE group_id = :g1
                UNION
                SELECT home_team_id FROM t_sport_match m
                JOIN t_sport_competition_round r ON m.round_id = r.id
                WHERE r.group_id = :g2 AND home_team_id IS NOT NULL
                UNION
                SELECT away_team_id FROM t_sport_match m
                JOIN t_sport_competition_round r ON m.round_id = r.id
                WHERE r.group_id = :g3 AND away_team_id IS NOT NULL
            )
        ");
        $this->db->bind(':g1', $groupId);
        $this->db->bind(':g2', $groupId);
        $this->db->bind(':g3', $groupId);
        $teams = $this->db->resultSet();

        if (empty($teams)) {
            return false;
        }

        // 3. Initialise le tableau de stats par équipe
        $stats = [];
        foreach ($teams as $tm) {
            $tId = (int)$tm['id'];
            $stats[$tId] = [
                'team_id'       => $tId,
                'team_name'     => $tm['name'],
                'played'        => 0,
                'won'           => 0,
                'drawn'         => 0,
                'lost'          => 0,
                'goals_for'     => 0,
                'goals_against' => 0,
                'goal_diff'     => 0,
                'points'        => 0,
            ];
        }

        // 4. Récupère tous les matchs joués (status_id = 3 = MATCH_STATUS_PLAYED) pour les rounds de ce groupe
        $this->db->query("
            SELECT m.home_team_id, m.away_team_id, m.score_home, m.score_away
            FROM t_sport_match m
            JOIN t_sport_competition_round r ON m.round_id = r.id
            WHERE r.group_id = :group_id
              AND m.status_id = 3
              AND m.score_home IS NOT NULL
              AND m.score_away IS NOT NULL
              AND m.home_team_id IS NOT NULL
              AND m.away_team_id IS NOT NULL
        ");
        $this->db->bind(':group_id', $groupId);
        $matches = $this->db->resultSet();

        foreach ($matches as $m) {
            $hId = (int)$m['home_team_id'];
            $aId = (int)$m['away_team_id'];
            $sh  = (int)$m['score_home'];
            $sa  = (int)$m['score_away'];

            if (!isset($stats[$hId]) || !isset($stats[$aId])) {
                continue;
            }

            // Domicile
            $stats[$hId]['played']++;
            $stats[$hId]['goals_for']     += $sh;
            $stats[$hId]['goals_against'] += $sa;

            // Extérieur
            $stats[$aId]['played']++;
            $stats[$aId]['goals_for']     += $sa;
            $stats[$aId]['goals_against'] += $sh;

            if ($sh > $sa) {
                // Victoire domicile
                $stats[$hId]['won']++;
                $stats[$hId]['points'] += $ptsWin;

                $stats[$aId]['lost']++;
                $stats[$aId]['points'] += $ptsLoss;
            } elseif ($sh < $sa) {
                // Victoire extérieur
                $stats[$aId]['won']++;
                $stats[$aId]['points'] += $ptsWin;

                $stats[$hId]['lost']++;
                $stats[$hId]['points'] += $ptsLoss;
            } else {
                // Match nul
                $stats[$hId]['drawn']++;
                $stats[$hId]['points'] += $ptsDraw;

                $stats[$aId]['drawn']++;
                $stats[$aId]['points'] += $ptsDraw;
            }
        }

        // Différence de buts
        foreach ($stats as $tId => &$st) {
            $st['goal_diff'] = $st['goals_for'] - $st['goals_against'];
        }
        unset($st);

        // 5. Tri des équipes (Points DESC, Diff DESC, BP DESC, Nom ASC)
        usort($stats, function ($a, $b) {
            if ($a['points'] !== $b['points']) {
                return $b['points'] <=> $a['points'];
            }
            if ($a['goal_diff'] !== $b['goal_diff']) {
                return $b['goal_diff'] <=> $a['goal_diff'];
            }
            if ($a['goals_for'] !== $b['goals_for']) {
                return $b['goals_for'] <=> $a['goals_for'];
            }
            return strcmp($a['team_name'], $b['team_name']);
        });

        // 6. Enregistrement en base
        $rank = 1;
        foreach ($stats as $row) {
            $this->db->query("
                INSERT INTO t_sport_standing (
                    group_id, team_id, rank, played, won, drawn, lost,
                    goals_for, goals_against, goal_diff, points, is_manual,
                    created_by
                ) VALUES (
                    :group_id, :team_id, :rank, :played, :won, :drawn, :lost,
                    :goals_for, :goals_against, :goal_diff, :points, FALSE,
                    :created_by
                )
                ON CONFLICT (group_id, team_id) DO UPDATE SET
                    rank          = EXCLUDED.rank,
                    played        = EXCLUDED.played,
                    won           = EXCLUDED.won,
                    drawn         = EXCLUDED.drawn,
                    lost          = EXCLUDED.lost,
                    goals_for     = EXCLUDED.goals_for,
                    goals_against = EXCLUDED.goals_against,
                    goal_diff     = EXCLUDED.goal_diff,
                    points        = EXCLUDED.points,
                    modified_at   = CURRENT_TIMESTAMP,
                    modified_by   = EXCLUDED.created_by
                WHERE t_sport_standing.is_manual = FALSE
            ");

            $this->db->bind(':group_id',      $groupId);
            $this->db->bind(':team_id',       $row['team_id']);
            $this->db->bind(':rank',          $rank);
            $this->db->bind(':played',        $row['played']);
            $this->db->bind(':won',           $row['won']);
            $this->db->bind(':drawn',         $row['drawn']);
            $this->db->bind(':lost',          $row['lost']);
            $this->db->bind(':goals_for',     $row['goals_for']);
            $this->db->bind(':goals_against', $row['goals_against']);
            $this->db->bind(':goal_diff',     $row['goal_diff']);
            $this->db->bind(':points',        $row['points']);
            $this->db->bind(':created_by',    $userId);

            $this->db->execute();
            $rank++;
        }

        return true;
    }

    /**
     * Modification manuelle d'une ligne de classement
     */
    public function updateStandingManual(array $data, ?int $userId = null): bool {
        $this->db->query("
            UPDATE t_sport_standing SET
                rank          = :rank,
                played        = :played,
                won           = :won,
                drawn         = :drawn,
                lost          = :lost,
                goals_for     = :goals_for,
                goals_against = :goals_against,
                goal_diff     = :goal_diff,
                points        = :points,
                is_manual     = TRUE,
                note          = :note,
                modified_at   = CURRENT_TIMESTAMP,
                modified_by   = :modified_by
            WHERE id = :id
        ");

        $this->db->bind(':id',            (int)$data['id']);
        $this->db->bind(':rank',          ($data['rank'] !== '' && $data['rank'] !== null) ? (int)$data['rank'] : null);
        $this->db->bind(':played',        (int)($data['played'] ?? 0));
        $this->db->bind(':won',           (int)($data['won'] ?? 0));
        $this->db->bind(':drawn',         (int)($data['drawn'] ?? 0));
        $this->db->bind(':lost',          (int)($data['lost'] ?? 0));
        $this->db->bind(':goals_for',     (int)($data['goals_for'] ?? 0));
        $this->db->bind(':goals_against', (int)($data['goals_against'] ?? 0));
        $this->db->bind(':goal_diff',     (int)($data['goal_diff'] ?? 0));
        $this->db->bind(':points',        (int)($data['points'] ?? 0));
        $this->db->bind(':note',          !empty($data['note']) ? trim($data['note']) : null);
        $this->db->bind(':modified_by',   $userId);

        return $this->db->execute();
    }

    /**
     * Réinitialise une ligne en mode automatique
     */
    public function resetStandingAuto(int $id): bool {
        $this->db->query("UPDATE t_sport_standing SET is_manual = FALSE, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Liste des groupes avec hiérarchie pour le sélecteur
     */
    public function getGroupsList(): array {
        $this->db->query("
            SELECT g.id, g.name,
                   ph.name   AS phase_name,
                   ed.id     AS edition_id,
                   ed.name   AS edition_name,
                   comp.id   AS competition_id,
                   comp.name AS competition_name
            FROM t_sport_competition_group g
            LEFT JOIN t_sport_competition_phase   ph   ON g.phase_id  = ph.id
            LEFT JOIN t_sport_competition_edition ed   ON ph.edition_id = ed.id
            LEFT JOIN t_sport_competition         comp ON ed.competition_id = comp.id
            ORDER BY comp.name ASC, ed.name ASC, ph.phase_order ASC, g.group_order ASC, g.name ASC
        ");
        return $this->db->resultSet();
    }
}
