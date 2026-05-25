<?php
// UserController
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/UserModel.php';

class UserController extends Controller {

    private function changedFields(array $before, array $after, array $labels): array {
        $changed = [];
        foreach ($labels as $field => $label) {
            $old = trim((string)($before[$field] ?? ''));
            $new = trim((string)($after[$field] ?? ''));
            if ($old !== $new) {
                $changed[] = ['label' => $label, 'old' => $old === '' ? 'Blank' : $old, 'new' => $new === '' ? 'Blank' : $new];
            }
        }
        return $changed;
    }

    private function sendAdminUpdateEmail(array $recipient, array $changes, string $subject = 'Your profile was updated by admin'): void {
        if (empty($recipient['email']) || empty($changes)) return;
        require_once APP_PATH . '/core/Mailer.php';
        $rows = '';
        foreach ($changes as $change) {
            $rows .= '<tr><td style="padding:8px;border-bottom:1px solid #eee;font-weight:600;">' . htmlspecialchars($change['label']) . '</td><td style="padding:8px;border-bottom:1px solid #eee;color:#666;">' . htmlspecialchars($change['old']) . '</td><td style="padding:8px;border-bottom:1px solid #eee;color:#111;">' . htmlspecialchars($change['new']) . '</td></tr>';
        }
        $when = date('d M Y, H:i:s');
        $body = "<div style='font-family:sans-serif;max-width:620px;margin:auto;'>
            <h2 style='color:#0a66c2;'>Account profile updated</h2>
            <p>Hello <strong>" . htmlspecialchars($recipient['name'] ?? 'User') . "</strong>,</p>
            <p>Your account information was updated by an administrator on <strong>{$when}</strong>.</p>
            <table style='width:100%;border-collapse:collapse;margin:18px 0;font-size:14px;'>
              <thead><tr><th style='text-align:left;padding:8px;background:#f3f6f8;'>Field</th><th style='text-align:left;padding:8px;background:#f3f6f8;'>Previous</th><th style='text-align:left;padding:8px;background:#f3f6f8;'>Updated</th></tr></thead>
              <tbody>{$rows}</tbody>
            </table>
            <p>If you did not expect these changes, please contact support or reply through the official support channel.</p>
            <p style='color:#888;font-size:12px;'>This is an automated no-reply notification.</p>
        </div>";
        (new Mailer($this->mailSettings()))->send($recipient['email'], $recipient['name'] ?? '', $subject, $body);
    }

    private function sendPasswordResetEmail(array $user): bool {
        if (empty($user['id']) || empty($user['email'])) return false;
        $token = bin2hex(random_bytes(32));
        (new UserModel())->saveResetToken((int)$user['id'], $token);
        $url = APP_URL . '/reset-user-password?token=' . $token;
        require_once APP_PATH . '/core/Mailer.php';
        $name = htmlspecialchars($user['name'] ?? 'User');
        $body = "<div style='font-family:sans-serif;max-width:520px;margin:auto;'>
            <h2 style='color:#0a66c2;'>Password reset requested</h2>
            <p>Hello <strong>{$name}</strong>,</p>
            <p>An administrator generated a secure password reset link for your account. This link expires in 1 hour and can be used once.</p>
            <p style='margin:24px 0;'><a href='{$url}' style='background:#0a66c2;color:#fff;padding:11px 22px;border-radius:20px;text-decoration:none;font-weight:700;'>Create New Password</a></p>
            <p>If you did not request help with your password, contact support immediately.</p>
        </div>";
        return (new Mailer($this->mailSettings()))->send($user['email'], $user['name'] ?? '', 'Create a new password', $body);
    }

