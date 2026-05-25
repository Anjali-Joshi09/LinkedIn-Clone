<?php // app/views/auth/login.php ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | LinkedIn</title>

<!-- Apply saved theme before paint (prevents FOUC) -->
<script>(function(){ var t=localStorage.getItem('li_theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>

<!-- Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Tabler Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
<!-- Auth CSS -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/auth.css">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/captcha.css">
</head>
<body>

<div class="logo-block">
  <div class="logo-row">
    <div class="li-box">in</div>
    <span class="wordmark">LinkedIn</span>
  </div>
  <span class="portal-badge">Admin Portal</span>
</div>

<div class="card">
  <h1 class="card-title">Sign in</h1>
  <p class="card-sub">Access your admin dashboard</p>

  <?php if(!empty($flash)): ?>
  <div class="alert alert-<?= $flash['type'] ?>">
    <i class="ti ti-<?= $flash['type']==='success'?'circle-check':'alert-circle' ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>
  <?php if(($_GET['reason'] ?? '') === 'timeout'): ?>
  <div class="alert alert-error">
    <i class="ti ti-clock"></i> You were logged out due to inactivity. Please login again.
  </div>
  <?php endif; ?>
  <?php if(!empty($_GET['reset'])): ?>
  <div class="alert alert-success"><i class="ti ti-circle-check"></i> Password reset link sent! Please check your email.</div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/login" autocomplete="on">
    <label class="lbl" for="email">Email Address</label>
    <input class="inp mb16" type="email" id="email" name="email" placeholder="admin@site.com" required autocomplete="email">

    <label class="lbl" for="password">Password</label>
    <div class="pw-wrap">
      <input class="inp" type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
      <button type="button" class="pw-toggle" onclick="togglePw()">Show</button>
    </div>

    <a href="#" class="forgot-link" onclick="document.getElementById('forgot-modal').classList.add('open');return false">
      Forgot password?
    </a>

    <div class="checkbox-row">
      <input type="checkbox" id="remember" name="remember">
      <label for="remember">Keep me signed in</label>
    </div>

    <div class="captcha-group">
      <label class="lbl" for="admin-captcha">Security Code</label>
      <div class="captcha-row">
        <img class="captcha-image" src="<?= APP_URL ?>/captcha.php" alt="Security code" id="admin-captcha-image">
        <button type="button" class="captcha-refresh" title="Refresh security code" onclick="refreshCaptcha('admin-captcha-image')">
          <i class="ti ti-refresh"></i>
        </button>
        <input class="inp captcha-input" type="text" id="admin-captcha" name="captcha" maxlength="6" placeholder="Enter code" required autocomplete="off">
      </div>
    </div>

    <button type="submit" class="btn-login">Sign In</button>

    <div class="secure-bar">
      <i class="ti ti-lock"></i>
      <p>This is a secure admin area. Unauthorized access is prohibited and monitored.</p>
    </div>
  </form>
</div>

<div class="footer-links">
  <a href="#">Privacy Policy</a>
  <a href="#">Terms of Service</a>
  <a href="#">Help Center</a>
</div>
<p class="copy">&copy; <?= date('Y') ?> LinkedIn Admin. All rights reserved.</p>

<div class="modal-bg" id="forgot-modal">
  <div class="modal-box">
    <h3>Reset Your Password</h3>
    <p>Enter your admin email address and we will send you a password reset link.</p>
    <form method="POST" action="<?= APP_URL ?>/forgot-password">
      <label class="lbl">Email Address</label>
      <input class="inp mb16" type="email" name="reset_email" placeholder="admin@site.com" required style="margin-bottom:0">
      <div class="modal-foot">
        <button type="submit" class="btn-primary">Send Reset Link</button>
        <button type="button" class="btn-outline" onclick="document.getElementById('forgot-modal').classList.remove('open')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Auth JS -->
<script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</body>
</html>
