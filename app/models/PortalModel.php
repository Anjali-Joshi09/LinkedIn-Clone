<?php
class PortalModel extends Model {
    public function findUserByEmail(string $email): ?array {
        return $this->queryOne("SELECT * FROM users WHERE email = ?", [$email]); //MOdel.php
    }

    public function findUser(int $id): ?array {
        return $this->queryOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findCompany(int $id): ?array {
        return $this->queryOne("SELECT * FROM companies WHERE id = ?", [$id]);
    }

    public function registerUser(array $data): int {
        $token = bin2hex(random_bytes(24));
        $this->execute(
            "INSERT INTO users (name,email,phone,password,role,headline,location,status,email_verified,email_token)
             VALUES (?,?,?,?,?,?,?,?,?,?)",
            [
                $data['name'], $data['email'], $data['phone'] ?? null,
                $data['password_hash'] ?? password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role'], $data['headline'] ?? null, $data['location'] ?? null,
                'active', 1, $token
            ]
        );

        $userId = (int) $this->lastId();

        if ($data['role'] === 'company') {
            $this->execute(
                "INSERT INTO companies (user_id,name,email,phone,industry,company_size,description,location,status)
                 VALUES (?,?,?,?,?,?,?,?,?)",
                [
                    $userId,
                    $data['company_name'] ?: $data['name'],
                    $data['email'],
                    $data['phone'] ?? null,
                    $data['industry'] ?? null,
                    $data['company_size'] ?? null,
                    $data['bio'] ?? null,
                    $data['location'] ?? null,
                    'pending'
                ]
            );
        }

        return $userId;
    }

    public function touchLogin(int $id): void {
        $this->execute("UPDATE users SET last_login = NOW(), status = IF(status='pending','active',status) WHERE id = ?", [$id]);
    }

    public function saveResetToken(int $id, string $token): void {
        $this->execute("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id=?", [$token, $id]);
    }

    public function findByResetToken(string $token): ?array {
        return $this->queryOne("SELECT * FROM users WHERE reset_token=? AND reset_expires > NOW()", [$token]);
    }

    public function updatePassword(int $id, string $password): void {
        $this->execute("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?", [password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function feed(int $userId, int $offset = 0): array {
        return $this->query(
            "SELECT p.*,
             u.name, u.headline, u.avatar, u.role AS author_role,
             c.id AS company_id, c.logo AS company_logo,
             (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id=p.id) AS likes,
             (SELECT COUNT(*) FROM comments c2 WHERE c2.post_id=p.id AND c2.status='active') AS comments_count,
             EXISTS(SELECT 1 FROM post_likes pl2 WHERE pl2.post_id=p.id AND pl2.user_id=?) AS liked,
             (SELECT status FROM connections
              WHERE (requester_id=? AND receiver_id=u.id) OR (requester_id=u.id AND receiver_id=?)
              LIMIT 1) AS connection_status,
             EXISTS(SELECT 1 FROM follows f
              WHERE f.follower_id=? AND f.followed_id=u.id) AS is_following_company
             FROM posts p JOIN users u ON u.id=p.user_id
             LEFT JOIN companies c ON c.user_id=u.id
             WHERE p.status='active'
             ORDER BY p.created_at DESC LIMIT 10 OFFSET " . max(0, $offset),
            [$userId, $userId, $userId, $userId]
        );
    }

    public function createPost(int $userId, string $content, ?string $media, string $type): int {
        $user = $this->findUser($userId);
        $this->execute(
            "INSERT INTO posts (user_id, content, media, media_type, author, status) VALUES (?,?,?,?,?, 'active')",
            [$userId, $content, $media, $type, $user['name'] ?? null]
        );

        $postId = (int) $this->lastId();
        $connections = $this->myConnections($userId);
        $posterName = $user['name'] ?? 'Someone';

        foreach ($connections as $conn) {
            $this->notify((int) $conn['id'], 'post', $posterName . ' shared a new post.', 'post', $postId);
        }

        return $postId;
    }

    public function toggleLike(int $postId, int $userId): array {
        $liked = $this->queryOne("SELECT id FROM post_likes WHERE post_id=? AND user_id=?", [$postId, $userId]);

        if ($liked) {
            $this->execute("DELETE FROM post_likes WHERE id=?", [$liked['id']]);
            $this->execute("UPDATE posts SET likes=GREATEST(likes-1,0) WHERE id=?", [$postId]);
            return ['liked' => false];
        }

        $this->execute("INSERT IGNORE INTO post_likes (post_id,user_id) VALUES (?,?)", [$postId, $userId]);
        $this->execute("UPDATE posts SET likes=likes+1 WHERE id=?", [$postId]);

        $post = $this->queryOne("SELECT user_id FROM posts WHERE id=?", [$postId]);
        if ($post && (int) $post['user_id'] !== $userId) {
            $liker = $this->queryOne("SELECT name FROM users WHERE id=?", [$userId]);
            $likerName = $liker ? $liker['name'] : 'Someone';
            $this->notifyWithSender((int) $post['user_id'], 'like', $likerName . ' liked your post.', 'post', $postId, $userId);
        }

        return ['liked' => true];
    }

    public function addComment(int $postId, int $userId, string $content, ?int $parentId = null): array {
        $this->execute(
            "INSERT INTO comments (post_id,user_id,content,parent_id) VALUES (?,?,?,?)",
            [$postId, $userId, $content, $parentId]
        );

        $this->execute("UPDATE posts SET comments_count=comments_count+1 WHERE id=?", [$postId]);

        $comment = $this->queryOne(
            "SELECT c.*, u.name, u.avatar
             FROM comments c
             JOIN users u ON u.id=c.user_id
             WHERE c.id=?",
            [(int) $this->lastId()]
        );

        $post = $this->queryOne("SELECT user_id FROM posts WHERE id=?", [$postId]);

        if ($post && (int) $post['user_id'] !== $userId) {
            $commenter = $this->queryOne("SELECT name FROM users WHERE id=?", [$userId]);
            $commenterName = $commenter ? $commenter['name'] : 'Someone';
            $this->notifyWithSender((int) $post['user_id'], 'comment', $commenterName . ' commented on your post.', 'post', $postId, $userId);
        }

        return $comment ?: [];
    }

    public function comments(int $postId): array {
        $rows = $this->query(
            "SELECT c.*, u.name, u.avatar
             FROM comments c
             JOIN users u ON u.id=c.user_id
             WHERE c.post_id=? AND c.status='active'
             ORDER BY c.id ASC",
            [$postId]
        );

        $top = [];
        $map = [];

        foreach ($rows as $r) {
            $map[$r['id']] = $r + ['replies' => []];
        }

        foreach ($map as $id => $r) {
            if (!empty($r['parent_id']) && isset($map[$r['parent_id']])) {
                $map[$r['parent_id']]['replies'][] = $r;
            } else {
                $top[] = &$map[$id];
            }
        }

        $result = [];
        foreach ($top as &$t) {
            $t['replies'] = $map[$t['id']]['replies'];
            $result[] = $t;
        }

        return $result;
    }

    public function deleteComment(int $commentId, int $userId, bool $isPostOwner = false): bool {
        $comment = $this->queryOne("SELECT post_id FROM comments WHERE id=? AND status='active'", [$commentId]);
        if (!$comment) return false;

        if ($isPostOwner) {
            $this->execute("UPDATE comments SET status='deleted' WHERE id=?", [$commentId]);
        } else {
            $this->execute("UPDATE comments SET status='deleted' WHERE id=? AND user_id=?", [$commentId, $userId]);
        }

        if ($comment['post_id']) {
            $this->execute(
                "UPDATE posts SET comments_count=GREATEST(comments_count-1,0) WHERE id=?",
                [(int) $comment['post_id']]
            );
        }

        return true;
    }

    public function commentCount(int $postId): int {
        $row = $this->queryOne("SELECT COUNT(*) as cnt FROM comments WHERE post_id=? AND status='active'", [$postId]);
        return (int) ($row['cnt'] ?? 0);
    }

    public function withdrawApplication(int $jobId, int $userId): bool {
        $this->execute("DELETE FROM applications WHERE job_id=? AND user_id=?", [$jobId, $userId]);
        $count = (int) $this->scalar("SELECT COUNT(*) FROM applications WHERE job_id=?", [$jobId]);
        $this->execute("UPDATE jobs SET applications_count=? WHERE id=?", [$count, $jobId]);
        return true;
    }

    public function postOwner(int $postId): int {
        $row = $this->queryOne("SELECT user_id FROM posts WHERE id=?", [$postId]);
        return (int) ($row['user_id'] ?? 0);
    }

    public function deletePost(int $postId, int $userId): bool {
        $owner = $this->postOwner($postId);
        if ($owner !== $userId) return false;
        $this->execute("DELETE FROM post_likes WHERE post_id=?", [$postId]);
        $this->execute("DELETE FROM comments WHERE post_id=?", [$postId]);
        $this->execute("DELETE FROM posts WHERE id=? AND user_id=?", [$postId, $userId]);
        return true;
    }

    public function createReport(int $reporterId, string $targetType, int $targetId, string $type, string $reason): void {
        $user = $this->findUser($reporterId);
        $this->execute(
            "INSERT INTO reports (reporter_id, reporter_email, target_type, target_id, type, reason, status) VALUES (?,?,?,?,?,?,'pending')",
            [$reporterId, $user['email'] ?? null, $targetType, $targetId, $type, $reason]
        );
    }

    public function likeCount(int $postId): int {
        $row = $this->queryOne("SELECT COUNT(*) as cnt FROM post_likes WHERE post_id=?", [$postId]);
        return (int) ($row['cnt'] ?? 0);
    }

    public function suggestedConnections(int $userId): array {
        return $this->query(
            "SELECT u.id, u.name, u.headline, u.avatar, u.location,
             (SELECT COUNT(*) FROM connections c2
              JOIN connections c3 ON (c3.requester_id = c2.receiver_id OR c3.receiver_id = c2.requester_id)
              WHERE (c2.requester_id=? OR c2.receiver_id=?)
              AND c2.status='accepted'
              AND (c3.requester_id=u.id OR c3.receiver_id=u.id)
              AND c3.status='accepted') AS mutual_count
             FROM users u
             WHERE u.role='user'
             AND u.id <> ?
             AND NOT EXISTS (
                SELECT 1 FROM connections c
                WHERE (c.requester_id=? AND c.receiver_id=u.id)
                   OR (c.receiver_id=? AND c.requester_id=u.id)
             )
             ORDER BY mutual_count DESC, u.id DESC LIMIT 12",
            [$userId, $userId, $userId, $userId, $userId]
        );
    }

    public function connectionRequests(int $userId): array {
        return $this->query(
            "SELECT c.*, u.name, u.headline, u.avatar
             FROM connections c
             JOIN users u ON u.id=c.requester_id
             WHERE c.receiver_id=? AND c.status='pending'
             ORDER BY c.id DESC",
            [$userId]
        );
    }

    public function myConnections(int $userId): array {
        return $this->query(
            "SELECT u.id, u.name, u.headline, u.avatar
             FROM connections c
             JOIN users u ON u.id = IF(c.requester_id=?, c.receiver_id, c.requester_id)
             WHERE (c.requester_id=? OR c.receiver_id=?) AND c.status='accepted'
             ORDER BY u.name ASC",
            [$userId, $userId, $userId]
        );
    }

    public function sendConnection(int $from, int $to): void {
        if ($from === $to) return;

        $this->execute(
            "INSERT IGNORE INTO connections (requester_id,receiver_id,status) VALUES (?,?, 'pending')",
            [$from, $to]
        );

        $sender = $this->queryOne("SELECT name FROM users WHERE id=?", [$from]);
        $senderName = $sender ? $sender['name'] : 'Someone';
        $this->notify($to, 'connection', $senderName . ' sent you a connection request.', 'user', $from);
    }

    public function removeConnection(int $userId, int $otherId): void {
        $this->execute(
            "DELETE FROM connections WHERE (requester_id=? AND receiver_id=?) OR (requester_id=? AND receiver_id=?)",
            [$userId, $otherId, $otherId, $userId]
        );
    }

    public function updateConnection(int $requesterId, int $receiverId, string $status): void {
        $row = $this->queryOne(
            "SELECT * FROM connections WHERE requester_id=? AND receiver_id=? AND status='pending'",
            [$requesterId, $receiverId]
        );
        if (!$row) return;

        $this->execute("UPDATE connections SET status=? WHERE id=?", [$status, $row['id']]);

        if ($status === 'accepted') {
            $acceptor = $this->queryOne("SELECT name FROM users WHERE id=?", [$receiverId]);
            $acceptorName = $acceptor ? $acceptor['name'] : 'Someone';
            $this->notify($requesterId, 'connection', $acceptorName . ' accepted your connection request.', 'user', $receiverId);
        }
    }

    public function jobs(array $filters = [], int $userId = 0): array {
        $sql = "SELECT j.*, c.name company, c.logo,
                EXISTS(SELECT 1 FROM saved_jobs sj WHERE sj.job_id=j.id AND sj.user_id=?) AS saved,
                EXISTS(SELECT 1 FROM applications a WHERE a.job_id=j.id AND a.user_id=?) AS applied
                FROM jobs j
                JOIN companies c ON c.id=j.company_id
                WHERE j.status = 'approved'";

        $params = [$userId, $userId];

        foreach (['location', 'job_type', 'experience_level'] as $field) {
            if (!empty($filters[$field])) {
                $sql .= " AND j.$field = ?";
                $params[] = $filters[$field];
            }
        }

        if (!empty($filters['q'])) {
            $sql .= " AND (j.title LIKE ? OR c.name LIKE ? OR j.description LIKE ?)";
            $like = '%' . $filters['q'] . '%';
            array_push($params, $like, $like, $like);
        }

        $sql .= " ORDER BY j.is_featured DESC, j.created_at DESC LIMIT 50";

        return $this->query($sql, $params);
    }

    public function applyJob(int $jobId, int $userId, string $letter, ?string $resume): void {
        $this->execute(
            "INSERT IGNORE INTO applications (job_id,user_id,cover_letter,resume,status) VALUES (?,?,?,?, 'applied')",
            [$jobId, $userId, $letter, $resume]
        );

        // Use a temp subquery alias to avoid MySQL same-table update restriction
        $count = (int) $this->scalar(
            "SELECT COUNT(*) FROM applications WHERE job_id=?",
            [$jobId]
        );
        $this->execute(
            "UPDATE jobs SET applications_count=? WHERE id=?",
            [$count, $jobId]
        );

        $job = $this->queryOne(
            "SELECT c.user_id, j.title
             FROM jobs j
             JOIN companies c ON c.id=j.company_id
             WHERE j.id=?",
            [$jobId]
        );

        if ($job && $job['user_id']) {
            $this->notify((int) $job['user_id'], 'application', 'New application for ' . $job['title'], 'job', $jobId);
        }
    }

    public function deleteJob(int $jobId, int $companyId): void {
        $this->execute("DELETE FROM applications WHERE job_id=?", [$jobId]);
        $this->execute("DELETE FROM saved_jobs WHERE job_id=?", [$jobId]);
        $this->execute("DELETE FROM jobs WHERE id=? AND company_id=?", [$jobId, $companyId]);
    }

    public function toggleSaveJob(int $jobId, int $userId): bool {
        $exists = $this->queryOne("SELECT id FROM saved_jobs WHERE job_id=? AND user_id=?", [$jobId, $userId]);

        if ($exists) {
            $this->execute("DELETE FROM saved_jobs WHERE job_id=? AND user_id=?", [$jobId, $userId]);
            return false;
        }

        $this->execute("INSERT IGNORE INTO saved_jobs (job_id,user_id) VALUES (?,?)", [$jobId, $userId]);
        return true;
    }

    // ── Real-time job feed ────────────────────────────────────────────────

    /**
     * Returns approved jobs posted AFTER the user's last visit to the Jobs board.
     * Each row includes saved/applied flags so the view can render them normally.
     */
    public function getNewJobs(int $userId): array {
        // Fetch the timestamp of the user's last Jobs-board visit
        $row = $this->queryOne(
            "SELECT seen_at FROM user_job_last_seen WHERE user_id = ?",
            [$userId]
        );

        // If the user has never visited, treat "now minus 7 days" as baseline
        // so we don't flood them with every job ever posted on first load.
        $since = $row ? $row['seen_at'] : date('Y-m-d H:i:s', strtotime('-7 days'));

        return $this->query(
            "SELECT j.*, c.name AS company, c.logo,
                    EXISTS(SELECT 1 FROM saved_jobs sj WHERE sj.job_id = j.id AND sj.user_id = ?) AS saved,
                    EXISTS(SELECT 1 FROM applications a  WHERE a.job_id  = j.id AND a.user_id  = ?) AS applied
             FROM   jobs j
             JOIN   companies c ON c.id = j.company_id
             WHERE  j.status = 'approved'
             AND    j.created_at > ?
             ORDER  BY j.is_featured DESC, j.created_at DESC
             LIMIT  50",
            [$userId, $userId, $since]
        );
    }

    /**
     * Upserts the user's last-seen timestamp for the Jobs board.
     * Call this whenever the user opens /jobs-board.
     */
    public function markJobsSeen(int $userId): void {
        $this->execute(
            "INSERT INTO user_job_last_seen (user_id, seen_at)
             VALUES (?, NOW())
             ON DUPLICATE KEY UPDATE seen_at = NOW()",
            [$userId]
        );
    }

    // ── End real-time job feed ────────────────────────────────────────────

    public function savedJobs(int $userId): array {
        return $this->query(
            "SELECT j.*, c.name company
             FROM saved_jobs s
             JOIN jobs j ON j.id=s.job_id
             JOIN companies c ON c.id=j.company_id
             WHERE s.user_id=?
             ORDER BY s.id DESC",
            [$userId]
        );
    }

    public function applications(int $userId): array {
        return $this->query(
            "SELECT a.*, j.title, c.name company
             FROM applications a
             JOIN jobs j ON j.id=a.job_id
             JOIN companies c ON c.id=j.company_id
             WHERE a.user_id=?
             ORDER BY a.id DESC",
            [$userId]
        );
    }

    public function companyByUser(int $userId): ?array {
        return $this->queryOne("SELECT * FROM companies WHERE user_id=?", [$userId]);
    }

    public function recruiterStats(int $companyId): array {
        return [
            'jobs' => (int) $this->scalar("SELECT COUNT(*) FROM jobs WHERE company_id=?", [$companyId]),
            'active' => (int) $this->scalar("SELECT COUNT(*) FROM jobs WHERE company_id=? AND status IN ('approved','pending')", [$companyId]),
            'applicants' => (int) $this->scalar("SELECT COUNT(*) FROM applications a JOIN jobs j ON j.id=a.job_id WHERE j.company_id=?", [$companyId]),
            'interviews' => (int) $this->scalar("SELECT COUNT(*) FROM applications a JOIN jobs j ON j.id=a.job_id WHERE j.company_id=? AND a.status='interview'", [$companyId]),
        ];
    }

    public function recruiterJobs(int $companyId): array {
        return $this->query("SELECT * FROM jobs WHERE company_id=? ORDER BY id DESC", [$companyId]);
    }

    public function saveJob(array $data, int $companyId): int {
        if (!empty($data['id'])) {
            $this->execute(
                "UPDATE jobs
                 SET title=?,description=?,requirements=?,benefits=?,location=?,job_type=?,experience_level=?,salary_min=?,salary_max=?,expires_at=?
                 WHERE id=? AND company_id=?",
                [
                    $data['title'],
                    $data['description'],
                    $data['requirements'],
                    $data['benefits'],
                    $data['location'],
                    $data['job_type'],
                    $data['experience_level'],
                    $data['salary_min'],
                    $data['salary_max'],
                    $data['expires_at'],
                    $data['id'],
                    $companyId
                ]
            );

            return (int) $data['id'];
        }

        $this->execute(
            "INSERT INTO jobs (company_id,title,description,requirements,benefits,location,job_type,experience_level,salary_min,salary_max,status,expires_at)
             VALUES (?,?,?,?,?,?,?,?,?,?, 'pending', ?)",
            [
                $companyId,
                $data['title'],
                $data['description'],
                $data['requirements'],
                $data['benefits'],
                $data['location'],
                $data['job_type'],
                $data['experience_level'],
                $data['salary_min'],
                $data['salary_max'],
                $data['expires_at']
            ]
        );

        return (int) $this->lastId();
    }

    public function applicants(int $companyId): array {
        return $this->query(
            "SELECT a.*, u.id as user_id, u.name, u.email, u.headline, u.location, u.avatar, u.skills, u.experience, j.title
             FROM applications a
             JOIN jobs j ON j.id=a.job_id
             JOIN users u ON u.id=a.user_id
             WHERE j.company_id=?
             ORDER BY FIELD(a.status,'applied','reviewing','shortlisted','interview','hired','rejected'), a.id DESC",
            [$companyId]
        );
    }

    public function updateApplicationStatus(int $appId, int $companyId, string $status): void {
        $app = $this->queryOne(
            "SELECT a.user_id, j.title
             FROM applications a
             JOIN jobs j ON j.id=a.job_id
             WHERE a.id=? AND j.company_id=?",
            [$appId, $companyId]
        );

        if (!$app) return;

        $this->execute("UPDATE applications SET status=? WHERE id=?", [$status, $appId]);
        $this->notify((int) $app['user_id'], 'application', 'Your application for ' . $app['title'] . ' is now ' . $status . '.', 'application', $appId);
    }

    public function conversations(int $userId): array {
        return $this->query(
            "SELECT other_user.id, other_user.name, other_user.headline, other_user.avatar, MAX(m.created_at) last_at,
             SUBSTRING_INDEX(GROUP_CONCAT(m.body ORDER BY m.created_at DESC SEPARATOR '||'), '||', 1) last_message,
             SUM(CASE WHEN m.receiver_id=? AND m.seen_at IS NULL THEN 1 ELSE 0 END) AS unread_count
             FROM messages m
             JOIN users other_user ON other_user.id = IF(m.sender_id=?, m.receiver_id, m.sender_id)
             WHERE m.sender_id=? OR m.receiver_id=?
             GROUP BY other_user.id, other_user.name, other_user.headline, other_user.avatar
             ORDER BY last_at DESC",
            [$userId, $userId, $userId, $userId]
        );
    }

    public function messages(int $userId, int $with): array {
        $this->execute(
            "UPDATE messages SET seen_at=NOW() WHERE receiver_id=? AND sender_id=? AND seen_at IS NULL",
            [$userId, $with]
        );

        return $this->query(
            "SELECT m.*, s.name sender_name
             FROM messages m
             JOIN users s ON s.id=m.sender_id
             WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
             ORDER BY m.id ASC",
            [$userId, $with, $with, $userId]
        );
    }

    public function sendMessage(int $from, int $to, string $body, ?string $file = null): array {
        $this->execute(
            "INSERT INTO messages (sender_id,receiver_id,body,attachment) VALUES (?,?,?,?)",
            [$from, $to, $body, $file]
        );

        $this->notify($to, 'message', 'You have a new message.', 'message', $from);

        return $this->queryOne("SELECT * FROM messages WHERE id=?", [(int) $this->lastId()]) ?: [];
    }

    public function notificationsFor(int $userId): array {
        return $this->query(
            "SELECT n.*,
             COALESCE(su.name, tu.name) AS sender_name,
             COALESCE(su.avatar, tu.avatar) AS sender_avatar
             FROM user_notifications n
             LEFT JOIN users su ON su.id = n.sender_id
             LEFT JOIN users tu ON tu.id = n.target_id AND n.target_type = 'user'
             WHERE n.user_id=?
             ORDER BY n.id DESC LIMIT 30",
            [$userId]
        );
    }

    public function notify(int $userId, string $type, string $message, ?string $targetType = null, ?int $targetId = null): void {
        $this->execute(
            "INSERT INTO user_notifications (user_id,type,message,target_type,target_id) VALUES (?,?,?,?,?)",
            [$userId, $type, $message, $targetType, $targetId]
        );
    }

    public function notifyWithSender(int $userId, string $type, string $message, ?string $targetType = null, ?int $targetId = null, ?int $senderId = null): void {
        $this->execute(
            "INSERT INTO user_notifications (user_id,type,message,target_type,target_id,sender_id) VALUES (?,?,?,?,?,?)",
            [$userId, $type, $message, $targetType, $targetId, $senderId]
        );
    }

    public function setCompanyPending(int $userId): void {
        $this->execute("UPDATE companies SET status='pending' WHERE user_id=?", [$userId]);
    }

    public function updateCompanyExtra(int $userId, array $data): void {
        $this->execute(
            "UPDATE companies
             SET website=COALESCE(NULLIF(?,\"\"),website),
                 linkedin_url=COALESCE(NULLIF(?,\"\"),linkedin_url),
                 description=COALESCE(NULLIF(?,\"\"),description)
             WHERE user_id=?",
            [
                $data['website'] ?? null,
                $data['linkedin_url'] ?? null,
                $data['description'] ?? null,
                $userId
            ]
        );
    }

    public function getJobById(int $jobId): ?array {
        return $this->queryOne("SELECT * FROM jobs WHERE id=?", [$jobId]);
    }

    public function markNotificationsRead(int $userId): void {
        $this->execute("UPDATE user_notifications SET read_at=NOW() WHERE user_id=? AND read_at IS NULL", [$userId]);
    }

    public function markOneNotificationRead(int $notifId, int $userId): void {
        $this->execute("UPDATE user_notifications SET read_at=NOW() WHERE id=? AND user_id=? AND read_at IS NULL", [$notifId, $userId]);
    }

    public function search(string $q, int $userId = 0): array {
        // Use FULLTEXT search (MATCH...AGAINST) instead of LIKE on TEXT columns.
        // LIKE '%term%' cannot use indexes and does a full table scan.
        // Requires FULLTEXT indexes — see database/schema.sql for ADD FULLTEXT INDEX statements.
        // Falls back to LIKE if the query is too short for FULLTEXT (< 3 chars).
        $like = '%' . $q . '%';
        $useFulltext = strlen($q) >= 3;

        // ── Users ──────────────────────────────────────────────
        if ($useFulltext) {
            $users = $this->query(
                "SELECT u.id, u.name, u.headline, u.avatar,
                        CASE WHEN EXISTS (
                            SELECT 1 FROM connections c
                            WHERE c.status = 'accepted'
                            AND (
                                (c.requester_id = ? AND c.receiver_id = u.id)
                                OR (c.receiver_id = ? AND c.requester_id = u.id)
                            )
                        ) THEN 1 ELSE 0 END AS connected
                 FROM users u
                 WHERE u.role='user' AND u.status='active' AND u.id != ?
                   AND MATCH(u.name, u.headline) AGAINST(? IN BOOLEAN MODE)
                 LIMIT 6",
                [$userId, $userId, $userId, $q . '*']
            );
        } else {
            $users = $this->query(
                "SELECT u.id, u.name, u.headline, u.avatar, 0 AS connected
                 FROM users u
                 WHERE u.role='user' AND u.status='active' AND u.id != ?
                   AND (u.name LIKE ? OR u.headline LIKE ?)
                 LIMIT 6",
                [$userId, $like, $like]
            );
        }

        // ── Companies ──────────────────────────────────────────
        if ($useFulltext) {
            $companies = $this->query(
                "SELECT c.id, c.user_id, c.name, c.industry, c.logo, u.name AS recruiter_name,
                        CASE WHEN EXISTS (
                            SELECT 1 FROM follows f
                            WHERE f.follower_id = ? AND f.followed_id = c.user_id
                        ) THEN 1 ELSE 0 END AS followed
                 FROM companies c
                 JOIN users u ON u.id = c.user_id
                 WHERE c.status='verified'
                   AND MATCH(c.name, c.industry) AGAINST(? IN BOOLEAN MODE)
                 LIMIT 6",
                [$userId, $q . '*']
            );
        } else {
            $companies = $this->query(
                "SELECT c.id, c.user_id, c.name, c.industry, c.logo, u.name AS recruiter_name, 0 AS followed
                 FROM companies c
                 JOIN users u ON u.id = c.user_id
                 WHERE c.status='verified' AND (c.name LIKE ? OR c.industry LIKE ?)
                 LIMIT 6",
                [$like, $like]
            );
        }

        // ── Jobs ───────────────────────────────────────────────
        if ($useFulltext) {
            $jobs = $this->query(
                "SELECT j.id, j.title, c.name company
                 FROM jobs j
                 JOIN companies c ON c.id=j.company_id
                 WHERE j.status='approved'
                   AND MATCH(j.title) AGAINST(? IN BOOLEAN MODE)
                 LIMIT 6",
                [$q . '*']
            );
        } else {
            $jobs = $this->query(
                "SELECT j.id, j.title, c.name company
                 FROM jobs j
                 JOIN companies c ON c.id=j.company_id
                 WHERE j.status='approved' AND j.title LIKE ?
                 LIMIT 6",
                [$like]
            );
        }

        // ── Posts ──────────────────────────────────────────────
        if ($useFulltext) {
            $posts = $this->query(
                "SELECT id, content, author FROM posts
                 WHERE status='active'
                   AND MATCH(content) AGAINST(? IN BOOLEAN MODE)
                 LIMIT 6",
                [$q . '*']
            );
        } else {
            $posts = $this->query(
                "SELECT id, content, author FROM posts
                 WHERE status='active' AND content LIKE ?
                 LIMIT 6",
                [$like]
            );
        }

        return compact('users', 'companies', 'jobs', 'posts');
    }

    public function updateProfile(int $userId, array $data): void {
        $this->execute(
            "UPDATE users
             SET name=?, phone=?, headline=?, bio=?, location=?, website=?,
                 avatar=COALESCE(?,avatar), cover=COALESCE(?,cover), resume=COALESCE(?,resume),
                 skills=?, experience=?, education=?, certifications=?, languages=?, social_links=?, profile_public=?
             WHERE id=?",
            [
                $data['name'],
                $data['phone'],
                $data['headline'],
                $data['bio'],
                $data['location'],
                $data['website'],
                $data['avatar'],
                $data['cover'],
                $data['resume'],
                $data['skills'],
                $data['experience'],
                $data['education'],
                $data['certifications'],
                $data['languages'],
                $data['social_links'],
                $data['profile_public'],
                $userId
            ]
        );
    }

    public function updateCompany(int $companyId, array $data): void {
        $this->execute(
            "UPDATE companies
             SET name=?, phone=?, website=?, industry=?, company_size=?, description=?, location=?, linkedin_url=?,
                 logo=COALESCE(?,logo), banner=COALESCE(?,banner)
             WHERE id=?",
            [
                $data['name'],
                $data['phone'],
                $data['website'],
                $data['industry'],
                $data['company_size'],
                $data['description'],
                $data['location'],
                $data['linkedin_url'],
                $data['logo'],
                $data['banner'],
                $companyId
            ]
        );
    }

    public function getFollowers(int $companyUserId): array {
        // Return list of users who follow this company
        return $this->query(
            "SELECT u.id, u.name, u.role, u.avatar, u.headline
             FROM follows f
             JOIN users u ON u.id = f.follower_id
             WHERE f.followed_id = ?
             ORDER BY f.id DESC",
            [$companyUserId]
        );
    }

    public function newApplicationsCount(int $companyId): int {
        // Count applications on this company's jobs submitted after last seen
        $row = $this->queryOne(
            "SELECT COUNT(*) AS cnt
             FROM applications a
             JOIN jobs j ON j.id = a.job_id
             WHERE j.company_id = ?
               AND a.created_at > COALESCE(
                   (SELECT seen_at FROM company_applicants_last_seen WHERE company_id = ?),
                   '1970-01-01'
               )",
            [$companyId, $companyId]
        );
        return $row ? (int)$row["cnt"] : 0;
    }

    public function markApplicantsSeen(int $companyId): void {
        $this->execute(
            "INSERT INTO company_applicants_last_seen (company_id, seen_at)
             VALUES (?, NOW())
             ON DUPLICATE KEY UPDATE seen_at = NOW()",
            [$companyId]
        );
    }

    public function newFollowersCount(int $companyUserId): int {
        try {
            // Ensure tracking table exists
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS company_network_last_seen (
                    company_user_id INT UNSIGNED NOT NULL PRIMARY KEY,
                    seen_at DATETIME NOT NULL
                ) ENGINE=InnoDB"
            );

            // Fetch last seen time separately to avoid MySQL subquery ambiguity
            $stmt = $this->db->prepare(
                "SELECT seen_at FROM company_network_last_seen WHERE company_user_id = ?"
            );
            $stmt->execute([$companyUserId]);
            $lastSeen = $stmt->fetchColumn();
            if (!$lastSeen) $lastSeen = '1970-01-01';

            $stmt2 = $this->db->prepare(
                "SELECT COUNT(*) AS cnt FROM follows
                 WHERE followed_id = ?
                   AND created_at > ?"
            );
            $stmt2->execute([$companyUserId, $lastSeen]);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row["cnt"] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function markNetworkSeen(int $companyUserId): void {
        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS company_network_last_seen (
                    company_user_id INT UNSIGNED NOT NULL PRIMARY KEY,
                    seen_at DATETIME NOT NULL
                ) ENGINE=InnoDB"
            );
            $stmt = $this->db->prepare(
                "INSERT INTO company_network_last_seen (company_user_id, seen_at)
                 VALUES (?, NOW())
                 ON DUPLICATE KEY UPDATE seen_at = NOW()"
            );
            $stmt->execute([$companyUserId]);
        } catch (\Exception $e) {
            // silently fail — non-critical tracking
        }
    }

    /**
     * For regular users: the nav badge is a live COUNT of pending connections,
     * so no separate "last seen" table is needed. This method is a no-op placeholder
     * kept here so the controller can call it symmetrically with markNetworkSeen().
     * The badge clears client-side on click (clearNetworkBadge) and naturally
     * drops to 0 in the DB once the user accepts/rejects all pending requests.
     */
    public function markUserNetworkSeen(int $userId): void {
        // No-op: user network badge is driven by live pending-connection count.
    }

    public function toggleFollowCompany(int $companyId, int $userId): bool {
        // follows table: follower_id = current user, followed_id = company's user_id
        // companyId here is actually the company's user_id (passed from controller)
        $exists = $this->queryOne(
            "SELECT id FROM follows WHERE follower_id=? AND followed_id=?",
            [$userId, $companyId]
        );
        if ($exists) {
            $this->execute("DELETE FROM follows WHERE follower_id=? AND followed_id=?", [$userId, $companyId]);
            return false;
        } else {
            $this->execute("INSERT IGNORE INTO follows (follower_id, followed_id) VALUES (?,?)", [$userId, $companyId]);
            return true;
        }
    }
}