<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl profile-layout">

  <div style="display:flex;flex-direction:column;gap:14px;">

    <!-- Hero / Identity card -->
    <section class="li-card company-hero" style="margin-bottom:0;">
      <div class="company-banner" style="<?= !empty($company['banner']) ? 'background-image:url(\''.APP_URL.'/'.e($company['banner']).'\');background-size:cover;background-position:center;' : '' ?>"></div>
      <div class="company-logo">
        <?= !empty($company['logo']) ? '<img src="'.APP_URL.'/'.e($company['logo']).'" alt="'.e($company['name']).'">' : initials($company['name']) ?>
      </div>
      <div style="padding:10px 22px 4px;">
        <h1 style="font-size:1.4rem;font-weight:800;margin:0 0 2px;"><?= e($company['name']) ?></h1>
        <?php if (!empty($company['industry'])): ?>
          <p style="margin:0 0 2px;font-size:.88rem;color:#444;"><?= e($company['industry']) ?></p>
        <?php endif; ?>
        <?php if (!empty($company['location'])): ?>
          <p style="margin:0 0 6px;font-size:.82rem;color:var(--muted);"><i class="bi bi-geo-alt me-1"></i><?= e($company['location']) ?></p>
        <?php endif; ?>
        <p style="margin:0 0 14px;font-size:.82rem;color:var(--li-blue);font-weight:600;">
          <i class="bi bi-people-fill me-1"></i><?= number_format($followersCount ?? 0) ?> follower<?= ($followersCount ?? 0) !== 1 ? 's' : '' ?>
        </p>
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding-bottom:16px;border-bottom:1px solid var(--line);">
          <a href="<?= APP_URL ?>/recruiter-jobs" class="btn btn-sm btn-primary" style="border-radius:18px;padding:5px 16px;font-weight:700;font-size:.82rem;">
            <i class="bi bi-plus-circle me-1"></i>Post a job
          </a>
          <?php if (!empty($company['website'])): ?>
          <a href="<?= e($company['website']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius:18px;padding:5px 14px;font-size:.82rem;">
            <i class="bi bi-globe me-1"></i>Website
          </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Stats row -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);text-align:center;padding:12px 8px;">
        <?php
        $statItems = [
          ['label'=>'Total Jobs',  'val'=>$stats['jobs']??0,       'icon'=>'bi-briefcase',        'color'=>'#0a66c2'],
          ['label'=>'Active',      'val'=>$stats['active']??0,     'icon'=>'bi-check-circle',      'color'=>'#057642'],
          ['label'=>'Applicants',  'val'=>$stats['applicants']??0, 'icon'=>'bi-person-lines-fill', 'color'=>'#e7a33e'],
          ['label'=>'Interviews',  'val'=>$stats['interviews']??0, 'icon'=>'bi-camera-video',      'color'=>'#6f42c1'],
        ];
        foreach ($statItems as $s): ?>
        <div style="padding:6px 4px;">
          <i class="bi <?= $s['icon'] ?>" style="color:<?= $s['color'] ?>;font-size:1.1rem;display:block;margin-bottom:3px;"></i>
          <strong style="font-size:1.2rem;font-weight:800;color:<?= $s['color'] ?>;"><?= number_format($s['val']) ?></strong>
          <span style="display:block;font-size:.7rem;color:var(--muted);margin-top:1px;"><?= $s['label'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- About -->
    <?php if (!empty($company['description'])): ?>
    <section class="li-card" style="padding:16px 20px;">
      <h2 style="font-size:.95rem;font-weight:800;margin:0 0 10px;">About</h2>
      <p style="font-size:.875rem;color:#444;line-height:1.6;margin:0;white-space:pre-line;"><?= e($company['description']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Company details -->
    <section class="li-card" style="padding:16px 20px;">
      <h2 style="font-size:.95rem;font-weight:800;margin:0 0 12px;">Company details</h2>
      <div style="display:flex;flex-direction:column;gap:11px;">
        <?php if (!empty($company['website'])): ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:.85rem;">
          <i class="bi bi-globe" style="color:var(--muted);width:18px;text-align:center;"></i>
          <a href="<?= e($company['website']) ?>" target="_blank" style="color:var(--li-blue);font-weight:600;word-break:break-all;"><?= e($company['website']) ?></a>
        </div>
        <?php endif; ?>
        <?php if (!empty($company['industry'])): ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:.85rem;color:#444;">
          <i class="bi bi-building" style="color:var(--muted);width:18px;text-align:center;"></i>
          <span><?= e($company['industry']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($company['company_size'])): ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:.85rem;color:#444;">
          <i class="bi bi-people" style="color:var(--muted);width:18px;text-align:center;"></i>
          <span><?= e($company['company_size']) ?> employees</span>
        </div>
        <?php endif; ?>
        <?php if (!empty($company['location'])): ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:.85rem;color:#444;">
          <i class="bi bi-geo-alt" style="color:var(--muted);width:18px;text-align:center;"></i>
          <span><?= e($company['location']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($company['phone'])): ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:.85rem;color:#444;">
          <i class="bi bi-telephone" style="color:var(--muted);width:18px;text-align:center;"></i>
          <span><?= e($company['phone']) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Quick nav -->
    <section class="li-card compact-list">
      <a href="<?= APP_URL ?>/recruiter-dashboard"><i class="bi bi-house me-2"></i>Dashboard</a>
      <a href="<?= APP_URL ?>/recruiter-jobs"><i class="bi bi-briefcase me-2"></i>Manage Jobs</a>
      <a href="<?= APP_URL ?>/applicants"><i class="bi bi-kanban me-2"></i>Applicants</a>
      <a href="<?= APP_URL ?>/network"><i class="bi bi-people me-2"></i>Followers</a>
    </section>

  </div>

  <!-- RIGHT — Edit form -->
  <aside class="li-card">
    <div class="card-title">Edit company profile</div>
    <form method="post" enctype="multipart/form-data" class="profile-form">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
      <label>Company name</label><input class="form-control" name="name" value="<?= e($company['name']) ?>">
      <label>Phone</label><input class="form-control" name="phone" value="<?= e($company['phone']) ?>">
      <label>Website</label><input class="form-control" name="website" value="<?= e($company['website']) ?>">
      <label>Industry</label><input class="form-control" name="industry" value="<?= e($company['industry']) ?>">
      <label>Employee count</label><input class="form-control" name="company_size" value="<?= e($company['company_size']) ?>">
      <label>Location</label><input class="form-control" name="location" value="<?= e($company['location']) ?>">
      <label>About company</label><textarea class="form-control" name="description" rows="4"><?= e($company['description']) ?></textarea>
      <label>Logo</label><input class="form-control" type="file" name="logo" accept="image/*">
      <label>Cover banner</label><input class="form-control" type="file" name="banner" accept="image/*">
      <button class="btn btn-primary w-100 mt-3">Save company</button>
    </form>
  </aside>

</div>