    public function index(): void {
        $this->requireAuth();
        // No-cache so browser back button always shows fresh list
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        $model   = new UserModel();
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'role'   => $_GET['role']   ?? '',
        ];
        $users     = $model->getAll($filters);
        $companies = (new CompanyModel())->getAll($_GET['cstatus'] ?? '');
        $flash     = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('users/index', compact('users', 'filters', 'companies', 'flash') + ['admin' => $this->admin()]);
    }

    public function block(): void {
        $this->requireAuth();
        $id    = (int) ($_GET['id'] ?? 0);
        $model = new UserModel();
        $user  = $model->find($id);
        if ($user) {
            $newStatus = $user['status'] === 'blocked' ? 'active' : 'blocked';
            $model->updateStatus($id, $newStatus, $_SESSION['admin_id']);
            $this->sendAdminUpdateEmail($user + ['email' => $user['email']], [[
                'label' => 'Account Status',
                'old' => $user['status'] ?? 'Unknown',
                'new' => $newStatus,
            ]], 'Your account status was updated');
            $this->flash('success', "User #{$id} " . ($newStatus === 'blocked' ? 'blocked' : 'unblocked') . '.');
        }
        $this->redirect(APP_URL . '/users');
    }

    public function delete(): void {
        $this->requireAuth();
        $id    = (int) ($_GET['id'] ?? 0);
        $model = new UserModel();
        $model->deleteUser($id, $_SESSION['admin_id']);
        $this->flash('success', "User #{$id} deleted.");
        $this->redirect(APP_URL . '/users');
    }

    public function edit(): void {
        $this->requireAuth();
        $id   = (int) ($_GET['id'] ?? 0);
        $model = new UserModel();
        $user  = $model->find($id);
        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect(APP_URL . '/users?tab=users');
            return;
        }
        // No-cache so browser back button always reloads fresh
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('users/edit', ['user' => $user, 'flash' => $flash, 'admin' => $this->admin()]);
    }

    public function update(): void {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/users');
            return;
        }
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->flash('error', 'Invalid user.');
            $this->redirect(APP_URL . '/users');
            return;
        }

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');

        if (!$name || !$email) {
            $this->flash('error', 'Name and email are required.');
            $this->redirect(APP_URL . '/users?action=edit&id=' . $id);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect(APP_URL . '/users?action=edit&id=' . $id);
            return;
        }
        $model = new UserModel();
        $before = $model->find($id);
        if (!$before) {
            $this->flash('error', 'User not found.');
            $this->redirect(APP_URL . '/users');
            return;
        }

        $data = [
            'name'     => $name,
            'email'    => $email,
            'phone'    => trim($_POST['phone']    ?? ''),
            'headline' => trim($_POST['headline'] ?? ''),
            'bio'      => trim($_POST['bio']      ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'website'  => trim($_POST['website']  ?? ''),
            'status'   => in_array($_POST['status'] ?? '', ['active','blocked','pending','suspended'])
                            ? $_POST['status'] : 'active',
        ];
        // Handle avatar upload
        if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = $this->uploadImage($_FILES['avatar'], 'avatars');
            if ($avatar) $data['avatar'] = $avatar;
        }

        // Handle cover upload
        if (!empty($_FILES['cover']['tmp_name']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $cover = $this->uploadImage($_FILES['cover'], 'covers');
            if ($cover) $data['cover'] = $cover;
        }

        $model->updateUser($id, $data, $_SESSION['admin_id']);
        $after = $model->find($id) ?: $data;
        $changes = $this->changedFields($before, $after, [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'headline' => 'Headline',
            'bio' => 'Bio/About',
            'location' => 'Location',
            'website' => 'Website',
            'status' => 'Account Status',
            'avatar' => 'Profile Photo',
            'cover' => 'Cover Photo',
        ]);
        $this->sendAdminUpdateEmail($after, $changes);
        $this->flash('success', 'User profile updated successfully.');
        $this->redirect(APP_URL . '/users?action=edit&id=' . $id);
    }

    public function sendReset(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $user = (new UserModel())->find($id);
        if (!$user) {
            $this->flash('error', 'User not found.');
            $this->redirect(APP_URL . '/users?tab=users');
            return;
        }
        $sent = $this->sendPasswordResetEmail($user);
        $this->flash($sent ? 'success' : 'error', $sent ? 'Password reset link sent to user.' : 'Could not send password reset email. Check SMTP settings.');
        $this->redirect(APP_URL . '/users?action=edit&id=' . $id);
    }

    /** Upload image — saves to public/uploads/{folder}/ */
    private function uploadImage(array $file, string $folder): ?string {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime    = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = bin2hex(random_bytes(12)) . '.' . $ext;
        $dir  = PUBLIC_PATH . '/uploads/' . $folder;

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $dest = $dir . '/' . $name;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/' . $folder . '/' . $name;
        }
        return null;
    }
}


// ─────────────────────────────────────────────────────────────
// CompanyController
require_once APP_PATH . '/models/OtherModels.php';

class CompanyController extends Controller {

