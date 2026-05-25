<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl li-grid">
  <aside class="li-left">
    <section class="li-card profile-mini">
      <div class="cover" style="background-image:url('<?= !empty($user['cover']) ? APP_URL.'/'.e($user['cover']) : '' ?>')"></div>
      <div class="avatar">
        <?php if(!empty($user['avatar'])): ?>
          <img src="<?= APP_URL.'/'.e($user['avatar']) ?>" alt="<?= e($user['name']) ?>">
        <?php else: ?>
          <?= initials($user['name']) ?>
        <?php endif; ?>
      </div>
      <h2><?= e($user['name']) ?></h2>
      <p><?= e($user['headline'] ?: 'Build your professional story') ?></p>
      <a href="<?= APP_URL ?>/profile">View profile</a>
    </section>
    <section class="li-card compact-list">
      <a href="<?= APP_URL ?>/network"><i class="bi bi-person-plus"></i> Grow your network</a>
      <a href="<?= APP_URL ?>/jobs-board"><i class="bi bi-briefcase"></i> Saved jobs</a>
      <a href="<?= APP_URL ?>/portal-notifications"><i class="bi bi-bell"></i> Notifications</a>
    </section>
  </aside>

  <section class="li-feed">
    <div class="li-card composer">
      <div class="composer-top">
        <div class="mini-avatar">
          <?php if(!empty($user['avatar'])): ?>
            <img src="<?= APP_URL.'/'.e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
          <?php else: ?>
            <?= initials($user['name']) ?>
          <?php endif; ?>
        </div>
        <button data-bs-toggle="modal" data-bs-target="#postModal">Start a post</button>
      </div>
      <div class="composer-actions"><span><i class="bi bi-image"></i> Media</span><span><i class="bi bi-file-earmark-pdf"></i> Document</span><span><i class="bi bi-briefcase"></i> Hiring</span></div>
    </div>
    <div id="feedList">
      <?php foreach($posts as $post): require VIEW_PATH.'/portal/partials/post.php'; endforeach; ?>
    </div>
    <div id="feedLoader" class="li-card skeleton-card"><div></div><div></div><div></div></div>
  </section>

  <aside class="li-right">
    <section class="li-card">
      <div class="card-title">Add to your feed</div>
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
          <button class="connect-btn" data-user="<?= (int)$person['id'] ?>">+ Connect</button>
        </div>
      <?php endforeach; ?>
    </section>
    <section class="li-card">
      <div class="card-title">Recommended jobs</div>
      <?php foreach($jobs as $job): ?>
        <a class="job-mini" href="<?= APP_URL ?>/jobs-board?q=<?= urlencode($job['title']) ?>">
          <div class="job-logo">
            <?php if(!empty($job['logo'])): ?>
              <img src="<?= APP_URL.'/'.e($job['logo']) ?>" alt="<?= e($job['company']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
            <?php else: ?>
              <?= initials($job['company']) ?>
            <?php endif; ?>
          </div>
          <div class="job-content">
            <h2><?= e($job['title']) ?></h2>
            <p><?= e($job['company']) ?> · <?= e($job['location']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </section>
  </aside>
</div>

<div class="modal fade" id="postModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
    <div class="modal-content" style="border-radius:12px;overflow:hidden;">
      <div class="modal-header" style="border-bottom:1px solid #e0e0e0;padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div class="mini-avatar" style="width:44px;height:44px;flex-shrink:0;">
            <?php if(!empty($user['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($user['avatar']) ?>" alt="<?= e($user['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($user['name']) ?>
            <?php endif; ?>
          </div>
          <div>
            <strong style="display:block;font-size:.95rem;"><?= e($user['name']) ?></strong>
            <button type="button" style="font-size:.75rem;background:#eee;border:none;border-radius:20px;padding:2px 10px;color:#333;font-weight:600;">🌐 Anyone</button>
          </div>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <form class="modal-content-inner ajax-post-form" enctype="multipart/form-data" style="background:#fff;">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <div class="modal-body" style="padding:16px 20px;min-height:160px;">
          <textarea class="post-textarea" name="content" id="postTextarea" placeholder="What do you want to talk about?" style="width:100%;border:none;outline:none;resize:none;font-size:1.1rem;min-height:120px;font-family:inherit;color:#333;" rows="5"></textarea>
          <!-- Emoji picker panel -->
          <div id="emojiPanel" style="display:none;flex-wrap:wrap;gap:4px;padding:8px 0;max-height:130px;overflow-y:auto;">
            <?php
            $emojis = ['😀','😂','😍','🥳','😎','🤔','👏','🙌','❤️','🔥','✅','💡','🚀','🎉','💪','👍','🙏','😊','😢','😮','🤝','🏆','💼','📢','🌟','⭐','💯','👀','🗣️','📌'];
            foreach($emojis as $emoji): ?>
              <span class="emoji-pick" style="font-size:1.3rem;cursor:pointer;padding:3px;border-radius:4px;transition:background .15s;" title="<?= $emoji ?>"><?= $emoji ?></span>
            <?php endforeach; ?>
          </div>
          <!-- Image preview area -->
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