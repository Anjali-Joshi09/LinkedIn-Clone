<?php /* app/views/users/index.php */ $pageTitle = 'User Management'; ?>
<?php
function uStatus(string $s): string {
    $m=['active'=>'green','verified'=>'green','approved'=>'green','blocked'=>'red','pending'=>'yellow','suspended'=>'red'];
    return "<span class='badge badge-".($m[strtolower($s)]??'blue')."'>".htmlspecialchars(ucfirst($s))."</span>";
}
?>

<!-- TAB NAVIGATION -->
<div style="display:flex;gap:0;margin-bottom:20px;border-bottom:2px solid #e0e0e0">
  <?php $tab = $_GET['tab'] ?? 'users'; ?>
  <a href="<?= APP_URL ?>/users?tab=users"
     style="padding:10px 24px;font-size:14px;font-weight:600;text-decoration:none;border-bottom:3px solid <?= $tab==='users'?'#0a66c2':'transparent' ?>;color:<?= $tab==='users'?'#0a66c2':'#888' ?>;margin-bottom:-2px">
    <i class="ti ti-users"></i> Users
  </a>
  <a href="<?= APP_URL ?>/users?tab=companies"
     style="padding:10px 24px;font-size:14px;font-weight:600;text-decoration:none;border-bottom:3px solid <?= $tab==='companies'?'#0a66c2':'transparent' ?>;color:<?= $tab==='companies'?'#0a66c2':'#888' ?>;margin-bottom:-2px">
    <i class="ti ti-building-community"></i> Companies
  </a>
</div>

<?php if($tab === 'users'): ?>
<!-- USERS SECTION -->
<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-users" style="color:#0a66c2"></i> All Users</span>
    <span style="font-size:12px;color:#888"><?= count($users) ?> users found</span>
  </div>
  <form method="GET" action="<?= APP_URL ?>/">
    <div class="filters" style="margin-bottom:16px">
      <input type="hidden" name="page" value="users">
      <input type="hidden" name="tab" value="users">
      <input class="filter-input" name="search" placeholder="🔍 Search name or email..."
        value="<?= htmlspecialchars($filters['search']??'') ?>" style="flex:1;min-width:200px"
        oninput="debounceSearch(this)">
      <select class="filter-input" name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <?php foreach(['active','blocked','pending','suspended'] as $s): ?>
          <option <?= ($filters['status']??'')===$s?'selected':'' ?>><?=$s?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-search"></i> Search</button>
      <?php if(!empty($filters['search'])||!empty($filters['status'])): ?>
      <a href="<?= APP_URL ?>/users?tab=users" class="btn btn-outline btn-sm"><i class="ti ti-x"></i> Clear</a>
      <?php endif; ?>
    </div>
  </form>
  <div class="tbl-wrap">
    <table>
      <thead><tr><th>#</th><th>User</th><th>Email</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if(empty($users)): ?>
          <tr><td colspan="6"><div class="empty-state"><i class="ti ti-users"></i><p>No users found.</p></div></td></tr>
        <?php else: foreach($users as $i=>$u): ?>
        <tr style="cursor:pointer" onclick="markSeenAndGo(this,'admin_seen_users','a[href*=\'users\'] .nav-badge','<?= APP_URL ?>/users?action=edit&id=<?= $u['id'] ?>')" title="Click to edit user profile" data-new-id="u<?= $u['id'] ?>">
          <td style="color:#aaa;font-size:12px"><?= $i+1 ?></td>
          <td><div style="display:flex;align-items:center;gap:9px">
            <?php if(!empty($u['avatar'])): ?>
              <img src="<?= APP_URL ?>/<?= htmlspecialchars($u['avatar']) ?>" alt=""
                style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid #e0e0e0;flex-shrink:0">
            <?php else: ?>
              <div class="av" style="background:#0a66c2;flex-shrink:0"><?= strtoupper(substr($u['name']??'U',0,2)) ?></div>
            <?php endif; ?>
            <div>
              <div style="font-weight:600;color:#191919"><?= htmlspecialchars($u['name']??'') ?></div>
              <div style="font-size:11px;color:#aaa">User</div>
            </div>
          </div></td>
          <td><?= htmlspecialchars($u['email']??'') ?></td>
          <td><?= date('M d, Y',strtotime($u['created_at']??'now')) ?></td>
          <td><?= uStatus($u['status']??'pending') ?></td>
          <td><div class="act-btns" onclick="event.stopPropagation()">
            <a href="<?= APP_URL ?>/users?action=block&id=<?= $u['id'] ?>" class="act-btn block" title="<?= ($u['status']??'')==='blocked'?'Unblock':'Block' ?>"
              onclick="return confirm('Block/Unblock this user?')"><i class="ti ti-ban"></i></a>
            <a href="<?= APP_URL ?>/users?action=delete&id=<?= $u['id'] ?>" class="act-btn del" title="Delete"
              onclick="return confirm('Permanently delete this user?')"><i class="ti ti-trash"></i></a>
          </div></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<!-- COMPANIES SECTION (shown as sub-tab) -->
