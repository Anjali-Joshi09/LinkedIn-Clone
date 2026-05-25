<?php
// app/controllers/AgentApprovalController.php
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Mailer.php';
require_once APP_PATH . '/models/AgentApprovalModel.php';
require_once APP_PATH . '/models/OtherModels.php';

class AgentApprovalController extends Controller {

    private function mailer(): Mailer {
        $db = (new SettingsModel())->getAll(); //othermodels.php
        $settings = [
            'smtp_host'       => !empty($db['smtp_host'])       ? $db['smtp_host']       : SMTP_HOST,
            'smtp_port'       => !empty($db['smtp_port'])       ? $db['smtp_port']       : SMTP_PORT,
            'smtp_user'       => !empty($db['smtp_user'])       ? $db['smtp_user']       : SMTP_USER,
            'smtp_pass'       => !empty($db['smtp_pass'])       ? $db['smtp_pass']       : SMTP_PASS,
            'smtp_from_email' => !empty($db['smtp_from_email']) ? $db['smtp_from_email'] : SMTP_FROM_EMAIL,
            'smtp_from_name'  => !empty($db['smtp_from_name'])  ? $db['smtp_from_name']  : SMTP_FROM_NAME,
        ];
        return new Mailer($settings);
    }

    /** List all agent approval requests */
    public function index(): void {
        $this->requireAuth(); //Controller.php
        $model     = new AgentApprovalModel();
        $status    = $_GET['status'] ?? '';     //pending, approved, rejected
        $approvals = $model->getAll($status);   //AgentApprovalModel.php
        $flash     = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $this->render('agents/index', compact('approvals', 'flash', 'status') + ['admin' => $this->admin(), 'pageTitle' => 'Company Approvals']);//Controller.php
    }

    public function approve(): void {
        $this->requireAuth(); //Controller.php
        $id    = (int) ($_GET['id'] ?? 0);
        $model = new AgentApprovalModel();
        $agent = $model->find($id); //MOdel.php


        if (!$agent) {
            $this->flash('error', 'Approval request not found.'); //Controller.php
            $this->redirect(APP_URL . '/agents'); //Controller.php
            return;
        }

        $approved = $model->approve($id, $_SESSION['admin_id']); //AgentApprovalModel.php

        if ($approved) {
            $mailer  = $this->mailer();
            $sent    = $mailer->sendAgentApproved($agent['email'], $agent['name']);//Mailer.php
            $mailMsg = $sent ? ' Approval email sent to agent.' : ' (Email could not be sent — check SMTP settings.)';
            $this->flash('success', "Agent '{$agent['name']}' approved successfully.{$mailMsg}");
        } else {
            $this->flash('error', 'Could not approve agent. Please try again.');
        }

        $this->redirect(APP_URL . '/agents');
    }

    public function reject(): void {
        $this->requireAuth(); //Controller.php  
        $id    = (int) ($_GET['id'] ?? 0);
        $note  = trim($_POST['note'] ?? $_GET['note'] ?? '');
        $model = new AgentApprovalModel();
        $agent = $model->find($id); //Model.php

        if (!$agent) {
            $this->flash('error', 'Approval request not found.');
            $this->redirect(APP_URL . '/agents');
            return;
        }

        $rejected = $model->reject($id, $_SESSION['admin_id'], $note);//AgentApprovalModel.php

        if ($rejected) {
            $mailer  = $this->mailer();
            $sent    = $mailer->sendAgentRejected($agent['email'], $agent['name'], $note);//MAiler.php  
            $mailMsg = $sent ? ' Rejection email sent to agent.' : ' (Email could not be sent — check SMTP settings.)';
            $this->flash('success', "Agent '{$agent['name']}' rejected.{$mailMsg}");
        } else {
            $this->flash('error', 'Could not reject agent. Please try again.');
        }

        $this->redirect(APP_URL . '/agents');
    }