    private function changedFields(array $before, array $after, array $labels): array {
        $changed = [];
        foreach ($labels as $field => $label) {
            $old = trim((string)($before[$field] ?? ''));
            $new = trim((string)($after[$field] ?? ''));
            if ($old !== $new) {
                $changed[] = ['label' => $label, 'old' => $old === '' ? 'Blank' : $old, 'new' => $new === '' ? 'Blank' : $new];
            }
        }
        return $changed;
    }

    private function sendAdminUpdateEmail(array $company, array $changes): void {
        if (empty($company['email']) || empty($changes)) return;
        require_once APP_PATH . '/core/Mailer.php';
        $rows = '';
        foreach ($changes as $change) {
            $rows .= '<tr><td style="padding:8px;border-bottom:1px solid #eee;font-weight:600;">' . htmlspecialchars($change['label']) . '</td><td style="padding:8px;border-bottom:1px solid #eee;color:#666;">' . htmlspecialchars($change['old']) . '</td><td style="padding:8px;border-bottom:1px solid #eee;color:#111;">' . htmlspecialchars($change['new']) . '</td></tr>';
        }
        $when = date('d M Y, H:i:s');
        $body = "<div style='font-family:sans-serif;max-width:620px;margin:auto;'>
            <h2 style='color:#0a66c2;'>Company profile updated</h2>
            <p>Hello <strong>" . htmlspecialchars($company['name'] ?? 'Company') . "</strong>,</p>
            <p>Your company profile was updated by an administrator on <strong>{$when}</strong>.</p>
            <table style='width:100%;border-collapse:collapse;margin:18px 0;font-size:14px;'>
              <thead><tr><th style='text-align:left;padding:8px;background:#f3f6f8;'>Field</th><th style='text-align:left;padding:8px;background:#f3f6f8;'>Previous</th><th style='text-align:left;padding:8px;background:#f3f6f8;'>Updated</th></tr></thead>
              <tbody>{$rows}</tbody>
            </table>
            <p>If you did not expect these changes, please contact support through the official support channel.</p>
            <p style='color:#888;font-size:12px;'>This is an automated no-reply notification.</p>
        </div>";
        (new Mailer($this->mailSettings()))->send($company['email'], $company['name'] ?? '', 'Your company profile was updated by admin', $body);
    }

    private function sendPasswordResetEmail(array $company): bool {
        if (empty($company['id']) || empty($company['email'])) return false;
        $token = bin2hex(random_bytes(32));
        $model = new CompanyModel();
        if (!$model->saveResetTokenForCompany((int)$company['id'], $token)) return false;
        $url = APP_URL . '/reset-user-password?token=' . $token;
        require_once APP_PATH . '/core/Mailer.php';
        $name = htmlspecialchars($company['name'] ?? 'Company');
        $body = "<div style='font-family:sans-serif;max-width:520px;margin:auto;'>
            <h2 style='color:#0a66c2;'>Password reset requested</h2>
            <p>Hello <strong>{$name}</strong>,</p>
            <p>An administrator generated a secure password reset link for your company account. This link expires in 1 hour and can be used once.</p>
            <p style='margin:24px 0;'><a href='{$url}' style='background:#0a66c2;color:#fff;padding:11px 22px;border-radius:20px;text-decoration:none;font-weight:700;'>Create New Password</a></p>
            <p>If you did not request help with your password, contact support immediately.</p>
        </div>";
        return (new Mailer($this->mailSettings()))->send($company['email'], $company['name'] ?? '', 'Create a new company account password', $body);
    }

    public function index(): void {
        $this->requireAuth();
        $model     = new CompanyModel();
        $companies = $model->getAll($_GET['status'] ?? '');
        $flash     = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('companies/index', compact('companies', 'flash') + ['admin' => $this->admin()]);
    }

    public function verify(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $model = new CompanyModel();
        $company = $model->find($id);
        $old = $company['status'] ?? '';
        $model->updateStatus($id, 'verified', $_SESSION['admin_id']);
        if ($company) {
            $company['status'] = 'verified';
            $this->sendAdminUpdateEmail($company, [['label' => 'Company Status', 'old' => $old, 'new' => 'verified']]);
        }
        $this->flash('success', "Company #{$id} verified.");
        $this->redirect(APP_URL . '/companies');
    }

