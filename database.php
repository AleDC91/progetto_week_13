<?php

namespace db {

    use PDO;

    class DB_PDO
    {
        private PDO $conn;
        private static ?DB_PDO $instance = null;

        private function __construct(array $config)
        {

            $serverConn = new PDO(
                $config['driver'] . ":host=" . $config['host'] . "; port=" . $config['port'] . ";",
                $config['user'],
                $config['password']
            );

            DBInitializer::initializeDatabase($serverConn, $config);

            $this->conn = new PDO(
                $config['driver'] . ":host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['database'],
                $config['user'],
                $config['password']
            );

        }

        public static function getInstance(array $config)
        {
            if (!static::$instance) {
                static::$instance = new DB_PDO($config);
            }
            return static::$instance;
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
        }


        private static function isTableEmpty(PDO $conn): bool
        {
            $stmt = $conn->query("SELECT COUNT(*) FROM users");
            return $stmt->fetchColumn() == 0;
        }

        private static function insertDefaultUsers(PDO $conn)
        {
            
            $defaultUsers = require_once('settings/defaultUsers.php');

            $insertQuery = "INSERT INTO users (firstname, lastname, email, password, isAdmin) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);

            foreach ($defaultUsers as $user) {
                $stmt->execute($user);
            }
        }
    }
}
