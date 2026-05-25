<?php
// app/views/layouts/admin.php
require_once APP_PATH . '/models/JobModel.php';
$db_ok = Database::getInstance() !== null;
$cur   = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> | LinkedIn Admin</title>

<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Tabler Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<!-- Admin CSS (custom, overrides Bootstrap where needed) -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<!-- TOAST CONTAINER -->
<div id="toast-wrap"></div>

<!-- IDLE LOGOUT MODAL -->
<div id="idle-modal">
  <div class="idle-box">
    <i class="ti ti-clock" style="font-size:40px;color:var(--orange);display:block;margin-bottom:12px"></i>
    <h3>Session Expiring</h3>
    <p>You have been <strong>inactive</strong>. Auto-logout in:</p>
    <div class="idle-countdown" id="idle-count">60</div>
    <p style="font-size:12px;color:#aaa;margin-bottom:16px">seconds</p>
    <button class="btn btn-primary" onclick="resetIdle()"><i class="ti ti-refresh"></i> Stay Logged In</button>
    <a href="<?= APP_URL ?>/logout" class="btn btn-outline"><i class="ti ti-logout"></i> Logout Now</a>
  </div>
</div>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="li-icon">in</div>
    <div>
      <div class="brand-name">LinkedIn</div>
      <small class="brand-sub">Admin Portal</small>
    </div>
  </div>

  <div class="nav-section">
    <div class="nav-label">Overview</div>
    <a class="nav-item <?= $cur==='dashboard'?'active':'' ?>" href="<?= APP_URL ?>/dashboard">
      <i class="ti ti-layout-dashboard"></i> Dashboard
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">Management</div>
    <a class="nav-item <?= $cur==='users'?'active':'' ?>" href="<?= APP_URL ?>/users">
      <i class="ti ti-users"></i> User Management
      <?php $nu=(new UserModel())->newCount() + (new CompanyModel())->newCount(); ?>
      <span class="nav-badge" data-admin-badge="users"<?= $nu>0 ? '' : ' style="display:none"' ?>><?= $nu ?></span>
    </a>
    <a class="nav-item <?= $cur==='jobs'?'active':'' ?>" href="<?= APP_URL ?>/jobs">
      <i class="ti ti-briefcase"></i> Jobs
      <?php $pj=(new JobModel())->pendingCount(); ?>
      <span class="nav-badge" data-admin-badge="jobs"<?= $pj>0 ? '' : ' style="display:none"' ?>><?= $pj ?></span>
    </a>
    <a class="nav-item <?= $cur==='content'?'active':'' ?>" href="<?= APP_URL ?>/content">
      <i class="ti ti-file-text"></i> Content / Posts
      <?php $np=(new PostModel())->newCount(); ?>
      <span class="nav-badge" data-admin-badge="content"<?= $np>0 ? '' : ' style="display:none"' ?>><?= $np ?></span>
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">Reports</div>
    <a class="nav-item <?= $cur==='reports'?'active':'' ?>" href="<?= APP_URL ?>/reports">
      <i class="ti ti-flag"></i> Reports
      <?php $pr=(new ReportModel())->pendingCount(); ?>
      <span class="nav-badge" data-admin-badge="reports"<?= $pr>0 ? '' : ' style="display:none"' ?>><?= $pr ?></span>
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">System</div>
    <a class="nav-item <?= $cur==='agents'?'active':'' ?>" href="<?= APP_URL ?>/agents">
      <i class="ti ti-user-check"></i> Company Approvals
      <?php $pc=(new AgentApprovalModel())->pendingCount(); ?>
      <span class="nav-badge" data-admin-badge="agents"<?= $pc>0 ? '' : ' style="display:none"' ?>><?= $pc ?></span>
    </a>
    <a class="nav-item <?= $cur==='notifications'?'active':'' ?>" href="<?= APP_URL ?>/notifications">
      <i class="ti ti-bell"></i> Notifications
    </a>
    <a class="nav-item <?= $cur==='settings'?'active':'' ?>" href="<?= APP_URL ?>/settings">
      <i class="ti ti-settings"></i> Settings
    </a>
  </div>

  <div class="sidebar-bottom">
    <div class="s-user">
      <div class="s-av"><?= strtoupper(substr($admin['name']??'A',0,2)) ?></div>
      <div>
        <div class="s-name"><?= htmlspecialchars($admin['name']??'Admin') ?></div>
        <div class="s-email"><?= htmlspecialchars($admin['email']??'') ?></div>
      </div>
      <a href="<?= APP_URL ?>/logout" class="logout-a" title="Logout"><i class="ti ti-logout"></i></a>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <div>
        <div class="topbar-title"><?= htmlspecialchars($pageTitle??'Dashboard') ?></div>
        <div class="breadcrumb">LinkedIn Admin &rsaquo; <?= htmlspecialchars($pageTitle??'Overview') ?></div>
      </div>
    </div>
    <div class="topbar-right">
      <a href="<?= APP_URL ?>/notifications" class="icon-btn" title="Notifications"><i class="ti ti-bell"></i></a>
      <div class="profile-wrap" style="position:relative">
        <div class="icon-btn" title="Admin Profile" onclick="toggleProfileMenu()" style="cursor:pointer">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff">
            <?= strtoupper(substr($admin['name']??'A',0,2)) ?>
          </div>
        </div>
        <div id="profile-menu" style="display:none;position:absolute;right:0;top:44px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 4px 20px rgba(0,0,0,0.12);min-width:220px;z-index:999">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border)">
            <div style="font-size:13px;font-weight:700;color:var(--text)"><?= htmlspecialchars($admin['name']??'Admin') ?></div>
            <div style="font-size:11px;color:var(--text3);margin-top:2px"><?= htmlspecialchars($admin['email']??'') ?></div>
            <div style="margin-top:6px"><span class="badge badge-blue" style="font-size:10px"><?= ucfirst($admin['role']??'admin') ?></span></div>
          </div>
          <div style="padding:6px 0">
            <button onclick="openAdminProfileModal();document.getElementById('profile-menu').style.display='none'" style="display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:var(--text2);text-decoration:none;background:none;border:none;width:100%;cursor:pointer" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background=''">
              <i class="ti ti-user-edit" style="font-size:16px"></i> Edit Profile
            </button>
            <a href="<?= APP_URL ?>/settings" style="display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:var(--text2);text-decoration:none" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background=''">
              <i class="ti ti-settings" style="font-size:16px"></i> Settings
            </a>
            <a href="<?= APP_URL ?>/logout" style="display:flex;align-items:center;gap:10px;padding:9px 16px;font-size:13px;color:var(--red);text-decoration:none" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background=''"
               class="admin-logout-link">
              <i class="ti ti-logout" style="font-size:16px"></i> Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <?php if(!$db_ok): ?>
    <div class="db-alert"><i class="ti ti-alert-triangle"></i><strong>Database not connected.</strong> &nbsp;Please update config/database.php with your credentials.</div>
    <?php endif; ?>

    <!-- Inner view -->
    <?php require VIEW_PATH.'/'.str_replace('.','/',$content_view).'.php'; ?>
  </div>