    public function block(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $model = new CompanyModel();
        $company = $model->find($id);
        $old = $company['status'] ?? '';
        $model->updateStatus($id, 'blocked', $_SESSION['admin_id']);
        if ($company) {
            $company['status'] = 'blocked';
            $this->sendAdminUpdateEmail($company, [['label' => 'Company Status', 'old' => $old, 'new' => 'blocked']]);
        }
        $this->flash('success', "Company #{$id} blocked.");
        $this->redirect(APP_URL . '/companies');
    }

    public function delete(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new CompanyModel())->delete($id);
        $this->flash('success', "Company #{$id} deleted.");
        $this->redirect(APP_URL . '/companies');
    }

    public function edit(): void {
        $this->requireAuth();
        $id      = (int) ($_GET['id'] ?? 0);
        $model   = new CompanyModel();
        $company = $model->find($id);
        if (!$company) {
            $this->flash('error', 'Company not found.');
            $this->redirect(APP_URL . '/users?tab=companies');
            return;
        }
        // No-cache so browser back button always reloads fresh
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('companies/edit', ['company' => $company, 'flash' => $flash, 'admin' => $this->admin()]);
    }

    public function update(): void {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(APP_URL . '/companies');
            return;
        }
        $this->verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->flash('error', 'Invalid company.');
            $this->redirect(APP_URL . '/companies');
            return;
        }

        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!$name || !$email) {
            $this->flash('error', 'Company name and email are required.');
            $this->redirect(APP_URL . '/companies?action=edit&id=' . $id);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect(APP_URL . '/companies?action=edit&id=' . $id);
            return;
        }

        $model = new CompanyModel();
        $before = $model->find($id);
        if (!$before) {
            $this->flash('error', 'Company not found.');
            $this->redirect(APP_URL . '/users?tab=companies');
            return;
        }

        $data = [
            'name'         => $name,
            'email'        => $email,
            'phone'        => trim($_POST['phone']        ?? ''),
            'industry'     => trim($_POST['industry']     ?? ''),
            'company_size' => trim($_POST['company_size'] ?? ''),
            'founded_year' => trim($_POST['founded_year'] ?? '') ?: null,
            'location'     => trim($_POST['location']     ?? ''),
            'website'      => trim($_POST['website']      ?? ''),
            'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
            'description'  => trim($_POST['description']  ?? ''),
            'status'       => in_array($_POST['status'] ?? '', ['pending','verified','blocked','rejected'])
                                ? $_POST['status'] : 'pending',
        ];

        // Handle logo upload
        if (!empty($_FILES['logo']['tmp_name'])) {
            $logo = $this->uploadImage($_FILES['logo'], 'companies');
            if ($logo) $data['logo'] = $logo;
        }

        // Handle banner upload
        if (!empty($_FILES['banner']['tmp_name'])) {
            $banner = $this->uploadImage($_FILES['banner'], 'companies/banners');
            if ($banner) $data['banner'] = $banner;
        }

        $model->updateCompany($id, $data, $_SESSION['admin_id']);
        $after = $model->find($id) ?: $data;
        $changes = $this->changedFields($before, $after, [
            'name' => 'Company Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'industry' => 'Industry',
            'company_size' => 'Company Size',
            'founded_year' => 'Founded Year',
            'location' => 'Location',
            'website' => 'Website',
            'linkedin_url' => 'LinkedIn URL',
            'description' => 'Description',
            'status' => 'Company Status',
            'logo' => 'Logo',
            'banner' => 'Cover Image',
        ]);
        $this->sendAdminUpdateEmail($after, $changes);
        $this->flash('success', 'Company profile updated successfully.');
        $this->redirect(APP_URL . '/companies?action=edit&id=' . $id);
    }

    public function sendReset(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $id = (int) ($_GET['id'] ?? 0);
        $company = (new CompanyModel())->find($id);
        if (!$company) {
            $this->flash('error', 'Company not found.');
            $this->redirect(APP_URL . '/users?tab=companies');
            return;
        }
        $sent = $this->sendPasswordResetEmail($company);
        $this->flash($sent ? 'success' : 'error', $sent ? 'Password reset link sent to company.' : 'Could not send password reset email. Check SMTP settings or linked company user.');
        $this->redirect(APP_URL . '/companies?action=edit&id=' . $id);
    }

    /** Upload image helper — saves to public/uploads/{folder}/ */
    private function uploadImage(array $file, string $folder): ?string {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        // Use mime_content_type() on the actual file — not the browser-supplied $file['type']
        // which can be spoofed to bypass the check (MIME confusion / MIME spoofing attack)
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null; // 5MB max

        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name    = bin2hex(random_bytes(12)) . '.' . $ext;
        $dir     = PUBLIC_PATH . '/uploads/' . $folder;

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $dest = $dir . '/' . $name;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/' . $folder . '/' . $name;
        }
        return null;
    }
}


