<?php
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/PortalModel.php';
require_once APP_PATH . '/helpers/portal.php';
require_once APP_PATH . '/helpers/captcha.php';

class PortalController extends Controller {
    private PortalModel $portal;

    public function __construct() {
        $this->portal = new PortalModel();
    }

    public function signin(): void {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect(APP_URL . '/' . (($_SESSION['user']['role'] ?? 'user') === 'company' ? 'recruiter-dashboard' : 'home')); //Controller.php
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $captcha = trim($_POST['captcha'] ?? '');
            if (!captcha_verify($captcha)) { //helpers/captcha.php
                $this->flash('error', 'Security code is incorrect. Please try again.');
                $this->redirect(APP_URL . '/signin');
            }
            $user = $this->portal->findUserByEmail($email); //PortalModel.php
            if (!$user || !password_verify($password, $user['password']) || in_array($user['status'], ['blocked','suspended'], true)) {
                $this->flash('error', 'Invalid credentials or blocked account.');
                $this->redirect(APP_URL . '/signin');
            }
            // Enforce email verification
            if (empty($user['email_verified'])) {
                $_SESSION['pending_verify_email'] = $user['email'];
                $this->flash('error', 'Please verify your email before signing in.');
                $this->redirect(APP_URL . '/verify-signup-otp');
            }
            session_regenerate_id(true);
            $this->portal->touchLogin((int) $user['id']); //PortalModel.php - updates last_login timestamp
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role'], 'avatar' => $user['avatar'] ?? null];
            $_SESSION['last_activity'] = time();
            $this->redirect(APP_URL . '/' . ($user['role'] === 'company' ? 'recruiter-dashboard' : 'home'));
        }
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->view('portal/auth', ['mode' => 'signin', 'flash' => $flash, 'csrf' => $this->csrfToken()]);
    }

    public function signup(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role = ($_POST['role'] ?? 'user') === 'company' ? 'company' : 'user';
            $data = [
                'name'         => trim($_POST['name'] ?? ''),
                'company_name' => trim($_POST['company_name'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'phone'        => trim($_POST['phone'] ?? ''),
                'password'     => $_POST['password'] ?? '',
                'role'         => $role,
                'headline'     => trim($_POST['headline'] ?? ($role === 'company' ? 'Recruiter' : 'Open to opportunities')),
                'industry'     => trim($_POST['industry'] ?? ''),
                'company_size' => trim($_POST['company_size'] ?? ''),
                'location'     => trim($_POST['location'] ?? ''),
                'bio'          => trim($_POST['bio'] ?? ''),
                'website'      => trim($_POST['website'] ?? ''),
                'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
            ];

            if (!captcha_verify(trim($_POST['captcha'] ?? ''))) { 
                $this->flash('error', 'Security code is incorrect. Please try again.');
                $this->redirect(APP_URL . '/signup');
            }

            // Password confirm check for company
            if ($role === 'company' && !empty($_POST['confirm_password']) && $_POST['password'] !== $_POST['confirm_password']) {
                $this->flash('error', 'Passwords do not match.');
                $this->redirect(APP_URL . '/signup');
            }

            // ── Detailed validation ──────────────────────────────
            if (!$data['name']) {
                $this->flash('error', 'Full name is required.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            if (!$data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->flash('error', 'Please enter a valid email address.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            $pw = $_POST['password'] ?? '';
            if (strlen($pw) < 8) {
                $this->flash('error', 'Password must be at least 8 characters long.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            if (!preg_match('/[A-Z]/', $pw)) {
                $this->flash('error', 'Password must contain at least one uppercase letter.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            if (!preg_match('/[0-9]/', $pw)) {
                $this->flash('error', 'Password must contain at least one number.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            if (!preg_match('/[^a-zA-Z0-9]/', $pw)) {
                $this->flash('error', 'Password must contain at least one special character (e.g. @, #, !).');
                $this->redirect(APP_URL . '/signup'); return;
            }
            if ($this->portal->findUserByEmail($data['email'])) { //PortalModel.php
                $this->flash('error', 'An account with this email already exists. Please sign in or use a different email.');
                $this->redirect(APP_URL . '/signup'); return;
            }
            // ────────────────────────────────────────────────────

            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);

            if (!$this->startSignupOtp($data)) {
                $this->flash('error', 'Could not send verification OTP. Please check your email address or try again later.');
                $this->redirect(APP_URL . '/signup');
            }

            $this->flash('success', 'We sent a 6-digit OTP to your email. Please verify to create your account.');
            $this->redirect(APP_URL . '/verify-signup-otp');
        }
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->view('portal/auth', ['mode' => 'signup', 'flash' => $flash, 'csrf' => $this->csrfToken()]);//Controller.php - generates or returns existing CSRF token
    }

    public function verifySignupOtp(): void {
        $pending = $_SESSION['pending_signup'] ?? null;
        if (!$pending || empty($pending['data'])) {
            $this->flash('error', 'Please start signup again.');
            $this->redirect(APP_URL . '/signup');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otp = preg_replace('/\D+/', '', $_POST['otp'] ?? '');
            if (($pending['expires_at'] ?? 0) < time()) {
                unset($_SESSION['pending_signup']);
                $this->flash('error', 'OTP expired. Please start signup again.');
                $this->redirect(APP_URL . '/signup');
            }

            if (($pending['attempts'] ?? 0) >= 5) {
                unset($_SESSION['pending_signup']);
                $this->flash('error', 'Too many incorrect OTP attempts. Please start signup again.');
                $this->redirect(APP_URL . '/signup');
            }

            if (!$otp || !password_verify($otp, $pending['otp_hash'] ?? '')) {
                $_SESSION['pending_signup']['attempts'] = (int)($pending['attempts'] ?? 0) + 1;
                $this->flash('error', 'Invalid OTP. Please try again.');
                $this->redirect(APP_URL . '/verify-signup-otp');
            }

            $this->completeVerifiedSignup($pending['data']);
            $role = $pending['data']['role'] ?? 'user';
            unset($_SESSION['pending_signup']);
            $this->redirect(APP_URL . '/' . ($role === 'company' ? 'recruiter-dashboard' : 'home'));
        }

        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->view('portal/auth', [
            'mode' => 'signup_otp',
            'flash' => $flash,
            'csrf' => $this->csrfToken(),
            'pendingEmail' => $pending['data']['email'] ?? '',
        ]);
    }

    public function resendSignupOtp(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/verify-signup-otp');
        }

        $pending = $_SESSION['pending_signup'] ?? null;
        if (!$pending || empty($pending['data'])) {
            $this->flash('error', 'Please start signup again.');
            $this->redirect(APP_URL . '/signup');
        }

        if (($pending['resend_after'] ?? 0) > time()) {
            $this->flash('error', 'Please wait before requesting another OTP.');
            $this->redirect(APP_URL . '/verify-signup-otp');
        }

        if (!$this->startSignupOtp($pending['data'])) {
            $this->flash('error', 'Could not resend OTP. Please try again later.');
            $this->redirect(APP_URL . '/verify-signup-otp');
        }

        $this->flash('success', 'A new OTP has been sent to your email.');
        $this->redirect(APP_URL . '/verify-signup-otp');
    }

    private function startSignupOtp(array $data): bool {
        $otp = (string) random_int(100000, 999999);
        $_SESSION['pending_signup'] = [
            'data' => $data,
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'expires_at' => time() + 600,
            'resend_after' => time() + 60,
            'attempts' => 0,
        ];

        require_once APP_PATH . '/core/Mailer.php';
        $mailer = new \Mailer($this->mailSettings());
        $name = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
        $body = "<div style='font-family:sans-serif;max-width:520px;margin:auto;'>
            <h2 style='color:#0a66c2;'>Verify your email</h2>
            <p>Hi <strong>{$name}</strong>,</p>
            <p>Use this OTP to complete your account creation:</p>
            <p style='font-size:28px;letter-spacing:6px;font-weight:700;color:#0a66c2;margin:24px 0;'>{$otp}</p>
            <p>This OTP expires in 10 minutes.</p>
            <p style='color:#888;font-size:12px;margin-top:20px;'>If you did not request this, please ignore this email.</p>
        </div>";

        return $mailer->send($data['email'], $data['name'], 'Your LinkedIn Clone signup OTP', $body);
    }

    private function completeVerifiedSignup(array $data): int {
        if ($this->portal->findUserByEmail($data['email'])) {
            $this->flash('error', 'This email has already been registered. Please sign in.');
            $this->redirect(APP_URL . '/signin');
        }

        $id = $this->portal->registerUser($data); //PortalModel.php - creates user and returns new ID
        $role = $data['role'] ?? 'user';

        if ($role === 'company') {
            $this->portal->updateCompanyExtra($id, [ //PortalModel.php - updates company-specific fields
                'website'      => $data['website'],
                'linkedin_url' => $data['linkedin_url'],
                'description'  => $data['bio'],
            ]);

            $signinUrl = APP_URL . '/signin';
            require_once APP_PATH . '/core/Mailer.php';
            $mailer = new \Mailer($this->mailSettings());
            $safeName = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
            $safeCompany = htmlspecialchars($data['company_name'], ENT_QUOTES, 'UTF-8');
            $mailer->send($data['email'], $data['name'],
                'Your recruiter account is under review - LinkedIn Clone',
                "<div style='font-family:sans-serif;max-width:520px;margin:auto;'>
                <h2 style='color:#0a66c2;'>Welcome, {$safeName}!</h2>
                <p>Thank you for registering <strong>{$safeCompany}</strong> on LinkedIn Clone.</p>
                <p>Your recruiter account has been submitted and is currently <strong>under admin review</strong>.</p>
                <p>You will receive another email once your account is approved and ready to use.</p>
                <p style='margin-top:24px;'>
                  <a href='{$signinUrl}' style='background:#0a66c2;color:#fff;padding:10px 22px;border-radius:20px;text-decoration:none;font-weight:700;'>Go to Sign In</a>
                </p>
                <p style='color:#888;font-size:12px;margin-top:20px;'>If you did not create this account, please ignore this email.</p>
                </div>"
            );

            require_once APP_PATH . '/models/AgentApprovalModel.php';
            $approvalModel = new \AgentApprovalModel();
            $approvalModel->createRequest([ //AgentApprovalModel.php - creates a pending approval request for this company
                'user_id'  => $id,
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'headline' => $data['headline'] ?? 'Recruiter',
                'bio'      => $data['bio'] ?? null,
                'location' => $data['location'] ?? null,
                'website'  => $data['website'] ?? null,
            ]);

            $this->portal->setCompanyPending($id); //PortalModel.php - sets a flag on the user record to indicate company approval is pending
        } else {
            $signinUrl = APP_URL . '/signin';
            require_once APP_PATH . '/core/Mailer.php';
            $mailer = new \Mailer($this->mailSettings());
            $safeName = htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8');
            $mailer->send($data['email'], $data['name'],
                'Welcome to LinkedIn Clone!',
                "<div style='font-family:sans-serif;max-width:520px;margin:auto;'>
                <h2 style='color:#0a66c2;'>Welcome, {$safeName}!</h2>
                <p>Your account has been successfully created on <strong>LinkedIn Clone</strong>.</p>
                <p>You can now connect with professionals, explore jobs, and grow your network.</p>
                <p style='margin-top:24px;'>
                  <a href='{$signinUrl}' style='background:#0a66c2;color:#fff;padding:10px 22px;border-radius:20px;text-decoration:none;font-weight:700;'>Go to Sign In</a>
                </p>
                <p style='color:#888;font-size:12px;margin-top:20px;'>If you did not create this account, please ignore this email.</p>
                </div>"
            );
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user'] = ['id' => $id, 'name' => $data['name'], 'email' => $data['email'], 'role' => $role, 'avatar' => null];
        $_SESSION['last_activity'] = time();

        return $id;
    }

    public function forgot(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $user = $this->portal->findUserByEmail($email);
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $this->portal->saveResetToken((int) $user['id'], $token);
                $url = APP_URL . '/reset-user-password?token=' . $token;
                require_once APP_PATH . '/core/Mailer.php';
                $mailer = new Mailer([]);
                $mailer->send($email, $user['name'], 'Reset your LinkedIn Clone password', "<p>Use this secure link to reset your password:</p><p><a href='{$url}'>Reset password</a></p>");
            }
            $this->flash('success', 'If the email exists, a reset link has been sent.');
            $this->redirect(APP_URL . '/signin');
        }
        $this->view('portal/auth', ['mode' => 'forgot', 'flash' => null, 'csrf' => $this->csrfToken()]);
    }

    public function reset(): void {
        $token = trim($_GET['token'] ?? '');
        $user = $token ? $this->portal->findByResetToken($token) : null;
        if (!$user) $this->redirect(APP_URL . '/signin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['password'] ?? '';
            if (strlen($pass) >= 8 && $pass === ($_POST['confirm_password'] ?? '')) {
                $this->portal->updatePassword((int) $user['id'], $pass);
                $this->flash('success', 'Password updated. Please sign in.');
                $this->redirect(APP_URL . '/signin');
            }
        }
        $this->view('portal/auth', ['mode' => 'reset', 'flash' => ['type' => 'error', 'message' => 'Enter matching passwords with at least 8 characters.'], 'csrf' => $this->csrfToken()]);
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        $this->redirect(APP_URL . '/signin');
    }

    public function home(): void {
        $this->requirePortalAuth();
        // Company users have their own dashboard as default home
        if (($_SESSION['user']['role'] ?? '') === 'company') {
            $this->redirect(APP_URL . '/recruiter-dashboard');
        }
        $user = $this->portal->findUser((int) $_SESSION['user_id']);
        $this->renderPortal('portal/home', [
            'pageTitle' => 'Home',
            'user' => $user,
            'posts' => $this->portal->feed((int) $user['id']),
            'suggestions' => $this->portal->suggestedConnections((int) $user['id']),
            'jobs' => array_slice($this->portal->jobs([], (int) $user['id']), 0, 4),
            'csrf' => $this->csrfToken(),
        ]);
    }

    public function network(): void {
        $this->requirePortalAuth();
        $id   = (int) $_SESSION['user_id'];
        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'company') {
            // Mark network as seen — clears the badge
            $this->portal->markNetworkSeen($id);
            $this->renderPortal('portal/network', [
                'pageTitle'   => 'My Network',
                'followers'   => $this->portal->getFollowers($id),
                'suggestions' => $this->portal->suggestedConnections($id),
                'csrf'        => $this->csrfToken(),
            ]);
        } else {
            // Mark network as seen for regular users — clears the pending badge
            $this->portal->markUserNetworkSeen($id);
            $this->renderPortal('portal/network', [
                'pageTitle'   => 'My Network',
                'requests'    => $this->portal->connectionRequests($id),
                'connections' => $this->portal->myConnections($id),
                'suggestions' => $this->portal->suggestedConnections($id),
                'csrf'        => $this->csrfToken(),
            ]);
        }
    }

    public function jobs(): void {
        $this->requirePortalAuth('user');
        $id = (int) $_SESSION['user_id'];

        // Capture new-job IDs BEFORE updating seen_at, so badges render on first load
        $newJobIds = array_column($this->portal->getNewJobs($id), 'id');

        // Record that the user has now seen the Jobs board (resets "new" baseline)
        $this->portal->markJobsSeen($id);

        $this->renderPortal('portal/jobs', [
            'pageTitle'  => 'Jobs',
            'jobs'       => $this->portal->jobs($_GET, $id),
            'applications' => $this->portal->applications($id),
            'savedJobs'  => $this->portal->savedJobs($id),
            'newJobIds'  => $newJobIds,   // array of job IDs that are new since last visit
            'csrf'       => $this->csrfToken(),
        ]);
    }

    public function messages(): void {
        $this->requirePortalAuth();
        $id = (int) $_SESSION['user_id'];
        $with = (int) ($_GET['with'] ?? 0);
        // Always fetch the chat person so name/photo shows immediately even before first message
        $chatWithUser = $with ? $this->portal->findUser($with) : null;
        $this->renderPortal('portal/messages', ['pageTitle' => 'Messaging', 'conversations' => $this->portal->conversations($id), 'connections' => $this->portal->myConnections($id), 'with' => $with, 'messages' => $with ? $this->portal->messages($id, $with) : [], 'chatWithUser' => $chatWithUser, 'csrf' => $this->csrfToken()]);
    }

    public function profile(): void {
        $this->requirePortalAuth();
        $id = (int) $_SESSION['user_id'];
        $user = $this->portal->findUser($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->portal->updateProfile($id, [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'headline' => trim($_POST['headline'] ?? ''),
                'bio' => trim($_POST['bio'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'skills' => trim($_POST['skills'] ?? ''),
                'experience' => trim($_POST['experience'] ?? ''),
                'education' => trim($_POST['education'] ?? ''),
                'certifications' => trim($_POST['certifications'] ?? ''),
                'languages' => trim($_POST['languages'] ?? ''),
                'social_links' => trim($_POST['social_links'] ?? ''),
                'profile_public' => isset($_POST['profile_public']) ? 1 : 0,
                'avatar' => $this->upload('avatar', 'avatars', ['image/jpeg','image/png','image/webp']),
                'cover' => $this->upload('cover', 'covers', ['image/jpeg','image/png','image/webp']),
                'resume' => $this->upload('resume', 'resumes', ['application/pdf']),
            ]);
            $this->flash('success', 'Profile updated.');
            $this->redirect(APP_URL . '/profile');
        }
        $this->renderPortal('portal/profile', ['pageTitle' => 'Profile', 'profile' => $user, 'completion' => $this->completion($user ?: []), 'connections' => $this->portal->myConnections((int)($_SESSION['user_id'] ?? 0)), 'csrf' => $this->csrfToken()]);
    }

    public function recruiterDashboard(): void {
        $this->requirePortalAuth('company');
        $company = $this->portal->companyByUser((int) $_SESSION['user_id']);
        if (!$company) $this->redirect(APP_URL . '/logout-user');

        // If still pending, show waiting page
        if (($company['status'] ?? 'pending') === 'pending') {
            $this->renderPortal('portal/pending_approval', ['pageTitle' => 'Awaiting Approval', 'company' => $company]);
            return;
        }

        $this->renderPortal('portal/recruiter_dashboard', ['pageTitle' => 'Recruiter Dashboard', 'company' => $company, 'stats' => $this->portal->recruiterStats((int) $company['id']), 'jobs' => $this->portal->recruiterJobs((int) $company['id']), 'applicants' => array_slice($this->portal->applicants((int) $company['id']), 0, 8), 'posts' => $this->portal->feed((int) $_SESSION['user_id']), 'suggestions' => $this->portal->suggestedConnections((int) $_SESSION['user_id']), 'csrf' => $this->csrfToken()]);
    }

    public function recruiterJobs(): void {
        $this->requirePortalAuth('company');
        $company = $this->portal->companyByUser((int) $_SESSION['user_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->portal->saveJob($_POST, (int) $company['id']);
            $this->flash('success', 'Job saved. It is ready for admin review/approval.');
            $this->redirect(APP_URL . '/recruiter-jobs');
        }
        $this->renderPortal('portal/recruiter_jobs', ['pageTitle' => 'Manage Jobs', 'company' => $company, 'jobs' => $this->portal->recruiterJobs((int) $company['id']), 'csrf' => $this->csrfToken()]);
    }

    public function applicants(): void {
        $this->requirePortalAuth('company');
        $company = $this->portal->companyByUser((int) $_SESSION['user_id']);
        // Mark applicants as seen — clears the badge
        if (!empty($company['id'])) {
            $this->portal->markApplicantsSeen((int) $company['id']);
        }
        $this->renderPortal('portal/applicants', ['pageTitle' => 'Applicants', 'company' => $company, 'applicants' => $this->portal->applicants((int) $company['id']), 'csrf' => $this->csrfToken()]);
    }

    public function companyProfile(): void {
        $this->requirePortalAuth('company');
        $company = $this->portal->companyByUser((int) $_SESSION['user_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->portal->updateCompany((int) $company['id'], [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'industry' => trim($_POST['industry'] ?? ''),
                'company_size' => trim($_POST['company_size'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                'logo' => $this->upload('logo', 'companies', ['image/jpeg','image/png','image/webp']),
                'banner' => $this->upload('banner', 'companies', ['image/jpeg','image/png','image/webp']),
            ]);
            $this->flash('success', 'Company profile updated.');
            $this->redirect(APP_URL . '/company-profile');
        }
        $stats          = $this->portal->recruiterStats((int) $company['id']);
        $followersCount = count($this->portal->getFollowers((int) $_SESSION['user_id']));
        $this->renderPortal('portal/company_profile', [
            'pageTitle'      => 'Company Profile',
            'company'        => $company,
            'stats'          => $stats,
            'followersCount' => $followersCount,
            'csrf'           => $this->csrfToken(),
        ]);
    }

    public function viewProfile(): void {
        $this->requirePortalAuth();
        $targetId = (int)($_GET['id'] ?? 0);
        if ($targetId === (int)($_SESSION['user_id'] ?? 0)) {
            header('Location: ' . APP_URL . '/profile'); exit;
        }
        $targetUser = $this->portal->findUser($targetId);
        if (!$targetUser) { header('Location: ' . APP_URL . '/network'); exit; }
        // If this is a company user, redirect to company profile page instead
        if (($targetUser['role'] ?? '') === 'company') {
            $company = $this->portal->companyByUser($targetId);
            if ($company) {
                header('Location: ' . APP_URL . '/view-company?id=' . (int)$company['id']);
                exit;
            }
        }
        $targetConnections = $this->portal->myConnections($targetId);
        $this->renderPortal('portal/view_profile', ['pageTitle' => e($targetUser['name']) . ' — Profile', 'targetUser' => $targetUser, 'targetConnections' => $targetConnections]);
    }

    public function viewCompany(): void {
        $this->requirePortalAuth();
        $companyId = (int)($_GET['id'] ?? 0);
        $company = $this->portal->findCompany($companyId);
        if (!$company) { header('Location: ' . APP_URL . '/home'); exit; }
        $jobs = $this->portal->recruiterJobs($companyId);
        $this->renderPortal('portal/view_company', [
            'pageTitle' => e($company['name']) . ' — Company',
            'company'   => $company,
            'jobs'      => $jobs,
        ]);
    }

    public function notifications(): void {
        $this->requirePortalAuth();
        $this->renderPortal('portal/notifications', ['pageTitle' => 'Notifications', 'notifications' => $this->portal->notificationsFor((int) $_SESSION['user_id'])]);
    }

    public function ajax(): void {
        ob_start(); // Buffer any stray PHP warnings so they don't corrupt JSON
        $this->requirePortalAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->verifyCsrf();
        $id = (int) $_SESSION['user_id'];
        $action = $_GET['action'] ?? '';
        switch ($action) {
            case 'mark-notifications-read':
                $this->portal->markNotificationsRead($id);
                $this->json(['ok' => true]);
            case 'mark-one-notification-read':
                $notifId = (int)($_POST['notif_id'] ?? 0);
                if ($notifId) $this->portal->markOneNotificationRead($notifId, $id);
                $this->json(['ok' => true]);
            case 'mark-applicants-seen':
                $company = $this->portal->companyByUser($id);
                if (!empty($company['id'])) {
                    $this->portal->markApplicantsSeen((int) $company['id']);
                }
                $this->json(['ok' => true]);
            case 'mark-network-seen':
                $role = $_SESSION['user']['role'] ?? '';
                if ($role === 'company') {
                    $this->portal->markNetworkSeen($id);
                } else {
                    $this->portal->markUserNetworkSeen($id);
                }
                $this->json(['ok' => true]);
            case 'feed':
                $this->json(['ok' => true, 'posts' => $this->portal->feed($id, (int) ($_GET['offset'] ?? 0))]);
            case 'post':
                $media = $this->upload('media', 'posts', ['image/jpeg','image/png','image/webp','video/mp4','application/pdf']);
                $type = $media ? (str_ends_with($media, '.pdf') ? 'document' : (str_ends_with($media, '.mp4') ? 'video' : 'image')) : 'none';
                $postId = $this->portal->createPost($id, trim($_POST['content'] ?? ''), $media, $type);
                $this->json(['ok' => true, 'post_id' => $postId]);
            case 'like':
                $result = $this->portal->toggleLike((int) $_POST['post_id'], $id);
                $result['count'] = $this->portal->likeCount((int) $_POST['post_id']);
                $this->json(['ok' => true] + $result);
            case 'comment':
                $comment = $this->portal->addComment((int) $_POST['post_id'], $id, trim($_POST['content'] ?? ''), (int) ($_POST['parent_id'] ?? 0) ?: null);
                $count = $this->portal->commentCount((int) $_POST['post_id']);
                $this->json(['ok' => true, 'comment' => $comment, 'count' => $count]);
            case 'delete-comment':
                $postId = (int) $_POST['post_id'];
                $postOwner = $this->portal->postOwner($postId);
                $this->portal->deleteComment((int) $_POST['comment_id'], $id, $postOwner === $id);
                $count = $this->portal->commentCount($postId);
                $this->json(['ok' => true, 'count' => $count]);
            case 'delete-post':
                $deleted = $this->portal->deletePost((int) $_POST['post_id'], $id);
                $this->json(['ok' => $deleted]);
            case 'report-post':
                $type = in_array($_POST['type'] ?? '', ['spam','offensive','harassment','fake','copyright','other']) ? $_POST['type'] : 'other';
                $reason = trim($_POST['reason'] ?? '');
                if (!$reason) { $this->json(['ok' => false, 'message' => 'Reason required'], 422); return; }
                $this->portal->createReport($id, 'post', (int) $_POST['post_id'], $type, $reason);
                $this->json(['ok' => true]);
            case 'withdraw-application':
                $this->portal->withdrawApplication((int) $_POST['job_id'], $id);
                $this->json(['ok' => true]);
            case 'comments':
                $this->json(['ok' => true, 'comments' => $this->portal->comments((int) $_GET['post_id'])]);
            case 'new-jobs':
                // Returns jobs posted after the user's last-seen timestamp.
                // Called by the JS poller every 30 seconds on the Jobs board.
                // Does NOT update seen_at — that only happens when the user visits the page.
                $this->json(['ok' => true, 'jobs' => $this->portal->getNewJobs($id)]);
            case 'save-job':
                $this->json(['ok' => true, 'saved' => $this->portal->toggleSaveJob((int) $_POST['job_id'], $id)]);
            case 'connect':
                $targetId = (int) ($_POST['user_id'] ?? $_POST['receiver_id'] ?? 0);
                $this->portal->sendConnection($id, $targetId);
                $this->json(['ok' => true]);
            case 'remove-connection':
                $this->portal->removeConnection($id, (int) $_POST['user_id']);
                $this->json(['ok' => true]);
            case 'connection':
                $status = $_POST['status'] ?? 'accepted';
                $requesterId = (int) $_POST['id'];
                $this->portal->updateConnection($requesterId, $id, $status);
                if ($status === 'accepted') {
                    $newConn = $this->portal->findUser($requesterId);
                    $this->json(['ok' => true, 'status' => 'accepted', 'user' => [
                        'id'       => $newConn['id'],
                        'name'     => $newConn['name'],
                        'headline' => $newConn['headline'] ?? '',
                        'avatar'   => $newConn['avatar'] ?? '',
                    ]]);
                }
                $this->json(['ok' => true, 'status' => $status]);
            case 'follow-user-company':
                // Follow/unfollow company: uses follows table with followed_id = company's user_id
                $targetUser = (int) $_POST['user_id'];
                $following = $this->portal->toggleFollowCompany($targetUser, $id);
                $this->json(['ok' => true, 'following' => $following]);
            case 'apply':
                $jobRow = $this->portal->getJobById((int)$_POST['job_id']);
                if ($jobRow && !empty($jobRow['expires_at']) && strtotime($jobRow['expires_at']) < time()) {
                    $this->json(['ok' => false, 'message' => 'This job posting has expired and is no longer accepting applications.']);
                    return;
                }
                $resume = $this->upload('resume', 'resumes', ['application/pdf']);
                if (!$resume) {
                    $this->json(['ok' => false, 'message' => 'Resume (PDF) is required to apply.']);
                    return;
                }
                $this->portal->applyJob((int) $_POST['job_id'], $id, trim($_POST['cover_letter'] ?? ''), $resume);
                $this->json(['ok' => true]);
            case 'app-status':
                $company = $this->portal->companyByUser($id);
                $this->portal->updateApplicationStatus((int) $_POST['application_id'], (int) $company['id'], $_POST['status'] ?? 'reviewing');
                $this->json(['ok' => true]);
            case 'send-message':
                $this->json(['ok' => true, 'message' => $this->portal->sendMessage($id, (int) $_POST['receiver_id'], trim($_POST['body'] ?? ''), $this->upload('attachment', 'posts', ['image/jpeg','image/png','application/pdf']))]);
            case 'messages':
                $this->json(['ok' => true, 'messages' => $this->portal->messages($id, (int) $_GET['with'])]);
            case 'search':
                $this->json(['ok' => true, 'results' => $this->portal->search(trim($_GET['q'] ?? ''), $id)]);
        }
        $this->json(['ok' => false, 'message' => 'Unknown action'], 404);
    }

    private function renderPortal(string $view, array $data = []): void {
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $freshUser = !empty($_SESSION['user_id'])
            ? ($this->portal->findUser((int) $_SESSION['user_id']) ?: $this->user())
            : $this->user();
        $data += ['currentUser' => $freshUser, 'flash' => $flash, 'csrf' => $this->csrfToken()];
        $this->render($view, $data, 'layouts/portal');
    }

    private function upload(string $field, string $folder, array $allowed): ?string {
        if (empty($_FILES[$field]['tmp_name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        if ($_FILES[$field]['size'] > 8 * 1024 * 1024) return null;
        $mime = mime_content_type($_FILES[$field]['tmp_name']);
        if (!in_array($mime, $allowed, true)) return null;
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        $name = $folder . '/' . bin2hex(random_bytes(12)) . '.' . $ext;
        $target = PUBLIC_PATH . '/uploads/' . $name;
        if (!is_dir(dirname($target))) mkdir(dirname($target), 0775, true);
        return move_uploaded_file($_FILES[$field]['tmp_name'], $target) ? 'uploads/' . $name : null;
    }

    private function completion(array $user): int {
        $fields = ['avatar','cover','headline','bio','location','website','skills','experience','education','resume'];
        $done = 0;
        foreach ($fields as $field) if (!empty($user[$field])) $done++;
        return (int) round(($done / count($fields)) * 100);
    }
}