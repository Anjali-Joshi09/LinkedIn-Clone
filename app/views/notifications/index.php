<?php /* app/views/notifications/index.php */ $pageTitle = 'Notifications'; ?>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-send" style="color:#0a66c2"></i> Send Notification / Email Broadcast</span>
  </div>

  <form method="POST" action="<?= APP_URL ?>/notifications?action=send">
    <!-- Quick-select buttons — clicking syncs the dropdown below -->
    <div class="notif-type-btns">
      <button type="button" class="type-btn sel" onclick="selType(this,'all_users')">All Users</button>
      <button type="button" class="type-btn"     onclick="selType(this,'all_companies')">Companies</button>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Recipient Group</label>
        <select class="form-control" name="recipient" id="recipient-select" onchange="syncButtons(this.value)">
          <option value="all_users">All Users</option>
          <option value="all_companies">All Companies</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Subject / Title</label>
        <input class="form-control" name="subject" placeholder="e.g. New Feature Announcement" required>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Message Body</label>
      <textarea class="form-control" name="message" placeholder="Write your notification message here..." rows="4" required></textarea>
    </div>

    <div>
      <button type="submit" name="action" value="send" class="btn btn-primary">
        <i class="ti ti-send"></i> Send Now
      </button>
    </div>
  </form>
</div>

<?php if (!empty($notifications)): ?>
<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-history" style="color:#0a66c2"></i> Notification History</span>
    <span style="font-size:12px;color:#888"><?= count($notifications) ?> records</span>
  </div>

  <?php foreach ($notifications as $n): ?>
  <div class="notif-list-item">
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
      <strong style="font-size:13px"><?= htmlspecialchars($n['subject'] ?? '') ?></strong>
      <span class="badge badge-<?= ['sent'=>'green','draft'=>'gray','failed'=>'red','scheduled'=>'yellow'][strtolower($n['status']??'draft')]??'blue' ?>">
        <?= htmlspecialchars(ucfirst($n['status'] ?? '')) ?>
      </span>
      <span style="font-size:12px;color:#888">
        &rarr; <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $n['recipient'] ?? ''))) ?>
      </span>
    </div>
    <div style="font-size:12px;color:#777;margin-bottom:3px"><?= htmlspecialchars(substr($n['message'] ?? '', 0, 120)) ?>...</div>
    <div style="font-size:11px;color:#aaa"><?= htmlspecialchars($n['created_at'] ?? '') ?></div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
// Quick-select button clicked → highlight it + sync dropdown
function selType(btn, value) {
  document.querySelectorAll('.type-btn').forEach(function(b){ b.classList.remove('sel'); });
  btn.classList.add('sel');
  document.getElementById('recipient-select').value = value;
}

// Dropdown changed → highlight matching button
function syncButtons(value) {
  var map = { 'all_users': 'All Users', 'all_companies': 'Companies' };
  document.querySelectorAll('.type-btn').forEach(function(b){
    b.classList.toggle('sel', b.textContent.trim() === (map[value] || ''));
  });
}
</script>
