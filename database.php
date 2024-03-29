<?php

namespace db {
    require_once("classes/Logger.php");

    use Logger;
    use PDO;

    class DB_PDO
    {
        private PDO $conn;
        private static ?DB_PDO $instance = null;

        private function __construct(array $config)
        {
            $logger = Logger::getInstance();
            try {
                $serverConn = new PDO(
                    $config['driver'] . ":host=" . $config['host'] . "; port=" . $config['port'] . ";",
                    $config['user'],
                    $config['password']
                );
            } catch (\PDOException $e) {
                $logger->log("Database non inizializzato ". $e->getMessage(), "ERROR");
                exit();
            }

            DBInitializer::initializeDatabase($serverConn, $config);

            $this->conn = new PDO(
                $config['driver'] . ":host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'],
                $config['user'],
                $config['password']
            );
        }

        public static function getInstance(array $config)
        {
            $logger = Logger::getInstance();
            try {
                if (!static::$instance) {
                    static::$instance = new DB_PDO($config);
                }
                return static::$instance;
            } catch (\PDOException $e) {
                $logger->log("Database non inizializzato ", "ERROR");
                exit();
            }
        }

        public function getConnection()
        {
            return $this->conn;
        }
    }


    class DBInitializer
    {
        public static function initializeDatabase(PDO $conn, array $config)
        {
            $logger = Logger::getInstance();

            try {

                $dbName = $config['database'];
                $createDbQuery = "CREATE DATABASE IF NOT EXISTS $dbName";
                $conn->exec($createDbQuery);

                $conn->exec("USE $dbName");

                $createTableQuery = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firstname VARCHAR(50) NOT NULL,
            lastname VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            auth_token VARCHAR(255),
            isAdmin BOOL NOT NULL DEFAULT FALSE
            )";

                $conn->exec($createTableQuery);

                if (self::isTableEmpty($conn)) {
                    self::insertDefaultUsers($conn);
                }
            } catch (\PDOException $e) {
                $logger->log("Database non inizializzato", "ERROR");
                exit();
            }
        }


        private static function isTableEmpty(PDO $conn): bool
        {
            $logger = Logger::getInstance();

            try {
                $stmt = $conn->query("SELECT COUNT(*) FROM users");
                return $stmt->fetchColumn() == 0;
            } catch (\PDOException $e) {
                $logger->log("Database non inizializzato", "ERROR");
                exit();
            }
        }

        private static function insertDefaultUsers(PDO $conn)
        {
            $logger = Logger::getInstance();

            try {

                $defaultUsers = require_once('settings/defaultUsers.php');

                $insertQuery = "INSERT INTO users (firstname, lastname, email, password, isAdmin) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);

                foreach ($defaultUsers as $user) {
                    $stmt->execute($user);
                }
            } catch (\PDOException $e) {
                $logger->log("Database non inizializzato", "ERROR");
                exit();
            }
        }
    }
}
