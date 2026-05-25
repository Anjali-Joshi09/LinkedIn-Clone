<?php
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/AdminModel.php';
require_once APP_PATH . '/helpers/captcha.php';

class AuthController extends Controller {

    public function loginPage(): void {
        if (!empty($_SESSION['admin_id'])) {
            $this->redirect(APP_URL . '/dashboard'); //Controller.php
        }
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->view('auth/login', ['flash' => $flash]); // COntroller.php
    }

    public function loginPost(): void {
        $this->requirePost(); // Controller.php

        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key      = 'login_fails_' . md5($ip);
        $attempts = $_SESSION[$key]['count'] ?? 0;
        $lockedAt = $_SESSION[$key]['locked_at'] ?? 0;
        $maxTries = 5;
        $lockSecs = 15 * 60; // 15 minutes

        if ($lockedAt && (time() - $lockedAt) < $lockSecs) {
            $wait = ceil(($lockSecs - (time() - $lockedAt)) / 60);
            $this->flash('error', "Too many failed attempts. Please try again in {$wait} minute(s).");
            $this->redirect(APP_URL . '/login');
            return;
        }
        // Reset lock if lockout period has passed
        if ($lockedAt && (time() - $lockedAt) >= $lockSecs) {
            unset($_SESSION[$key]);
            $attempts = 0;
        }

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $captcha  = trim($_POST['captcha']  ?? '');
        if (!$email || !$password) {
            $this->flash('error', 'Please fill in all fields.');
            $this->redirect(APP_URL . '/login');
            return;
        }
        if (!captcha_verify($captcha)) {      //helper/captcha.php
            $this->flash('error', 'Security code is incorrect. Please try again.');
            $this->redirect(APP_URL . '/login');
            return;
        }
        $model = new AdminModel();
        $admin = $model->authenticate($email, $password); //AdminModel.php
        if (!$admin) {
            // Track failed attempt
            $_SESSION[$key]['count'] = $attempts + 1;
            if ($_SESSION[$key]['count'] >= $maxTries) {
                $_SESSION[$key]['locked_at'] = time();
                $this->flash('error', 'Too many failed attempts. Account locked for 15 minutes.');
            } else {
                $remaining = $maxTries - $_SESSION[$key]['count'];
                $this->flash('error', "Invalid email or password. {$remaining} attempt(s) remaining.");
            }
            $this->redirect(APP_URL . '/login');
            return;
        }
        // Successful login — clear fail counter
        unset($_SESSION[$key]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin']    = [
            'id'    => $admin['id'],
            'name'  => $admin['name'],
            'email' => $admin['email'],
            'role'  => $admin['role'],
        ];
        $this->flash('success', 'Welcome back, ' . $admin['name'] . '!');
        $this->redirect(APP_URL . '/dashboard');
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        $this->redirect(APP_URL . '/login');
    }

    public function resetPassword(): void {
        $token = trim($_GET['token'] ?? '');
        if (!$token) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        $model = new AdminModel();
        $admin = $model->findByResetToken($token); //AdminModel.php

        // Token valid for 1 hour
        if (!$admin || (isset($admin['reset_token_at']) && strtotime($admin['reset_token_at']) < (time() - 3600))) {
            $this->flash('error', 'This password reset link is invalid or has expired. Please request a new one.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 8) {
                $this->view('auth/reset_password', ['token' => $token, 'flash' => ['type' => 'error', 'message' => 'Password must be at least 8 characters.']]);
                return;
            }
            if ($password !== $confirm) {
                $this->view('auth/reset_password', ['token' => $token, 'flash' => ['type' => 'error', 'message' => 'Passwords do not match.']]);
                return;
            }

            $model->updatePassword($admin['id'], $password); //AdminMOdel.php
            $model->clearResetToken($admin['id']); //AdminModel.php
            $this->flash('success', 'Password reset successfully. Please log in with your new password.');
            $this->redirect(APP_URL . '/login');
            return;
        }

        $this->view('auth/reset_password', ['token' => $token, 'flash' => null]);
    }

    public function forgotPassword(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['reset_email'] ?? '');
            if ($email) {
                $model = new AdminModel();
                $admin = $model->findByEmail($email); //AdminModel.php
                if ($admin) {
                    $token = bin2hex(random_bytes(32));
                    $model->saveResetToken($admin['id'], $token); //AdminModel.php
                    $resetUrl = APP_URL . '/reset-password?token=' . $token;
                    require_once APP_PATH . '/core/Mailer.php';
                    require_once APP_PATH . '/models/OtherModels.php';
                    $db = (new SettingsModel())->getAll();
                    $settings = [
                        'smtp_host'       => !empty($db['smtp_host'])       ? $db['smtp_host']       : SMTP_HOST,
                        'smtp_port'       => !empty($db['smtp_port'])       ? $db['smtp_port']       : SMTP_PORT,
                        'smtp_user'       => !empty($db['smtp_user'])       ? $db['smtp_user']       : SMTP_USER,
                        'smtp_pass'       => !empty($db['smtp_pass'])       ? $db['smtp_pass']       : SMTP_PASS,
                        'smtp_from_email' => !empty($db['smtp_from_email']) ? $db['smtp_from_email'] : SMTP_FROM_EMAIL,
                        'smtp_from_name'  => !empty($db['smtp_from_name'])  ? $db['smtp_from_name']  : SMTP_FROM_NAME,
                    ];
                    $mailer = new Mailer($settings);
                    $body   = "<div style='font-family:sans-serif;max-width:500px;margin:0 auto;padding:30px'>
                      <h2 style='color:#0a66c2'>LinkedIn Admin &mdash; Password Reset</h2>
                      <p>Hi <strong>{$admin['name']}</strong>,</p>
                      <p>Click the button below to reset your password. This link expires in 1 hour.</p>
                      <p style='margin:24px 0'>
                        <a href='{$resetUrl}' style='background:#0a66c2;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:600'>Reset Password</a>
                      </p>
                      <p style='color:#888;font-size:12px'>If you did not request this, please ignore this email.</p>
                    </div>";
                    $mailer->send($email, $admin['name'], 'LinkedIn Admin — Password Reset', $body);
                }
                $this->redirect(APP_URL . '/login?reset=1');
                return;
            }
        }
        $this->redirect(APP_URL . '/login');
    }
}