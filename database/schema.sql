-- ============================================================
--  LinkedIn — Full Database Schema
--  Run this in phpMyAdmin or MySQL CLI:
--    mysql -u root -p linkedin_admin < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS linkedin_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE linkedin_admin;

-- ── ADMINS ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,           -- bcrypt hash
    role         ENUM('super_admin','admin','moderator') DEFAULT 'admin',
    avatar           VARCHAR(255)  DEFAULT NULL,
    reset_token      VARCHAR(100)  DEFAULT NULL,
    reset_token_at   DATETIME      DEFAULT NULL,
    last_login       DATETIME      DEFAULT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default super-admin  (password: Admin@1234)
-- NOTE: Do NOT use this INSERT directly — the hash below is a placeholder and login will fail.
-- Run public/setup.php in your browser to create a working admin account (password: Admin@1234).
-- Alternatively: php -r "echo password_hash('Admin@1234', PASSWORD_DEFAULT);" to generate a real hash.
-- INSERT INTO admins (name, email, password, role) VALUES
-- ('Super Admin', 'admin@site.com', '<paste_generated_hash_here>', 'super_admin');

-- ── USERS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    phone           VARCHAR(20)   DEFAULT NULL,
    password        VARCHAR(255)  NOT NULL,
    role            ENUM('user','company') DEFAULT 'user',
    avatar          VARCHAR(255)  DEFAULT NULL,
    headline        VARCHAR(255)  DEFAULT NULL,
    bio             TEXT          DEFAULT NULL,
    location        VARCHAR(150)  DEFAULT NULL,
    website         VARCHAR(255)  DEFAULT NULL,
    status          ENUM('active','blocked','pending','suspended') DEFAULT 'pending',
    email_verified  TINYINT(1)    DEFAULT 0,
    email_token     VARCHAR(100)  DEFAULT NULL,
    reset_token     VARCHAR(100)  DEFAULT NULL,
    reset_expires   DATETIME      DEFAULT NULL,
    last_login      DATETIME      DEFAULT NULL,
    created_at      DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email  (email),
    INDEX idx_status (status),
    INDEX idx_role   (role)
) ENGINE=InnoDB;

-- ── COMPANIES ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS companies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED  DEFAULT NULL,     -- linked user account
    name            VARCHAR(200)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    phone           VARCHAR(20)   DEFAULT NULL,
    website         VARCHAR(255)  DEFAULT NULL,
    logo            VARCHAR(255)  DEFAULT NULL,
    banner          VARCHAR(255)  DEFAULT NULL,
    industry        VARCHAR(100)  DEFAULT NULL,
    company_size    VARCHAR(50)   DEFAULT NULL,     -- e.g. "50-200"
    founded_year    YEAR          DEFAULT NULL,
    description     TEXT          DEFAULT NULL,
    location        VARCHAR(200)  DEFAULT NULL,
    linkedin_url    VARCHAR(255)  DEFAULT NULL,
    status          ENUM('pending','verified','blocked','rejected') DEFAULT 'pending',
    jobs_count      INT UNSIGNED  DEFAULT 0,
    created_at      DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ── JOBS ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS jobs (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id       INT UNSIGNED  NOT NULL,
    title            VARCHAR(200)  NOT NULL,
    description      LONGTEXT      NOT NULL,
    requirements     TEXT          DEFAULT NULL,
    benefits         TEXT          DEFAULT NULL,
    location         VARCHAR(200)  DEFAULT NULL,
    job_type         ENUM('full_time','part_time','remote','contract','internship') DEFAULT 'full_time',
    experience_level ENUM('entry','mid','senior','executive') DEFAULT 'mid',
    salary_min       DECIMAL(12,2) DEFAULT NULL,
    salary_max       DECIMAL(12,2) DEFAULT NULL,
    salary_currency  VARCHAR(10)   DEFAULT 'USD',
    is_featured      TINYINT(1)    DEFAULT 0,
    status           ENUM('pending','approved','rejected','expired','closed') DEFAULT 'pending',
    applications_count INT UNSIGNED DEFAULT 0,
    views_count      INT UNSIGNED  DEFAULT 0,
    expires_at       DATE          DEFAULT NULL,
    created_at       DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_status     (status),
    INDEX idx_featured   (is_featured),
    INDEX idx_company    (company_id)
) ENGINE=InnoDB;

