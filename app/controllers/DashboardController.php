<?php
// app/controllers/DashboardController.php

require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/models/DashboardModel.php';

class DashboardController extends Controller {

    public function index(): void {
        $this->requireAuth(); //Controller.php

        $model = new DashboardModel();

        $stats     = $model->getStats(); //DashboardModel.php
        $chart     = $model->getChartData(); //DashboardModel.php
        $activity  = $model->getRecentActivity(10); //DashboardModel.php
        $flash     = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->render('dashboard/index', [      //controller.php
            'stats'    => $stats,
            'chart'    => $chart,
            'activity' => $activity,
            'flash'    => $flash,
            'admin'    => $this->admin(),
        ]);
    }

    public function badges(): void {
        $this->requireAuth(); //Controller.php
        require_once APP_PATH . '/models/UserModel.php';
        require_once APP_PATH . '/models/JobModel.php';
        require_once APP_PATH . '/models/OtherModels.php';
        require_once APP_PATH . '/models/AgentApprovalModel.php';

        $userIds = (new UserModel())->newIds();
        $companyIds = (new CompanyModel())->newIds();
        $postIds = array_map(fn($id) => 'p' . $id, (new PostModel())->newIds());
        $reportIds = array_map(fn($id) => 'r' . $id, (new ReportModel())->pendingIds());
        $agentIds = (new AgentApprovalModel())->pendingIds();
        $jobIds = (new JobModel())->pendingIds();

        $this->json([
            'ok' => true,
            'ids' => [
                'users' => array_values(array_map('strval', array_merge($userIds, $companyIds))),
                'content' => array_values(array_map('strval', $postIds)),
                'reports' => array_values(array_map('strval', $reportIds)),
                'agents' => array_values(array_map('strval', $agentIds)),
                'jobs' => array_values(array_map('strval', $jobIds)),
            ],
        ]);
    }
}
