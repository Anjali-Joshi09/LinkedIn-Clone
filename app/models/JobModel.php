<?php
// app/models/JobModel.php

require_once APP_PATH . '/core/Model.php';

class JobModel extends Model {
    protected string $table = 'jobs';

    public function getAll(string $status = ''): array {
        $sql    = "SELECT j.*, c.name AS company FROM jobs j LEFT JOIN companies c ON j.company_id = c.id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND j.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY j.id DESC LIMIT 50";
        return $this->query($sql, $params);
    }

    public function pendingCount(): int {
        return (int) $this->scalar("SELECT COUNT(*) FROM jobs WHERE status='pending'");
    }

    public function pendingIds(): array {
        $rows = $this->query("SELECT id FROM jobs WHERE status='pending'");
        return array_map(fn($r) => 'j' . $r['id'], $rows);
    }

    public function hide(int $id, int $adminId): int {
        $rows = $this->execute("UPDATE jobs SET status = 'hidden' WHERE id = ?", [$id]);
        if ($rows) {
            $this->logActivity('job_hidden', "Job #$id hidden by admin", $adminId);
        }
        return $rows;
    }

    public function unhide(int $id, int $adminId): int {
        $rows = $this->execute("UPDATE jobs SET status = 'pending' WHERE id = ?", [$id]);
        if ($rows) {
            $this->logActivity('job_unhidden', "Job #$id restored to pending by admin", $adminId);
        }
        return $rows;
    }

    public function updateStatus(int $id, string $status, int $adminId): int {
        $rows = $this->execute("UPDATE jobs SET status = ? WHERE id = ?", [$status, $id]);
        if ($rows) {
            $this->logActivity('job_' . $status, "Job #$id status set to $status", $adminId);
        }
        return $rows;
    }

    public function toggleFeatured(int $id, int $featured): int {
        return $this->execute("UPDATE jobs SET is_featured = ? WHERE id = ?", [$featured, $id]);
    }
}