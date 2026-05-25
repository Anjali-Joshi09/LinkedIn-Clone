<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl profile-layout">

  <!-- LEFT — Profile sections -->
  <section style="display:flex;flex-direction:column;gap:12px;">

    <!-- Hero card -->
    <div class="li-card profile-hero" style="text-align:left;">
      <div class="cover" style="<?= !empty($targetUser['cover']) ? 'background-image:url(\''.APP_URL.'/'.e($targetUser['cover']).'\');background-size:cover;background-position:center;' : '' ?>"></div>
      <div class="avatar big" style="margin:-70px 0 12px 24px;">
        <?= !empty($targetUser['avatar']) ? '<img src="'.APP_URL.'/'.e($targetUser['avatar']).'" alt="'.e($targetUser['name']).'" style="width:100%;height:100%;object-fit:cover;">' : initials($targetUser['name']) ?>
      </div>
      <div style="padding:0 24px 18px;">
        <h1 style="margin:0 0 4px;"><?= e($targetUser['name']) ?></h1>
        <?php if (!empty($targetUser['headline'])): ?>
          <p style="margin:0 0 4px;font-size:.95rem;color:#444;"><?= e($targetUser['headline']) ?></p>
        <?php endif; ?>
        <?php if (!empty($targetUser['location'])): ?>
          <p style="margin:0 0 8px;font-size:.83rem;color:var(--muted);"><i class="bi bi-geo-alt me-1"></i><?= e($targetUser['location']) ?></p>
        <?php endif; ?>
        <span style="display:inline-block;font-size:.83rem;color:var(--li-blue);font-weight:700;margin-bottom:<?= !empty($targetUser['website']) ? '6px' : '0' ?>;">
          <i class="bi bi-people-fill me-1"></i><?= count($targetConnections ?? []) ?> connection<?= count($targetConnections ?? []) !== 1 ? 's' : '' ?>
        </span>
        <?php if (!empty($targetUser['website'])): ?>
        <div>
          <a href="<?= e($targetUser['website']) ?>" target="_blank" style="font-size:.82rem;color:var(--li-blue);"><i class="bi bi-globe me-1"></i><?= e($targetUser['website']) ?></a>
        </div>
        <?php endif; ?>

        <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
          <a href="<?= APP_URL ?>/messages?with=<?= (int)$targetUser['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-chat-dots me-1"></i>Message</a>
        </div>
      </div>
    </div>

    <!-- About -->
    <?php if (!empty($targetUser['bio'])): ?>
    <section class="li-card profile-section">
      <h2>About</h2>
      <p style="white-space:pre-line;"><?= e($targetUser['bio']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Experience -->
    <?php if (!empty($targetUser['experience'])): ?>
    <section class="li-card profile-section">
      <h2>Experience</h2>
      <p style="white-space:pre-line;"><?= e($targetUser['experience']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Education -->
    <?php if (!empty($targetUser['education'])): ?>
    <section class="li-card profile-section">
      <h2>Education</h2>
      <p style="white-space:pre-line;"><?= e($targetUser['education']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Skills -->
    <?php if (!empty($targetUser['skills'])): ?>
    <section class="li-card profile-section">
      <h2>Skills</h2>
      <?php
        $skillList = array_filter(array_map('trim', preg_split('/[,\n]+/', $targetUser['skills'])));
        if ($skillList):
      ?>
      <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">
        <?php foreach ($skillList as $skill): ?>
          <span style="background:var(--soft);color:var(--li-blue);border:1px solid #c8d8ea;border-radius:16px;padding:4px 14px;font-size:.82rem;font-weight:600;"><?= e($skill) ?></span>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p><?= e($targetUser['skills']) ?></p>
      <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if (!empty($targetUser['certifications'])): ?>
    <section class="li-card profile-section">
      <h2>Certifications</h2>
      <p style="white-space:pre-line;"><?= e($targetUser['certifications']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Languages -->
    <?php if (!empty($targetUser['languages'])): ?>
    <section class="li-card profile-section">
      <h2>Languages</h2>
      <p><?= e($targetUser['languages']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Social links -->
    <?php if (!empty($targetUser['social_links'])): ?>
    <section class="li-card profile-section">
      <h2>Social links</h2>
      <p style="white-space:pre-line;"><?= e($targetUser['social_links']) ?></p>
    </section>
    <?php endif; ?>

    <!-- Connections -->
    <?php if (!empty($targetConnections)): ?>
    <section class="li-card profile-section">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <h2 style="margin:0;">Connections <span style="font-size:.85rem;color:var(--muted);font-weight:400;">(<?= count($targetConnections) ?>)</span></h2>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;">
        <?php foreach (array_slice($targetConnections, 0, 6) as $conn): ?>
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

    <div style="margin-top:4px;">
      <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Go Back</a>
    </div>

  </section>

  <!-- RIGHT — Info sidebar -->
  <aside class="li-card" style="align-self:start;">
    <div class="card-title">Profile Info</div>
    <div style="display:flex;flex-direction:column;gap:14px;padding:12px 20px 20px;">

      <?php if (!empty($targetUser['email'])): ?>
      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Email</div>
        <div style="font-size:.88rem;color:#333;word-break:break-all;"><?= e($targetUser['email']) ?></div>
      </div>
      <?php endif; ?>

      <?php if (!empty($targetUser['phone'])): ?>
      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Phone</div>
        <div style="font-size:.88rem;color:#333;"><?= e($targetUser['phone']) ?></div>
      </div>
      <?php endif; ?>

      <?php if (!empty($targetUser['location'])): ?>
      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Location</div>
        <div style="font-size:.88rem;color:#333;"><i class="bi bi-geo-alt me-1"></i><?= e($targetUser['location']) ?></div>
      </div>
      <?php endif; ?>

      <?php if (!empty($targetUser['website'])): ?>
      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Website</div>
        <a href="<?= e($targetUser['website']) ?>" target="_blank" style="font-size:.88rem;color:var(--li-blue);text-decoration:none;word-break:break-all;">
          <i class="bi bi-globe me-1"></i><?= e($targetUser['website']) ?>
        </a>
      </div>
      <?php endif; ?>

      <?php if (!empty($targetUser['languages'])): ?>
      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Languages</div>
        <div style="font-size:.88rem;color:#333;"><?= e($targetUser['languages']) ?></div>
      </div>
      <?php endif; ?>

      <div>
        <div style="font-size:.75rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px;">Connections</div>
        <div style="font-size:.88rem;color:#333;"><i class="bi bi-people-fill me-1" style="color:var(--li-blue);"></i><?= count($targetConnections ?? []) ?> connection<?= count($targetConnections ?? []) !== 1 ? 's' : '' ?></div>
      </div>

    </div>
  </aside>

</div>