-- ── APPLICATIONS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS applications (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id       INT UNSIGNED  NOT NULL,
    user_id      INT UNSIGNED  NOT NULL,
    cover_letter TEXT          DEFAULT NULL,
    resume       VARCHAR(255)  DEFAULT NULL,     -- file path
    status       ENUM('applied','shortlisted','interview','offered','rejected','withdrawn') DEFAULT 'applied',
    notes        TEXT          DEFAULT NULL,     -- internal recruiter notes
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_application (job_id, user_id),
    FOREIGN KEY (job_id)  REFERENCES jobs(id)  ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status  (status),
    INDEX idx_job     (job_id),
    INDEX idx_user    (user_id)
) ENGINE=InnoDB;

-- ── POSTS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS posts (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED  NOT NULL,
    content        TEXT          NOT NULL,
    media          VARCHAR(255)  DEFAULT NULL,    -- image/video URL
    media_type     ENUM('image','video','document','none') DEFAULT 'none',
    visibility     ENUM('public','connections','private') DEFAULT 'public',
    likes          INT UNSIGNED  DEFAULT 0,
    comments_count INT UNSIGNED  DEFAULT 0,
    shares_count   INT UNSIGNED  DEFAULT 0,
    status         ENUM('active','hidden','reported','offensive','deleted') DEFAULT 'active',
    author         VARCHAR(100)  DEFAULT NULL,    -- denormalized for quick display
    created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status  (status),
    INDEX idx_user    (user_id)
) ENGINE=InnoDB;

-- ── REPORTS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reports (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id    INT UNSIGNED  DEFAULT NULL,
    reporter_email VARCHAR(150)  DEFAULT NULL,
    target_type    ENUM('user','post','job','company','comment') NOT NULL,
    target_id      INT UNSIGNED  NOT NULL,
    type           ENUM('spam','offensive','fake','harassment','copyright','other') DEFAULT 'other',
    reason         TEXT          NOT NULL,
    status         ENUM('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
    admin_note     TEXT          DEFAULT NULL,
    reviewed_by    INT UNSIGNED  DEFAULT NULL,
    reviewed_at    DATETIME      DEFAULT NULL,
    created_at     DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id)  ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status      (status),
    INDEX idx_target_type (target_type)
) ENGINE=InnoDB;

