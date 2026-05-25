<?php

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Mailer.php';

session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
]);
session_start();

require_once APP_PATH . '/models/AdminModel.php';
require_once APP_PATH . '/models/DashboardModel.php';
require_once APP_PATH . '/models/UserModel.php';
require_once APP_PATH . '/models/JobModel.php';
require_once APP_PATH . '/models/OtherModels.php';
require_once APP_PATH . '/models/AgentApprovalModel.php';
require_once APP_PATH . '/models/PortalModel.php';

require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/SectionControllers.php';
require_once APP_PATH . '/controllers/AgentApprovalController.php';
require_once APP_PATH . '/controllers/AdminProfileController.php';
require_once APP_PATH . '/controllers/PortalController.php';


$page   = $_GET['page']   ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$method = $_SERVER['REQUEST_METHOD'];

// ── Dispatch 
switch ($page) {

    case 'signin':                (new PortalController())->signin();             break;
    case 'signup':                (new PortalController())->signup();             break;
    case 'verify-signup-otp':     (new PortalController())->verifySignupOtp();    break;
    case 'resend-signup-otp':     (new PortalController())->resendSignupOtp();    break;
    case 'forgot-user-password':  (new PortalController())->forgot();             break;
    case 'reset-user-password':   (new PortalController())->reset();              break;
    case 'logout-user':           (new PortalController())->logout();             break;
    case 'home':                  (new PortalController())->home();               break;
    case 'network':               (new PortalController())->network();            break;
    case 'jobs-board':            (new PortalController())->jobs();               break;
    case 'messages':              (new PortalController())->messages();           break;
    case 'profile':               (new PortalController())->profile();            break;
    case 'recruiter-dashboard':   (new PortalController())->recruiterDashboard(); break;
    case 'recruiter-jobs':        (new PortalController())->recruiterJobs();      break;
    case 'applicants':            (new PortalController())->applicants();         break;
    case 'company-profile':       (new PortalController())->companyProfile();     break;
    case 'portal-notifications':  (new PortalController())->notifications();      break;
    case 'view-profile':          (new PortalController())->viewProfile();         break;
    case 'view-company':          (new PortalController())->viewCompany();         break;
    case 'ajax':                  (new PortalController())->ajax();               break;

    // ── AUTH 
    case 'login':
        $ctrl = new AuthController();
        $method === 'POST' ? $ctrl->loginPost() : $ctrl->loginPage();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // ── DASHBOARD 
    case 'dashboard':
    case '':
        (new DashboardController())->index();
        break;

    case 'admin-badges':
        (new DashboardController())->badges();
        break;

    // ── USERS─
    case 'users':
        $ctrl = new UserController();
        switch ($action) {
            case 'block':  $ctrl->block();  break;
            case 'delete': $ctrl->delete(); break;
            case 'edit':   $ctrl->edit();   break;
            case 'send-reset': $ctrl->sendReset(); break;
            case 'update':
                if ($method === 'POST') $ctrl->update();
                else $ctrl->index();
                break;
            default:       $ctrl->index();  break;
        }
        break;

    // ── COMPANIES 
    case 'companies':
        $ctrl = new CompanyController();
        switch ($action) {
            case 'verify': $ctrl->verify(); break;
            case 'block':  $ctrl->block();  break;
            case 'delete': $ctrl->delete(); break;
            case 'edit':   $ctrl->edit();   break;
            case 'send-reset': $ctrl->sendReset(); break;
            case 'update':
                if ($method === 'POST') $ctrl->update();
                else $ctrl->index();
                break;
            default:       $ctrl->index();  break;
        }
        break;

    // ── JOBS
    case 'jobs':
        $ctrl = new JobController();
        if ($method === 'POST' && $action === 'toggle-featured') {
            $ctrl->toggleFeatured();
        } else {
            switch ($action) {
                case 'approve': $ctrl->approve(); break;
                case 'reject':  $ctrl->reject();  break;
                case 'delete':  $ctrl->delete();  break;
                case 'hide':    $ctrl->hide();    break;
                case 'unhide':  $ctrl->unhide();  break;
                default:        $ctrl->index();   break;
            }
        }
        break;

    // ── CONTENT
    case 'content':
        $ctrl = new ContentController();
        switch ($action) {
            case 'hide':   $ctrl->hide();   break;
            case 'unhide': $ctrl->unhide(); break;
            case 'delete': $ctrl->delete(); break;
            default:       $ctrl->index();  break;
        }
        break;

    case 'reports':
        $ctrl = new ReportController();
        switch ($action) {
            case 'resolve': $ctrl->resolve(); break;
            case 'delete-post': $ctrl->deletePost(); break;
            default:        $ctrl->index();   break;
        }
        break;

    case 'notifications':
        $ctrl = new NotificationController();
        if ($method === 'POST' && $action === 'send') {
            $ctrl->send();
        } else {
            $ctrl->index();
        }
        break;

    case 'settings':
        $ctrl = new SettingsController();
        if ($method === 'POST' && $action === 'save') {
            $ctrl->save();
        } elseif ($method === 'POST' && $action === 'test-smtp') {
            $ctrl->testSmtp();
        } else {
            $ctrl->index();
        }
        break;

    case 'agents':
        $ctrl = new AgentApprovalController();
        switch ($action) {
            case 'approve':         $ctrl->approve();       break;
            case 'reject':          $ctrl->reject();        break;
            case 'block':           $ctrl->block();         break;
            case 'unblock':         $ctrl->unblock();       break;
            case 'delete':          $ctrl->delete();        break;
            case 'submit-profile':  $ctrl->submitProfile(); break;
            default:                $ctrl->index();         break;
        }
        break;

    case 'admin-profile':
        $ctrl = new AdminProfileController();
        if ($method === 'POST' && $action === 'save') {
            $ctrl->save();
        } else {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        break;

    case 'forgot-password':
        (new AuthController())->forgotPassword();
        break;
    case 'reset-password':
        (new AuthController())->resetPassword();
        break;

    default:
        http_response_code(404);
        $home = APP_URL . '/home';
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width,initial-scale=1'>
        <title>404 — Page Not Found</title>
        <style>
          body{font-family:'Segoe UI',sans-serif;background:#f3f2ef;display:flex;align-items:center;
               justify-content:center;min-height:100vh;margin:0}
          .box{background:#fff;border-radius:12px;padding:48px 40px;text-align:center;
               max-width:420px;box-shadow:0 2px 16px rgba(0,0,0,.08)}
          h1{color:#0a66c2;font-size:64px;margin:0 0 8px}
          h2{margin:0 0 12px;color:#1d2226}p{color:#666;margin:0 0 28px}
          a{background:#0a66c2;color:#fff;padding:10px 24px;border-radius:24px;
            text-decoration:none;font-weight:600;display:inline-block}
          a:hover{background:#004182}
        </style></head><body>
        <div class='box'>
          <h1>404</h1><h2>Page Not Found</h2>
          <p>The page you are looking for does not exist or has been moved.</p>
          <a href='{$home}'>Go to Home</a>
        </div></body></html>";
        break;
}