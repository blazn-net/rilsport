<?php
namespace Module\Zone\Model;

use Core\Database;

class Zone {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // -------------------------------------------------------
    // Récupérer les enfants d'une zone (depuis la BDD)
    // -------------------------------------------------------

    public function getChildren(int $parentId, string $langCode = 'fr'): array {
        $this->db->query("
            SELECT z.id, z.geonames_id, z.type_code, z.parent_id,
                   COALESCE(i.name, z.name_default) AS name,
                   z.children_loaded, z.country_code, z.status_id
            FROM t_zone_zone z
            LEFT JOIN t_zone_zone_i18n i ON i.zone_id = z.id AND i.lang_code = :lang
            WHERE z.parent_id = :parent_id
              AND z.status_id = 1
            ORDER BY name ASC
        ");
        $this->db->bind(':lang',      $langCode);
        $this->db->bind(':parent_id', $parentId);
        return $this->db->resultSet();
    }

    // -------------------------------------------------------
    // Récupérer la zone racine (Monde)
    // -------------------------------------------------------

    public function getRootZone(string $langCode = 'fr'): ?array {
        $this->db->query("
            SELECT z.id, z.geonames_id, z.type_code, z.parent_id,
                   COALESCE(i.name, z.name_default) AS name,
                   z.children_loaded, z.country_code, z.status_id
            FROM t_zone_zone z
            LEFT JOIN t_zone_zone_i18n i ON i.zone_id = z.id AND i.lang_code = :lang
            WHERE z.parent_id IS NULL
              AND z.status_id = 1
            LIMIT 1
        ");
        $this->db->bind(':lang', $langCode);
        return $this->db->single() ?: null;
    }

    // -------------------------------------------------------
    // Récupérer une zone par son ID
    // -------------------------------------------------------

    public function getZoneById(int $id, string $langCode = 'fr'): ?array {
        $this->db->query("
            SELECT z.id, z.geonames_id, z.type_code, z.parent_id,
                   z.name_default,
                   COALESCE(i.name, z.name_default) AS name,
                   z.children_loaded, z.country_code, z.status_id
            FROM t_zone_zone z
            LEFT JOIN t_zone_zone_i18n i ON i.zone_id = z.id AND i.lang_code = :lang
            WHERE z.id = :id
        ");
        $this->db->bind(':lang', $langCode);
        $this->db->bind(':id',   $id);
        return $this->db->single() ?: null;
    }

    // -------------------------------------------------------
    // Insérer une zone (retourne l'ID inséré)
    // -------------------------------------------------------

    public function insertZone(array $data): int {
        $geonamesId = !empty($data['geonames_id']) ? (int) $data['geonames_id'] : null;

        if ($geonamesId !== null) {
            $this->db->query("
                INSERT INTO t_zone_zone
                    (geonames_id, type_code, parent_id, name_default, country_code, children_loaded, created_by)
                VALUES
                    (:geonames_id, :type_code, :parent_id, :name_default, :country_code, FALSE, :created_by)
                ON CONFLICT (geonames_id) DO UPDATE SET
                    type_code    = EXCLUDED.type_code,
                    parent_id    = EXCLUDED.parent_id,
                    name_default = EXCLUDED.name_default,
                    country_code = EXCLUDED.country_code
                RETURNING id
            ");
            $this->db->bind(':geonames_id',  $geonamesId);
        } else {
            $this->db->query("
                INSERT INTO t_zone_zone
                    (geonames_id, type_code, parent_id, name_default, country_code, children_loaded, created_by)
                VALUES
                    (NULL, :type_code, :parent_id, :name_default, :country_code, FALSE, :created_by)
                RETURNING id
            ");
        }

        $this->db->bind(':type_code',    $data['type_code']);
        $this->db->bind(':parent_id',    !empty($data['parent_id']) ? (int)$data['parent_id'] : null);
        $this->db->bind(':name_default', $data['name_default']);
        $this->db->bind(':country_code', !empty($data['country_code']) ? strtoupper(trim($data['country_code'])) : null);
        $this->db->bind(':created_by',   $data['created_by'] ?? null);
        $row = $this->db->single();
        return (int) ($row['id'] ?? 0);
    }

    // -------------------------------------------------------
    // Mettre à jour une zone existante
    // -------------------------------------------------------

    public function updateZone(array $data): bool {
        $this->db->query("
            UPDATE t_zone_zone
            SET type_code    = :type_code,
                parent_id    = :parent_id,
                name_default = :name_default,
                country_code = :country_code,
                geonames_id  = :geonames_id,
                status_id    = :status_id,
                modified_by  = :modified_by,
                modified_at  = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id',           (int) $data['id']);
        $this->db->bind(':type_code',    $data['type_code']);
        $this->db->bind(':parent_id',    !empty($data['parent_id']) ? (int)$data['parent_id'] : null);
        $this->db->bind(':name_default', $data['name_default']);
        $this->db->bind(':country_code', !empty($data['country_code']) ? strtoupper(trim($data['country_code'])) : null);
        $this->db->bind(':geonames_id',  !empty($data['geonames_id']) ? (int)$data['geonames_id'] : null);
        $this->db->bind(':status_id',    isset($data['status_id']) ? (int)$data['status_id'] : 1);
        $this->db->bind(':modified_by',  $data['modified_by'] ?? null);
        return $this->db->execute();
    }

    // -------------------------------------------------------
    // Types de zones (world, continent, country, admin1, admin2)
    // -------------------------------------------------------

    public function getZoneTypes(): array {
        $this->db->query("SELECT code, level, name FROM t_zone_type ORDER BY level ASC");
        return $this->db->resultSet();
    }

    // -------------------------------------------------------
    // Récupérer les traductions d'une zone (tableau [lang_code => name])
    // -------------------------------------------------------

    public function getZoneI18n(int $zoneId): array {
        $this->db->query("SELECT lang_code, name FROM t_zone_zone_i18n WHERE zone_id = :zone_id");
        $this->db->bind(':zone_id', $zoneId);
        $rows = $this->db->resultSet();
        $result = [];
        foreach ($rows as $r) {
            $result[$r['lang_code']] = $r['name'];
        }
        return $result;
    }

    // -------------------------------------------------------
    // Vérifier si une zone possède des sous-zones enfants
    // -------------------------------------------------------

    public function hasChildren(int $zoneId): int {
        $this->db->query("SELECT COUNT(*) AS total FROM t_zone_zone WHERE parent_id = :id AND status_id = 1");
        $this->db->bind(':id', $zoneId);
        $row = $this->db->single();
        return (int) ($row['total'] ?? 0);
    }

    // -------------------------------------------------------
    // Supprimer une zone (soft delete par défaut ou hard delete)
    // -------------------------------------------------------

    public function deleteZone(int $zoneId, bool $hardDelete = false): bool {
        if ($hardDelete) {
            $this->db->query("DELETE FROM t_zone_zone WHERE id = :id");
        } else {
            $this->db->query("UPDATE t_zone_zone SET status_id = 0, modified_at = CURRENT_TIMESTAMP WHERE id = :id");
        }
        $this->db->bind(':id', $zoneId);
        return $this->db->execute();
    }

    // -------------------------------------------------------
    // Liste des parents potentiels (world, continent, country, admin1)
    // -------------------------------------------------------

    public function getPotentialParents(string $langCode = 'fr', ?int $excludeId = null): array {
        $sql = "
            SELECT z.id, z.type_code,
                   COALESCE(i.name, z.name_default) AS name
            FROM t_zone_zone z
            LEFT JOIN t_zone_zone_i18n i ON i.zone_id = z.id AND i.lang_code = :lang
            WHERE z.status_id = 1
              AND z.type_code IN ('world', 'continent', 'country', 'admin1')
        ";
        if ($excludeId !== null) {
            $sql .= " AND z.id != :exclude_id";
        }
        $sql .= " ORDER BY z.type_code ASC, name ASC";

        $this->db->query($sql);
        $this->db->bind(':lang', $langCode);
        if ($excludeId !== null) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        return $this->db->resultSet();
    }

    // -------------------------------------------------------
    // Insérer les traductions d'une zone
    // -------------------------------------------------------

    public function upsertZoneI18n(int $zoneId, string $langCode, string $name): void {
        $this->db->query("
            INSERT INTO t_zone_zone_i18n (zone_id, lang_code, name)
            VALUES (:zone_id, :lang_code, :name)
            ON CONFLICT (zone_id, lang_code) DO UPDATE SET name = EXCLUDED.name
        ");
        $this->db->bind(':zone_id',   $zoneId);
        $this->db->bind(':lang_code', $langCode);
        $this->db->bind(':name',      $name);
        $this->db->execute();
    }

    // -------------------------------------------------------
    // Marquer les enfants comme chargés
    // -------------------------------------------------------

    public function markChildrenLoaded(int $zoneId): void {
        $this->db->query("
            UPDATE t_zone_zone
            SET children_loaded = TRUE, modified_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $this->db->bind(':id', $zoneId);
        $this->db->execute();
    }

    // -------------------------------------------------------
    // Récupérer toutes les langues actives (pour les i18n)
    // -------------------------------------------------------

    public function getActiveLangs(): array {
        $this->db->query("SELECT lang_code FROM t_lang_lang WHERE status_id = 1 ORDER BY lang_code");
        return $this->db->resultSet();
    }

    // -------------------------------------------------------
    // Vérifier si children_loaded est TRUE
    // -------------------------------------------------------

    public function areChildrenLoaded(int $zoneId): bool {
        $this->db->query("SELECT children_loaded FROM t_zone_zone WHERE id = :id");
        $this->db->bind(':id', $zoneId);
        $row = $this->db->single();
        return isset($row['children_loaded']) && $row['children_loaded'];
    }
}