</div>

<!-- ── ADMIN PROFILE MODAL ──────────────────────────────────── -->
<div id="admin-profile-modal">
  <div class="ap-box">
    <div class="ap-header">
      <div class="ap-header-av" id="ap-av"><?= strtoupper(substr($admin['name']??'A',0,2)) ?></div>
      <div>
        <h3 style="font-size:16px;font-weight:700;color:var(--text);margin-bottom:3px">Edit Profile</h3>
        <p style="font-size:12px;color:var(--text3)">Update your admin account details</p>
      </div>
      <button onclick="closeAdminProfileModal()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:var(--text3);font-size:22px;padding:4px;border-radius:4px;line-height:1"><i class="ti ti-x"></i></button>
    </div>
    <div class="ap-body">
      <div id="ap-alert" style="display:none"></div>
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" id="ap-name" class="form-control" value="<?= htmlspecialchars($admin['name']??'') ?>" placeholder="Your full name">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" id="ap-email" class="form-control" value="<?= htmlspecialchars($admin['email']??'') ?>" placeholder="your@email.com">
      </div>
      <hr style="border:none;border-top:1px solid var(--border);margin:16px 0">
      <p style="font-size:12px;font-weight:600;color:var(--text3);margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">Change Password <span style="font-weight:400">(leave blank to keep current)</span></p>
      <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" id="ap-password" class="form-control" placeholder="Min. 8 characters">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Confirm New Password</label>
        <input type="password" id="ap-confirm" class="form-control" placeholder="Re-enter new password">
      </div>
    </div>
    <div class="ap-footer">
      <button class="btn btn-outline" onclick="closeAdminProfileModal()"><i class="ti ti-x"></i> Cancel</button>
      <button class="btn btn-primary" id="ap-save-btn" onclick="saveAdminProfile()"><i class="ti ti-device-floppy"></i> Save Changes</button>
    </div>
  </div>
