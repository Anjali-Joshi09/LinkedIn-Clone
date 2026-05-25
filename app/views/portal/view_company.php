<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-md" style="max-width:860px;">
  <section class="li-card" style="overflow:visible;">
    <!-- Banner -->
    <div class="cover" style="height:160px;<?= !empty($company['banner']) ? 'background-image:url(\''.APP_URL.'/'.e($company['banner']).'\')'  : 'background:linear-gradient(135deg,#0a66c2,#004182)' ?>;background-size:cover;background-position:center;"></div>

    <!-- Logo + Name -->
    <div style="padding:0 24px 20px;position:relative;">
      <div class="avatar big" style="margin-top:-60px;border:4px solid #fff;margin-bottom:12px;border-radius:8px;">
        <?php if(!empty($company['logo'])): ?>
          <img src="<?= APP_URL.'/'.e($company['logo']) ?>" alt="<?= e($company['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
        <?php else: ?>
          <?= initials($company['name']) ?>
        <?php endif; ?>
      </div>
      <h1 style="font-size:1.5rem;font-weight:700;margin:0 0 4px;"><?= e($company['name']) ?></h1>
      <?php if(!empty($company['industry'])): ?>
        <p style="color:#555;margin:0 0 4px;"><?= e($company['industry']) ?></p>
      <?php endif; ?>
      <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:6px;">
        <?php if(!empty($company['location'])): ?>
          <span style="color:#888;font-size:.85rem;"><i class="bi bi-geo-alt"></i> <?= e($company['location']) ?></span>
        <?php endif; ?>
        <?php if(!empty($company['company_size'])): ?>
          <span style="color:#888;font-size:.85rem;"><i class="bi bi-people"></i> <?= e($company['company_size']) ?> employees</span>
        <?php endif; ?>
        <?php if(!empty($company['founded_year'])): ?>
          <span style="color:#888;font-size:.85rem;"><i class="bi bi-calendar3"></i> Founded <?= e($company['founded_year']) ?></span>
        <?php endif; ?>
        <?php if(!empty($company['website'])): ?>
          <a href="<?= e($company['website']) ?>" target="_blank" rel="noopener" style="color:#0a66c2;font-size:.85rem;"><i class="bi bi-link-45deg"></i> Website</a>
        <?php endif; ?>
      </div>
      <?php if(($viewerRole ?? 'user') !== 'company'): ?>
      <div style="margin-top:14px;">
        <button id="followCompanyBtn"
          class="btn <?= ($isFollowing ?? false) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm"
          data-company-id="<?= (int)$company['id'] ?>"
          data-following="<?= ($isFollowing ?? false) ? '1' : '0' ?>"
          style="border-radius:20px;font-weight:600;min-width:110px;">
          <?php if($isFollowing ?? false): ?>
            <i class="bi bi-check-lg me-1"></i> Following
          <?php else: ?>
            <i class="bi bi-plus-lg me-1"></i> Follow
          <?php endif; ?>
        </button>
      </div>
      <script>
      (function(){
        const btn = document.getElementById('followCompanyBtn');
        if(!btn) return;
        btn.addEventListener('click', async function(){
          const companyId = btn.dataset.companyId;
          const isFollowing = btn.dataset.following === '1';
          if(isFollowing && !confirm('Unfollow this company?')) return;
          try {
            const fd = new FormData();
            fd.append('company_id', companyId);
            fd.append('csrf_token', window.CSRF_TOKEN || '');
            const res = await fetch(window.APP_URL + '/ajax?action=follow-company', {
              method: 'POST', body: new URLSearchParams(fd)
            }).then(r => r.json());
            if(res.ok) {
              if(res.following) {
                btn.className = 'btn btn-primary btn-sm';
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Following';
                btn.dataset.following = '1';
                if(window.toast) window.toast('You are now following this company');
              } else {
                btn.className = 'btn btn-outline-primary btn-sm';
                btn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> Follow';
                btn.dataset.following = '0';
                if(window.toast) window.toast('Unfollowed');
              }
            }
          } catch(e) { if(window.toast) window.toast('Something went wrong'); }
        });
        btn.style.borderRadius='20px';btn.style.fontWeight='600';btn.style.minWidth='110px';
      })();
      </script>
      <?php endif; ?>
    </div>
  </section>

  <?php if(!empty($company['description'])): ?>
  <section class="li-card" style="margin-top:12px;padding:20px 24px;">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:8px;">About</h2>
    <p style="color:#444;line-height:1.6;margin:0;"><?= nl2br(e($company['description'])) ?></p>
  </section>
  <?php endif; ?>

  <?php if(!empty($jobs)): ?>
  <section class="li-card" style="margin-top:12px;padding:20px 24px;">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:14px;">Open Jobs</h2>
    <?php foreach($jobs as $job): ?>
      <a class="job-mini" href="<?= APP_URL ?>/jobs-board?q=<?= urlencode($job['title']) ?>" style="display:block;padding:10px 0;border-bottom:1px solid #f0f0f0;text-decoration:none;color:inherit;">
        <strong style="display:block;font-size:.9rem;color:#333;"><?= e($job['title']) ?></strong>
        <span style="font-size:.8rem;color:#888;"><?= e($job['location'] ?? '') ?> · <?= e($job['job_type'] ?? '') ?></span>
      </a>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <div style="margin-top:16px;">
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Go Back</a>
  </div>
</div>