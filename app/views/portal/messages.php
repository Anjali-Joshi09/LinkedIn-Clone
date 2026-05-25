<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl messaging-shell">
  <aside class="li-card conversation-list">
    <div class="card-title">Messaging</div>

    <!-- Existing conversations -->
    <?php foreach($conversations as $c): ?>
      <a class="conversation <?= $with===$c['id']?'active':'' ?>" href="<?= APP_URL ?>/messages?with=<?= (int)$c['id'] ?>" style="position:relative;">
        <div class="mini-avatar">
          <?php if(!empty($c['avatar'])): ?>
            <img src="<?= APP_URL.'/'.e($c['avatar']) ?>" alt="<?= e($c['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
          <?php else: ?>
            <?= initials($c['name']) ?>
          <?php endif; ?>
        </div>
        <div style="flex:1;min-width:0;">
          <strong><?= e($c['name']) ?></strong>
          <span><?= e($c['last_message'] ?: $c['headline']) ?></span>
        </div>
        <?php if(!empty($c['unread_count']) && (int)$c['unread_count'] > 0): ?>
          <span style="background:#cc1016;color:#fff;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:9px;display:flex;align-items:center;justify-content:center;padding:0 4px;flex-shrink:0;">
            <?= (int)$c['unread_count'] ?>
          </span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>

    <!-- My Connections to start new chat -->
    <?php if(!empty($connections)): ?>
      <div style="padding:10px 12px 4px;font-size:.78rem;color:#666;font-weight:600;text-transform:uppercase;letter-spacing:.05em;border-top:1px solid #eee;margin-top:6px;">
        My Connections
      </div>
      <?php foreach($connections as $conn): ?>
        <?php $alreadyInConv = false;
          foreach($conversations as $c) { if($c['id'] == $conn['id']) { $alreadyInConv = true; break; } }
          if($alreadyInConv) continue; ?>
        <a class="conversation <?= $with===$conn['id']?'active':'' ?>" href="<?= APP_URL ?>/messages?with=<?= (int)$conn['id'] ?>">
          <div class="mini-avatar">
            <?php if(!empty($conn['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($conn['avatar']) ?>" alt="<?= e($conn['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($conn['name']) ?>
            <?php endif; ?>
          </div>
          <div>
            <strong><?= e($conn['name']) ?></strong>
            <span style="color:#0a66c2;font-size:.78rem;"><?= e($conn['headline'] ?: 'Start a conversation') ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if(!$conversations && empty($connections)): ?>
      <p class="muted" style="padding:12px;font-size:.85rem;">No conversations yet. Connect with people to start messaging.</p>
    <?php endif; ?>
  </aside>

  <section class="li-card chat-panel">
    <?php if(!$with): ?>
      <div class="empty-chat">
        <i class="bi bi-chat-square-dots"></i>
        <h2>Select a conversation</h2>
        <p>Choose from your connections or existing conversations on the left to start chatting.</p>
      </div>
    <?php else: ?>
      <?php
        // Prefer chatWithUser (passed from controller) — always available even for new chats
        $chatPerson = $chatWithUser ?? null;
        if (!$chatPerson) {
          foreach($conversations as $c) { if($c['id'] == $with) { $chatPerson = $c; break; } }
        }
        if (!$chatPerson) {
          foreach($connections as $c) { if($c['id'] == $with) { $chatPerson = $c; break; } }
        }
      ?>
      <!-- Chat Header with person's name and profile photo — shown immediately on selection -->
      <div class="chat-header" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #eee;background:#fff;">
        <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$with ?>" style="display:flex;align-items:center;gap:12px;text-decoration:none;color:inherit;">
          <div class="mini-avatar" style="cursor:pointer;">
            <?php if(!empty($chatPerson['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($chatPerson['avatar']) ?>" alt="<?= e($chatPerson['name'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($chatPerson['name'] ?? 'U') ?>
            <?php endif; ?>
          </div>
          <div>
            <strong style="display:block;font-size:.95rem;color:#000;"><?= e($chatPerson['name'] ?? 'User') ?></strong>
            <span style="font-size:.78rem;color:#666;"><?= e($chatPerson['headline'] ?? ($chatPerson['role'] === 'company' ? 'Company' : 'LinkedIn member')) ?></span>
          </div>
        </a>
      </div>

      <div id="chatMessages" data-with="<?= (int)$with ?>">
        <?php foreach($messages as $m): ?>
          <div class="chat-msg <?= (int)$m['sender_id']===(int)$_SESSION['user_id']?'mine':'' ?>">
            <p><?= e($m['body']) ?></p>
            <span><?= time_ago($m['created_at']) ?> <?= $m['seen_at']?'· Seen':'' ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <form id="messageForm" class="message-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <input type="hidden" name="receiver_id" value="<?= (int)$with ?>">
        <!-- Emoji picker trigger -->
        <div class="emoji-picker-wrap" style="position:relative;">
          <button type="button" class="icon-pill" id="emojiToggle" title="Emoji"><i class="bi bi-emoji-smile"></i></button>
          <div id="emojiPicker" style="display:none;position:absolute;bottom:48px;left:0;background:#fff;border:1px solid #ddd;border-radius:12px;padding:10px;box-shadow:0 8px 24px rgba(0,0,0,.15);width:280px;z-index:100;">
            <div style="display:flex;flex-wrap:wrap;gap:6px;max-height:180px;overflow-y:auto;font-size:1.4rem;">
              <?php $emojis = ['😀','😂','😍','🥰','😊','😎','🤔','😅','😭','🥺','😡','🤩','👍','👎','❤️','🔥','🎉','✅','💯','🙏','👏','💪','🤝','😮','😴','🤣','😏','😒','🥳','🤗','😬','🤦','🤷','💀','👀','🫡','😤','🤪','🥸','😙']; foreach($emojis as $em): ?>
                <span class="emoji-opt" style="cursor:pointer;padding:4px;border-radius:6px;transition:background .15s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background=''" data-emoji="<?= $em ?>"><?= $em ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <input name="body" class="form-control" id="msgBody" placeholder="Write a message..." autocomplete="off">
        <input type="file" name="attachment" hidden id="msgFile">
        <label for="msgFile" class="icon-pill"><i class="bi bi-paperclip"></i></label>
        <button class="btn btn-primary"><i class="bi bi-send"></i></button>
      </form>
    <?php endif; ?>
  </section>
</div>

<script>
// Emoji picker
(function(){
  const toggle = document.getElementById('emojiToggle');
  const picker = document.getElementById('emojiPicker');
  const msgBody = document.getElementById('msgBody');
  if (!toggle || !picker) return;
  toggle.addEventListener('click', function(e){
    e.stopPropagation();
    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
  });
  document.querySelectorAll('.emoji-opt').forEach(function(el){
    el.addEventListener('click', function(){
      if (msgBody) {
        const pos = msgBody.selectionStart;
        const val = msgBody.value;
        msgBody.value = val.slice(0, pos) + el.dataset.emoji + val.slice(pos);
        msgBody.focus();
        msgBody.selectionStart = msgBody.selectionEnd = pos + el.dataset.emoji.length;
      }
      picker.style.display = 'none';
    });
  });
  document.addEventListener('click', function(){ picker.style.display = 'none'; });
})();


</script>