</div>

<!-- ── DETAIL MODAL ─────────────────────────────────────────── -->
<div id="detail-modal">
  <div class="detail-box">
    <div class="detail-header">
      <div class="detail-header-icon" id="dm-icon-box">U</div>
      <div class="detail-header-info">
        <div class="detail-header-name" id="dm-name">Loading...</div>
        <div class="detail-header-sub" id="dm-sub"></div>
      </div>
      <button class="detail-close" onclick="closeDetailModal()"><i class="ti ti-x"></i></button>
    </div>
    <div class="detail-body" id="dm-body"></div>
    <div class="detail-footer">
      <span class="detail-id-chip" id="dm-id-chip"></span>
      <button class="btn btn-outline btn-sm" onclick="closeDetailModal()"><i class="ti ti-x"></i> Close</button>
    </div>
  </div>
</div>

<!-- Pass APP_URL to JS -->
<script>
window.APP_URL = '<?= APP_URL ?>';
<?php
$_u_ids = (new UserModel())->newIds();
$_c_ids = (new CompanyModel())->newIds();
$_p_ids = (new PostModel())->newIds();
$_r_ids = array_map(fn($id) => 'r'.$id, (new ReportModel())->pendingIds());
$_a_ids = (new AgentApprovalModel())->pendingIds();
$_j_ids = (new JobModel())->pendingIds();
?>
window.BADGE_IDS = {
  users:   <?= json_encode(array_map('strval', array_merge($_u_ids, $_c_ids))) ?>,
  content: <?= json_encode(array_map(fn($id) => 'p'.$id, $_p_ids)) ?>,
  reports: <?= json_encode(array_map('strval', $_r_ids)) ?>,
  agents:  <?= json_encode(array_map('strval', $_a_ids)) ?>,
  jobs:    <?= json_encode(array_map('strval', $_j_ids)) ?>,
};
</script>

<!-- Flash message bootstrap for toast -->
<?php if(!empty($flash)): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  showToast(<?= json_encode($flash['message']) ?>, <?= json_encode($flash['type']==='success'?'success':'error') ?>);
});
</script>
<?php endif; ?>

<!-- Bootstrap 5 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Admin JS (all interactions, modals, idle logout) -->
<script src="<?= APP_URL ?>/assets/js/admin.js"></script>
<script>
// Modern logout confirmation for admin
document.querySelectorAll('.admin-logout-link, a[href*="page=logout"]').forEach(function(link) {
  if (link.dataset.logoutBound) return;
  link.dataset.logoutBound = '1';
  link.addEventListener('click', function(e) {
    e.preventDefault();
    var href = this.href;
    Swal.fire({
      title: 'Log Out',
      html: '<p style="color:#555;font-size:.95rem;margin:0;">You will need to sign in again to continue.</p>',
      icon: 'warning',
      iconColor: '#e7a33e',
      showCancelButton: true,
      confirmButtonText: 'Log Out',
      cancelButtonText: 'Stay',
      confirmButtonColor: '#cc1016',
      cancelButtonColor: '#0a66c2',
      reverseButtons: true,
      focusCancel: true,
      borderRadius: '16px'
    }).then(function(r) {
      if (r.isConfirmed) window.location.href = href;
    });
  });
});
</script>
</body>
</html>
