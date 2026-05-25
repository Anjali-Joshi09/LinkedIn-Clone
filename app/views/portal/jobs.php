<?php
require_once APP_PATH . '/helpers/portal.php';
// $newJobIds is passed from the controller — IDs of jobs posted since the user's last visit
$newJobIds = $newJobIds ?? [];

// Sort jobs: new ones first, then featured, then rest (preserving existing order within groups)
usort($jobs, function($a, $b) use ($newJobIds) {
    $aNew = in_array($a['id'], $newJobIds) ? 1 : 0;
    $bNew = in_array($b['id'], $newJobIds) ? 1 : 0;
    if ($aNew !== $bNew) return $bNew - $aNew; // new jobs first
    return 0; // keep original order otherwise
});
?>

<div class="container-xl jobs-layout">
  <aside class="li-card">
    <form class="filter-stack" method="get">
      <input type="hidden" name="page" value="jobs-board">

      <label>Search</label>
      <input class="form-control" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Title, company, keyword">

      <label>Location</label>
      <input class="form-control" name="location" value="<?= e($_GET['location'] ?? '') ?>" placeholder="Remote, Noida, Pune">

      <label>Type</label>
      <select class="form-select" name="job_type">
        <option value="">Any</option>
        <?php foreach(['full_time','part_time','remote','contract','internship'] as $t): ?>
          <option value="<?= $t ?>" <?= ($_GET['job_type'] ?? '') === $t ? 'selected' : '' ?>>
            <?= e(ucwords(str_replace('_', ' ', $t))) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Experience</label>
      <select class="form-select" name="experience_level">
        <option value="">Any</option>
        <?php foreach(['entry','mid','senior','executive'] as $t): ?>
          <option value="<?= $t ?>" <?= ($_GET['experience_level'] ?? '') === $t ? 'selected' : '' ?>>
            <?= e(ucfirst($t)) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button class="btn btn-primary w-100 mt-2">
        <i class="bi bi-funnel-fill me-1"></i>Filter Jobs
      </button>

      <?php if(!empty($_GET['q']) || !empty($_GET['location']) || !empty($_GET['job_type']) || !empty($_GET['experience_level'])): ?>
        <a href="<?= APP_URL ?>/jobs-board" class="btn btn-outline-secondary w-100 mt-1" style="font-size:.85rem;">
          <i class="bi bi-x-circle me-1"></i>Clear Filters
        </a>
      <?php endif; ?>
    </form>
  </aside>

  <section>
    <div class="li-card mb-3">
      <div class="card-title d-flex align-items-center gap-2">
        <?= count($jobs) ?> jobs matching your search
        <?php if (!empty($newJobIds)): ?>
          <span class="badge bg-danger ms-1" style="font-size:.72rem;padding:4px 8px;border-radius:20px;animation:pulseBadge 2s ease-in-out infinite;">
            <?= count($newJobIds) ?> New
          </span>
        <?php endif; ?>
      </div>
    </div>

    <?php if(empty($jobs)): ?>
      <div class="li-card" style="text-align:center;padding:40px 20px;color:#666;">
        <i class="bi bi-briefcase" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:12px;"></i>
        <strong style="font-size:1rem;display:block;margin-bottom:6px;">No jobs found</strong>
        <p style="font-size:.88rem;margin:0;">Try adjusting your search or filters.</p>
      </div>
    <?php endif; ?>

    <?php foreach($jobs as $job): ?>
      <article class="li-card job-card <?= in_array((int)$job['id'], $newJobIds) ? 'job-card--new' : '' ?>" data-job-id="<?= (int)$job['id'] ?>" style="cursor:pointer;">
        <div class="job-logo">
          <?php if(!empty($job['logo'])): ?>
            <img src="<?= APP_URL . '/' . e($job['logo']) ?>" alt="<?= e($job['company']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
          <?php else: ?>
            <?= initials($job['company']) ?>
          <?php endif; ?>
        </div>

        <div class="job-content" style="flex:1;">
          <h2 style="font-size:1rem;font-weight:600;margin-bottom:2px;display:flex;align-items:center;gap:6px;">
            <?= e($job['title']) ?>
            <?php if(in_array((int)$job['id'], $newJobIds)): ?>
              <span class="new-job-badge">NEW</span>
            <?php endif; ?>
          </h2>

          <p style="color:#555;font-size:.875rem;margin-bottom:4px;">
            <?= e($job['company']) ?> · <?= e($job['location']) ?> ·
            <span class="badge bg-light text-dark border" style="font-size:.75rem;">
              <?= e(ucwords(str_replace('_', ' ', $job['job_type']))) ?>
            </span>
          </p>

          <p style="color:#777;font-size:.82rem;margin-bottom:6px;">
            <?= e(substr(strip_tags($job['description']), 0, 140)) ?>...
          </p>

          <span class="salary" style="font-size:.82rem;color:#0a66c2;font-weight:500;">
            <?= e(money_range($job)) ?>
          </span>
        </div>

        <div class="job-actions" style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;min-width:110px;">
          <button class="btn btn-sm <?= !empty($job['saved']) ? 'btn-primary' : 'btn-outline-secondary' ?> save-job-btn"
                  data-job="<?= (int)$job['id'] ?>"
                  title="<?= !empty($job['saved']) ? 'Saved' : 'Save job' ?>"
                  style="font-size:.78rem;padding:4px 10px;">
            <i class="bi <?= !empty($job['saved']) ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
            <?= !empty($job['saved']) ? ' Saved' : ' Save' ?>
          </button>

          <?php
            $isExpired = !empty($job['expires_at']) && strtotime($job['expires_at']) < time();
          ?>
          <?php if(!empty($job['applied'])): ?>
            <button class="btn btn-sm btn-success withdraw-btn"
                    data-job="<?= (int)$job['id'] ?>"
                    style="font-size:.78rem;padding:4px 12px;">
              <i class="bi bi-check-circle-fill me-1"></i>Applied
            </button>
          <?php elseif($isExpired): ?>
            <button class="btn btn-sm btn-secondary" disabled
                    style="font-size:.78rem;padding:4px 12px;opacity:.6;cursor:not-allowed;">
              <i class="bi bi-x-circle me-1"></i>Expired
            </button>
          <?php else: ?>
            <button class="btn btn-sm btn-primary apply-btn"
                    data-job="<?= (int)$job['id'] ?>"
                    style="font-size:.78rem;padding:4px 12px;">
              <i class="bi bi-lightning-fill me-1"></i>Easy Apply
            </button>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <aside>
    <section class="li-card mb-3">
      <div class="card-title">Applied Jobs</div>

      <?php if(!$applications): ?>
        <p class="muted" style="font-size:.85rem;padding:0 16px 12px;">No applications yet.</p>
      <?php endif; ?>

      <?php foreach($applications as $app): ?>
        <div class="status-row"
             data-applied-job="<?= (int)$app['job_id'] ?>"
             style="cursor:pointer;padding:10px 16px;border-bottom:1px solid #f0f0f0;"
             onclick="openAppliedJobDetail(<?= (int)$app['job_id'] ?>, '<?= e(addslashes($app['title'])) ?>', '<?= e(addslashes($app['company'])) ?>', '<?= e(addslashes($app['status'])) ?>')">
          <strong style="display:block;font-size:.85rem;"><?= e($app['title']) ?></strong>
          <span style="font-size:.78rem;color:#666;"><?= e($app['company']) ?></span>
        </div>
      <?php endforeach; ?>
    </section>

    <section class="li-card">
      <div class="card-title">Saved Jobs</div>

      <?php if(!$savedJobs): ?>
        <p class="muted" style="font-size:.85rem;padding:0 16px 12px;">No saved jobs.</p>
      <?php endif; ?>

      <?php foreach($savedJobs as $job): ?>
        <div class="status-row" style="cursor:pointer;padding:10px 16px;border-bottom:1px solid #f0f0f0;" onclick="openJobDetail(<?= (int)$job['id'] ?>)">
          <strong style="display:block;font-size:.85rem;"><?= e($job['title']) ?></strong>
          <span style="font-size:.78rem;color:#666;"><?= e($job['company']) ?></span>
        </div>
      <?php endforeach; ?>
    </section>
  </aside>
