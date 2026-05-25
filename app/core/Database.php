<?php
// app/core/Database.php

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): ?PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Return null so controllers can show a friendly DB error
                error_log('DB Connection failed: ' . $e->getMessage());
                return null;
            }
        }
        return self::$instance;
    }
}
