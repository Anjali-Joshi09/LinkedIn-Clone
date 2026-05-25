<?php
// app/models/AgentApprovalModel.php
require_once APP_PATH . '/core/Model.php';

class AgentApprovalModel extends Model {
    protected string $table = 'agent_approvals';

    /** Get all pending approvals */
    public function getPending(): array {
        return $this->query(
            "SELECT aa.*, u.email as user_email, u.role
             FROM agent_approvals aa
             LEFT JOIN users u ON aa.user_id = u.id
             WHERE aa.status = 'pending'
             ORDER BY aa.id DESC"
        );
    }

    /** Get all approvals with optional status filter */
    public function getAll(string $status = ''): array {
        $sql    = "SELECT aa.*, u.email as user_email
                   FROM agent_approvals aa
                   LEFT JOIN users u ON aa.user_id = u.id
                   WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND aa.status = ?"; $params[] = $status; }
        $sql .= " ORDER BY aa.id DESC LIMIT 50";
        return $this->query($sql, $params); //Model.php
    }

    /** Find approval by user_id */
    public function findByUserId(int $userId): ?array {
        return $this->queryOne(
            "SELECT * FROM agent_approvals WHERE user_id = ? LIMIT 1", [$userId]
        );
    }

    /** Create a new approval request (or resubmit existing) */
    public function createRequest(array $data): int {

    $existing = $this->findByUserId($data['user_id']);

    if ($existing) {

        $this->execute(
            "UPDATE agent_approvals SET 
             name=?, 
             phone=?, 
             headline=?, 
             bio=?, 
             location=?, 
             website=?,
             status='pending', 
             admin_note=NULL, 
             reviewed_by=NULL, 
             reviewed_at=NULL, 
             updated_at=NOW()
             WHERE user_id=?",
            [
                $data['name'],
                $data['phone'] ?? null,
                $data['headline'] ?? null,
                $data['bio'] ?? null,
                $data['location'] ?? null,
                $data['website'] ?? null,
                $data['user_id']
            ]
        );

        return $existing['id'];
    }

    $this->execute(
        "INSERT INTO agent_approvals 
        (user_id, name, email, phone, headline, bio, location, website)
         VALUES (?,?,?,?,?,?,?,?)",
        [
            $data['user_id'],
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['headline'] ?? null,
            $data['bio'] ?? null,
            $data['location'] ?? null,
            $data['website'] ?? null
        ]
    );

    return (int) $this->lastId();
}

    /** Approve an agent */
    public function approve(int $id, int $adminId): bool {
        $rows = $this->execute(
            "UPDATE agent_approvals SET status='approved', reviewed_by=?, reviewed_at=NOW(), notified_at=NOW() WHERE id=?",
            [$adminId, $id]
        );
        if ($rows) {
            $approval = $this->find($id);
            if ($approval) {
                $this->execute("UPDATE users SET status='active' WHERE id=?", [$approval['user_id']]);
                $this->execute("UPDATE companies SET status='verified' WHERE user_id=?", [$approval['user_id']]);
                $this->logActivity('agent_approved', "Agent #{$id} ({$approval['name']}) approved", $adminId, $approval['user_id']);
            }
        }
        return $rows > 0;
    }

    /** Reject an agent */
    public function reject(int $id, int $adminId, string $note = ''): bool {
        $rows = $this->execute(
            "UPDATE agent_approvals SET status='rejected', admin_note=?, reviewed_by=?, reviewed_at=NOW(), notified_at=NOW() WHERE id=?",
            [$note, $adminId, $id]
        );
        if ($rows) {
            $approval = $this->find($id);
            if ($approval) {
                $this->execute("UPDATE users SET status='suspended' WHERE id=?", [$approval['user_id']]);
                $this->logActivity('agent_rejected', "Agent #{$id} ({$approval['name']}) rejected", $adminId, $approval['user_id']);
            }
        }
        return $rows > 0;
    }

    /** Pending count for sidebar badge */
    public function pendingCount(): int {
        return (int) $this->scalar("SELECT COUNT(*) FROM agent_approvals WHERE status='pending'");
    }

    public function pendingIds(): array {
        $rows = $this->query("SELECT id FROM agent_approvals WHERE status='pending'");
        return array_map(fn($r) => 'a' . $r['id'], $rows);
    }
}