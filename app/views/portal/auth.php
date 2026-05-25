<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LinkedIn Clone | <?= e(ucfirst(str_replace('-', ' ', $mode))) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/portal.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/captcha.css">
  <style>
    .role-tab-btn {
      flex: 1;
      padding: 10px 0;
      border: none;
      background: transparent;
      font-weight: 600;
      font-size: 14px;
      color: #666;
      border-bottom: 3px solid transparent;
      transition: all .2s;
      cursor: pointer;
    }
    .role-tab-btn.active { color: #0a66c2; border-bottom-color: #0a66c2; }
    .role-tabs { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 18px; }
    .role-form-section { display: none; }
    .role-form-section.active { display: block; }
    .pw-wrap { position: relative; margin-bottom: 6px; }
    .pw-wrap .form-control { padding-right: 52px; }
    .pw-toggle {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer; color: #888;
      font-size: 13px; font-weight: 600; padding: 4px 6px; transition: color .2s; font-family: inherit;
    }
    .pw-toggle:hover { color: #0a66c2; }
    .location-suggestions-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid #ddd;
      border-top: none;
      border-radius: 0 0 6px 6px;
      z-index: 999;
      max-height: 200px;
      overflow-y: auto;
      display: none;
      box-shadow: 0 4px 12px rgba(0,0,0,.12);
    }
    .location-suggestions-dropdown .suggestion-item {
      padding: 8px 12px;
      cursor: pointer;
      font-size: 14px;
      color: #333;
    }
    .location-suggestions-dropdown .suggestion-item:hover {
      background: #f0f7ff;
      color: #0a66c2;
    }
    /* ── Password strength ── */
    .pw-strength-bar {
      height: 5px; border-radius: 4px; background: #e0e0e0;
      margin: 6px 0 4px; overflow: hidden;
    }
    .pw-strength-fill { height: 100%; width: 0; border-radius: 4px; transition: width .3s, background .3s; }
    .pw-rules { display: flex; flex-wrap: wrap; gap: 5px 10px; margin-bottom: 8px; }
    .pw-rule { font-size: 11.5px; color: #e53935; transition: color .2s; }
    .pw-rule.pass { color: #2e7d32; }
    .pw-rule.pass::first-letter { content: "✓"; }
    /* ── Email error ── */
    input.invalid-field { border-color: #e53935 !important; }
    input.valid-field   { border-color: #2e7d32 !important; }
  </style>
</head>
<body class="auth-body">
<div class="auth-shell">
  <div class="auth-brand"><span>Linked</span><b>in</b></div>
  <div class="auth-card">
    <?php if(!empty($flash)): ?><div class="alert alert-<?= $flash['type']==='success'?'success':'danger' ?> py-2"><?= e($flash['message']) ?></div><?php endif; ?>

    <?php if($mode === 'signin'): ?>
      <h1>Sign in</h1><p>Stay updated on your professional world.</p>
      <form method="post" action="<?= APP_URL ?>/signin">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <label>Email</label><input class="form-control" type="email" name="email" required>
        <label>Password</label>
        <div class="pw-wrap">
          <input class="form-control" type="password" name="password" id="signin-password" required>
          <button type="button" class="pw-toggle" onclick="togglePortalPw('signin-password', this)">Show</button>
        </div>
        <div class="captcha-group">
          <label for="portal-signin-captcha">Security Code</label>
          <div class="captcha-row">
            <img class="captcha-image" src="<?= APP_URL ?>/captcha.php" alt="Security code" id="portal-signin-captcha-image">
            <button type="button" class="captcha-refresh" title="Refresh security code" onclick="refreshCaptcha('portal-signin-captcha-image')">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
            <input class="form-control captcha-input" type="text" id="portal-signin-captcha" name="captcha" maxlength="6" placeholder="Enter code" required autocomplete="off">
          </div>
        </div>
        <button class="btn btn-primary w-100 mt-3">Sign in</button>
      </form>
      <a class="auth-link" href="<?= APP_URL ?>/forgot-user-password">Forgot password?</a>
      <div class="auth-switch">New here? <a href="<?= APP_URL ?>/signup">Join now</a></div>

    <?php elseif($mode === 'signup'): ?>
      <h1>Join your professional community</h1>

      <!-- Role Tabs -->
      <div class="role-tabs">
        <button class="role-tab-btn active" onclick="switchRole('user', this)">
          <i class="bi bi-person-fill me-1"></i> Normal User
        </button>
        <button class="role-tab-btn" onclick="switchRole('company', this)">
          <i class="bi bi-briefcase-fill me-1"></i> Recruiter / Company
        </button>
      </div>

      <!-- USER SIGNUP FORM -->
      <div id="form-user" class="role-form-section active">
        <form method="post" action="<?= APP_URL ?>/signup">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="role" value="user">
          <label>Full Name <span class="text-danger">*</span></label>
          <input class="form-control" name="name" placeholder="e.g. Rahul Sharma" required>
          <label>Email <span class="text-danger">*</span></label>
          <input class="form-control" type="email" name="email" id="user-email" placeholder="you@email.com" required oninput="validateEmail(this,'user-email-err')">
          <small id="user-email-err" class="text-danger" style="display:none;"></small>
          <label>Password <span class="text-danger">*</span></label>
          <div class="pw-wrap">
            <input class="form-control" type="password" name="password" id="user-password" minlength="8" placeholder="Min 8 characters" required oninput="checkPwStrength('user-password','user-pw-strength','user-pw-rules')">
            <button type="button" class="pw-toggle" onclick="togglePortalPw('user-password', this)">Show</button>
          </div>
          <div id="user-pw-strength" class="pw-strength-bar" style="display:none;"><div class="pw-strength-fill"></div></div>
          <div id="user-pw-rules" class="pw-rules" style="display:none;">
            <span id="user-rule-len"  class="pw-rule">✗ Min 8 characters</span>
            <span id="user-rule-upper" class="pw-rule">✗ One uppercase letter</span>
            <span id="user-rule-num"  class="pw-rule">✗ One number</span>
            <span id="user-rule-sym"  class="pw-rule">✗ One special character</span>
          </div>
          <label>Headline</label>
          <input class="form-control" name="headline" placeholder="e.g. Software Engineer | Open to opportunities">
          <label>Location</label>
          <div class="position-relative">
            <input class="form-control" name="location" id="user-location" placeholder="e.g. Bengaluru, Remote" autocomplete="off">
            <div id="user-location-suggestions" class="location-suggestions-dropdown"></div>
          </div>
          <div class="captcha-group">
            <label for="portal-user-captcha">Security Code</label>
            <div class="captcha-row">
              <img class="captcha-image" src="<?= APP_URL ?>/captcha.php" alt="Security code" id="portal-user-captcha-image">
              <button type="button" class="captcha-refresh" title="Refresh security code" onclick="refreshCaptcha('portal-user-captcha-image')">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
              <input class="form-control captcha-input" type="text" id="portal-user-captcha" name="captcha" maxlength="6" placeholder="Enter code" required autocomplete="off">
            </div>
          </div>
          <button class="btn btn-primary w-100 mt-3">Agree &amp; Join as User</button>
        </form>
      </div>

      <!-- RECRUITER SIGNUP FORM -->
      <div id="form-company" class="role-form-section">
        <form method="post" action="<?= APP_URL ?>/signup" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="role" value="company">

          <p style="font-size:.82rem;color:#666;background:#f0f7ff;border-radius:8px;padding:10px 12px;margin-bottom:4px;">
            <i class="bi bi-info-circle me-1"></i> After signup, your account will be reviewed by admin. You will receive an email once approved.
          </p>

          <div style="font-weight:700;font-size:.8rem;color:#0a66c2;text-transform:uppercase;letter-spacing:.05em;margin:14px 0 6px;">
            Personal Details
          </div>
          <label>Your Full Name <span class="text-danger">*</span></label>
          <input class="form-control" name="name" placeholder="e.g. Priya Mehta" required>
          <label>Work Email <span class="text-danger">*</span></label>
          <input class="form-control" type="email" name="email" id="rec-email" placeholder="priya@techcorp.com" required oninput="validateEmail(this,'rec-email-err')">
          <small id="rec-email-err" class="text-danger" style="display:none;"></small>
          <label>Phone Number</label>
          <input class="form-control" type="tel" name="phone" placeholder="e.g. +91 9876543210">
          <label>Password <span class="text-danger">*</span></label>
          <div class="pw-wrap">
            <input class="form-control" type="password" name="password" id="rec-password" minlength="8" placeholder="Min 8 characters" required oninput="checkPwStrength('rec-password','rec-pw-strength','rec-pw-rules');checkConfirm()">
            <button type="button" class="pw-toggle" onclick="togglePortalPw('rec-password', this)">Show</button>
          </div>
          <div id="rec-pw-strength" class="pw-strength-bar" style="display:none;"><div class="pw-strength-fill"></div></div>
          <div id="rec-pw-rules" class="pw-rules" style="display:none;">
            <span id="rec-rule-len"   class="pw-rule">✗ Min 8 characters</span>
            <span id="rec-rule-upper" class="pw-rule">✗ One uppercase letter</span>
            <span id="rec-rule-num"   class="pw-rule">✗ One number</span>
            <span id="rec-rule-sym"   class="pw-rule">✗ One special character</span>
          </div>
          <label>Confirm Password <span class="text-danger">*</span></label>
          <div class="pw-wrap">
            <input class="form-control" type="password" name="confirm_password" id="rec-confirm-password" minlength="8" placeholder="Re-enter password" required oninput="checkConfirm()">
            <button type="button" class="pw-toggle" onclick="togglePortalPw('rec-confirm-password', this)">Show</button>
          </div>
          <small id="rec-confirm-err" class="text-danger" style="display:none;">Passwords do not match.</small>

          <div style="font-weight:700;font-size:.8rem;color:#0a66c2;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 6px;">
            Company Details
          </div>
          <label>Company Name <span class="text-danger">*</span></label>
          <input class="form-control" name="company_name" placeholder="e.g. TechCorp India Pvt. Ltd." required>
          <label>Company Website</label>
          <input class="form-control" type="url" name="website" placeholder="e.g. https://techcorp.in">
          <div class="row g-2 mt-1">
            <div class="col">
              <label>Industry</label>
              <select class="form-control" name="industry">
                <option value="">Select industry</option>
                <option>IT Services</option>
                <option>Software Product</option>
                <option>E-Commerce</option>
                <option>Banking & Finance</option>
                <option>Healthcare</option>
                <option>Education</option>
                <option>Manufacturing</option>
                <option>Consulting</option>
                <option>Media & Entertainment</option>
                <option>Retail</option>
                <option>Logistics</option>
                <option>Real Estate</option>
                <option>Other</option>
              </select>
            </div>
            <div class="col">
              <label>Company Size</label>
              <select class="form-control" name="company_size">
                <option value="">Select size</option>
                <option>1-10</option>
                <option>11-50</option>
                <option>51-200</option>
                <option>201-500</option>
                <option>501-1000</option>
                <option>1001-5000</option>
                <option>5000+</option>
              </select>
            </div>
          </div>
          <label>Location</label>
          <div class="position-relative">
            <input class="form-control" name="location" id="company-location" placeholder="e.g. Mumbai, Maharashtra" autocomplete="off">
            <div id="company-location-suggestions" class="location-suggestions-dropdown"></div>
          </div>
          <label>Company LinkedIn URL</label>
          <input class="form-control" type="url" name="linkedin_url" placeholder="e.g. https://linkedin.com/company/techcorp">
          <label>About Company</label>
          <textarea class="form-control" name="bio" rows="3" placeholder="Brief description of your company, culture, and what you do..."></textarea>

          <div style="font-weight:700;font-size:.8rem;color:#0a66c2;text-transform:uppercase;letter-spacing:.05em;margin:16px 0 6px;">
            Your Role at Company
          </div>
          <label>Your Job Title / Designation</label>
          <input class="form-control" name="headline" placeholder="e.g. HR Manager, Talent Acquisition Lead">

          <div class="captcha-group">
            <label for="portal-company-captcha">Security Code</label>
            <div class="captcha-row">
              <img class="captcha-image" src="<?= APP_URL ?>/captcha.php" alt="Security code" id="portal-company-captcha-image">
              <button type="button" class="captcha-refresh" title="Refresh security code" onclick="refreshCaptcha('portal-company-captcha-image')">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
              <input class="form-control captcha-input" type="text" id="portal-company-captcha" name="captcha" maxlength="6" placeholder="Enter code" required autocomplete="off">
            </div>
          </div>

          <button class="btn btn-success w-100 mt-4" id="recruiterSignupBtn">
            <i class="bi bi-send me-1"></i> Submit for Approval
          </button>
          <p style="font-size:.75rem;color:#888;text-align:center;margin-top:8px;">By clicking you agree to our Terms. Your account will be reviewed before activation.</p>
        </form>
      </div>

      <div class="auth-switch mt-3">Already on LinkedIn? <a href="<?= APP_URL ?>/signin">Sign in</a></div>

    <?php elseif($mode === 'signup_otp'): ?>
      <h1>Verify your email</h1>
      <p>Enter the 6-digit OTP sent to <?= e($pendingEmail ?? 'your email') ?>.</p>
      <form method="post" action="<?= APP_URL ?>/verify-signup-otp">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <label>Email OTP</label>
        <input class="form-control" type="text" name="otp" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required>
        <button class="btn btn-primary w-100 mt-3">Verify &amp; Create Account</button>
      </form>
      <form method="post" action="<?= APP_URL ?>/resend-signup-otp" class="mt-2">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <button class="btn btn-outline-primary w-100">Resend OTP</button>
      </form>
      <div class="auth-switch mt-3">Wrong email? <a href="<?= APP_URL ?>/signup">Start again</a></div>

    <?php elseif($mode === 'forgot'): ?>
      <h1>Reset password</h1><p>Enter your account email and we will send a reset link.</p>
      <form method="post" action="<?= APP_URL ?>/forgot-user-password">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <label>Email</label><input class="form-control" type="email" name="email" required>
        <button class="btn btn-primary w-100 mt-3">Send reset link</button>
      </form>

    <?php else: ?>
      <h1>Create new password</h1>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <label>Password</label><input class="form-control" type="password" name="password" minlength="8" required>
        <label>Confirm password</label><input class="form-control" type="password" name="confirm_password" minlength="8" required>
        <button class="btn btn-primary w-100 mt-3">Update password</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<script>
function switchRole(role, clickedBtn) {
  document.querySelectorAll('.role-form-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.role-tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('form-' + role).classList.add('active');
  clickedBtn.classList.add('active');
}

// Show/hide password toggle
function togglePortalPw(id, btn) {
  var inp = document.getElementById(id);
  if (!inp) return;
  if (inp.type === 'password') { inp.type = 'text'; btn.textContent = 'Hide'; }
  else { inp.type = 'password'; btn.textContent = 'Show'; }
}

function refreshCaptcha(imageId) {
  var img = document.getElementById(imageId);
  if (!img) return;
  img.src = img.src.split('?')[0] + '?refresh=' + Date.now();
}

// Location suggestions using OpenStreetMap Nominatim (free, no API key)
function setupLocationSuggestions(inputId, dropdownId) {
  var input = document.getElementById(inputId);
  var dropdown = document.getElementById(dropdownId);
  if (!input || !dropdown) return;

  var debounceTimer;
  input.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    var query = this.value.trim();
    if (query.length < 3) { dropdown.style.display = 'none'; return; }
    debounceTimer = setTimeout(function () {
      fetch('https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=6&q=' + encodeURIComponent(query), {
        headers: { 'Accept-Language': 'en' }
      })
      .then(function(r){ return r.json(); })
      .then(function(results) {
        dropdown.innerHTML = '';
        if (!results.length) { dropdown.style.display = 'none'; return; }
        results.forEach(function(place) {
          var parts = [];
          var a = place.address || {};
          if (a.city || a.town || a.village || a.municipality) parts.push(a.city || a.town || a.village || a.municipality);
          if (a.state) parts.push(a.state);
          if (a.country) parts.push(a.country);
          var label = parts.length ? parts.join(', ') : place.display_name;
          var item = document.createElement('div');
          item.className = 'suggestion-item';
          item.textContent = label;
          item.addEventListener('mousedown', function(e) {
            e.preventDefault();
            input.value = label;
            dropdown.style.display = 'none';
          });
          dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
      })
      .catch(function(){ dropdown.style.display = 'none'; });
    }, 350);
  });

  input.addEventListener('blur', function() {
    setTimeout(function(){ dropdown.style.display = 'none'; }, 200);
  });
  input.addEventListener('focus', function() {
    if (dropdown.children.length > 0) dropdown.style.display = 'block';
  });
}

document.addEventListener('DOMContentLoaded', function() {
  setupLocationSuggestions('user-location', 'user-location-suggestions');
  setupLocationSuggestions('company-location', 'company-location-suggestions');
});

// ── Email validation ────────────────────────────────────────
function validateEmail(input, errId) {
  var val = input.value.trim();
  var re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var err = document.getElementById(errId);
  if (!val) {
    input.classList.remove('invalid-field','valid-field');
    if (err) err.style.display = 'none';
    return;
  }
  if (!re.test(val)) {
    input.classList.add('invalid-field'); input.classList.remove('valid-field');
    if (err) { err.textContent = 'Please enter a valid email address (e.g. name@example.com)'; err.style.display = 'block'; }
  } else {
    input.classList.remove('invalid-field'); input.classList.add('valid-field');
    if (err) err.style.display = 'none';
  }
}

// ── Password strength checker ────────────────────────────────
function checkPwStrength(inputId, barId, rulesId) {
  var pw = document.getElementById(inputId).value;
  var bar = document.getElementById(barId);
  var rulesEl = document.getElementById(rulesId);
  if (!bar || !rulesEl) return;

  var baseId = rulesId.replace('-pw-rules','');
  var rLen   = document.getElementById(baseId + '-rule-len');
  var rUpper = document.getElementById(baseId + '-rule-upper');
  var rNum   = document.getElementById(baseId + '-rule-num');
  var rSym   = document.getElementById(baseId + '-rule-sym');

  if (!pw) { bar.style.display = 'none'; rulesEl.style.display = 'none'; return; }
  bar.style.display = 'block'; rulesEl.style.display = 'flex';

  var hasLen   = pw.length >= 8;
  var hasUpper = /[A-Z]/.test(pw);
  var hasNum   = /[0-9]/.test(pw);
  var hasSym   = /[^a-zA-Z0-9]/.test(pw);

  function setRule(el, pass, okText, failText) {
    if (!el) return;
    el.textContent = (pass ? '✓ ' : '✗ ') + (pass ? okText : failText);
    el.classList.toggle('pass', pass);
  }
  setRule(rLen,   hasLen,   'Min 8 characters',      'Min 8 characters');
  setRule(rUpper, hasUpper, 'One uppercase letter',   'One uppercase letter');
  setRule(rNum,   hasNum,   'One number',             'One number');
  setRule(rSym,   hasSym,   'One special character',  'One special character');

  var score = [hasLen, hasUpper, hasNum, hasSym].filter(Boolean).length;
  var fill  = bar.querySelector('.pw-strength-fill');
  var pct   = score * 25;
  var colors = ['','#e53935','#fb8c00','#f9a825','#2e7d32'];
  fill.style.width = pct + '%';
  fill.style.background = colors[score] || '#e0e0e0';
}

// ── Confirm password match ───────────────────────────────────
function checkConfirm() {
  var pw  = (document.getElementById('rec-password') || {}).value || '';
  var cpw = (document.getElementById('rec-confirm-password') || {}).value || '';
  var err = document.getElementById('rec-confirm-err');
  var inp = document.getElementById('rec-confirm-password');
  if (!cpw) {
    if (inp) inp.classList.remove('invalid-field','valid-field');
    if (err) err.style.display = 'none';
    return;
  }
  if (pw !== cpw) {
    if (inp) { inp.classList.add('invalid-field'); inp.classList.remove('valid-field'); }
    if (err) err.style.display = 'block';
  } else {
    if (inp) { inp.classList.remove('invalid-field'); inp.classList.add('valid-field'); }
    if (err) err.style.display = 'none';
  }
}

// ── Block form submit if validation errors ───────────────────
document.addEventListener('DOMContentLoaded', function() {
  // User form
  var userForm = document.querySelector('#form-user form');
  if (userForm) {
    userForm.addEventListener('submit', function(e) {
      var emailEl = document.getElementById('user-email');
      var pwEl    = document.getElementById('user-password');
      var re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
      var pw = pwEl ? pwEl.value : '';
      var errors = [];
      if (emailEl && !re.test(emailEl.value.trim())) errors.push('Valid email required.');
      if (pw.length < 8)            errors.push('Password must be at least 8 characters.');
      if (!/[A-Z]/.test(pw))        errors.push('Password needs an uppercase letter.');
      if (!/[0-9]/.test(pw))        errors.push('Password needs a number.');
      if (!/[^a-zA-Z0-9]/.test(pw)) errors.push('Password needs a special character.');
      if (errors.length) { e.preventDefault(); alert(errors.join('\n')); }
    });
  }
  // Recruiter form
  var recForm = document.querySelector('#form-company form');
  if (recForm) {
    recForm.addEventListener('submit', function(e) {
      var emailEl = document.getElementById('rec-email');
      var pwEl    = document.getElementById('rec-password');
      var cpwEl   = document.getElementById('rec-confirm-password');
      var re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
      var pw = pwEl ? pwEl.value : '';
      var errors = [];
      if (emailEl && !re.test(emailEl.value.trim())) errors.push('Valid email required.');
      if (pw.length < 8)            errors.push('Password must be at least 8 characters.');
      if (!/[A-Z]/.test(pw))        errors.push('Password needs an uppercase letter.');
      if (!/[0-9]/.test(pw))        errors.push('Password needs a number.');
      if (!/[^a-zA-Z0-9]/.test(pw)) errors.push('Password needs a special character.');
      if (cpwEl && pw !== cpwEl.value) errors.push('Passwords do not match.');
      if (errors.length) { e.preventDefault(); alert(errors.join('\n')); }
    });
  }
});
</script>
</body>
</html>