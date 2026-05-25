<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-md">
  <section class="li-card">
    <div class="card-title">Notifications</div>
    <?php if(!$notifications): ?>
      <div style="text-align:center;padding:40px 20px;color:#888;">
        <i class="bi bi-bell-slash" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:10px;"></i>
        <strong style="display:block;font-size:.95rem;margin-bottom:4px;">You are all caught up!</strong>
        <p style="font-size:.83rem;margin:0;">No new notifications right now.</p>
      </div>
    <?php endif; ?>
    <?php foreach($notifications as $n): ?>
      <?php
        $icon = 'bi-bell'; $iconColor = '#0a66c2';
        if ($n['type'] === 'connection') { $icon = 'bi-person-plus-fill'; $iconColor = '#0a66c2'; }
        elseif ($n['type'] === 'application') { $icon = 'bi-briefcase-fill'; $iconColor = '#7b61ff'; }
        elseif ($n['type'] === 'like') { $icon = 'bi-hand-thumbs-up-fill'; $iconColor = '#0a66c2'; }
        elseif ($n['type'] === 'comment') { $icon = 'bi-chat-fill'; $iconColor = '#057642'; }

        // Build click URL
        $notifUrl = '#';
        if ($n['type'] === 'like' && $n['target_type'] === 'post' && $n['target_id']) {
            $notifUrl = APP_URL . '/home#post-' . (int)$n['target_id'];
        } elseif ($n['type'] === 'comment' && $n['target_type'] === 'post' && $n['target_id']) {
            $notifUrl = APP_URL . '/home#comment-section-' . (int)$n['target_id'];
        } elseif ($n['type'] === 'connection' && $n['target_type'] === 'user' && $n['target_id']) {
            $notifUrl = APP_URL . '/view-profile?id=' . (int)$n['target_id'];
        } elseif ($n['type'] === 'message') {
            $notifUrl = APP_URL . '/messages';
        } elseif ($n['type'] === 'follow' && $n['target_type'] === 'user' && $n['target_id']) {
            // Someone followed the company → redirect to that person's profile
            $notifUrl = APP_URL . '/view-profile?id=' . (int)$n['target_id'];
        } elseif ($n['type'] === 'application') {
            // Recruiter: "New application for X" (target_type=job) → applicants page, scroll to that job section
            // User: "Your application is now ..." → jobs-board
            $isRecruiterNotif = ($n['target_type'] === 'job');
            if ($isRecruiterNotif && $n['target_id']) {
                $notifUrl = APP_URL . '/applicants#job-' . (int)$n['target_id'];
            } elseif ($isRecruiterNotif) {
                $notifUrl = APP_URL . '/applicants';
            } else {
                $notifUrl = APP_URL . '/jobs-board';
            }
        }
      ?>
      <a href="<?= $notifUrl ?>" class="notification-row <?= empty($n['read_at'])?'unread':'' ?>" data-notif-id="<?= (int)$n['id'] ?>" style="text-decoration:none;color:inherit;display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid #f0f0f0;<?= empty($n['read_at']) ? 'background:#dbeafe;border-left:4px solid #0a66c2;' : 'background:#fff;border-left:4px solid transparent;' ?>">
        <?php if(!empty($n['sender_avatar'])): ?>
          <img src="<?= APP_URL.'/'.e($n['sender_avatar']) ?>" alt="<?= e($n['sender_name'] ?? '') ?>" style="width:42px;height:42px;border-radius:50%;object-fit:cover;flex-shrink:0;">
        <?php else: ?>
          <div style="background:<?= $iconColor ?>1a;color:<?= $iconColor ?>;width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.1rem;">
            <i class="bi <?= $icon ?>"></i>
          </div>
        <?php endif; ?>
        <div style="flex:1;">
          <?php if($n['type'] === 'like'): ?>
            <p style="margin:0;font-size:.9rem;"><strong><?= e($n['sender_name'] ?? 'Someone') ?></strong> <strong>liked</strong> your post.</p>
          <?php elseif($n['type'] === 'comment'): ?>
            <p style="margin:0;font-size:.9rem;"><strong><?= e($n['sender_name'] ?? 'Someone') ?></strong> <strong>commented</strong> on your post.</p>
          <?php elseif($n['type'] === 'application'): ?>
            <p style="margin:0;font-size:.9rem;">
              <?= e($n['message']) ?>
              <?php if(empty($n['read_at']) && $n['target_type'] === 'job'): ?>
                <span style="display:inline-block;margin-left:6px;font-size:.68rem;font-weight:700;background:#7b61ff;color:#fff;border-radius:10px;padding:1px 8px;vertical-align:middle;letter-spacing:.3px;">NEW</span>
              <?php endif; ?>
            </p>
          <?php elseif($n['type'] === 'connection' && !empty($n['sender_name'])): ?>
            <p style="margin:0;font-size:.9rem;">
              <strong><?= e($n['sender_name']) ?></strong>
              <?php if(strpos(strtolower($n['message']), 'accepted') !== false): ?> <strong>accepted</strong> your connection request.
              <?php else: ?> sent you a <strong>connection request</strong>.
              <?php endif; ?>
            </p>
          <?php else: ?>
            <p style="margin:0;font-size:.9rem;"><?= e($n['message']) ?></p>
          <?php endif; ?>
          <span class="notif-time" style="color:#888;font-size:.78rem;"><?= time_ago($n['created_at']) ?></span>
        </div>
        <?php if(empty($n['read_at'])): ?>
          <div class="unread-dot" style="width:10px;height:10px;background:#0a66c2;border-radius:50%;flex-shrink:0;margin-top:6px;"></div>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </section>
</div>
<script>
document.querySelectorAll('.notification-row.unread').forEach(function(row) {
  row.addEventListener('click', function() {
    var notifId = this.getAttribute('data-notif-id');
    // Turant white kar do
    this.style.background = '#fff';
    this.style.borderLeft = '4px solid transparent';
    var dot = this.querySelector('.unread-dot');
    if (dot) dot.remove();
    fetch('<?= APP_URL ?>/ajax?action=mark-one-notification-read', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token': window.CSRF_TOKEN},
      body: 'notif_id=' + notifId
    });
  });
});
</script>