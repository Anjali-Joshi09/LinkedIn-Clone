<?php
// app/models/ReportModel.php
require_once APP_PATH . '/core/Model.php';

class ReportModel extends Model {
    protected string $table = 'reports';
    public function getPending(int $limit = 20): array {
        $limit = max(1, min(100, $limit));
        $reports = $this->query(
            "SELECT r.*,
                    p.id AS post_id, p.user_id AS post_user_id, p.content AS post_content,
                    p.media AS post_media, p.media_type AS post_media_type,
                    p.visibility AS post_visibility, p.likes AS post_likes,
                    p.comments_count AS post_comments_count, p.shares_count AS post_shares_count,
                    p.status AS post_status, p.author AS post_author, p.created_at AS post_created_at,
                    u.name AS creator_name, u.email AS creator_email, u.phone AS creator_phone,
                    u.role AS creator_role, u.headline AS creator_headline, u.bio AS creator_bio,
                    u.location AS creator_location, u.website AS creator_website,
                    u.status AS creator_status, u.avatar AS creator_avatar, u.created_at AS creator_created_at,
                    c.id AS creator_company_id, c.name AS creator_company_name, c.email AS creator_company_email,
                    c.industry AS creator_company_industry, c.company_size AS creator_company_size,
                    c.location AS creator_company_location, c.website AS creator_company_website,
                    c.status AS creator_company_status,
                    ru.name AS reporter_name, ru.email AS reporter_user_email,
                    (SELECT COUNT(*) FROM reports r2 WHERE r2.target_type=r.target_type AND r2.target_id=r.target_id) AS total_report_count,
                    (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id=p.id) AS live_likes_count,
                    (SELECT COUNT(*) FROM comments cm WHERE cm.post_id=p.id AND cm.status='active') AS live_comments_count
             FROM reports r
             LEFT JOIN posts p ON r.target_type='post' AND p.id=r.target_id
             LEFT JOIN users u ON u.id=p.user_id
             LEFT JOIN companies c ON c.user_id=u.id
             LEFT JOIN users ru ON ru.id=r.reporter_id
             WHERE r.status='pending'
             ORDER BY r.id DESC
             LIMIT {$limit}"
        );

        // Batch-fetch all comments in ONE query instead of one query per report (fixes N+1)
        $postIds = array_values(array_filter(array_unique(
            array_map(fn($r) => (int)($r['post_id'] ?? 0),
                array_filter($reports, fn($r) => ($r['target_type'] ?? '') === 'post'))
        )));

        $commentsByPost = [];
        if (!empty($postIds)) {
            $placeholders = implode(',', array_fill(0, count($postIds), '?'));
            $allComments  = $this->query(
                "SELECT cm.id, cm.post_id, cm.content, cm.status, cm.created_at,
                        cu.name AS author_name, cu.email AS author_email
                 FROM comments cm
                 LEFT JOIN users cu ON cu.id=cm.user_id
                 WHERE cm.post_id IN ({$placeholders})
                 ORDER BY cm.post_id, cm.created_at ASC, cm.id ASC",
                $postIds
            );
            foreach ($allComments as $cm) {
                $commentsByPost[(int)$cm['post_id']][] = $cm;
            }
        }

        foreach ($reports as &$report) {
            $pid = (int)($report['post_id'] ?? 0);
            $report['comments'] = ($pid && isset($commentsByPost[$pid]))
                ? array_slice($commentsByPost[$pid], 0, 50)
                : [];
        }
        unset($report);

        return $reports;
    }

    public function resolve(int $id, string $status, int $adminId): int {
        $rows = $this->execute(
            "UPDATE reports SET status=?, reviewed_by=?, reviewed_at=NOW() WHERE id=?",
            [$status, $adminId, $id]
        );
        if ($rows) $this->logActivity('report_' . $status, "Report #$id marked $status", $adminId);
        return $rows;
    }

    public function pendingCount(): int {
        $r = $this->query("SELECT COUNT(*) as cnt FROM reports WHERE status='pending'");
        return (int)($r[0]['cnt'] ?? 0);
    }

    public function pendingIds(): array {
        $rows = $this->query("SELECT id FROM reports WHERE status='pending'");
        return array_column($rows, 'id');
    }

    public function deletePostTarget(int $reportId, int $adminId): bool {
        $report = $this->queryOne("SELECT * FROM reports WHERE id=? AND target_type='post'", [$reportId]);
        if (!$report) return false;

        $postId = (int) $report['target_id'];
        $this->execute("DELETE FROM reports WHERE target_type='post' AND target_id=?", [$postId]);
        $this->execute("DELETE FROM user_notifications WHERE target_type='post' AND target_id=?", [$postId]);
        $rows = $this->execute("DELETE FROM posts WHERE id=?", [$postId]);
        if ($rows) $this->logActivity('reported_post_deleted', "Reported post #{$postId} permanently deleted", $adminId);
        return $rows > 0;
    }
}

// ─────────────────────────────────────────────────────────────

// app/models/SupportModel.php
class SupportModel extends Model {
    protected string $table = 'support_tickets';

    public function getAll(int $limit = 20): array {
        return $this->query("SELECT * FROM support_tickets ORDER BY id DESC LIMIT ?", [$limit]);
    }

    public function getOpen(int $limit = 20): array {
        return $this->query("SELECT * FROM support_tickets WHERE status='open' ORDER BY id DESC LIMIT ?", [$limit]);
    }

    public function getReplies(int $ticketId): array {
        return $this->query("SELECT * FROM ticket_replies WHERE ticket_id=? ORDER BY id ASC", [$ticketId]);
    }