</div>

<div class="modal fade" id="jobDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div class="d-flex align-items-center gap-3" id="jobDetailHeader" style="flex:1;"></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="jobDetailBody"></div>
      <div class="modal-footer border-0 pt-0" id="jobDetailFooter"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="appliedJobDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div class="d-flex align-items-center gap-3" id="appliedJobDetailHeader" style="flex:1;"></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="appliedJobDetailBody"></div>

      <div class="modal-footer border-0 pt-0" id="appliedJobDetailFooter"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="applyModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" id="applyForm" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-lightning-fill text-primary me-2"></i>Easy Apply
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <input type="hidden" name="job_id" id="applyJobId">

        <div class="mb-3">
          <label class="form-label fw-semibold">Cover Note</label>
          <textarea class="form-control" name="cover_letter" rows="4" placeholder="Write a brief note to the recruiter..."></textarea>
        </div>

        <div class="mb-2">
          <label class="form-label fw-semibold">Resume (PDF) <span class="text-danger">*</span></label>
          <input class="form-control form-control-sm" type="file" name="resume" accept="application/pdf" required>
          <div class="form-text text-danger" id="resumeError" style="display:none;">Please upload your resume (PDF) before submitting.</div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm px-4">
          <i class="bi bi-send me-1"></i>Submit
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* NEW badge on individual job cards */
.new-job-badge {
  display: inline-block;
  background: #dc3545;
  color: #fff;
  font-size: .62rem;
  font-weight: 700;
  letter-spacing: .04em;
  padding: 2px 7px;
  border-radius: 20px;
  vertical-align: middle;
  text-transform: uppercase;
  flex-shrink: 0;
}

