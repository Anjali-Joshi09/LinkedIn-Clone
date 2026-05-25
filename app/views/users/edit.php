<?php /* app/views/users/edit.php */
$pageTitle = 'Edit User — ' . htmlspecialchars($user['name'] ?? '');
$sc = ['active'=>'green','verified'=>'green','approved'=>'green','blocked'=>'red','pending'=>'yellow','suspended'=>'red'];
$st = strtolower($user['status'] ?? 'pending');
?>

<!-- Back -->
<div style="margin-bottom:20px">
  <a href="<?= APP_URL ?>/users?tab=users" class="btn btn-outline btn-sm">
    <i class="ti ti-arrow-left"></i> Back to Users
  </a>
</div>

<!-- Flash -->
<?php if(!empty($flash)): ?>
<div style="margin-bottom:18px;padding:12px 16px;border-radius:8px;font-size:13px;font-weight:500;
  background:<?= $flash['type']==='success'?'#f0faf4':'#fff5f5' ?>;
  color:<?= $flash['type']==='success'?'#057642':'#cc1016' ?>;
  border:1px solid <?= $flash['type']==='success'?'#b7dfca':'#f5c6c6' ?>">
  <i class="ti ti-<?= $flash['type']==='success'?'circle-check':'alert-circle' ?>"></i>
  <?= htmlspecialchars($flash['message']) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= APP_URL ?>/users?action=update" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

<!-- ── Cover + Avatar Hero Card ── -->
<div class="panel" style="padding:0;overflow:hidden;margin-bottom:20px">

  <!-- Cover banner -->
  <div id="cover-preview" style="
    height:180px;
    background:<?= !empty($user['cover'])
      ? 'url(\''.APP_URL.'/'.htmlspecialchars($user['cover']).'\') center/cover no-repeat'
      : 'linear-gradient(135deg,#0a66c2,#004182)' ?>;
    position:relative;">
    <label for="cover-input" style="
      position:absolute;bottom:12px;right:14px;
      background:rgba(0,0,0,0.55);color:#fff;
      padding:7px 14px;border-radius:20px;font-size:12px;font-weight:600;
      cursor:pointer;display:flex;align-items:center;gap:6px;
      backdrop-filter:blur(4px);transition:background .2s"
      onmouseover="this.style.background='rgba(0,0,0,0.75)'"
      onmouseout="this.style.background='rgba(0,0,0,0.55)'">
      <i class="ti ti-camera"></i> Change Cover
    </label>
    <input type="file" id="cover-input" name="cover" accept="image/*" style="display:none"
      onchange="previewCover(this)">
  </div>

  <!-- Avatar + name row -->
  <div style="padding:0 24px 20px;position:relative">
    <!-- Avatar -->
    <div style="position:relative;display:inline-block;margin-top:-44px;margin-bottom:10px">
      <div id="avatar-wrap" style="
        width:88px;height:88px;border-radius:50%;
        border:4px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,0.15);
        overflow:hidden;background:#0a66c2;
        display:flex;align-items:center;justify-content:center">
        <?php if(!empty($user['avatar'])): ?>
          <img src="<?= APP_URL ?>/<?= htmlspecialchars($user['avatar']) ?>"
            alt="" style="width:100%;height:100%;object-fit:cover">
        <?php else: ?>
          <span style="font-size:28px;font-weight:700;color:#fff">
            <?= strtoupper(substr($user['name']??'U',0,2)) ?>
          </span>
        <?php endif; ?>
      </div>
      <label for="avatar-input" style="
        position:absolute;bottom:-2px;right:-2px;
        width:26px;height:26px;border-radius:50%;
        background:#0a66c2;color:#fff;
        display:flex;align-items:center;justify-content:center;
        cursor:pointer;font-size:13px;
        border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.2)"
        title="Change profile photo">
        <i class="ti ti-camera"></i>
      </label>
      <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none"
        onchange="previewAvatar(this)">
    </div>

    <div style="display:inline-block;vertical-align:bottom;margin-left:14px;padding-bottom:4px">
      <div style="font-size:18px;font-weight:700;color:#191919"><?= htmlspecialchars($user['name']??'') ?></div>
      <div style="font-size:12px;color:#888;margin-top:2px"><?= htmlspecialchars($user['email']??'') ?></div>
      <div style="margin-top:6px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">
        <span class="badge badge-<?= $sc[$st]??'blue' ?>"><?= ucfirst($st) ?></span>
        <span class="badge badge-blue"><?= ucfirst($user['role']??'user') ?></span>
        <?php if(!empty($user['email_verified']) && $user['email_verified']): ?>
          <span class="badge badge-green"><i class="ti ti-circle-check"></i> Verified</span>
        <?php endif; ?>
        <span style="font-size:11px;color:#aaa">ID #<?= (int)$user['id'] ?></span>
      </div>
    </div>
  </div>
