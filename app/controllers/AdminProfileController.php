<?php
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/AdminModel.php';

class AdminProfileController extends Controller {

    public function save(): void {

        if (empty($_SESSION['admin_id'])) {
            $this->redirect(APP_URL . '/login');
            return;
        }

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';

        if (!$name || !$email) {
            $this->flash('error', 'Name and email are required.');
            $this->redirect(APP_URL . '/dashboard');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect(APP_URL . '/dashboard');
            return;
        }
        if ($password !== '' && strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters.');
            $this->redirect(APP_URL . '/dashboard');
            return;
        }

        $adminId = (int) $_SESSION['admin_id'];
        $model   = new AdminModel();

   //     $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                  //strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false ||
                 // !empty($_SERVER['HTTP_FETCH_MODE']);

        try {
            $model->execute(
                "UPDATE admins SET name = ?, email = ? WHERE id = ?",
                [$name, $email, $adminId]
            );

            if ($password !== '') {
                $model->updatePassword($adminId, $password); //AdminModel.php
            }

            $updated = $model->queryOne("SELECT id, name, email, role FROM admins WHERE id = ?", [$adminId]);//AdminModel, Model
            if ($updated) {
                $_SESSION['admin'] = [
                    'id'    => $updated['id'],
                    'name'  => $updated['name'],
                    'email' => $updated['email'],
                    'role'  => $updated['role'],
                ];
            } // sesion update

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;

        } catch (Throwable $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}