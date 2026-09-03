<?php
namespace Module\Zone\Model;

use Core\Database;

/**
 * GeoNamesService — Proxy vers l'API GeoNames
 *
 * Utilise le username défini dans la config de l'application (GEONAMES_USERNAME).
 * Toutes les requêtes passent par secure.geonames.org (HTTPS).
 */
class GeoNamesService {

    private string $username;
    private string $baseUrl = 'https://secure.geonames.org';

    public function __construct() {
        // Défini dans config/config.php : define('GEONAMES_USERNAME', 'votre_username');
        $this->username = defined('GEONAMES_USERNAME') ? GEONAMES_USERNAME : '';
    }

    // -------------------------------------------------------
    // Récupérer les enfants d'une zone GeoNames
    // -------------------------------------------------------

    public function getChildren(int $geonamesId): array {
        $url = "{$this->baseUrl}/childrenJSON"
             . "?geonameId={$geonamesId}"
             . "&username={$this->username}";

        $raw = $this->httpGet($url);
        if ($raw === null) {
            return [];
        }

        $json = json_decode($raw, true);
        return $json['geonames'] ?? [];
    }

    // -------------------------------------------------------
    // Récupérer les noms alternatifs d'une zone dans une langue
    // -------------------------------------------------------

    public function getAlternateName(int $geonamesId, string $langCode): ?string {
        $url = "{$this->baseUrl}/getJSON"
             . "?geonameId={$geonamesId}"
             . "&username={$this->username}";

        $raw = $this->httpGet($url);
        if ($raw === null) {
            return null;
        }

        $json = json_decode($raw, true);

        // Chercher dans alternateNames le nom pour la langue demandée
        foreach ($json['alternateNames'] ?? [] as $alt) {
            if (($alt['lang'] ?? '') === $langCode && !empty($alt['name'])) {
                return $alt['name'];
            }
        }

        // Fallback : nom par défaut GeoNames
        return $json['name'] ?? null;
    }

    // -------------------------------------------------------
    // Récupérer les noms dans plusieurs langues en un seul appel
    // -------------------------------------------------------

    public function getNamesForLangs(int $geonamesId, array $langCodes): array {
        $url = "{$this->baseUrl}/getJSON"
             . "?geonameId={$geonamesId}"
             . "&username={$this->username}";

        $raw = $this->httpGet($url);
        if ($raw === null) {
            return [];
        }

        $json    = json_decode($raw, true);
        $altNames = $json['alternateNames'] ?? [];
        $defaultName = $json['name'] ?? null;

        $result = [];
        foreach ($langCodes as $lang) {
            $found = null;
            foreach ($altNames as $alt) {
                if (($alt['lang'] ?? '') === $lang && !empty($alt['name'])) {
                    $found = $alt['name'];
                    break;
                }
            }
            // Fallback sur le nom anglais par défaut
            $result[$lang] = $found ?? $defaultName;
        }

        return $result;
    }

    // -------------------------------------------------------
    // Mapper le fcode GeoNames vers le type_code du module zone
    // -------------------------------------------------------

    public static function mapFcodeToTypeCode(string $fcode, string $fcl): string {
        // Pays
        if ($fcode === 'PCLI' || $fcode === 'PCLD' || $fcode === 'PCLF' || $fcl === 'A' && str_starts_with($fcode, 'PCL')) {
            return 'country';
        }
        // Continents (GeoNames les identifie avec fcl=L ou fcode=CONT)
        if ($fcode === 'CONT' || $fcl === 'L') {
            return 'continent';
        }
        // Admin1 (région, état, province...)
        if ($fcode === 'ADM1') {
            return 'admin1';
        }
        // Admin2 (département, comté...)
        if ($fcode === 'ADM2') {
            return 'admin2';
        }
        // Fallback
        return 'admin1';
    }

    // -------------------------------------------------------
    // HTTP GET générique (cURL)
    // -------------------------------------------------------

    private function httpGet(string $url): ?string {
        if (empty($this->username)) {
            error_log('[GeoNamesService] GEONAMES_USERNAME non défini dans config.php');
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => 'rilsport/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("[GeoNamesService] cURL error: {$error}");
            return null;
        }

        if ($httpCode !== 200) {
            error_log("[GeoNamesService] HTTP {$httpCode} for URL: {$url}");
            return null;
        }

        return $response;
    }
}
