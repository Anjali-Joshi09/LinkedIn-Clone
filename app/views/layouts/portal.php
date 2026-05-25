<?php
require_once APP_PATH . '/helpers/portal.php';
$cur = $_GET['page'] ?? 'home';
$isCompany = ($currentUser['role'] ?? '') === 'company';

// Load company for recruiter nav logo
$_navCompany = null;
if ($isCompany && !empty($_SESSION['user_id'])) {
    require_once APP_PATH . '/models/PortalModel.php';
    $_pm = new PortalModel();
    $_navCompany = $_pm->companyByUser((int)$_SESSION['user_id']);
}

// Badge counts
$_uid = (int)($_SESSION['user_id'] ?? 0);
if ($_uid) {
    require_once APP_PATH . '/core/Database.php';
    $_db = Database::getInstance();
    // Unread messages count
    $_stmt = $_db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id=? AND seen_at IS NULL");
    $_stmt->execute([$_uid]);
    $navUnreadMessages = (int)$_stmt->fetchColumn();
    // Unread notifications count
    $_stmt2 = $_db->prepare("SELECT COUNT(*) FROM user_notifications WHERE user_id=? AND read_at IS NULL");
    $_stmt2->execute([$_uid]);
    $navUnreadAlerts = (int)$_stmt2->fetchColumn();
    // Pending connection requests (user only) OR new followers (company)
    $navPendingConnections = 0;
    if (($currentUser['role'] ?? '') === 'company') {
        require_once APP_PATH . '/models/PortalModel.php';
        if (!isset($_pm)) { $_pm = new PortalModel(); }
        $navPendingConnections = $_pm->newFollowersCount($_uid);
    } else {
        $_stmt3 = $_db->prepare("SELECT COUNT(*) FROM connections WHERE receiver_id=? AND status='pending'");
        $_stmt3->execute([$_uid]);
        $navPendingConnections = (int)$_stmt3->fetchColumn();
    }
    // New jobs since user's last visit to Jobs board (user role only)
    $navNewJobs = 0;
    if (($currentUser['role'] ?? '') === 'user') {
        $_stmt4 = $_db->prepare(
            "SELECT COUNT(*) FROM jobs j
             WHERE j.status = 'approved'
             AND j.created_at > COALESCE(
                 (SELECT seen_at FROM user_job_last_seen WHERE user_id = ?),
                 DATE_SUB(NOW(), INTERVAL 7 DAY)
             )"
        );
        $_stmt4->execute([$_uid]);
        $navNewJobs = (int)$_stmt4->fetchColumn();
    }
    // New applications badge (company role only)
    $navNewApplications = 0;
    if (($currentUser['role'] ?? '') === 'company') {
        require_once APP_PATH . '/models/PortalModel.php';
        if (!isset($_pm)) { $_pm = new PortalModel(); }
        // Ensure tracking table exists
        $_db->exec("CREATE TABLE IF NOT EXISTS company_applicants_last_seen (
            company_id INT NOT NULL PRIMARY KEY,
            seen_at DATETIME NOT NULL
        )");
        $_navComp = $_navCompany ?? $_pm->companyByUser($_uid);
        if (!empty($_navComp['id'])) {
            $navNewApplications = $_pm->newApplicationsCount((int)$_navComp['id']);
        }
    }
} else {
    $navUnreadMessages = 0; $navUnreadAlerts = 0; $navPendingConnections = 0; $navNewJobs = 0; $navNewApplications = 0;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'LinkedIn') ?> | LinkedIn Clone</title>
  <script>
  window.APP_URL = '<?= APP_URL ?>';
  window.CSRF_TOKEN = '<?= e($csrf ?? '') ?>';
  window.CURRENT_USER_ID = <?= (int)($_SESSION['user_id'] ?? 0) ?>;
  window.CURRENT_USER_AVATAR = '<?= e($currentUser['avatar'] ?? '') ?>';
  window.CURRENT_USER_NAME = '<?= e($currentUser['name'] ?? '') ?>';
  window.CURRENT_USER_ROLE = '<?= e($currentUser['role'] ?? 'user') ?>';
  </script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/portal.css">