    /** Called when an company submits their profile */
    public function submitProfile(): void {
        $this->requirePost(); //Controller.php

        $data = [
            'user_id'  => (int) ($_POST['user_id'] ?? 0),
            'name'     => trim($_POST['name']     ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'phone'    => trim($_POST['phone']    ?? ''),
            'headline' => trim($_POST['headline'] ?? ''),
            'bio'      => trim($_POST['bio']      ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'website'  => trim($_POST['website']  ?? ''),
        ];

        if (!$data['user_id'] || !$data['name'] || !$data['email']) {
            $this->json(['success' => false, 'message' => 'Missing required fields.'], 400);
            return;
        }

        $model    = new AgentApprovalModel();
        $id       = $model->createRequest($data); //AgentApprovalModel.php
        $mailer   = $this->mailer();
        $settings = (new SettingsModel())->getAll();

        // 1. Notify agent their account is pending review
        $mailer->sendAgentAccountCreated($data['email'], $data['name']);//Mailer.php

        // 2. Notify admin of new approval request
        $adminEmail = !empty($settings['smtp_user']) ? $settings['smtp_user'] : SMTP_USER;
        $approveUrl = APP_URL . "/agents?action=approve&id={$id}";
        $rejectUrl  = APP_URL . "/agents?action=reject&id={$id}";
        $mailer->sendAgentRequestToAdmin($adminEmail, 'Admin', $data, $approveUrl, $rejectUrl);//Mailer.php

        $this->json(['success' => true, 'message' => 'Profile submitted for approval.']);//Controller.php
    }

    /** Block a company account */
    public function block(): void {
        $this->requireAuth();// COntroller.php
        $id = (int) ($_GET['id'] ?? 0);
        $model = new AgentApprovalModel();
        $agent = $model->find($id);//Model.php
        if (!$agent) {
            $this->flash('error', 'Request not found.');
            $this->redirect(APP_URL . '/agents');
            return;
        }
        require_once APP_PATH . '/core/Database.php';
        $db = Database::getInstance();
        $db->prepare("UPDATE agent_approvals SET status='rejected', admin_note='Blocked by admin' WHERE id=?")->execute([$id]);
        if (!empty($agent['user_id'])) {
            $db->prepare("UPDATE users SET status='blocked' WHERE id=?")->execute([$agent['user_id']]);
            $db->prepare("UPDATE companies SET status='blocked' WHERE user_id=?")->execute([$agent['user_id']]);
        }
        $this->flash('success', "Company '{$agent['name']}' has been blocked.");
        $this->redirect(APP_URL . '/agents');
    }

    /** Unblock a company account */
    public function unblock(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $model = new AgentApprovalModel();
        $agent = $model->find($id);
        if (!$agent) {
            $this->flash('error', 'Request not found.');
            $this->redirect(APP_URL . '/agents');
            return;
        }
        require_once APP_PATH . '/core/Database.php';
        $db = Database::getInstance();
        // Restore agent_approvals status to approved
        $db->prepare("UPDATE agent_approvals SET status='approved', admin_note=NULL WHERE id=?")->execute([$id]);
        // Unblock the user + company accounts
        if (!empty($agent['user_id'])) {
            $db->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$agent['user_id']]);
            $db->prepare("UPDATE companies SET status='verified' WHERE user_id=?")->execute([$agent['user_id']]);
        }
        $this->flash('success', "Company '{$agent['name']}' has been unblocked.");
        $this->redirect(APP_URL . '/agents');
    }

    /** Delete a company/agent account */
    public function delete(): void {
        $this->requireAuth();
        $id = (int) ($_GET['id'] ?? 0);
        $model = new AgentApprovalModel();
        $agent = $model->find($id);
        if (!$agent) {
            $this->flash('error', 'Request not found.');
            $this->redirect(APP_URL . '/agents');
            return;
        }
        require_once APP_PATH . '/core/Database.php';
        $db = Database::getInstance();
        // Delete agent_approvals record
        $db->prepare("DELETE FROM agent_approvals WHERE id=?")->execute([$id]);
        // Optionally deactivate user account
        if (!empty($agent['user_id'])) {
            $db->prepare("UPDATE users SET status='deleted' WHERE id=?")->execute([$agent['user_id']]);
            $db->prepare("UPDATE companies SET status='rejected' WHERE user_id=?")->execute([$agent['user_id']]);
        }
        $this->flash('success', "Company '{$agent['name']}' has been deleted.");
        $this->redirect(APP_URL . '/agents');
    }
}
