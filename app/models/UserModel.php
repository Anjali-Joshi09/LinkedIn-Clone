<?php
// app/models/UserModel.php

require_once APP_PATH . '/core/Model.php';

class UserModel extends Model {
    protected string $table = 'users';

    public function getAll(array $filters = []): array {
        $sql    = "SELECT * FROM users WHERE role = 'user'";
        $params = [];

        if (!empty($filters['search'])) {
            $sql      .= " AND (name LIKE ? OR email LIKE ?)";
            $like      = '%' . $filters['search'] . '%';
            $params[]  = $like;
            $params[]  = $like;
        }
        if (!empty($filters['status'])) {
            $sql     .= " AND status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['role'])) {
            $sql     .= " AND role = ?";
            $params[] = $filters['role'];
        }

        $sql .= " ORDER BY id DESC LIMIT 50";
        return $this->query($sql, $params);
    }

    public function updateStatus(int $id, string $status, int $adminId): int {
        $rows = $this->execute("UPDATE users SET status = ? WHERE id = ?", [$status, $id]);
        if ($rows) {
            $this->logActivity('user_status_changed', "User #$id status changed to $status", $adminId, $id);
        }
        return $rows;
    }

    public function deleteUser(int $id, int $adminId): int {
        $user = $this->find($id);
        $rows = $this->execute("DELETE FROM users WHERE id = ?", [$id]);
        if ($rows && $user) {
            $this->logActivity('user_deleted', "User '{$user['name']}' (#{$id}) deleted", $adminId);
        }
        return $rows;
    }

    public function updateUser(int $id, array $data, int $adminId): bool {
        $allowed = ['name','email','phone','headline','bio','location','website','status','avatar','cover'];
        $sets    = [];
        $params  = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $sets[]   = "$col = ?";
                $params[] = $data[$col];
            }
        }

        if (!empty($sets)) {
            $params[] = $id;
            $this->execute("UPDATE users SET " . implode(', ', $sets) . " WHERE id = ?", $params);
        }

        $this->logActivity('user_updated', "User #{$id} profile updated by admin", $adminId, $id);
        return true;
    }

    public function saveResetToken(int $id, string $token): void {
        $this->execute("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id=?", [$token, $id]);
    }

    public function newCount(): int {
        $r = $this->query("SELECT COUNT(*) as cnt FROM users WHERE role='user' AND created_at >= NOW() - INTERVAL 24 HOUR");
        return (int)($r[0]['cnt'] ?? 0);
    }

    public function newIds(): array {
        $rows = $this->query("SELECT id FROM users WHERE role='user' AND created_at >= NOW() - INTERVAL 24 HOUR");
        return array_map(fn($r) => 'u' . $r['id'], $rows);
    }
}
