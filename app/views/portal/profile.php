<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl profile-layout">

  <!-- LEFT — Profile sections -->
  <section style="display:flex;flex-direction:column;gap:12px;">

    <!-- Hero card -->
    <div class="li-card profile-hero" style="text-align:left;">
      <div class="cover" style="<?= !empty($profile['cover']) ? 'background-image:url(\''.APP_URL.'/'.e($profile['cover']).'\');background-size:cover;background-position:center;' : '' ?>"></div>
      <div class="avatar big" style="margin:-70px 0 12px 24px;"><?= !empty($profile['avatar']) ? '<img src="'.APP_URL.'/'.e($profile['avatar']).'">' : initials($profile['name']) ?></div>
      <div style="padding:0 24px 18px;">
        <h1 style="margin:0 0 4px;"><?= e($profile['name']) ?></h1>
        <?php if (!empty($profile['headline'])): ?>
          <p style="margin:0 0 4px;font-size:.95rem;color:#444;"><?= e($profile['headline']) ?></p>
        <?php endif; ?>
        <?php if (!empty($profile['location'])): ?>
          <p style="margin:0 0 8px;font-size:.83rem;color:var(--muted);"><i class="bi bi-geo-alt me-1"></i><?= e($profile['location']) ?></p>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/network" style="display:inline-block;font-size:.83rem;color:var(--li-blue);font-weight:700;text-decoration:none;margin-bottom:<?= !empty($profile['website']) ? '6px' : '0' ?>;">
          <i class="bi bi-people-fill me-1"></i><?= count($connections ?? []) ?> connection<?= count($connections ?? []) !== 1 ? 's' : '' ?>
        </a>
        <?php if (!empty($profile['website'])): ?>
        <div>
          <a href="<?= e($profile['website']) ?>" target="_blank" style="font-size:.82rem;color:var(--li-blue);"><i class="bi bi-globe me-1"></i><?= e($profile['website']) ?></a>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- About -->
    <?php if (!empty($profile['bio'])): ?>
    <section class="li-card profile-section">
      <h2>About</h2>
      <p style="white-space:pre-line;"><?= e($profile['bio']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Experience -->
    <?php if (!empty($profile['experience'])): ?>
    <section class="li-card profile-section">
      <h2>Experience</h2>
      <p style="white-space:pre-line;"><?= e($profile['experience']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Education -->
    <?php if (!empty($profile['education'])): ?>
    <section class="li-card profile-section">
      <h2>Education</h2>
      <p style="white-space:pre-line;"><?= e($profile['education']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Skills -->
    <?php if (!empty($profile['skills'])): ?>
    <section class="li-card profile-section">
      <h2>Skills</h2>
      <?php
        $skillList = array_filter(array_map('trim', preg_split('/[,\n]+/', $profile['skills'])));
        if ($skillList):
      ?>
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">
        <?php foreach ($skillList as $skill): ?>
          <span style="background:var(--soft);color:var(--li-blue);border:1px solid #c8d8ea;border-radius:16px;padding:4px 14px;font-size:.82rem;font-weight:600;"><?= e($skill) ?></span>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p><?= e($profile['skills']) ?></p>
      <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if (!empty($profile['certifications'])): ?>
    <section class="li-card profile-section">
      <h2>Certifications</h2>
      <p style="white-space:pre-line;"><?= e($profile['certifications']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Languages -->
    <?php if (!empty($profile['languages'])): ?>
    <section class="li-card profile-section">
      <h2>Languages</h2>
      <p><?= e($profile['languages']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Social links -->
    <?php if (!empty($profile['social_links'])): ?>
    <section class="li-card profile-section">
      <h2>Social links</h2>
      <p style="white-space:pre-line;"><?= e($profile['social_links']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Connections section -->
    <?php if (!empty($connections)): ?>
    <section class="li-card profile-section">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <h2 style="margin:0;">Connections <span style="font-size:.85rem;color:var(--muted);font-weight:400;">(<?= count($connections) ?>)</span></h2>
        <a href="<?= APP_URL ?>/network" style="font-size:.82rem;color:var(--li-blue);font-weight:700;">See all</a>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;">
        <?php foreach (array_slice($connections, 0, 6) as $conn): ?>
        <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$conn['id'] ?>" style="text-decoration:none;color:inherit;text-align:center;border:1px solid var(--line);border-radius:8px;overflow:hidden;padding-bottom:12px;display:block;transition:box-shadow .15s;" onmouseover="this.style.boxShadow='0 2px 12px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
          <div style="height:56px;background:linear-gradient(135deg,#0a66c2,#004182);"></div>
          <div style="margin:-22px auto 6px;width:44px;height:44px;border-radius:50%;border:2px solid #fff;overflow:hidden;background:#dce6f1;display:grid;place-items:center;font-weight:800;color:var(--li-blue);font-size:.9rem;">
            <?= !empty($conn['avatar']) ? '<img src="'.APP_URL.'/'.e($conn['avatar']).'" style="width:100%;height:100%;object-fit:cover;">' : initials($conn['name']) ?>
          </div>
          <div style="font-size:.82rem;font-weight:700;padding:0 8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($conn['name']) ?></div>
          <?php if (!empty($conn['headline'])): ?>
          <div style="font-size:.72rem;color:var(--muted);padding:2px 8px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($conn['headline']) ?></div>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

  </section>

  <!-- RIGHT — Strength + Edit form -->
  <aside class="li-card">
    <div class="card-title">Profile Strength</div>
    <?php
      $pct = (int)$completion;
      if ($pct >= 80)      { $lbl = 'All-Star';     $clr = '#057642'; }
      elseif ($pct >= 60)  { $lbl = 'Advanced';     $clr = '#0a66c2'; }
      elseif ($pct >= 40)  { $lbl = 'Intermediate'; $clr = '#e7a33e'; }
      elseif ($pct >= 20)  { $lbl = 'Beginner';     $clr = '#e7832a'; }
      else                 { $lbl = 'Starter';       $clr = '#cc1016'; }
    ?>
    <div class="profile-strength-wrap">
      <div class="strength-row">
        <span class="strength-label" style="color:<?= $clr ?>;"><?= $lbl ?></span>
        <span class="strength-pct"   style="color:<?= $clr ?>;"><?= $pct ?>%</span>
      </div>
      <div class="strength-bar-bg">
        <div class="strength-bar-fill" style="width:<?= $pct ?>%;background:<?= $clr ?>;"></div>
      </div>
      <p class="strength-hint"><?= $pct < 100 ? 'Complete your profile to boost visibility' : '&#10003; Your profile is 100% complete!' ?></p>
    </div>
    <form method="post" enctype="multipart/form-data" class="profile-form">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
      <label>Name</label><input class="form-control" name="name" value="<?= e($profile['name']) ?>">
      <label>Headline</label><input class="form-control" name="headline" value="<?= e($profile['headline']) ?>">
      <label>Location</label><input class="form-control" name="location" value="<?= e($profile['location']) ?>">
      <label>Phone</label><input class="form-control" name="phone" value="<?= e($profile['phone']) ?>">
      <label>Website</label><input class="form-control" name="website" value="<?= e($profile['website']) ?>">
      <label>About</label><textarea class="form-control" name="bio"><?= e($profile['bio']) ?></textarea>
      <label>Experience</label><textarea class="form-control" name="experience"><?= e($profile['experience'] ?? '') ?></textarea>
      <label>Education</label><textarea class="form-control" name="education"><?= e($profile['education'] ?? '') ?></textarea>
      <label>Skills</label><textarea class="form-control" name="skills"><?= e($profile['skills'] ?? '') ?></textarea>
      <label>Certifications</label><textarea class="form-control" name="certifications"><?= e($profile['certifications'] ?? '') ?></textarea>
      <label>Languages</label><input class="form-control" name="languages" value="<?= e($profile['languages'] ?? '') ?>">
      <label>Social links</label><textarea class="form-control" name="social_links"><?= e($profile['social_links'] ?? '') ?></textarea>
      <label>Profile photo</label><input class="form-control" type="file" name="avatar" accept="image/*">
      <label>Cover banner</label><input class="form-control" type="file" name="cover" accept="image/*">
      <label>Resume PDF</label><input class="form-control" type="file" name="resume" accept="application/pdf">
      <button class="btn btn-primary w-100 mt-3">Save profile</button>
    </form>
  </aside>
</div>