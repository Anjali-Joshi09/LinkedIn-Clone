<?php
// app/models/DashboardModel.php

require_once APP_PATH . '/core/Model.php';

class DashboardModel extends Model {

    public function getStats(): array {
        return [
            'total_users'        => $this->scalar("SELECT COUNT(*) FROM users"),
            'total_companies'    => $this->scalar("SELECT COUNT(*) FROM companies"),
            'total_posts'        => $this->scalar("SELECT COUNT(*) FROM posts"),
            'total_jobs'         => $this->scalar("SELECT COUNT(*) FROM jobs"),
            'total_applications' => $this->scalar("SELECT COUNT(*) FROM applications"),
            'pending_reports'    => $this->scalar("SELECT COUNT(*) FROM reports WHERE status='pending'"),
            'open_tickets'       => $this->scalar("SELECT COUNT(*) FROM support_tickets WHERE status='open'"),
        ];
    }

    public function getChartData(): array {
        $labels = [];
        $users  = [];
        $apps   = [];

        for ($i = 5; $i >= 0; $i--) {
            $month    = date('M', strtotime("-{$i} months"));
            $ym       = date('Y-m', strtotime("-{$i} months"));
            $labels[] = $month;
            $users[]  = (int) $this->scalar(
                "SELECT COUNT(*) FROM users WHERE DATE_FORMAT(created_at,'%Y-%m') = ?", [$ym]
            );
            $apps[]   = (int) $this->scalar(
                "SELECT COUNT(*) FROM applications WHERE DATE_FORMAT(created_at,'%Y-%m') = ?", [$ym]
            );
        }

        return compact('labels', 'users', 'apps');
    }

    public function getRecentActivity(int $limit = 10): array {
        return $this->query(
            "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT ?", [$limit]
        );
    }
}