// ─────────────────────────────────────────────────────────────
// JobController

class JobController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $model = new JobModel();
        $jobs  = $model->getAll($_GET['status'] ?? '');
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('jobs/index', compact('jobs', 'flash') + ['admin' => $this->admin()]);
    }

    public function approve(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new JobModel())->updateStatus($id, 'approved', $_SESSION['admin_id']);
        $this->flash('success', "Job #{$id} approved.");
        $this->redirect(APP_URL . '/jobs');
    }

    public function reject(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new JobModel())->updateStatus($id, 'rejected', $_SESSION['admin_id']);
        $this->flash('success', "Job #{$id} rejected.");
        $this->redirect(APP_URL . '/jobs');
    }

    public function delete(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new JobModel())->delete($id);
        $this->flash('success', "Job #{$id} deleted.");
        $this->redirect(APP_URL . '/jobs');
    }

    public function hide(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new JobModel())->hide($id, $_SESSION['admin_id']);
        $this->flash('success', "Job #{$id} hidden.");
        $this->redirect(APP_URL . '/jobs');
    }

    public function unhide(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new JobModel())->unhide($id, $_SESSION['admin_id']);
        $this->flash('success', "Job #{$id} restored to pending.");
        $this->redirect(APP_URL . '/jobs');
    }

    /** AJAX: toggle featured status */
    public function toggleFeatured(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $id       = (int) ($_POST['id']       ?? 0);
        $featured = (int) ($_POST['featured'] ?? 0);
        (new JobModel())->toggleFeatured($id, $featured);
        $this->json(['success' => true]);
    }
}


// ─────────────────────────────────────────────────────────────
// ContentController

class ContentController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $model = new PostModel();
        $posts = $model->getAll($_GET['status'] ?? '');
        $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('content/index', compact('posts', 'flash') + ['admin' => $this->admin()]);
    }

    public function hide(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new PostModel())->updateStatus($id, 'hidden', $_SESSION['admin_id']);
        $this->flash('success', "Post #{$id} hidden.");
        $this->redirect(APP_URL . '/content');
    }

    public function unhide(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new PostModel())->updateStatus($id, 'active', $_SESSION['admin_id']);
        $this->flash('success', "Post #{$id} restored.");
        $this->redirect(APP_URL . '/content');
    }

    public function delete(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        (new PostModel())->updateStatus($id, 'deleted', $_SESSION['admin_id']);
        $this->flash('success', "Post #{$id} deleted.");
        $this->redirect(APP_URL . '/content');
    }
}


// ─────────────────────────────────────────────────────────────
// ReportController

class ReportController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $reports = (new ReportModel())->getPending();
        $flash   = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('reports/index', compact('reports', 'flash') + ['admin' => $this->admin()]);
    }

    public function resolve(): void {
        $this->requireAuth();
        $id     = (int) ($_GET['id'] ?? 0);
        $status = in_array($_GET['status'] ?? '', ['resolved','dismissed']) ? $_GET['status'] : 'resolved';
        (new ReportModel())->resolve($id, $status, $_SESSION['admin_id']);
        $this->flash('success', "Report #{$id} {$status}.");
        $this->redirect(APP_URL . '/reports');
    }

    public function deletePost(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        if ((new ReportModel())->deletePostTarget($id, $_SESSION['admin_id'])) {
            $this->flash('success', 'Reported post deleted permanently.');
        } else {
            $this->flash('error', 'Could not delete the reported post.');
        }
        $this->redirect(APP_URL . '/reports');
    }
}


// ─────────────────────────────────────────────────────────────
// NotificationController

