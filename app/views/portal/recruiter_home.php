<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl li-grid">
  <!-- LEFT SIDEBAR -->
  <aside class="li-left">
    <section class="li-card profile-mini">
      <div class="cover" style="background-image:url('<?= !empty($company['banner']) ? APP_URL.'/'.e($company['banner']) : '' ?>')"></div>
      <div class="avatar" style="border-radius:8px;">
        <?php if(!empty($company['logo'])): ?>
          <img src="<?= APP_URL.'/'.e($company['logo']) ?>" alt="<?= e($company['name']) ?>">
        <?php else: ?>
          <?= initials($company['name']) ?>
        <?php endif; ?>
      </div>
      <h2><?= e($company['name']) ?></h2>
      <p><?= e($company['industry'] ?: 'Add your industry') ?></p>
      <a href="<?= APP_URL ?>/company-profile">Edit company profile</a>
    </section>

    <section class="li-card compact-list">
      <a href="<?= APP_URL ?>/recruiter-dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="<?= APP_URL ?>/recruiter-jobs"><i class="bi bi-briefcase"></i> Post a Job</a>
      <a href="<?= APP_URL ?>/applicants"><i class="bi bi-people"></i> Applications</a>
      <a href="<?= APP_URL ?>/portal-notifications"><i class="bi bi-bell"></i> Notifications</a>
    </section>

    <!-- Hiring Snapshot -->
    <section class="li-card" style="padding:14px 16px;">
      <div style="font-weight:700;font-size:.88rem;margin-bottom:10px;color:#444;">Hiring Snapshot</div>
      <?php
      $snapItems = [
        'jobs'       => ['Total Jobs',   'bi-briefcase',         '#0a66c2'],
        'active'     => ['Active Jobs',  'bi-check-circle',      '#057642'],
        'applicants' => ['Applicants',   'bi-person-lines-fill', '#e7a33e'],
        'interviews' => ['Interviews',   'bi-camera-video',      '#8f5849'],
      ];
      foreach ($snapItems as $k => [$label, $icon, $color]): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f0f0f0;">
        <span style="font-size:.82rem;color:#555;display:flex;align-items:center;gap:7px;">
          <i class="bi <?= $icon ?>" style="color:<?= $color ?>;font-size:.95rem;"></i><?= $label ?>
        </span>
        <strong style="font-size:.95rem;color:<?= $color ?>;"><?= (int)($stats[$k] ?? 0) ?></strong>
      </div>
      <?php endforeach; ?>
    </section>
  </aside>

  <!-- CENTER — Feed -->
  <section class="li-feed">
    <!-- Post Composer (identical to user) -->
    <div class="li-card composer">
      <div class="composer-top">
        <a href="<?= APP_URL ?>/company-profile" style="text-decoration:none;">
        <div class="mini-avatar" style="cursor:pointer;">
          <?php if(!empty($company['logo'])): ?>
            <img src="<?= APP_URL.'/'.e($company['logo']) ?>" alt="<?= e($company['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
          <?php else: ?>
            <?= initials($company['name']) ?>
          <?php endif; ?>
        </div>
        </a>
        <button data-bs-toggle="modal" data-bs-target="#postModal">Start a post</button>
      </div>
      <div class="composer-actions">
        <span><i class="bi bi-image"></i> Media</span>
        <span><i class="bi bi-file-earmark-pdf"></i> Document</span>
        <span><i class="bi bi-briefcase"></i> Post Job</span>
      </div>
    </div>

    <div id="feedList">
      <?php foreach($posts as $post): require VIEW_PATH.'/portal/partials/post.php'; endforeach; ?>
    </div>
    <div id="feedLoader" class="li-card skeleton-card"><div></div><div></div><div></div></div>
  </section>

  <!-- RIGHT SIDEBAR -->
  <aside class="li-right">
    <!-- Open Roles -->
    <section class="li-card">
      <div class="card-title">Open Roles</div>
      <?php if(empty($jobs)): ?>
        <p style="padding:8px 16px 16px;font-size:.85rem;color:#888;">No jobs posted yet.</p>
      <?php else: ?>
        <?php foreach(array_slice($jobs, 0, 4) as $job): ?>
          <a class="job-mini" href="<?= APP_URL ?>/recruiter-jobs">
            <strong><?= e($job['title']) ?></strong>
            <span><?= e($job['company'] ?? $company['name']) ?> · <?= e($job['location'] ?? '') ?></span>
          </a>
        <?php endforeach; ?>
        <a href="<?= APP_URL ?>/recruiter-jobs" style="display:block;text-align:center;padding:10px;font-size:.8rem;color:var(--li-blue);font-weight:600;border-top:1px solid var(--line);">
          See all jobs <i class="bi bi-arrow-right"></i>
        </a>
      <?php endif; ?>
    </section>

    <!-- Suggested People to Follow -->
    <section class="li-card">
      <div class="card-title">Add to your network</div>
      <?php foreach($suggestions as $person): ?>
        <div class="suggestion">
          <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$person['id'] ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;flex:1;min-width:0;">
            <div class="mini-avatar" style="cursor:pointer;flex-shrink:0;">
              <?php if(!empty($person['avatar'])): ?>
                <img src="<?= APP_URL.'/'.e($person['avatar']) ?>" alt="<?= e($person['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
              <?php else: ?>
                <?= initials($person['name']) ?>
              <?php endif; ?>
            </div>
            <div style="min-width:0;overflow:hidden;">
              <strong style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($person['name']) ?></strong>
              <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;"><?= e($person['headline'] ?: 'LinkedIn member') ?></span>
            </div>
          </a>
          <a class="btn btn-outline-primary btn-sm" href="<?= APP_URL ?>/messages?with=<?= (int)$person['id'] ?>"><i class="bi bi-chat-dots"></i> Message</a>
        </div>
      <?php endforeach; ?>
    </section>
  </aside>
