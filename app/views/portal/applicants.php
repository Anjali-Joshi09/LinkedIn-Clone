<?php require_once APP_PATH . '/helpers/portal.php';
$stages = ['applied','reviewing','shortlisted','interview','hired','rejected'];
$stageMeta = [
  'applied'     => ['label'=>'Applied',     'color'=>'#0a66c2', 'icon'=>'bi-file-earmark-person'],
  'reviewing'   => ['label'=>'Reviewing',   'color'=>'#e7a33e', 'icon'=>'bi-eye'],
  'shortlisted' => ['label'=>'Shortlisted', 'color'=>'#8f5849', 'icon'=>'bi-star-fill'],
  'interview'   => ['label'=>'Interview',   'color'=>'#6f42c1', 'icon'=>'bi-camera-video'],
  'hired'       => ['label'=>'Hired',       'color'=>'#057642', 'icon'=>'bi-check-circle-fill'],
  'rejected'    => ['label'=>'Rejected',    'color'=>'#cc1016', 'icon'=>'bi-x-circle'],
];
?>
<div class="container-xl">

  <!-- Header -->
  <div class="li-card mb-3" style="display:flex;align-items:center;justify-content:space-between;padding:16px 22px;">
    <div>
      <h2 style="font-size:1.1rem;font-weight:800;margin:0;"><i class="bi bi-people-fill me-2" style="color:var(--li-blue);"></i>Applicant Tracking</h2>
      <p style="font-size:.8rem;color:var(--muted);margin:3px 0 0;">Click a candidate's name to view their full profile. Use the dropdown to update their stage.</p>
    </div>
    <span style="font-size:.85rem;font-weight:700;color:var(--li-blue);background:var(--soft);padding:6px 14px;border-radius:20px;">
      <?= count($applicants) ?> applicant<?= count($applicants) !== 1 ? 's' : '' ?>
    </span>
  </div>

  <!-- Stage summary -->
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <?php foreach ($stages as $stage):
      $count = count(array_filter($applicants, fn($a) => $a['status'] === $stage)); ?>
    <div style="display:flex;align-items:center;gap:6px;background:#fff;border:1px solid var(--line);border-radius:20px;padding:5px 13px;font-size:.78rem;color:#555;">
      <i class="bi <?= $stageMeta[$stage]['icon'] ?>" style="color:<?= $stageMeta[$stage]['color'] ?>;"></i>
      <span><?= $stageMeta[$stage]['label'] ?></span>
      <strong style="color:<?= $stageMeta[$stage]['color'] ?>;" data-stage-summary="<?= $stage ?>"><?= $count ?></strong>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Kanban Board -->
  <div class="ats-board">
    <?php foreach ($stages as $stage):
      $stageApplicants = array_filter($applicants, fn($a) => $a['status'] === $stage); ?>
    <section class="ats-col" data-stage="<?= $stage ?>">
      <!-- Column header -->
      <div class="ats-col-header" style="--stage-color:<?= $stageMeta[$stage]['color'] ?>;">
        <i class="bi <?= $stageMeta[$stage]['icon'] ?>"></i>
        <h2><?= $stageMeta[$stage]['label'] ?></h2>
        <span class="ats-col-count"><?= count($stageApplicants) ?></span>
      </div>

      <div class="ats-cards-wrap">
        <?php if (empty($stageApplicants)): ?>
          <div class="ats-empty-col">
            <i class="bi bi-inbox"></i>
            <span>Empty</span>
          </div>
        <?php endif; ?>

        <?php foreach ($stageApplicants as $a):
          $profileUrl = APP_URL . '/view-profile?id=' . (int)($a['user_id'] ?? $a['id']);
        ?>
        <article class="li-card candidate" data-id="<?= (int)$a['id'] ?>" data-job-id="<?= (int)($a['job_id'] ?? 0) ?>">

          <!-- Top: avatar + name (both clickable) -->
          <div class="candidate-top">
            <a href="<?= $profileUrl ?>" style="text-decoration:none;flex-shrink:0;" title="View profile">
              <div class="mini-avatar candidate-avatar">
                <?= !empty($a['avatar']) ? '<img src="'.APP_URL.'/'.e($a['avatar']).'">' : initials($a['name']) ?>
              </div>
            </a>
            <div class="candidate-name-wrap" style="min-width:0;flex:1;">
              <a href="<?= $profileUrl ?>" style="text-decoration:none;" title="View profile">
                <strong class="candidate-name" style="color:var(--li-blue);"><?= e($a['name']) ?></strong>
              </a>
              <?php if (!empty($a['headline'])): ?>
              <span class="candidate-headline"><?= e($a['headline']) ?></span>
              <?php endif; ?>
            </div>
          </div>

          <!-- Applied job -->
          <?php if (!empty($a['title'])): ?>
          <div class="candidate-job-tag">
            <i class="bi bi-briefcase me-1"></i><?= e($a['title']) ?>
          </div>
          <?php endif; ?>

          <!-- Location -->
          <?php if (!empty($a['location'])): ?>
          <div style="font-size:.72rem;color:var(--muted);"><i class="bi bi-geo-alt me-1"></i><?= e($a['location']) ?></div>
          <?php endif; ?>

          <!-- Applied date -->
          <?php if (!empty($a['applied_at'])): ?>
          <div class="candidate-date"><i class="bi bi-clock me-1"></i><?= e(date('M j, Y', strtotime($a['applied_at']))) ?></div>
          <?php endif; ?>

          <!-- Cover letter preview -->
          <?php if (!empty($a['cover_letter'])): ?>
          <div class="candidate-cover" title="<?= htmlspecialchars($a['cover_letter'], ENT_QUOTES) ?>">
            <i class="bi bi-chat-quote me-1"></i><?= e(mb_strimwidth($a['cover_letter'], 0, 75, '…')) ?>
          </div>
          <?php endif; ?>

          <!-- Actions: status dropdown + resume + profile link -->
          <div class="candidate-actions">
            <select class="form-select form-select-sm app-status" data-id="<?= (int)$a['id'] ?>">
              <?php foreach ($stages as $s): ?>
              <option value="<?= $s ?>" <?= $s === $a['status'] ? 'selected' : '' ?>><?= $stageMeta[$s]['label'] ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($a['resume'])): ?>
            <a href="<?= APP_URL ?>/<?= e($a['resume']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary resume-btn" title="Download Resume">
              <i class="bi bi-file-earmark-pdf"></i>
            </a>
            <?php endif; ?>
            <a href="<?= $profileUrl ?>" class="btn btn-sm btn-outline-primary resume-btn" title="View Full Profile">
              <i class="bi bi-person-lines-fill"></i>
            </a>
          </div>

        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>


<script>
(function() {
  var hash = window.location.hash; // e.g. #job-5
  if (!hash || !hash.startsWith('#job-')) return;
  var jobId = parseInt(hash.replace('#job-', ''), 10);
  if (!jobId) return;
  // Find all cards for this job
  var cards = document.querySelectorAll('.candidate[data-job-id="' + jobId + '"]');
  if (!cards.length) return;
  // Scroll to first matching card
  setTimeout(function() {
    cards[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    // Highlight all matching cards
    cards.forEach(function(card) {
      card.style.outline = '2.5px solid #0a66c2';
      card.style.boxShadow = '0 0 0 4px rgba(10,102,194,.15)';
      setTimeout(function() {
        card.style.outline = '';
        card.style.boxShadow = '';
        card.style.transition = 'outline .6s, box-shadow .6s';
      }, 3000);
    });
  }, 300);
})();
</script>
</div>