</div>

<!-- ── Form Fields ── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

  <!-- LEFT -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px">
        <span class="panel-title"><i class="ti ti-user" style="color:#0a66c2"></i> Basic Information</span>
      </div>

      <div class="form-group">
        <label class="form-label">Full Name <span style="color:#cc1016">*</span></label>
        <input type="text" name="name" class="form-control" required
          value="<?= htmlspecialchars($user['name']??'') ?>" placeholder="Full name">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address <span style="color:#cc1016">*</span></label>
        <input type="email" name="email" class="form-control" required
          value="<?= htmlspecialchars($user['email']??'') ?>" placeholder="email@example.com">
      </div>
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
          value="<?= htmlspecialchars($user['phone']??'') ?>" placeholder="+91 XXXXX XXXXX">
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <?php foreach(['active','blocked','pending','suspended'] as $s): ?>
            <option value="<?= $s ?>" <?= ($user['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Headline</label>
        <input type="text" name="headline" class="form-control"
          value="<?= htmlspecialchars($user['headline']??'') ?>" placeholder="e.g. Software Engineer at XYZ">
      </div>
      <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
          value="<?= htmlspecialchars($user['location']??'') ?>" placeholder="City, Country">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Website</label>
        <input type="url" name="website" class="form-control"
          value="<?= htmlspecialchars($user['website']??'') ?>" placeholder="https://example.com">
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div style="display:flex;flex-direction:column;gap:20px">
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px">
        <span class="panel-title"><i class="ti ti-file-text" style="color:#0a66c2"></i> About</span>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Bio / About</label>
        <textarea name="bio" class="form-control" rows="6"
          placeholder="Short bio..."><?= htmlspecialchars($user['bio']??'') ?></textarea>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px">
        <span class="panel-title"><i class="ti ti-lock" style="color:#0a66c2"></i> Password Security</span>
      </div>
      <p style="font-size:12px;color:#777;margin:0 0 14px">Admins cannot view or manually set passwords. Send a secure reset link so the user can create a new password.</p>
      <a href="<?= APP_URL ?>/users?action=send-reset&id=<?= (int)$user['id'] ?>"
         class="btn btn-outline"
         onclick="return confirm('Send a secure password reset link to this user?')">
        <i class="ti ti-mail"></i> Send Reset Link
      </a>
    </div>
  </div>

</div>

<!-- Meta info -->
<div style="background:#f8f9fa;border-radius:8px;padding:14px 16px;margin:4px 0 20px;font-size:12px;color:#888;display:flex;gap:24px;flex-wrap:wrap">
  <span><i class="ti ti-calendar"></i> Joined: <strong><?= date('M d, Y', strtotime($user['created_at']??'now')) ?></strong></span>
  <?php if(!empty($user['last_login'])): ?>
  <span><i class="ti ti-clock"></i> Last Login: <strong><?= date('M d, Y H:i', strtotime($user['last_login'])) ?></strong></span>
  <?php endif; ?>
  <span><i class="ti ti-id"></i> User ID: <strong>#<?= (int)$user['id'] ?></strong></span>
</div>

<!-- Actions -->
<div style="display:flex;gap:12px;justify-content:flex-end;padding-bottom:8px">
  <a href="<?= APP_URL ?>/users?tab=users" class="btn btn-outline">
    <i class="ti ti-x"></i> Cancel
  </a>
  <button type="submit" class="btn btn-primary">
    <i class="ti ti-device-floppy"></i> Save Changes
  </button>
</div>

</form>

<script>
function previewCover(input) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var el = document.getElementById('cover-preview');
    el.style.backgroundImage    = 'url(' + e.target.result + ')';
    el.style.backgroundSize     = 'cover';
    el.style.backgroundPosition = 'center';
  };
  reader.readAsDataURL(input.files[0]);
}

function previewAvatar(input) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('avatar-wrap').innerHTML =
      '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