<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-building-community" style="color:#057642"></i> All Companies</span>
    <div class="filters">
      <?php foreach([''=>'All','pending'=>'Pending','verified'=>'Verified','blocked'=>'Blocked'] as $v=>$l): ?>
      <a href="<?= APP_URL ?>/users?tab=companies&cstatus=<?=$v?>"
         class="btn btn-sm <?= ($_GET['cstatus']??'')===$v?'btn-primary':'btn-outline' ?>"><?=$l?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr><th>#</th><th>Company</th><th>Email</th><th>Registered</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if(empty($companies)): ?>
          <tr><td colspan="6"><div class="empty-state"><i class="ti ti-building"></i><p>No companies found.</p></div></td></tr>
        <?php else: foreach($companies as $i=>$c): ?>
        <tr style="cursor:pointer" onclick="markSeenAndGo(this,'admin_seen_users','a[href*=\'users\'] .nav-badge','<?= APP_URL ?>/companies?action=edit&id=<?= $c['id'] ?>')" title="Click to edit company profile" data-new-id="c<?= $c['id'] ?>">
          <td style="color:#aaa;font-size:12px"><?= $i+1 ?></td>
          <td><div style="display:flex;align-items:center;gap:9px">
            <?php if(!empty($c['logo'])): ?>
              <img src="<?= APP_URL ?>/<?= htmlspecialchars($c['logo']) ?>" alt=""
                style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e0e0e0;flex-shrink:0">
            <?php else: ?>
              <div class="av" style="background:#057642;border-radius:6px;flex-shrink:0"><?= strtoupper(substr($c['name']??'C',0,2)) ?></div>
            <?php endif; ?>
            <div>
              <div style="font-weight:600;color:#191919"><?= htmlspecialchars($c['name']??'') ?></div>
              <div style="font-size:11px;color:#aaa"><?= htmlspecialchars($c['industry']??'Company') ?></div>
            </div>
          </div></td>
          <td><?= htmlspecialchars($c['email']??'') ?></td>
          <td><?= date('M d, Y',strtotime($c['created_at']??'now')) ?></td>
          <td><span class="badge badge-<?=['pending'=>'yellow','verified'=>'green','blocked'=>'red','rejected'=>'gray'][strtolower($c['status']??'pending')]??'blue'?>"><?= ucfirst($c['status']??'pending') ?></span></td>
          <td><div class="act-btns" onclick="event.stopPropagation()">
            <a href="<?= APP_URL ?>/companies?action=verify&id=<?=$c['id']?>" class="act-btn approve" title="Verify" onclick="return confirm('Verify this company?')"><i class="ti ti-circle-check"></i></a>
            <a href="<?= APP_URL ?>/companies?action=block&id=<?=$c['id']?>" class="act-btn block" title="Block" onclick="return confirm('Block?')"><i class="ti ti-ban"></i></a>
            <a href="<?= APP_URL ?>/companies?action=delete&id=<?=$c['id']?>" class="act-btn del" title="Delete" onclick="return confirm('Delete?')"><i class="ti ti-trash"></i></a>
          </div></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>