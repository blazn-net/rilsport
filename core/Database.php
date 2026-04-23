<?php
namespace Core;

use PDO;
use PDOException;

/*
 * Database Wrapper PDO
 * Prépare les requêtes et gère l'exécution
 */
class Database {
    private $host = DB_HOST;
    private $port = DB_PORT;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct() {
        // Set DSN
        $dsn = 'pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname . ';options=\'--client_encoding=UTF8\'';
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prépare la requête
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Exécute la requête avec des requêtes préparées via bind
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Exécute
    public function execute($params = null) {
        if ($params !== null) {
            return $this->stmt->execute($params);
        }
        return $this->stmt->execute();
    }

    // Récupère une liste de résultats
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Récupère un seul résultat
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }

    // Compte les lignes modifiées/trouvées
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}
