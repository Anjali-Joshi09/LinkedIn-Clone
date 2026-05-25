<?php /* app/views/settings/index.php */ $pageTitle = 'Site Settings'; ?>

<?php if(!empty($flash)): ?>
<div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?>" style="margin-bottom:18px">
  <i class="ti ti-<?= $flash['type']==='success'?'circle-check':'alert-circle' ?>"></i>
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<!-- ── ADMIN PROFILE ───────────────────────────────────────── -->
<div class="panel" style="margin-bottom:18px">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-user-circle" style="color:#0a66c2"></i> Admin Profile</span>
    <span style="font-size:12px;color:#888">Update your admin account name, email or password</span>
  </div>
  <div class="form-grid" style="margin-bottom:16px">
    <div class="form-group">
      <label class="form-label">Full Name</label>
      <input class="form-control" id="sp-name" value="<?= htmlspecialchars($admin['name'] ?? '') ?>" placeholder="Your full name">
    </div>
    <div class="form-group">
      <label class="form-label">Email Address</label>
      <input class="form-control" id="sp-email" type="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" placeholder="your@email.com">
    </div>
  </div>
  <hr style="border:none;border-top:1px solid var(--border);margin:4px 0 16px">
  <p style="font-size:12px;font-weight:600;color:var(--text3);margin-bottom:12px;text-transform:uppercase;letter-spacing:.05em">
    Change Password <span style="font-weight:400">(leave blank to keep current)</span>
  </p>
  <div class="form-grid" style="margin-bottom:0">
    <div class="form-group">
      <label class="form-label">New Password</label>
      <input class="form-control" id="sp-password" type="password" placeholder="Min. 8 characters">
    </div>
    <div class="form-group">
      <label class="form-label">Confirm New Password</label>
      <input class="form-control" id="sp-confirm" type="password" placeholder="Re-enter new password">
    </div>
  </div>
  <div id="sp-alert" style="display:none;margin-top:12px"></div>
  <div style="margin-top:16px">
    <button class="btn btn-primary" id="sp-save-btn" onclick="saveSettingsProfile()">
      <i class="ti ti-device-floppy"></i> Save Profile
    </button>
  </div>
</div>

<!-- ── SMTP / EMAIL SETTINGS ──────────────────────────────── -->
<form method="POST" action="<?= APP_URL ?>/settings?action=save">
  <div class="panel">
    <div class="panel-head">
      <span class="panel-title"><i class="ti ti-mail" style="color:#0a66c2"></i> SMTP / Email Settings</span>
      <span style="font-size:12px;color:#888">Used for sending system emails (approvals, welcome, password reset)</span>
    </div>
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">SMTP Host</label>
        <input class="form-control" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
      </div>
      <div class="form-group">
        <label class="form-label">SMTP Port</label>
        <input class="form-control" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" placeholder="587">
      </div>
      <div class="form-group">
        <label class="form-label">SMTP Username</label>
        <input class="form-control" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>" placeholder="your@gmail.com">
      </div>
      <div class="form-group">
        <label class="form-label">SMTP Password</label>
        <input class="form-control" type="password" name="smtp_pass" placeholder="Leave blank to keep current password">
      </div>
      <div class="form-group">
        <label class="form-label">From Email Address</label>
        <input class="form-control" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? '') ?>" placeholder="noreply@yoursite.com">
      </div>
      <div class="form-group">
        <label class="form-label">From Name</label>
        <input class="form-control" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? '') ?>" placeholder="LinkedIn Admin">
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:4px">
      <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Save SMTP Settings</button>
      <button type="button" class="btn btn-outline" id="smtp-test-btn" onclick="sendTestEmail()">
        <i class="ti ti-send"></i> Send Test Email
      </button>
      <span id="smtp-test-result" style="font-size:13px;display:none"></span>
    </div>
  </div>
</form>

<script>
// ── ADMIN PROFILE SAVE (reuses existing AdminProfileController) ──
function saveSettingsProfile() {
  var name     = document.getElementById('sp-name').value.trim();
  var email    = document.getElementById('sp-email').value.trim();
  var password = document.getElementById('sp-password').value;
  var confirm  = document.getElementById('sp-confirm').value;
  var alertEl  = document.getElementById('sp-alert');
  var btn      = document.getElementById('sp-save-btn');

  alertEl.style.display = 'none';

  if (!name || !email) {
    showSpAlert('error', 'Name and email are required.'); return;
  }
  if (password && password.length < 8) {
    showSpAlert('error', 'Password must be at least 8 characters.'); return;
  }
  if (password && password !== confirm) {
    showSpAlert('error', 'Passwords do not match.'); return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader"></i> Saving…';

  var fd = new FormData();
  fd.append('name', name);
  fd.append('email', email);
  if (password) fd.append('password', password);

  fetch('<?= APP_URL ?>/admin-profile?action=save', { method:'POST', body:fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        showSpAlert('success', 'Profile updated successfully.');
        document.getElementById('sp-password').value = '';
        document.getElementById('sp-confirm').value  = '';
      } else {
        showSpAlert('error', d.error || 'Could not save profile.');
      }
    })
    .catch(function(){ showSpAlert('error', 'Network error. Please try again.'); })
    .finally(function(){
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-device-floppy"></i> Save Profile';
    });
}

function showSpAlert(type, msg) {
  var el = document.getElementById('sp-alert');
  el.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger');
  el.innerHTML = '<i class="ti ti-' + (type === 'success' ? 'circle-check' : 'alert-circle') + '"></i> ' + msg;
  el.style.display = 'block';
}

// ── SMTP TEST EMAIL ──────────────────────────────────────────
function sendTestEmail() {
  var btn    = document.getElementById('smtp-test-btn');
  var result = document.getElementById('smtp-test-result');

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader"></i> Sending…';
  result.style.display = 'none';

  fetch('<?= APP_URL ?>/settings?action=test-smtp', { method:'POST' })
    .then(function(r){ return r.json(); })
    .then(function(d){
      result.style.display = 'inline';
      if (d.success) {
        result.style.color = '#16a34a';
        result.innerHTML   = '<i class="ti ti-circle-check"></i> Test email sent to <strong>' + (d.email || '') + '</strong>';
      } else {
        result.style.color = '#dc2626';
        result.innerHTML   = '<i class="ti ti-alert-circle"></i> ' + (d.error || 'Failed to send.');
      }
    })
    .catch(function(){
      result.style.display = 'inline';
      result.style.color   = '#dc2626';
      result.innerHTML     = '<i class="ti ti-alert-circle"></i> Network error.';
    })
    .finally(function(){
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-send"></i> Send Test Email';
    });
}
</script>