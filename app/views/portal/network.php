<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<?php $isCompany = ($_SESSION['user']['role'] ?? '') === 'company'; ?>
<div class="container-xl two-col">

<?php if ($isCompany): ?>
  <!-- ── COMPANY NETWORK VIEW ── -->
  <!-- Left column: My Followers -->
  <div>
    <section class="li-card">
      <div class="card-title">My Followers <span class="badge bg-secondary ms-1"><?= count($followers) ?></span></div>
      <?php if (!$followers): ?>
        <p class="muted" style="padding:8px 16px 16px;">No followers yet. Share your profile to grow your audience.</p>
      <?php endif; ?>
      <?php foreach ($followers as $f): ?>
        <div class="people-row">
          <div class="mini-avatar" style="cursor:pointer;flex-shrink:0;<?= $f['role'] === 'company' ? 'border-radius:8px;' : '' ?>">
            <?php if (!empty($f['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($f['avatar']) ?>" alt="<?= e($f['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:<?= $f['role'] === 'company' ? '8px' : '50%' ?>;">
            <?php else: ?>
              <?= initials($f['name']) ?>
            <?php endif; ?>
          </div>
          <a href="<?= APP_URL ?>/<?= $f['role'] === 'company' ? 'view-company?id=' : 'view-profile?id=' ?><?= (int)$f['id'] ?>" style="flex:1;min-width:0;text-decoration:none;color:inherit;overflow:hidden;">
            <strong style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($f['name']) ?></strong>
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:#666;"><?= e($f['headline'] ?: ($f['role'] === 'company' ? 'Company' : 'LinkedIn member')) ?></span>
          </a>
          <a class="btn btn-outline-primary btn-sm" href="<?= APP_URL ?>/messages?with=<?= (int)$f['id'] ?>" style="flex-shrink:0;white-space:nowrap;">
            <i class="bi bi-chat-dots"></i> Message
          </a>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <!-- Right column: Suggestions for company -->
  <section class="li-card">
    <div class="card-title">People you may know</div>
    <div class="people-grid">
      <?php if (!$suggestions): ?>
        <p class="muted" style="padding:8px 16px 16px;grid-column:1/-1;">No suggestions at the moment.</p>
      <?php endif; ?>
      <?php foreach ($suggestions as $person): ?>
        <div class="person-card">
          <div class="cover"></div>
          <div class="avatar">
            <?php if (!empty($person['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($person['avatar']) ?>" alt="<?= e($person['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($person['name']) ?>
            <?php endif; ?>
          </div>
          <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$person['id'] ?>" style="text-decoration:none;color:inherit;">
            <strong style="display:block;padding:0 10px;"><?= e($person['name']) ?></strong>
            <span style="display:block;padding:0 10px;font-size:12px;color:#666;"><?= e($person['headline'] ?: 'LinkedIn member') ?></span>
            <?php if (!empty($person['mutual_count']) && $person['mutual_count'] > 0): ?>
              <span style="display:block;padding:2px 10px 0;font-size:11px;color:#0a66c2;"><i class="bi bi-people-fill"></i> <?= (int)$person['mutual_count'] ?> mutual connection<?= $person['mutual_count'] > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </a>
          <a class="btn btn-outline-primary btn-sm" style="margin-top:10px;" href="<?= APP_URL ?>/messages?with=<?= (int)$person['id'] ?>"><i class="bi bi-chat-dots"></i> Message</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

<?php else: ?>
  <!-- ── USER NETWORK VIEW (unchanged) ── -->
  <!-- Left column -->
  <div>
    <!-- Invitations / Requests -->
    <section class="li-card mb-3">
      <div class="card-title">Invitations <span class="badge bg-primary ms-1"><?= count($requests) ?></span></div>
      <?php if(!$requests): ?>
        <p class="muted" style="padding:8px 16px 16px;">No pending invitations.</p>
      <?php endif; ?>
      <?php foreach($requests as $req): ?>
        <div class="people-row">
          <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$req['requester_id'] ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;flex:1;min-width:0;overflow:hidden;">
            <div class="mini-avatar" style="cursor:pointer;flex-shrink:0;">
              <?php if(!empty($req['avatar'])): ?>
                <img src="<?= APP_URL.'/'.e($req['avatar']) ?>" alt="<?= e($req['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
              <?php else: ?>
                <?= initials($req['name']) ?>
              <?php endif; ?>
            </div>
            <div style="cursor:pointer;min-width:0;">
              <strong><?= e($req['name']) ?></strong>
              <span><?= e($req['headline'] ?: 'LinkedIn member') ?></span>
            </div>
          </a>
          <button class="btn btn-outline-secondary btn-sm connection-action" data-id="<?= (int)$req['requester_id'] ?>" data-status="rejected">Ignore</button>
          <button class="btn btn-primary btn-sm connection-action" data-id="<?= (int)$req['requester_id'] ?>" data-status="accepted">Accept</button>
        </div>
      <?php endforeach; ?>
    </section>

    <!-- My Connections -->
    <section class="li-card">
      <div class="card-title">My Connections <span class="badge bg-secondary ms-1"><?= count($connections) ?></span></div>
      <?php if(!$connections): ?><p class="muted" style="padding:8px 16px 16px;">You have no connections yet.</p><?php endif; ?>
      <?php foreach($connections as $conn): ?>
        <div class="people-row">
          <div class="mini-avatar" style="cursor:pointer;flex-shrink:0;">
            <?php if(!empty($conn['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($conn['avatar']) ?>" alt="<?= e($conn['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($conn['name']) ?>
            <?php endif; ?>
          </div>
          <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$conn['id'] ?>" style="flex:1;min-width:0;text-decoration:none;color:inherit;overflow:hidden;">
            <strong style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($conn['name']) ?></strong>
            <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:#666;"><?= e($conn['headline'] ?: 'LinkedIn member') ?></span>
          </a>
          <a class="btn btn-outline-primary btn-sm" href="<?= APP_URL ?>/messages?with=<?= (int)$conn['id'] ?>" style="flex-shrink:0;white-space:nowrap;">
            <i class="bi bi-chat-dots"></i> Message
          </a>
        </div>
      <?php endforeach; ?>
    </section>
  </div>

  <!-- Right column: Suggestions -->
  <section class="li-card">
    <div class="card-title">People you may know</div>
    <div class="people-grid">
      <?php if(!$suggestions): ?>
        <p class="muted" style="padding:8px 16px 16px;grid-column:1/-1;">No suggestions at the moment.</p>
      <?php endif; ?>
      <?php foreach($suggestions as $person): ?>
        <div class="person-card">
          <div class="cover"></div>
          <div class="avatar">
            <?php if(!empty($person['avatar'])): ?>
              <img src="<?= APP_URL.'/'.e($person['avatar']) ?>" alt="<?= e($person['name']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
            <?php else: ?>
              <?= initials($person['name']) ?>
            <?php endif; ?>
          </div>
          <a href="<?= APP_URL ?>/view-profile?id=<?= (int)$person['id'] ?>" style="text-decoration:none;color:inherit;">
            <strong style="display:block;padding:0 10px;"><?= e($person['name']) ?></strong>
            <span style="display:block;padding:0 10px;font-size:12px;color:#666;"><?= e($person['headline'] ?: 'LinkedIn member') ?></span>
            <?php if(!empty($person['mutual_count']) && $person['mutual_count'] > 0): ?>
              <span style="display:block;padding:2px 10px 0;font-size:11px;color:#0a66c2;"><i class="bi bi-people-fill"></i> <?= (int)$person['mutual_count'] ?> mutual connection<?= $person['mutual_count'] > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </a>
          <button class="btn btn-outline-primary connect-btn" style="margin-top:10px;" data-user="<?= (int)$person['id'] ?>">Connect</button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

<?php endif; ?>
</div>