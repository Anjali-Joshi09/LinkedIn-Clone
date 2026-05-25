<?php // app/views/auth/reset_password.php ?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | LinkedIn Admin</title>

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
</head>
<body>

<button class="theme-btn" onclick="toggleTheme()" title="Toggle theme">
  <i class="ti ti-moon icon-moon"></i>
  <i class="ti ti-sun icon-sun"></i>
</button>

<div class="logo-block">
  <div class="logo-row">
    <div class="li-box">in</div>
    <span class="wordmark">LinkedIn</span>
  </div>
  <span class="portal-badge">Admin Portal</span>
</div>

<div class="card">
  <h2 class="card-title">Reset Your Password</h2>
  <p class="card-sub">Enter a new password for your admin account.</p>

  <?php if (!empty($flash)): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?>">
    <i class="ti ti-<?= $flash['type'] === 'error' ? 'alert-circle' : 'circle-check' ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/reset-password?token=<?= htmlspecialchars($token) ?>">
    <label class="lbl mt" for="password">New Password</label>
    <div class="pw-wrap">
      <input class="inp" type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters">
      <button type="button" class="pw-toggle" onclick="togglePwById('password', this)">Show</button>
    </div>
    <p class="hint">Minimum 8 characters.</p>

    <label class="lbl mt" for="confirm_password">Confirm New Password</label>
    <div class="pw-wrap">
      <input class="inp" type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Re-enter new password">
      <button type="button" class="pw-toggle" onclick="togglePwById('confirm_password', this)">Show</button>
    </div>

    <button type="submit" class="btn-submit">Reset Password</button>
  </form>

  <a href="<?= APP_URL ?>/login" class="back-link">
    <i class="ti ti-arrow-left" style="font-size:12px"></i> Back to Login
  </a>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Auth JS -->
<script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</body>
</html>
