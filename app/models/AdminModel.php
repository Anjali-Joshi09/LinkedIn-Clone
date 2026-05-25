<?php
// app/models/AdminModel.php

require_once APP_PATH . '/core/Model.php';

class AdminModel extends Model {
    protected string $table = 'admins';

    public function findByEmail(string $email): ?array {
        return $this->queryOne("SELECT * FROM admins WHERE email = ? LIMIT 1", [$email]);
    }

    public function authenticate(string $email, string $password): ?array {
        $admin = $this->findByEmail($email);
        if ($admin && password_verify($password, $admin['password'])) {
            $this->execute("UPDATE admins SET last_login = NOW() WHERE id = ?", [$admin['id']]);
            return $admin;
        }
        return null;
    }

    public function touchLogin(int $id): void {
        $this->execute("UPDATE admins SET last_login = NOW() WHERE id = ?", [$id]);
    }

    public function saveResetToken(int $id, string $token): void {
        $this->execute(
            "UPDATE admins SET reset_token=?, reset_token_at=NOW() WHERE id=?",
            [$token, $id]
        );
    }

    public function findByResetToken(string $token): ?array {
        return $this->queryOne(
            "SELECT * FROM admins WHERE reset_token=? LIMIT 1",
            [$token]
        );
    }

    public function clearResetToken(int $id): void {
        $this->execute("UPDATE admins SET reset_token=NULL, reset_token_at=NULL WHERE id=?", [$id]);
    }

    public function updatePassword(int $id, string $newPassword): void {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->execute("UPDATE admins SET password=? WHERE id=?", [$hash, $id]);
    }

    /** Public wrapper — lets controllers run arbitrary queries */
    public function execute(string $sql, array $params = []): int {
        return parent::execute($sql, $params);
    }

    /** Public wrapper — lets controllers fetch a single row */
    public function queryOne(string $sql, array $params = []): ?array {
        return parent::queryOne($sql, $params);
    }
}