</div>

<!-- Post Modal (identical to user) -->
<div class="modal fade" id="postModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
    <div class="modal-content" style="border-radius:12px;overflow:hidden;">
      <div class="modal-header" style="border-bottom:1px solid #e0e0e0;padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="mini-avatar" style="width:44px;height:44px;flex-shrink:0;">
            <?php if(!empty($company['logo'])): ?>
              <img src="<?= APP_URL.'/'.e($company['logo']) ?>" alt="<?= e($company['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($company['name']) ?>
            <?php endif; ?>
          </div>
          <div>
            <strong style="display:block;font-size:.95rem;"><?= e($company['name']) ?></strong>
            <button type="button" style="font-size:.75rem;background:#eee;border:none;border-radius:20px;padding:2px 10px;color:#333;font-weight:600;">🌐 Anyone</button>
          </div>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <form class="modal-content-inner ajax-post-form" enctype="multipart/form-data" style="background:#fff;">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <div class="modal-body" style="padding:16px 20px;min-height:160px;">
          <textarea class="post-textarea" name="content" id="postTextarea" placeholder="What do you want to share?" style="width:100%;border:none;outline:none;resize:none;font-size:1.1rem;min-height:120px;font-family:inherit;color:#333;" rows="5"></textarea>
          <div id="emojiPanel" style="display:none;flex-wrap:wrap;gap:4px;padding:8px 0;max-height:130px;overflow-y:auto;">
            <?php
            $emojis = ['😀','😂','😍','🥳','😎','🤔','👏','🙌','❤️','🔥','✅','💡','🚀','🎉','💪','👍','🙏','😊','😢','😮','🤝','🏆','💼','📢','🌟','⭐','💯','👀','🗣️','📌'];
            foreach($emojis as $emoji): ?>
              <span class="emoji-pick" style="font-size:1.3rem;cursor:pointer;padding:3px;border-radius:4px;transition:background .15s;" title="<?= $emoji ?>"><?= $emoji ?></span>
            <?php endforeach; ?>
          </div>
          <div id="mediaPreviewArea" style="display:none;position:relative;margin-top:10px;border-radius:8px;overflow:hidden;border:1px solid #e0e0e0;">
            <img id="mediaPreviewImg" src="" alt="Preview" style="width:100%;max-height:320px;object-fit:cover;display:none;">
            <video id="mediaPreviewVid" src="" controls style="width:100%;max-height:320px;display:none;"></video>
            <div id="mediaPreviewDoc" style="display:none;padding:14px 16px;background:#f8f9fa;display:flex;align-items:center;gap:10px;"><i class="bi bi-file-earmark-pdf" style="font-size:1.4rem;color:#e74c3c;"></i><span id="mediaPreviewDocName" style="font-size:.9rem;font-weight:600;color:#333;"></span></div>
            <button type="button" id="removeMediaBtn" style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.55);border:none;color:#fff;border-radius:50%;width:28px;height:28px;font-size:.85rem;cursor:pointer;display:flex;align-items:center;justify-content:center;"><i class="bi bi-x-lg"></i></button>
          </div>
        </div>
        <div class="modal-footer" style="padding:10px 20px;border-top:1px solid #e0e0e0;justify-content:space-between;background:#fff;">
          <div style="display:flex;gap:4px;align-items:center;">
            <label for="postMediaInput" title="Add photo/video/document" style="cursor:pointer;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;transition:background .15s;" onmouseover="this.style.background='#eef3f8'" onmouseout="this.style.background='transparent'">
              <i class="bi bi-image" style="font-size:1.25rem;color:#666;"></i>
            </label>
            <input type="file" id="postMediaInput" name="media" accept="image/*,video/mp4,application/pdf" style="display:none;">
            <button type="button" id="emojiToggleBtn" title="Emoji" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border:none;background:transparent;border-radius:50%;font-size:1.25rem;cursor:pointer;transition:background .15s;" onmouseover="this.style.background='#eef3f8'" onmouseout="this.style.background='transparent'">🙂</button>
          </div>
          <button type="submit" class="btn btn-primary px-4" id="postSubmitBtn" disabled style="border-radius:20px;font-weight:700;">Post</button>
        </div>
      </form>
    </div>
  </div>
</div>