    public function addReply(int $ticketId, int $adminId, string $message): int {
        return $this->execute(
            "INSERT INTO ticket_replies (ticket_id, sender, admin_id, message) VALUES (?,?,?,?)",
            [$ticketId, 'admin', $adminId, $message]
        );
    }

    public function updateStatus(int $id, string $status): int {
        return $this->execute("UPDATE support_tickets SET status=? WHERE id=?", [$status, $id]);
    }
}

// ─────────────────────────────────────────────────────────────

// app/models/SettingsModel.php
class SettingsModel extends Model {
    protected string $table = 'site_settings';

    public function getAll(): array {
        $rows = $this->query("SELECT `key`, `value` FROM site_settings");
        $out  = [];
        foreach ($rows as $r) $out[$r['key']] = $r['value'];
        return $out;
    }

    public function set(string $key, string $value): int {
        return $this->execute(
            "INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=?",
            [$key, $value, $value]
        );
    }

    public function saveMany(array $kvPairs): void {
        foreach ($kvPairs as $k => $v) $this->set($k, $v);
    }
}

// ─────────────────────────────────────────────────────────────

// app/models/CompanyModel.php
class CompanyModel extends Model {
    protected string $table = 'companies';

    public function getAll(string $status = ''): array {
        $sql    = "SELECT * FROM companies WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND status=?"; $params[] = $status; }
        $sql .= " ORDER BY id DESC LIMIT 50";
        return $this->query($sql, $params);
    }

    public function updateStatus(int $id, string $status, int $adminId): int {
        $rows = $this->execute("UPDATE companies SET status=? WHERE id=?", [$status, $id]);
        if ($rows) $this->logActivity("company_{$status}", "Company #$id set to $status", $adminId);
        return $rows;
    }

    public function updateCompany(int $id, array $data, int $adminId): bool {
        $allowed = ['name','email','phone','industry','company_size','founded_year',
                    'location','website','linkedin_url','description','status','logo','banner'];
        $sets   = [];
        $params = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $sets[]   = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (!empty($sets)) {
            $params[] = $id;
            $this->execute("UPDATE companies SET " . implode(', ', $sets) . " WHERE id = ?", $params);
        }
        $this->logActivity('company_updated', "Company #{$id} profile updated by admin", $adminId);
        return true;
    }

    public function findLinkedUser(int $companyId): ?array {
        return $this->queryOne(
            "SELECT u.* FROM users u JOIN companies c ON c.user_id=u.id WHERE c.id=? LIMIT 1",
            [$companyId]
        );
    }

    public function saveResetTokenForCompany(int $companyId, string $token): bool {
        $rows = $this->execute(
            "UPDATE users u JOIN companies c ON c.user_id=u.id
             SET u.reset_token=?, u.reset_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR)
             WHERE c.id=?",
            [$token, $companyId]
        );
        return $rows > 0;
    }

    public function newCount(): int {
        $r = $this->query("SELECT COUNT(*) as cnt FROM companies WHERE created_at >= NOW() - INTERVAL 24 HOUR");
        return (int)($r[0]['cnt'] ?? 0);
    }

    public function newIds(): array {
        $rows = $this->query("SELECT id FROM companies WHERE created_at >= NOW() - INTERVAL 24 HOUR");
        return array_map(fn($r) => 'c' . $r['id'], $rows);
    }
}

// ─────────────────────────────────────────────────────────────

// app/models/PostModel.php
class PostModel extends Model {
    protected string $table = 'posts';

    public function getAll(string $status = ''): array {
        $sql    = "SELECT p.*, u.name AS author FROM posts p LEFT JOIN users u ON p.user_id=u.id WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND p.status=?"; $params[] = $status; }
        $sql .= " ORDER BY p.id DESC LIMIT 50";
        return $this->query($sql, $params);
    }

    public function updateStatus(int $id, string $status, int $adminId): int {
        $rows = $this->execute("UPDATE posts SET status=? WHERE id=?", [$status, $id]);
        if ($rows) $this->logActivity("post_{$status}", "Post #$id set to $status", $adminId);
        return $rows;
    }

    public function newCount(): int {
        $r = $this->query("SELECT COUNT(*) as cnt FROM posts WHERE created_at >= NOW() - INTERVAL 24 HOUR");
        return (int)($r[0]['cnt'] ?? 0);
    }

    public function newIds(): array {
        $rows = $this->query("SELECT id FROM posts WHERE created_at >= NOW() - INTERVAL 24 HOUR");
        return array_column($rows, 'id');
    }
}

// ─────────────────────────────────────────────────────────────

// app/models/NotificationModel.php
class NotificationModel extends Model {
    protected string $table = 'notifications';

    public function create(array $data, int $adminId): int {
        $status  = $data['status'] ?? 'sent';
        $sentAt  = $status === 'sent' ? date('Y-m-d H:i:s') : null;
        $this->execute(
            "INSERT INTO notifications (recipient, user_id, type, subject, message, status, scheduled_at, sent_at, sent_by)
             VALUES (?,?,?,?,?,?,?,?,?)",
            [
                $data['recipient']    ?? 'all_users',
                $data['user_id']      ?? null,
                $data['type']         ?? 'email',
                $data['subject'],
                $data['message'],
                $status,
                $data['scheduled_at'] ?? null,
                $sentAt,
                $adminId,
            ]
        );
        return (int) $this->lastId();
    }

    public function getAll(int $limit = 30): array {
        return $this->query("SELECT * FROM notifications ORDER BY id DESC LIMIT ?", [$limit]);
    }
}