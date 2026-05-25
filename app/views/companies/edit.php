<?php /* app/views/companies/edit.php */
$pageTitle = 'Edit Company — ' . htmlspecialchars($company['name'] ?? '');
$sc = ['pending'=>'yellow','verified'=>'green','blocked'=>'red','rejected'=>'gray'];
$st = strtolower($company['status'] ?? 'pending');
?>

<!-- Back button -->
<div style="margin-bottom:20px">
  <a href="<?= APP_URL ?>/users?tab=companies" class="btn btn-outline btn-sm">
    <i class="ti ti-arrow-left"></i> Back to Companies
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

<form method="POST" action="<?= APP_URL ?>/companies?action=update" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= (int)$company['id'] ?>">

<!-- Banner + Logo Hero -->
<div class="panel" style="padding:0;overflow:hidden;margin-bottom:20px">

  <!-- Banner preview -->
  <div id="banner-preview" style="
    height:180px;
    background:<?= !empty($company['banner']) ? 'url(\''.APP_URL.'/'.htmlspecialchars($company['banner']).'\') center/cover no-repeat' : 'linear-gradient(135deg,#0a66c2,#004182)' ?>;
    position:relative;">
    <!-- Banner upload overlay -->
    <label for="banner-input" style="
      position:absolute;bottom:12px;right:14px;
      background:rgba(0,0,0,0.55);color:#fff;
      padding:7px 14px;border-radius:20px;font-size:12px;font-weight:600;
      cursor:pointer;display:flex;align-items:center;gap:6px;
      backdrop-filter:blur(4px);transition:background .2s"
      onmouseover="this.style.background='rgba(0,0,0,0.75)'"
      onmouseout="this.style.background='rgba(0,0,0,0.55)'">
      <i class="ti ti-camera"></i> Change Cover
    </label>
    <input type="file" id="banner-input" name="banner" accept="image/*" style="display:none"
      onchange="previewImage(this,'banner-preview','banner-bg')">
  </div>

  <!-- Logo + Info row -->
  <div style="padding:0 24px 20px;position:relative">
    <!-- Logo -->
    <div style="position:relative;display:inline-block;margin-top:-44px;margin-bottom:10px">
      <div id="logo-wrap" style="
        width:88px;height:88px;border-radius:10px;
        border:4px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,0.15);
        overflow:hidden;background:#057642;
        display:flex;align-items:center;justify-content:center">
        <?php if(!empty($company['logo'])): ?>
          <img id="logo-img" src="<?= APP_URL ?>/<?= htmlspecialchars($company['logo']) ?>"
            alt="" style="width:100%;height:100%;object-fit:cover">
        <?php else: ?>
          <span id="logo-initials" style="font-size:28px;font-weight:700;color:#fff">
            <?= strtoupper(substr($company['name']??'C',0,2)) ?>
          </span>
        <?php endif; ?>
      </div>
      <label for="logo-input" style="
        position:absolute;bottom:-4px;right:-4px;
        width:26px;height:26px;border-radius:50%;
        background:#0a66c2;color:#fff;
        display:flex;align-items:center;justify-content:center;
        cursor:pointer;font-size:13px;
        border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.2)"
        title="Change logo">
        <i class="ti ti-camera"></i>
      </label>
      <input type="file" id="logo-input" name="logo" accept="image/*" style="display:none"
        onchange="previewLogo(this)">
    </div>

    <div style="display:inline-block;vertical-align:bottom;margin-left:14px;padding-bottom:4px">
      <div style="font-size:18px;font-weight:700;color:#191919"><?= htmlspecialchars($company['name']??'') ?></div>
      <div style="font-size:12px;color:#888;margin-top:2px"><?= htmlspecialchars($company['email']??'') ?></div>
      <div style="margin-top:6px">
        <span class="badge badge-<?= $sc[$st]??'blue' ?>"><?= ucfirst($st) ?></span>
        <span style="font-size:11px;color:#aaa;margin-left:8px">ID #<?= (int)$company['id'] ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Form Fields -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

  <!-- LEFT column -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px">
        <span class="panel-title"><i class="ti ti-building" style="color:#057642"></i> Basic Information</span>
      </div>

      <div class="form-group">
        <label class="form-label">Company Name <span style="color:#cc1016">*</span></label>
        <input type="text" name="name" class="form-control" required
          value="<?= htmlspecialchars($company['name']??'') ?>" placeholder="Company name">
      </div>
      <div class="form-group">
        <label class="form-label">Email Address <span style="color:#cc1016">*</span></label>
        <input type="email" name="email" class="form-control" required
          value="<?= htmlspecialchars($company['email']??'') ?>" placeholder="company@example.com">
      </div>
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control"
          value="<?= htmlspecialchars($company['phone']??'') ?>" placeholder="+91 XXXXX XXXXX">
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <?php foreach(['pending','verified','blocked','rejected'] as $s): ?>
            <option value="<?= $s ?>" <?= ($company['status']??'')===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Industry</label>
        <input type="text" name="industry" class="form-control"
          value="<?= htmlspecialchars($company['industry']??'') ?>" placeholder="e.g. Technology, Finance">
      </div>
      <div class="form-group">
        <label class="form-label">Company Size</label>
        <select name="company_size" class="form-control">
          <option value="">Select size</option>
          <?php foreach(['1-10','11-50','51-200','201-500','501-1000','1001-5000','5000+'] as $sz): ?>
            <option <?= ($company['company_size']??'')===$sz?'selected':'' ?>><?= $sz ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Founded Year</label>
        <input type="number" name="founded_year" class="form-control" min="1800" max="<?= date('Y') ?>"
          value="<?= htmlspecialchars($company['founded_year']??'') ?>" placeholder="e.g. 2010">
      </div>
    </div>
  </div>

  <!-- RIGHT column -->
  <div>
    <div class="panel">
      <div class="panel-head" style="padding-bottom:12px">
        <span class="panel-title"><i class="ti ti-map-pin" style="color:#057642"></i> Details & Links</span>
      </div>

      <div class="form-group">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
          value="<?= htmlspecialchars($company['location']??'') ?>" placeholder="City, Country">
      </div>
      <div class="form-group">
        <label class="form-label">Website</label>
        <input type="url" name="website" class="form-control"
          value="<?= htmlspecialchars($company['website']??'') ?>" placeholder="https://example.com">
      </div>
      <div class="form-group">
        <label class="form-label">LinkedIn URL</label>
        <input type="url" name="linkedin_url" class="form-control"
          value="<?= htmlspecialchars($company['linkedin_url']??'') ?>" placeholder="https://linkedin.com/company/...">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">About / Description</label>
        <textarea name="description" class="form-control" rows="7"
          placeholder="Company description..."><?= htmlspecialchars($company['description']??'') ?></textarea>
      </div>
    </div>
  </div>

