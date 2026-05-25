<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<article class="li-card post-card" data-post="<?= (int)$post['id'] ?>" data-owner="<?= (int)($post['user_id'] ?? 0) === (int)($_SESSION['user_id'] ?? -1) ? '1' : '0' ?>" id="post-<?= (int)$post['id'] ?>">
  <?php
    $profileUrl = (($post['author_role'] ?? '') === 'company' && !empty($post['company_id']))
      ? APP_URL.'/view-company?id='.(int)$post['company_id']
      : APP_URL.'/view-profile?id='.(int)($post['user_id'] ?? 0);
  ?>
  <header class="post-head">
    <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;cursor:pointer;" onclick="if(!event.target.closest('button')){window.location='<?= htmlspecialchars($profileUrl, ENT_QUOTES) ?>'}">
      <div class="mini-avatar" style="cursor:pointer;flex-shrink:0;"><?php
        $isCompanyPost = ($post['author_role'] ?? '') === 'company';
        $avSrc = $isCompanyPost && !empty($post['company_logo']) ? $post['company_logo'] : ($post['avatar'] ?? '');
        echo !empty($avSrc) ? '<img src="'.APP_URL.'/'.e($avSrc).'">' : initials($post['name'] ?? $post['author'] ?? 'Member');
      ?></div>
      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
          <strong><?= e($post['name'] ?? $post['author']) ?></strong>
          <?php
            $isOwnPost = (int)($post['user_id'] ?? 0) === (int)($_SESSION['user_id'] ?? -1);
            $isCompanyAuthor = ($post['author_role'] ?? '') === 'company';
            if (!$isOwnPost):
              if ($isCompanyAuthor):
                // Company post — show Follow/Following button
                $isFollowing = !empty($post['is_following_company']);
                if (!$isFollowing):
          ?>
                  <button class="post-follow-company-btn" data-user="<?= (int)($post['user_id'] ?? 0) ?>"
                    style="font-size:.72rem;padding:2px 10px;border-radius:14px;border:1px solid #0a66c2;color:#0a66c2;background:#fff;cursor:pointer;font-weight:600;white-space:nowrap;">
                    <i class="bi bi-plus-lg"></i> Follow
                  </button>
          <?php   else: ?>
                  <button class="post-follow-company-btn post-following-btn" data-user="<?= (int)($post['user_id'] ?? 0) ?>"
                    style="font-size:.72rem;padding:2px 10px;border-radius:14px;border:1px solid #057642;color:#057642;background:#fff;cursor:pointer;font-weight:600;white-space:nowrap;">
                    <i class="bi bi-check2"></i> Following
                  </button>
          <?php   endif;
              else:
                // User post — show Connect/Pending button only for non-company viewers
                $connStatus = $post['connection_status'] ?? null;
                $viewerIsCompany = (($currentUser['role'] ?? $_SESSION['role'] ?? '') === 'company');
                if (!$viewerIsCompany):
                  if ($connStatus === null):
          ?>
                  <button class="post-connect-btn" data-user="<?= (int)($post['user_id'] ?? 0) ?>"
                    style="font-size:.72rem;padding:2px 10px;border-radius:14px;border:1px solid #0a66c2;color:#0a66c2;background:#fff;cursor:pointer;font-weight:600;white-space:nowrap;">
                    <i class="bi bi-person-plus-fill"></i> Connect
                  </button>
          <?php   elseif ($connStatus === 'pending'): ?>
                  <span style="font-size:.72rem;color:#888;font-weight:600;"><i class="bi bi-clock"></i> Pending</span>
          <?php   elseif ($connStatus === 'accepted'): ?>
                  <button class="post-connected-btn" data-user="<?= (int)($post['user_id'] ?? 0) ?>"
                    style="font-size:.72rem;padding:2px 10px;border-radius:14px;border:1px solid #057642;color:#057642;background:#fff;cursor:pointer;font-weight:600;white-space:nowrap;">
                    <i class="bi bi-check2"></i> Connected
                  </button>
          <?php   endif;
                endif;
              endif;
            endif;
          ?>
        </div>
        <span><?= e($post['headline'] ?? 'LinkedIn member') ?> · <?= time_ago($post['created_at']) ?></span>
      </div>
    </div>
    <div style="position:relative;margin-left:auto;" class="post-menu-wrap">
      <button class="icon-only post-menu-btn" data-post="<?= (int)$post['id'] ?>"><i class="bi bi-three-dots"></i></button>
      <div class="post-menu-dropdown" id="post-menu-<?= (int)$post['id'] ?>" style="display:none;position:absolute;right:0;top:36px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:180px;z-index:99;">
        <?php if((int)($post['user_id'] ?? 0) === (int)($_SESSION['user_id'] ?? -1)): ?>
          <button class="post-delete-btn" data-post="<?= (int)$post['id'] ?>" style="display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;border:none;background:none;color:#cc1016;font-size:.9rem;font-weight:600;cursor:pointer;border-radius:8px;">
            <i class="bi bi-trash3"></i> Delete post
          </button>
        <?php else: ?>
          <button class="post-report-btn" data-post="<?= (int)$post['id'] ?>" style="display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;border:none;background:none;color:#333;font-size:.9rem;cursor:pointer;border-radius:8px;">
            <i class="bi bi-flag"></i> Report post
          </button>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <p class="post-body"><?= nl2br(e($post['content'])) ?></p>
  <?php if(!empty($post['media'])): ?>
    <div class="post-media">
      <?php if($post['media_type']==='image'): ?><img src="<?= APP_URL ?>/<?= e($post['media']) ?>" alt="Post media">
      <?php elseif($post['media_type']==='video'): ?><video src="<?= APP_URL ?>/<?= e($post['media']) ?>" controls></video>
      <?php else: ?><a class="doc-preview" href="<?= APP_URL ?>/<?= e($post['media']) ?>" target="_blank"><i class="bi bi-file-earmark-pdf"></i> View document</a><?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="post-counts" id="post-counts-<?= (int)$post['id'] ?>">
    <span id="like-count-<?= (int)$post['id'] ?>"><?= (int)$post['likes'] ?> likes</span>
    <span id="comment-count-<?= (int)$post['id'] ?>"><?= (int)$post['comments_count'] ?> comments</span>
  </div>
  <div class="post-actions">
    <button class="like-btn <?= !empty($post['liked'])?'active':'' ?>" data-id="<?= (int)$post['id'] ?>">
      <?php if(!empty($post['liked'])): ?><i class="bi bi-hand-thumbs-up-fill"></i> Unlike<?php else: ?><i class="bi bi-hand-thumbs-up"></i> Like<?php endif; ?>
    </button>
    <button class="comment-toggle" data-id="<?= (int)$post['id'] ?>"><i class="bi bi-chat-text"></i> Comment</button>
  </div>
  <!-- Comment box: hidden by default, shown on Comment button click -->
  <div class="comment-box" id="comments-<?= (int)$post['id'] ?>" style="display:none;">
    <form class="comment-form" data-id="<?= (int)$post['id'] ?>">
      <div class="comment-input-wrap">
        <input class="form-control" name="content" placeholder="Add a comment..." autocomplete="off">
        <button type="submit" class="comment-post-btn">Post</button>
      </div>
    </form>
    <div class="comments-list" id="comments-list-<?= (int)$post['id'] ?>" data-owner="<?= (int)($post['user_id'] ?? 0) === (int)($_SESSION['user_id'] ?? -1) ? '1' : '0' ?>" data-loaded="0"></div>
    <div class="see-all-wrap" id="see-all-<?= (int)$post['id'] ?>" style="display:none;padding:6px 0 0;">
      <button class="see-all-btn" data-id="<?= (int)$post['id'] ?>" style="background:none;border:none;color:#0a66c2;font-weight:600;font-size:.85rem;cursor:pointer;padding:0;">
        View all comments
      </button>
    </div>
  </div>
  <!-- Report Modal -->
  <div class="report-modal-overlay" id="report-modal-<?= (int)$post['id'] ?>" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1050;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:100%;max-width:460px;margin:16px;box-shadow:0 8px 32px rgba(0,0,0,.18);">
      <div style="padding:18px 20px;border-bottom:1px solid #e0e0e0;display:flex;align-items:center;justify-content:space-between;">
        <strong style="font-size:1rem;">Report this post</strong>
        <button class="report-modal-close" data-post="<?= (int)$post['id'] ?>" style="border:none;background:none;font-size:1.3rem;cursor:pointer;color:#666;line-height:1;">&times;</button>
      </div>
      <div style="padding:18px 20px;">
        <label style="font-size:.85rem;font-weight:600;color:#333;display:block;margin-bottom:8px;">Issue type</label>
        <select class="report-type-select form-select" style="width:100%;margin-bottom:14px;padding:8px 12px;border:1px solid #ccc;border-radius:6px;font-size:.9rem;">
          <option value="spam">Spam</option>
          <option value="offensive">Offensive / Hateful</option>
          <option value="harassment">Harassment</option>
          <option value="fake">False information</option>
          <option value="copyright">Copyright violation</option>
          <option value="other">Other</option>
        </select>
        <label style="font-size:.85rem;font-weight:600;color:#333;display:block;margin-bottom:8px;">Describe the issue</label>
        <textarea class="report-reason-input" placeholder="Tell us what's wrong with this post..." style="width:100%;border:1px solid #ccc;border-radius:6px;padding:10px 12px;font-size:.9rem;resize:vertical;min-height:90px;font-family:inherit;box-sizing:border-box;"></textarea>
        <div class="report-error" style="display:none;color:#cc1016;font-size:.82rem;margin-top:6px;"></div>
      </div>
      <div style="padding:12px 20px;border-top:1px solid #e0e0e0;display:flex;justify-content:flex-end;gap:10px;">
        <button class="report-modal-close" data-post="<?= (int)$post['id'] ?>" style="padding:8px 20px;border:1px solid #ccc;border-radius:20px;background:#fff;cursor:pointer;font-size:.9rem;">Cancel</button>
        <button class="report-submit-btn" data-post="<?= (int)$post['id'] ?>" style="padding:8px 20px;border:none;border-radius:20px;background:#0a66c2;color:#fff;font-weight:700;cursor:pointer;font-size:.9rem;">Submit report</button>
      </div>
    </div>
  </div>
</article>