class NotificationController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $notifications = (new NotificationModel())->getAll();
        $flash         = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('notifications/index', compact('notifications', 'flash') + ['admin' => $this->admin()]);
    }

    public function send(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $this->requirePost();
        $recipient = $_POST['recipient'] ?? 'all_users';
        $subject   = trim($_POST['subject'] ?? '');
        $message   = trim($_POST['message'] ?? '');
        $isSchedule = ($_POST['action'] ?? '') === 'schedule';

        if (!$subject || !$message) {
            $this->flash('error', 'Subject and message are required.');
            $this->redirect(APP_URL . '/notifications');
            return;
        }

        $data = [
            'recipient'    => $recipient,
            'subject'      => $subject,
            'message'      => $message,
            'type'         => 'email',
            'status'       => $isSchedule ? 'scheduled' : 'sent',
            'scheduled_at' => $isSchedule ? ($_POST['scheduled_at'] ?? null) : null,
        ];
        (new NotificationModel())->create($data, $_SESSION['admin_id']);

        // Push to user_notifications so users see it in their alerts panel
        if (!$isSchedule) {
            require_once APP_PATH . '/core/Database.php';
            require_once APP_PATH . '/models/PortalModel.php';
            $db  = Database::getInstance();
            $pm  = new PortalModel();
            $notifMsg = $subject . ': ' . $message;

            if ($recipient === 'all_users') {
                $rows = $db->query("SELECT id FROM users WHERE role='user' AND status='active'")->fetchAll(PDO::FETCH_COLUMN);
            } elseif ($recipient === 'all_companies') {
                $rows = $db->query("SELECT id FROM users WHERE role='company' AND status='active'")->fetchAll(PDO::FETCH_COLUMN);
            } elseif ($recipient === 'active_users') {
                $rows = $db->query("SELECT id FROM users WHERE status='active' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $rows = [];
            }

            foreach ($rows as $uid) {
                $pm->notify((int)$uid, 'system', $notifMsg, null, null);
            }

            $count = count($rows);
            $this->flash('success', "Notification sent to {$count} user(s) successfully.");
        } else {
            $this->flash('success', 'Notification scheduled successfully.');
        }

        $this->redirect(APP_URL . '/notifications');
    }
}


// ─────────────────────────────────────────────────────────────
// SettingsController

class SettingsController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $settings = (new SettingsModel())->getAll();
        $flash    = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
        $this->render('settings/index', compact('settings', 'flash') + ['admin' => $this->admin()]);
    }

    public function save(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $this->requirePost();
        $model   = new SettingsModel();
        $allowed = ['smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_email','smtp_from_name'];
        $data = [];
        foreach ($allowed as $key) {
            $data[$key] = trim($_POST[$key] ?? '');
        }
        // Keep existing password if blank
        if ($data['smtp_pass'] === '') {
            unset($data['smtp_pass']);
        }
        $model->saveMany($data);
        $this->flash('success', 'SMTP settings saved successfully.');
        $this->redirect(APP_URL . '/settings');
    }

    public function testSmtp(): void {
        $this->requireAuth();
        header('Content-Type: application/json');
        $db = (new SettingsModel())->getAll();
        $settings = [
            'smtp_host'       => !empty($db['smtp_host'])       ? $db['smtp_host']       : SMTP_HOST,
            'smtp_port'       => !empty($db['smtp_port'])       ? $db['smtp_port']       : SMTP_PORT,
            'smtp_user'       => !empty($db['smtp_user'])       ? $db['smtp_user']       : SMTP_USER,
            'smtp_pass'       => !empty($db['smtp_pass'])       ? $db['smtp_pass']       : SMTP_PASS,
            'smtp_from_email' => !empty($db['smtp_from_email']) ? $db['smtp_from_email'] : SMTP_FROM_EMAIL,
            'smtp_from_name'  => !empty($db['smtp_from_name'])  ? $db['smtp_from_name']  : SMTP_FROM_NAME,
        ];
        $toEmail = !empty($settings['smtp_user']) ? $settings['smtp_user'] : SMTP_USER;
        $toName  = $settings['smtp_from_name'] ?: 'Admin';
        try {
            $mailer = new Mailer($settings);
            $sent   = $mailer->send(
                $toEmail, $toName,
                'LinkedIn Admin — SMTP Test Email',
                "<div style='font-family:sans-serif;max-width:480px;margin:auto;padding:30px'>
                  <h2 style='color:#0a66c2'>SMTP Test Successful</h2>
                  <p>This is a test email sent from your <strong>LinkedIn Admin</strong> panel.</p>
                  <p>If you received this, your SMTP configuration is working correctly.</p>
                  <p style='color:#888;font-size:12px;margin-top:20px'>Sent at: " . date('d M Y, H:i:s') . "</p>
                </div>"
            );
            if ($sent) {
                echo json_encode(['success' => true, 'email' => $toEmail]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Mailer returned false. Check SMTP credentials.']);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}