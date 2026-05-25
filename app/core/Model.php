<?php
// app/core/Model.php

abstract class Model {
    protected ?PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Run a query and return all rows */
    protected function query(string $sql, array $params = []): array {
        if (!$this->db) return [];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Return a single row */
    protected function queryOne(string $sql, array $params = []): ?array {
        $rows = $this->query($sql, $params);
        return $rows[0] ?? null;
    }

    /** Return a single scalar value */
    protected function scalar(string $sql, array $params = [], mixed $default = 0): mixed {
        if (!$this->db) return $default;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    }

    /** Execute INSERT/UPDATE/DELETE and return affected rows */
    protected function execute(string $sql, array $params = []): int {
        if (!$this->db) return 0;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Return last inserted ID */
    protected function lastId(): string {
        return $this->db?->lastInsertId() ?? '0';
    }

    /** Count rows in $this->table */
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) $sql .= " WHERE $where";
        return (int) $this->scalar($sql, $params);
    }

    /** Find by primary key */
    public function find(int $id): ?array {
        return $this->queryOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    /** Delete by primary key */
    public function delete(int $id): int {
        return $this->execute("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    /** Log an admin action */
    protected function logActivity(string $type, string $description, ?int $adminId = null, ?int $userId = null): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $this->execute(
            "INSERT INTO activity_log (admin_id, user_id, type, description, ip_address) VALUES (?, ?, ?, ?, ?)",
            [$adminId, $userId, $type, $description, $ip]
        );
    }
}
