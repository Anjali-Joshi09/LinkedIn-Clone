<?php /* app/views/companies/index.php */ $pageTitle = 'Companies'; ?>
<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-building-community" style="color:#057642"></i> All Companies</span>
    <form method="GET" action="<?= APP_URL ?>/" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="page" value="companies">
      <select class="filter-input" name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <?php foreach(['pending','verified','blocked','rejected'] as $s): ?>
          <option <?= ($_GET['status']??'')===$s?'selected':'' ?>><?=$s?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr><th>#</th><th>Company</th><th>Email</th><th>Registered</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if(empty($companies)): ?>
          <tr><td colspan="6"><div class="empty-state"><i class="ti ti-building"></i><p>No companies yet.</p></div></td></tr>
        <?php else: foreach($companies as $i=>$c): ?>
        <tr style="cursor:pointer" onclick="window.location='<?= APP_URL ?>/companies?action=edit&id=<?= $c['id'] ?>'" title="Click to edit company profile">
          <td style="color:#aaa;font-size:12px"><?= $i+1 ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:9px">
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
            </div>
          </td>
          <td><?= htmlspecialchars($c['email']??'') ?></td>
          <td><?= date('M d, Y', strtotime($c['created_at']??'now')) ?></td>
          <td>
            <?php $sc=['pending'=>'yellow','verified'=>'green','blocked'=>'red','rejected'=>'gray']; ?>
            <span class="badge badge-<?= $sc[strtolower($c['status']??'pending')]??'blue' ?>"><?= ucfirst($c['status']??'pending') ?></span>
          </td>
          <td>
            <div class="act-btns" onclick="event.stopPropagation()">
              <a href="<?= APP_URL ?>/companies?action=verify&id=<?= $c['id'] ?>" class="act-btn approve" title="Verify"
                onclick="return confirm('Verify this company?')"><i class="ti ti-circle-check"></i></a>
              <a href="<?= APP_URL ?>/companies?action=block&id=<?= $c['id'] ?>" class="act-btn block" title="Block"
                onclick="return confirm('Block this company?')"><i class="ti ti-ban"></i></a>
              <a href="<?= APP_URL ?>/companies?action=delete&id=<?= $c['id'] ?>" class="act-btn del" title="Delete"
                onclick="return confirm('Permanently delete this company?')"><i class="ti ti-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>