</div>

<!-- Meta info -->
<div style="background:#f8f9fa;border-radius:8px;padding:14px 16px;margin:4px 0 20px;font-size:12px;color:#888;display:flex;gap:24px;flex-wrap:wrap">
  <span><i class="ti ti-calendar"></i> Registered: <strong><?= date('M d, Y', strtotime($company['created_at']??'now')) ?></strong></span>
  <span><i class="ti ti-id"></i> Company ID: <strong>#<?= (int)$company['id'] ?></strong></span>
  <?php if(!empty($company['jobs_count'])): ?>
  <span><i class="ti ti-briefcase"></i> Total Jobs: <strong><?= (int)$company['jobs_count'] ?></strong></span>
  <?php endif; ?>
</div>

<!-- Action buttons -->
<div style="display:flex;gap:12px;justify-content:flex-end;padding-bottom:8px">
  <a href="<?= APP_URL ?>/companies?action=send-reset&id=<?= (int)$company['id'] ?>"
     class="btn btn-outline"
     onclick="return confirm('Send a secure password reset link to this company account?')">
    <i class="ti ti-mail"></i> Send Reset Link
  </a>
  <a href="<?= APP_URL ?>/users?tab=companies" class="btn btn-outline">
    <i class="ti ti-x"></i> Cancel
  </a>
  <button type="submit" class="btn btn-primary">
    <i class="ti ti-device-floppy"></i> Save Changes
  </button>
</div>

</form>

<script>
// Preview banner on file select
function previewImage(input, wrapperId) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById(wrapperId).style.backgroundImage = 'url(' + e.target.result + ')';
    document.getElementById(wrapperId).style.backgroundSize  = 'cover';
    document.getElementById(wrapperId).style.backgroundPosition = 'center';
  };
  reader.readAsDataURL(input.files[0]);
}

// Preview logo on file select
function previewLogo(input) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var wrap = document.getElementById('logo-wrap');
    wrap.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