/* Subtle green left-border highlight for new job cards */
.job-card--new {
  border-left: 3px solid #198754 !important;
}

/* Pulsing animation for the count badge in the header */
@keyframes pulseBadge {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: .75; transform: scale(1.08); }
}

</style>

<script>
// IDs of jobs that are "new" as of page load (passed from PHP)
window.newJobIds = <?= json_encode(array_map('intval', $newJobIds), JSON_HEX_TAG) ?>;

// Sync URL search param to global search bar so user sees what they searched
(function syncSearchBar() {
  var q = <?= json_encode($_GET['q'] ?? '') ?>;
  if (q) {
    var bar = document.getElementById('globalSearch');
    if (bar && !bar.value) bar.value = q;
  }
})();

// ── Silent live polling — auto-injects new jobs to top every 30s ─────────
(function startJobPoller() {
  const POLL_INTERVAL = 30_000;

  async function pollNewJobs() {
    try {
      const res  = await fetch(window.APP_URL + '/ajax?action=new-jobs', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();
      if (!data.ok || !Array.isArray(data.jobs)) return;

      // Find jobs not yet shown on the page
      const shownIds     = new Set(Array.from(document.querySelectorAll('.job-card[data-job-id]')).map(el => parseInt(el.dataset.jobId)));
      const brandNewJobs = data.jobs.filter(j => !shownIds.has(j.id));
      if (brandNewJobs.length === 0) return;

      // Inject each new job silently at the top
      const section = document.querySelector('.jobs-layout > section');
      const emptyCard = section ? section.querySelector('.li-card[style*="text-align:center"]') : null;
      if (emptyCard) emptyCard.remove();

      brandNewJobs.forEach(job => {
        const html = buildJobCardHTML(job);
        const firstCard = section ? section.querySelector('.job-card') : null;
        if (firstCard) firstCard.insertAdjacentHTML('beforebegin', html);
        else if (section) section.insertAdjacentHTML('beforeend', html);
        window.jobsData.unshift(job);
        window.newJobIds.push(job.id);
      });

      // Update the job count label in header
      const countEl = section ? section.querySelector('.li-card:first-child .card-title') : null;
      if (countEl) {
        const total    = document.querySelectorAll('.job-card').length;
        const newCount = window.newJobIds.length;
        countEl.innerHTML = total + ' jobs matching your search'
          + (newCount > 0 ? ' <span class="badge bg-danger ms-1" style="font-size:.72rem;padding:4px 8px;border-radius:20px;">' + newCount + ' New</span>' : '');
      }

      // Update Jobs nav badge
      updateJobsNavBadge(window.newJobIds.length);

    } catch (_) { /* silently ignore network errors */ }
  }

  function updateJobsNavBadge(count) {
    const link = document.getElementById('jobsNavLink');
    if (!link) return;
    let badge = document.getElementById('jobsNavBadge');
    if (count > 0) {
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'nav-badge';
        badge.id = 'jobsNavBadge';
        link.appendChild(badge);
      }
      badge.textContent = count;
    } else if (badge) {
      badge.remove();
    }
  }

  setInterval(pollNewJobs, POLL_INTERVAL);
})();