-- ── SUPPORT TICKETS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS support_tickets (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED  DEFAULT NULL,
    email        VARCHAR(150)  NOT NULL,
    name         VARCHAR(100)  DEFAULT NULL,
    subject      VARCHAR(255)  NOT NULL,
    message      TEXT          NOT NULL,
    priority     ENUM('low','normal','high','urgent') DEFAULT 'normal',
    status       ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    assigned_to  INT UNSIGNED  DEFAULT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)  ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status   (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB;

-- ── TICKET REPLIES ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ticket_replies (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id  INT UNSIGNED  NOT NULL,
    sender     ENUM('user','admin') NOT NULL,
    admin_id   INT UNSIGNED  DEFAULT NULL,
    message    TEXT          NOT NULL,
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id)  REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── NOTIFICATIONS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient    ENUM('all_users','all_companies','active_users','specific') DEFAULT 'all_users',
    user_id      INT UNSIGNED  DEFAULT NULL,
    type         ENUM('email','push','both') DEFAULT 'email',
    subject      VARCHAR(255)  NOT NULL,
    message      TEXT          NOT NULL,
    status       ENUM('draft','scheduled','sent','failed') DEFAULT 'draft',
    scheduled_at DATETIME      DEFAULT NULL,
    sent_at      DATETIME      DEFAULT NULL,
    sent_by      INT UNSIGNED  DEFAULT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sent_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── ACTIVITY LOG ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_log (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED  DEFAULT NULL,
    user_id     INT UNSIGNED  DEFAULT NULL,
    type        VARCHAR(50)   NOT NULL,          -- e.g. 'user_blocked', 'job_approved'
    description TEXT          NOT NULL,
    ip_address  VARCHAR(45)   DEFAULT NULL,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE SET NULL,
    INDEX idx_type       (type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- ── SITE SETTINGS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS site_settings (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`      VARCHAR(100) NOT NULL UNIQUE,
    `value`    TEXT         DEFAULT NULL,
    updated_at DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_settings (`key`, `value`) VALUES
('site_name',           'LinkedIn')
,
('site_url',            'https://linkedin-admin.com'),
('tagline',             'Connect top talent with the world\'s best companies'),
('maintenance_mode',    '0'),
('allow_user_reg',      '1'),
('allow_company_reg',   '1'),
('email_verification',  '1'),
('job_auto_approve',    '0'),
('smtp_host',           ''),
('smtp_port',           '587'),
('smtp_user',           ''),
('smtp_pass',           ''),
('smtp_from_email',     'noreply@linkedin-admin.com'),
('smtp_from_name',      'LinkedIn')

ON DUPLICATE KEY UPDATE `key` = `key`;

-- ── ADMIN SESSIONS (optional, if not using PHP sessions) ─────
-- Using PHP native sessions is fine; this table is for reference.

-- ── COMMENTS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id    INT UNSIGNED  NOT NULL,
    user_id    INT UNSIGNED  NOT NULL,
    content    TEXT          NOT NULL,
    status     ENUM('active','hidden','deleted') DEFAULT 'active',
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id)  ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE,
    INDEX idx_post (post_id)
) ENGINE=InnoDB;

-- ── CONNECTIONS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS connections (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    requester_id INT UNSIGNED NOT NULL,
    receiver_id  INT UNSIGNED NOT NULL,
    status       ENUM('pending','accepted','rejected','blocked') DEFAULT 'pending',
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_connection (requester_id, receiver_id),
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── AGENT APPROVALS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS agent_approvals (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED  DEFAULT NULL,
    name         VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL,
    phone        VARCHAR(20)   DEFAULT NULL,
    headline     VARCHAR(255)  DEFAULT NULL,
    bio          TEXT          DEFAULT NULL,
    location     VARCHAR(150)  DEFAULT NULL,
    website      VARCHAR(255)  DEFAULT NULL,
    status       ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note   TEXT          DEFAULT NULL,
    reviewed_by  INT UNSIGNED  DEFAULT NULL,
    reviewed_at  DATETIME      DEFAULT NULL,
    notified_at  DATETIME      DEFAULT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)  ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ============================================================
--  PORTAL EXTENSIONS: User panel, recruiter panel, feed, ATS
-- ============================================================

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS cover VARCHAR(255) DEFAULT NULL AFTER avatar,
  ADD COLUMN IF NOT EXISTS resume VARCHAR(255) DEFAULT NULL AFTER website,
  ADD COLUMN IF NOT EXISTS skills TEXT DEFAULT NULL AFTER resume,
  ADD COLUMN IF NOT EXISTS experience TEXT DEFAULT NULL AFTER skills,
  ADD COLUMN IF NOT EXISTS education TEXT DEFAULT NULL AFTER experience,
  ADD COLUMN IF NOT EXISTS certifications TEXT DEFAULT NULL AFTER education,
  ADD COLUMN IF NOT EXISTS languages VARCHAR(255) DEFAULT NULL AFTER certifications,
  ADD COLUMN IF NOT EXISTS social_links TEXT DEFAULT NULL AFTER languages,
  ADD COLUMN IF NOT EXISTS profile_public TINYINT(1) DEFAULT 1 AFTER social_links;

ALTER TABLE comments
  ADD COLUMN IF NOT EXISTS parent_id INT UNSIGNED DEFAULT NULL AFTER user_id,
  ADD INDEX idx_parent (parent_id);

ALTER TABLE applications
  MODIFY status ENUM('applied','reviewing','shortlisted','interview','hired','rejected','offered','withdrawn') DEFAULT 'applied';

ALTER TABLE jobs
  MODIFY status ENUM('pending','approved','rejected','expired','closed','hidden') DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS post_likes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_post_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS saved_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_saved_post (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS saved_jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_saved_job (job_id, user_id),
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS follows (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    follower_id INT UNSIGNED NOT NULL,
    followed_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_follow (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS blocks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    blocker_id INT UNSIGNED NOT NULL,
    blocked_id INT UNSIGNED NOT NULL,
    reason VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_block (blocker_id, blocked_id),
    FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    attachment VARCHAR(255) DEFAULT NULL,
    seen_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_thread (sender_id, receiver_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('connection','like','comment','message','job','application','system') DEFAULT 'system',
    message VARCHAR(255) NOT NULL,
    target_type VARCHAR(50) DEFAULT NULL,
    target_id INT UNSIGNED DEFAULT NULL,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, read_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS search_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    query VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_query (query)
) ENGINE=InnoDB;

-- Demo data. Password for all demo accounts: Password@123
INSERT INTO users (id, name, email, phone, password, role, headline, bio, location, website, status, email_verified, skills, experience, education, languages, profile_public)
VALUES
(101, 'Ananya Sharma', 'user@demo.com', '9999990001', '$2y$10$3waF5x/nPt43oJoCidCc7e4cdkz9GxcMjvVyMJVlZckLBNjd14P96', 'user', 'Full Stack Developer | PHP, JavaScript, MySQL', 'I build fast, accessible business applications and love product-minded engineering.', 'Noida, India', 'https://portfolio.example.com', 'active', 1, 'PHP, JavaScript, MySQL, Bootstrap, AJAX', 'Software Engineer at Ebizon\nBuilt CRM, job portal, and analytics dashboards.', 'B.Tech Computer Science', 'English, Hindi', 1),
(102, 'Rohan Mehta', 'rohan@demo.com', '9999990002', '$2y$10$3waF5x/nPt43oJoCidCc7e4cdkz9GxcMjvVyMJVlZckLBNjd14P96', 'user', 'UI Engineer focused on scalable design systems', 'Frontend engineer with a taste for clean interfaces.', 'Bengaluru, India', NULL, 'active', 1, 'HTML, CSS, Bootstrap, JavaScript', 'UI Engineer at PixelWorks', 'MCA', 'English', 1),
(103, 'Priya Kapoor', 'priya@demo.com', '9999990003', '$2y$10$3waF5x/nPt43oJoCidCc7e4cdkz9GxcMjvVyMJVlZckLBNjd14P96', 'user', 'Data Analyst | SQL, Excel, BI', 'Turning messy data into business clarity.', 'Gurugram, India', NULL, 'active', 1, 'SQL, Excel, Power BI, Statistics', 'Data Analyst at GrowthLab', 'MBA Analytics', 'English, Hindi', 1),
(201, 'TalentBridge Recruiter', 'recruiter@demo.com', '9999991001', '$2y$10$3waF5x/nPt43oJoCidCc7e4cdkz9GxcMjvVyMJVlZckLBNjd14P96', 'company', 'Senior Technical Recruiter', 'Hiring engineers for modern product teams.', 'Remote', 'https://talentbridge.example.com', 'active', 1, 'Hiring, Sourcing, Screening', 'Recruiter at TalentBridge', 'MBA HR', 'English', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO companies (id, user_id, name, email, phone, website, industry, company_size, founded_year, description, location, status)
VALUES
(201, 201, 'TalentBridge Labs', 'recruiter@demo.com', '9999991001', 'https://talentbridge.example.com', 'Technology Hiring', '51-200', 2017, 'TalentBridge Labs helps product companies hire strong engineering, design, and data talent.', 'Remote / India', 'verified')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO jobs (id, company_id, title, description, requirements, benefits, location, job_type, experience_level, salary_min, salary_max, salary_currency, is_featured, status, expires_at)
VALUES
(301, 201, 'Core PHP Full Stack Developer', 'Build responsive MVC applications with Core PHP, Bootstrap, JavaScript, AJAX and MySQL.', '3+ years PHP, PDO, MySQL, Bootstrap, secure coding.', 'Remote flexibility, health insurance, learning budget.', 'Noida / Hybrid', 'full_time', 'mid', 700000, 1200000, 'INR', 1, 'approved', DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
(302, 201, 'Frontend Engineer - Bootstrap UI', 'Create polished dashboards, feeds, forms and recruiter workflows for B2B SaaS products.', 'HTML5, CSS3, Bootstrap 5, vanilla JavaScript, accessibility.', 'Flexible hours, strong design culture.', 'Remote', 'remote', 'mid', 600000, 1000000, 'INR', 0, 'approved', DATE_ADD(CURDATE(), INTERVAL 35 DAY)),
(303, 201, 'Junior Data Analyst', 'Support hiring analytics and reporting using SQL dashboards and clean data pipelines.', 'SQL, Excel, reporting fundamentals.', 'Mentorship and growth path.', 'Gurugram', 'full_time', 'entry', 450000, 700000, 'INR', 0, 'approved', DATE_ADD(CURDATE(), INTERVAL 30 DAY))
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO posts (id, user_id, content, media_type, visibility, likes, comments_count, shares_count, status, author, created_at)
VALUES
(401, 101, 'Excited to share a new Core PHP MVC project with AJAX-powered feed, recruiter workflows, and job applications. Clean architecture still matters.', 'none', 'public', 12, 1, 2, 'active', 'Ananya Sharma', NOW() - INTERVAL 2 HOUR),
(402, 102, 'Small UI details matter: sticky navigation, fast filters, predictable forms, and polished empty states can make an admin app feel genuinely professional.', 'none', 'public', 18, 0, 4, 'active', 'Rohan Mehta', NOW() - INTERVAL 1 DAY),
(403, 201, 'We are hiring PHP and frontend engineers for remote-friendly roles. Easy Apply is open on our latest jobs.', 'none', 'public', 25, 0, 8, 'active', 'TalentBridge Recruiter', NOW() - INTERVAL 3 HOUR)
ON DUPLICATE KEY UPDATE content = VALUES(content);

INSERT IGNORE INTO connections (requester_id, receiver_id, status) VALUES
(102, 101, 'pending'),
(101, 103, 'accepted');

INSERT IGNORE INTO applications (job_id, user_id, cover_letter, status) VALUES
(301, 101, 'I have hands-on Core PHP MVC experience and can contribute quickly.', 'reviewing'),
(302, 102, 'I enjoy building polished Bootstrap interfaces with vanilla JavaScript.', 'shortlisted');

INSERT IGNORE INTO messages (sender_id, receiver_id, body, seen_at, created_at) VALUES
(201, 101, 'Hi Ananya, your profile looks like a strong match for our PHP role.', NULL, NOW() - INTERVAL 20 MINUTE),
(101, 201, 'Thanks. I would love to learn more about the team and process.', NOW(), NOW() - INTERVAL 10 MINUTE);

INSERT IGNORE INTO user_notifications (user_id, type, message, target_type, target_id) VALUES
(101, 'connection', 'Rohan sent you a connection request.', 'user', 102),
(101, 'application', 'Your application for Core PHP Full Stack Developer moved to reviewing.', 'job', 301),
(201, 'application', 'Ananya Sharma applied to Core PHP Full Stack Developer.', 'job', 301);
