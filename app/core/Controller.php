<?php

abstract class Controller {

    /** Render a view file with data */
    protected function view(string $viewPath, array $data = []): void {
        extract($data);
        $fullPath = VIEW_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($fullPath)) {
            die("View not found: $fullPath");
        }
        require $fullPath;
    }

    /** Render layout + inner view */
    protected function render(string $view, array $data = [], string $layout = 'layouts/admin'): void {
        $data['content_view'] = $view;
        $this->view($layout, $data);
    }

    /** Redirect to a URL */
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }

    /** Return JSON (for AJAX endpoints) */
    protected function json(mixed $data, int $code = 200): void {
        if (ob_get_level()) ob_clean(); // Discard any stray output (warnings, notices)
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /** Check admin session; redirect if not logged in */
    protected function requireAuth(): void {
    if (empty($_SESSION['admin_id'])) {
        $this->redirect(APP_URL . '/login');
    }

    // Auto logout after inactivity
    $timeout = SESSION_LIFETIME; // 7200 seconds = 2 hours
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        $this->redirect(APP_URL . '/login?reason=timeout');
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
}

    protected function requirePortalAuth(?string $role = null): void {
        if (empty($_SESSION['user_id'])) {
            $this->redirect(APP_URL . '/signin');
        }
        if ($role && (($_SESSION['user']['role'] ?? '') !== $role)) {
            $target = ($_SESSION['user']['role'] ?? 'user') === 'company' ? 'recruiter-dashboard' : 'home';
            $this->redirect(APP_URL . '/' . $target);
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_LIFETIME) {
            session_unset();
            session_destroy();
            $this->redirect(APP_URL . '/signin?reason=timeout');
        }
        $_SESSION['last_activity'] = time();
    }

    protected function user(): array {
        return $_SESSION['user'] ?? [];
    }

    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->json(['ok' => false, 'message' => 'Security token expired. Please refresh and try again.'], 419);
        }
    }

    /** Current logged-in admin */
    protected function admin(): array {
        return $_SESSION['admin'] ?? [];
    }

    /** Flash a one-time session message */
    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /** POST only guard */
    protected function requirePost(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/dashboard');
        }
    }

    /** Sanitize a string input */
    protected function input(string $key, string $default = ''): string {
        return htmlspecialchars(trim($_POST[$key] ?? $_GET[$key] ?? $default));
    }

    /** Build SMTP settings array from DB, falling back to constants */
    protected function mailSettings(): array {
        require_once APP_PATH . '/models/OtherModels.php';
        $db = (new SettingsModel())->getAll();
        return [
            'smtp_host'       => !empty($db['smtp_host'])       ? $db['smtp_host']       : SMTP_HOST,
            'smtp_port'       => !empty($db['smtp_port'])       ? $db['smtp_port']       : SMTP_PORT,
            'smtp_user'       => !empty($db['smtp_user'])       ? $db['smtp_user']       : SMTP_USER,
            'smtp_pass'       => !empty($db['smtp_pass'])       ? $db['smtp_pass']       : SMTP_PASS,
            'smtp_from_email' => !empty($db['smtp_from_email']) ? $db['smtp_from_email'] : SMTP_FROM_EMAIL,
            'smtp_from_name'  => !empty($db['smtp_from_name'])  ? $db['smtp_from_name']  : SMTP_FROM_NAME,
        ];
    }
}