// ── Global data used by modal functions ──────────────────────────────────
window.jobsData         = <?= json_encode(array_values($jobs), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.applicationsData = <?= json_encode(array_values($applications), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function escJobHtml(value) {
  return String(value || '').replace(/[&<>"']/g, function (c) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[c];
  });
}

function openJobDetail(jobId) {
  const job = window.jobsData.find(j => j.id == jobId);
  if (!job) return;

  const applied = job.applied == 1 || job.applied === true;
  const company = escJobHtml(job.company || '');
  const title = escJobHtml(job.title || '');
  const location = escJobHtml(job.location || '');
  const jobType = escJobHtml(String(job.job_type || '').replace(/_/g, ' '));
  const experience = escJobHtml(job.experience_level || '');
  const description = job.description || 'No description provided.';

  document.getElementById('jobDetailHeader').innerHTML = `
    <div style="background:#e8f0fe;color:#0a66c2;width:56px;height:56px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem;flex-shrink:0;">
      ${company.substring(0, 2).toUpperCase()}
    </div>
    <div>
      <h5 style="margin:0;font-weight:700;">${title}</h5>
      <p style="margin:0;color:#555;font-size:.9rem;">${company} · ${location}</p>
      <span class="badge bg-light text-dark border" style="font-size:.75rem;">${jobType}</span>
      ${experience ? `<span class="badge bg-light text-dark border ms-1" style="font-size:.75rem;">${experience}</span>` : ''}
    </div>`;

  document.getElementById('jobDetailBody').innerHTML = `
    <div style="border-bottom:1px solid #eee;padding-bottom:12px;margin-bottom:12px;">
      <span style="color:#0a66c2;font-weight:600;font-size:.95rem;">
        ${job.salary_min ? '₹' + job.salary_min + ' - ₹' + job.salary_max : 'Salary not disclosed'}
      </span>
    </div>
    <h6 style="font-weight:700;margin-bottom:8px;">Job Description</h6>
    <div style="color:#444;font-size:.9rem;line-height:1.7;">${description}</div>`;

  document.getElementById('jobDetailFooter').innerHTML = applied
    ? `<button type="button" class="btn btn-success withdraw-btn" data-job="${job.id}">
         <i class="bi bi-check-circle-fill me-1"></i>Applied
       </button>`
    : (job.expires_at && new Date(job.expires_at) < new Date()
        ? `<button type="button" class="btn btn-secondary" disabled style="opacity:.6;cursor:not-allowed;">
             <i class="bi bi-x-circle me-1"></i>Expired
           </button>`
        : `<button type="button" class="btn btn-primary apply-btn" data-job="${job.id}">
             <i class="bi bi-lightning-fill me-1"></i>Easy Apply
           </button>`);

  new bootstrap.Modal(document.getElementById('jobDetailModal')).show();
}

function openAppliedJobDetail(jobId, title, company, status) {
  const job = window.jobsData ? window.jobsData.find(j => j.id == jobId) : null;

  const cleanTitle = escJobHtml(job ? job.title : title);
  const cleanCompany = escJobHtml(job ? job.company : company);
  const cleanStatus = escJobHtml(status || 'applied');
  const location = job ? escJobHtml(job.location || '') : '';
  const jobType = job ? escJobHtml(String(job.job_type || '').replace(/_/g, ' ')) : '';
  const experience = job ? escJobHtml(job.experience_level || '') : '';
  const description = job ? (job.description || 'No description provided.') : 'No description provided.';
  const statusColor = cleanStatus === 'accepted' ? '#198754' : (cleanStatus === 'rejected' ? '#dc3545' : '#f0ad4e');

  document.getElementById('appliedJobDetailHeader').innerHTML = `
    <div style="background:#e8f0fe;color:#0a66c2;width:56px;height:56px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem;flex-shrink:0;">
      ${cleanCompany.substring(0, 2).toUpperCase()}
    </div>
    <div>
      <h5 style="margin:0;font-weight:700;">${cleanTitle}</h5>
      <p style="margin:0;color:#555;font-size:.9rem;">${cleanCompany}${location ? ' · ' + location : ''}</p>
      ${jobType ? `<span class="badge bg-light text-dark border" style="font-size:.75rem;">${jobType}</span>` : ''}
      ${experience ? `<span class="badge bg-light text-dark border ms-1" style="font-size:.75rem;">${experience}</span>` : ''}
    </div>`;

  document.getElementById('appliedJobDetailBody').innerHTML = `
    <div style="border-bottom:1px solid #eee;padding-bottom:12px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
      <span style="color:#0a66c2;font-weight:600;font-size:.95rem;">
        ${job && job.salary_min ? '₹' + job.salary_min + ' - ₹' + job.salary_max : 'Salary not disclosed'}
      </span>
      <span style="background:${statusColor};color:#fff;padding:4px 14px;border-radius:20px;font-size:.82rem;font-weight:600;">
        ${cleanStatus.charAt(0).toUpperCase() + cleanStatus.slice(1)}
      </span>
    </div>
    <h6 style="font-weight:700;margin-bottom:8px;">Job Description</h6>
    <div style="color:#444;font-size:.9rem;line-height:1.7;">${description}</div>`;

  document.getElementById('appliedJobDetailFooter').innerHTML = '';

  new bootstrap.Modal(document.getElementById('appliedJobDetailModal')).show();
}

function triggerApply(jobId) {
  document.getElementById('applyJobId').value = jobId;
  new bootstrap.Modal(document.getElementById('applyModal')).show();
}
</script> 