</head>
<body>
<nav class="li-topbar">
  <div class="container-fluid li-nav-inner">
    <a class="li-brand" href="<?= APP_URL ?>/<?= $isCompany ? 'recruiter-dashboard' : 'home' ?>">in</a>
    <div class="li-search">
      <i class="bi bi-search"></i>
      <input id="globalSearch" type="search" placeholder="Search" autocomplete="off">
      <div id="searchResults" class="search-results"></div>
    </div>
    <div class="li-nav-links">
      <?php if($isCompany): ?>
        <a class="<?= $cur==='recruiter-dashboard'?'active':'' ?>" href="<?= APP_URL ?>/recruiter-dashboard"><i class="bi bi-house-door-fill"></i><span>Home</span></a>
        <a class="<?= $cur==='network'?'active':'' ?>" href="<?= APP_URL ?>/network" style="position:relative;" onclick="clearNetworkBadge()"><i class="bi bi-people-fill"></i><span>Network</span><?php if($navPendingConnections>0): ?><span class="nav-badge" id="networkBadge"><?= $navPendingConnections ?></span><?php endif; ?></a>
        <a class="<?= $cur==='recruiter-jobs'?'active':'' ?>" href="<?= APP_URL ?>/recruiter-jobs"><i class="bi bi-briefcase-fill"></i><span>Post Jobs</span></a>
        <a class="<?= $cur==='applicants'?'active':'' ?>" href="<?= APP_URL ?>/applicants" style="position:relative;" onclick="clearApplicantsBadge()"><i class="bi bi-kanban-fill"></i><span>Applicants</span><?php if($navNewApplications>0): ?><span class="nav-badge" id="applicantsBadge"><?= $navNewApplications ?></span><?php endif; ?></a>
        <a class="<?= in_array($cur,['profile','company-profile'])?'active':'' ?>" href="<?= APP_URL ?>/company-profile">
          <?php if(!empty($_navCompany['logo'])): ?>
            <img src="<?= APP_URL.'/'.e($_navCompany['logo']) ?>" alt="Me" style="width:24px;height:24px;object-fit:cover;border-radius:50%;vertical-align:middle;">
          <?php else: ?>
            <i class="bi bi-building"></i>
          <?php endif; ?>
          <span>Me</span>
        </a>
      <?php else: ?>
        <a class="<?= $cur==='home'?'active':'' ?>" href="<?= APP_URL ?>/home"><i class="bi bi-house-door-fill"></i><span>Home</span></a>
        <a class="<?= $cur==='network'?'active':'' ?>" href="<?= APP_URL ?>/network" style="position:relative;" onclick="clearNetworkBadge()"><i class="bi bi-people-fill"></i><span>Network</span><?php if($navPendingConnections>0): ?><span class="nav-badge" id="networkBadge"><?= $navPendingConnections ?></span><?php endif; ?></a>
        <a class="<?= $cur==='jobs-board'?'active':'' ?>" href="<?= APP_URL ?>/jobs-board" style="position:relative;" id="jobsNavLink"><i class="bi bi-briefcase-fill"></i><span>Jobs</span><?php if($navNewJobs>0): ?><span class="nav-badge" id="jobsNavBadge"><?= $navNewJobs ?></span><?php endif; ?></a>
        <a class="<?= $cur==='profile'?'active':'' ?>" href="<?= APP_URL ?>/profile">
            <?php if(!empty($currentUser['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($currentUser['avatar']) ?>" alt="Me" style="width:24px;height:24px;object-fit:cover;border-radius:50%;vertical-align:middle;">
            <?php else: ?>
              <i class="bi bi-person-circle"></i>
            <?php endif; ?>
            <span>Me</span>
          </a>
      <?php endif; ?>
      <a class="<?= $cur==='messages'?'active':'' ?>" href="<?= APP_URL ?>/messages" style="position:relative;"><i class="bi bi-chat-dots-fill"></i><span>Messaging</span><?php if($navUnreadMessages>0): ?><span class="nav-badge" id="msgBadge"><?= $navUnreadMessages ?></span><?php endif; ?></a>
      <a class="<?= $cur==='portal-notifications'?'active':'' ?>" href="<?= APP_URL ?>/portal-notifications" id="alertNavLink" style="position:relative;" onclick="clearAlertBadge()"><i class="bi bi-bell-fill"></i><span>Alerts</span><?php if($navUnreadAlerts>0 && $cur!=='portal-notifications'): ?><span class="nav-badge" id="alertBadge"><?= $navUnreadAlerts ?></span><?php endif; ?></a>
      <a href="<?= APP_URL ?>/logout-user"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
    </div>
  </div>
</nav>
<main class="li-page">
  <?php if(!empty($flash)): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var wrap = document.getElementById('toastWrap') || document.body;
      var el = document.createElement('div');
      el.className = 'li-toast';
      el.textContent = <?= json_encode($flash['message']) ?>;
      <?php if($flash['type'] !== 'success'): ?>
      el.style.background = '#cc1016';
      <?php endif; ?>
      wrap.appendChild(el);
      setTimeout(function() { el.remove(); }, 5000);
    });
    </script>
  <?php endif; ?>
  <?php require VIEW_PATH.'/'.str_replace('.','/',$content_view).'.php'; ?>
</main>
<div class="toast-wrap" id="toastWrap"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
<script src="<?= APP_URL ?>/assets/js/portal.js"></script>
<script>
function clearNetworkBadge() {
  var b = document.getElementById('networkBadge');
  if (b) b.remove();
  fetch('<?= APP_URL ?>/ajax?action=mark-network-seen', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':window.CSRF_TOKEN},
    body: new URLSearchParams({})
  });
}
function clearAlertBadge() {
  var b = document.getElementById('alertBadge');
  if (b) b.remove();
}
function clearApplicantsBadge() {
  var b = document.getElementById('applicantsBadge');
  if (b) b.remove();
  fetch('<?= APP_URL ?>/ajax?action=mark-applicants-seen', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':window.CSRF_TOKEN},
    body: new URLSearchParams({})
  });
}
</script>
</body>
</html>