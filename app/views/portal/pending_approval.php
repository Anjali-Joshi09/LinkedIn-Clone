<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-md" style="max-width:600px;padding-top:60px;">
  <section class="li-card" style="text-align:center;padding:48px 32px;">
    <div style="font-size:3.5rem;margin-bottom:16px;">⏳</div>
    <h1 style="font-size:1.4rem;font-weight:700;margin-bottom:10px;">Your account is under review</h1>
    <p style="color:#555;line-height:1.7;margin-bottom:20px;">
      Hi <strong><?= e($company['name']) ?></strong>, your recruiter account has been submitted and is awaiting admin approval.<br>
      You will receive an email once your account is approved. This usually takes a short time.
    </p>
    <div style="background:#f0f7ff;border-radius:10px;padding:16px 20px;color:#0a66c2;font-size:.9rem;">
      <i class="bi bi-envelope-fill me-2"></i>An email has been sent to <strong><?= e($currentUser['email'] ?? '') ?></strong>
    </div>
    <a href="<?= APP_URL ?>/logout-user" class="btn btn-outline-secondary mt-4">Sign out</a>
  </section>
</div>
