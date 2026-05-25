<?php require_once APP_PATH . '/helpers/portal.php'; ?>
<div class="container-xl li-grid">

  <!-- LEFT SIDEBAR -->
  <aside class="li-left">
    <section class="li-card profile-mini">
      <div class="cover company-banner" style="height:62px;<?= !empty($company['banner']) ? 'background-image:url(\''.APP_URL.'/'.e($company['banner']).'\')'  : '' ?>"></div>
      <div class="avatar" style="border-radius:8px;">
        <?= !empty($company['logo']) ? '<img src="'.APP_URL.'/'.e($company['logo']).'">' : initials($company['name']) ?>
      </div>
      <h2><?= e($company['name']) ?></h2>
      <p><?= e($company['industry'] ?: 'Add your industry') ?></p>
      <a href="<?= APP_URL ?>/company-profile">Edit company profile</a>
    </section>
    <section class="li-card compact-list">
      <a href="<?= APP_URL ?>/recruiter-dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="<?= APP_URL ?>/applicants"><i class="bi bi-people me-2"></i>Applications</a>
      <a href="<?= APP_URL ?>/portal-notifications"><i class="bi bi-bell me-2"></i>Notifications</a>
    </section>
    <section class="li-card" style="padding:14px 16px;">
      <div style="font-weight:700;font-size:.88rem;margin-bottom:8px;">Job Stats</div>
      <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f0f0;">
        <span style="font-size:.82rem;color:#555;">Total Jobs</span>
        <strong style="color:#0a66c2;"><?= count($jobs) ?></strong>
      </div>
      <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f0f0f0;">
        <span style="font-size:.82rem;color:#555;">Active</span>
        <strong style="color:#057642;"><?= count(array_filter($jobs, fn($j) => in_array($j['status'], ['approved','pending']))) ?></strong>
      </div>
      <div style="display:flex;justify-content:space-between;padding:6px 0;">
        <span style="font-size:.82rem;color:#555;">Applications</span>
        <strong style="color:#e7a33e;"><?= array_sum(array_column($jobs, 'applications_count')) ?></strong>
      </div>
    </section>
  </aside>

  <!-- CENTER — Job Form + Job List -->
  <section style="display:flex;flex-direction:column;gap:12px;">

    <!-- Post/Edit Job Form -->
    <div class="li-card" id="jobFormCard">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 12px;">
        <div style="font-weight:700;font-size:1rem;" id="jobFormTitle"><i class="bi bi-plus-circle me-2" style="color:#0a66c2;"></i>Post a New Job</div>
      </div>
      <form method="post" class="job-form" id="jobForm" style="padding:0 18px 18px;">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <input type="hidden" name="id" id="jobId" value="">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Job Title <span style="color:#cc1016;">*</span></label>
            <input class="form-control" name="title" id="fTitle" placeholder="e.g. Senior PHP Developer" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Company Name</label>
            <input class="form-control" name="company_display" id="fCompany" value="<?= e($company['name']) ?>" readonly style="background:#f8f9fa;">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Location</label>
            <input class="form-control" name="location" id="fLocation" placeholder="e.g. Remote, New York, Hybrid">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Application Deadline</label>
            <input class="form-control" type="date" name="expires_at" id="fExpires">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Job Type</label>
            <select class="form-select" name="job_type" id="fJobType">
              <option value="full_time">Full Time</option>
              <option value="part_time">Part Time</option>
              <option value="remote">Remote</option>
              <option value="contract">Contract</option>
              <option value="internship">Internship</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Experience Level</label>
            <select class="form-select" name="experience_level" id="fExp">
              <option value="entry">Entry Level</option>
              <option value="mid">Mid Level</option>
              <option value="senior">Senior Level</option>
              <option value="executive">Executive</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Salary Range</label>
            <div style="display:flex;align-items:center;gap:6px;">
              <input class="form-control" type="number" name="salary_min" id="fSalMin" placeholder="Min" style="min-width:0;flex:1;">
              <span style="color:#888;font-weight:600;white-space:nowrap;">–</span>
              <input class="form-control" type="number" name="salary_max" id="fSalMax" placeholder="Max" style="min-width:0;flex:1;">
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Required Skills</label>
            <input class="form-control" name="requirements" id="fReq" placeholder="e.g. PHP, MySQL, Bootstrap, REST APIs">
          </div>
          <div class="col-12">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Job Description <span style="color:#cc1016;">*</span></label>
            <textarea class="form-control" name="description" id="fDesc" rows="4" placeholder="Describe the role, responsibilities, and what makes this opportunity exciting…" required></textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-bold" style="font-size:.82rem;color:#555;">Benefits</label>
            <textarea class="form-control" name="benefits" id="fBenefits" rows="2" placeholder="e.g. Health insurance, remote flexibility, learning budget…"></textarea>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
          <button type="submit" class="btn btn-primary px-4" style="border-radius:20px;font-weight:700;" id="jobSubmitBtn">
            <i class="bi bi-send me-1"></i>Publish Job
          </button>
          <button type="button" class="btn btn-outline-secondary px-4" style="border-radius:20px;font-weight:600;display:none;" id="cancelEditBtn" onclick="resetJobForm()">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Your Jobs List -->
    <div class="li-card">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px 10px;">
        <span style="font-weight:700;font-size:.95rem;"><i class="bi bi-briefcase me-2" style="color:#0a66c2;"></i>Your Jobs</span>
        <span style="font-size:.82rem;color:#888;"><?= count($jobs) ?> total</span>
      </div>
      <?php if(empty($jobs)): ?>
        <div style="text-align:center;padding:40px 20px;color:#888;">
          <i class="bi bi-briefcase-fill" style="font-size:2.5rem;opacity:.25;display:block;margin-bottom:10px;"></i>
          <strong style="display:block;font-size:.95rem;margin-bottom:4px;">No jobs posted yet</strong>
          <p style="font-size:.83rem;margin:0;">Use the form above to post your first job.</p>
        </div>
      <?php else: ?>
        <?php foreach($jobs as $job): ?>
        <div id="job-<?= (int)$job['id'] ?>" style="padding:16px 18px;border-top:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;gap:14px;" data-job-id="<?= (int)$job['id'] ?>">
          <div style="display:flex;gap:14px;align-items:flex-start;flex:1;min-width:0;">
            <div class="job-logo" style="flex-shrink:0;">
              <?= !empty($company['logo']) ? '<img src="'.APP_URL.'/'.e($company['logo']).'" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">' : '<span style="font-weight:800;font-size:1rem;">'.initials($company['name']).'</span>' ?>
            </div>
            <div class="job-content" style="flex:1;min-width:0;">
              <div style="display:flex;align-items:center;gap:6px;flex-wrap:nowrap;margin-bottom:2px;">
                <h2 style="font-size:.95rem;margin:0;"><?= e($job['title']) ?></h2>
                <span style="font-size:.73rem;color:#aaa;">· <?= time_ago($job['created_at']) ?></span>
              </div>
              <p style="font-size:.82rem;color:#555;margin:0 0 4px;"><?= e($job['location'] ?? '—') ?> · <?= e(str_replace('_',' ', ucfirst($job['job_type'] ?? ''))) ?> · <?= e(ucfirst($job['experience_level'] ?? '')) ?></p>
              <?php if(!empty($job['salary_min'])): ?>
                <span class="salary" style="font-size:.78rem;"><?= e($job['salary_currency'] ?? 'USD') ?> <?= number_format((float)$job['salary_min']) ?> – <?= number_format((float)$job['salary_max']) ?></span>
              <?php endif; ?>
              <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:nowrap;overflow:hidden;">
                <span class="badge" style="font-size:.72rem;padding:4px 10px;border-radius:20px;background:<?= $job['status']==='approved' ? '#e6f4ea' : ($job['status']==='rejected' ? '#fdecea' : '#fff3cd') ?>;color:<?= $job['status']==='approved' ? '#057642' : ($job['status']==='rejected' ? '#cc1016' : '#856404') ?>;">
                  <?= e(ucfirst($job['status'])) ?>
                </span>
                <span style="font-size:.76rem;color:#666;"><i class="bi bi-people me-1"></i><?= (int)$job['applications_count'] ?> applicants</span>
                <?php if(!empty($job['expires_at'])): ?>
                  <?php
                    $expTs = strtotime($job['expires_at']);
                    $now   = time();
                    $diff  = $expTs - $now;
                    $daysLeft = (int)ceil($diff / 86400);
                  ?>
                  <?php if($diff < 0): ?>
                    <span style="font-size:.72rem;padding:4px 10px;border-radius:20px;background:#fdecea;color:#cc1016;font-weight:600;">
                      <i class="bi bi-x-circle me-1"></i>Expired
                    </span>
                  <?php elseif($daysLeft <= 3): ?>
                    <span style="font-size:.76rem;color:#e67e00;font-weight:600;"><i class="bi bi-calendar-x me-1"></i>Expires in <?= $daysLeft ?> day<?= $daysLeft!=1?'s':'' ?></span>
                  <?php else: ?>
                    <span style="font-size:.76rem;color:#888;"><i class="bi bi-calendar me-1"></i>Expires <?= e(date('M j, Y', $expTs)) ?></span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
              <button class="btn btn-sm btn-outline-primary edit-job-btn" style="border-radius:20px;font-size:.78rem;"
                data-id="<?= (int)$job['id'] ?>"
                data-title="<?= e($job['title']) ?>"
                data-location="<?= e($job['location'] ?? '') ?>"
                data-job-type="<?= e($job['job_type'] ?? 'full_time') ?>"
                data-exp="<?= e($job['experience_level'] ?? 'mid') ?>"
                data-sal-min="<?= e($job['salary_min'] ?? '') ?>"
                data-sal-max="<?= e($job['salary_max'] ?? '') ?>"
                data-req="<?= e($job['requirements'] ?? '') ?>"
                data-desc="<?= e($job['description'] ?? '') ?>"
                data-benefits="<?= e($job['benefits'] ?? '') ?>"
                data-expires="<?= e($job['expires_at'] ?? '') ?>">
                <i class="bi bi-pencil"></i> Edit
              </button>
              <button class="btn btn-sm btn-outline-danger delete-job-btn" style="border-radius:20px;font-size:.78rem;" data-id="<?= (int)$job['id'] ?>">
                <i class="bi bi-trash"></i> Delete
              </button>
              <a href="<?= APP_URL ?>/applicants" class="btn btn-sm btn-outline-secondary" style="border-radius:20px;font-size:.78rem;">
                <i class="bi bi-people"></i> View
              </a>
            </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </section>

  <!-- RIGHT SIDEBAR -->
  <aside class="li-right">
    <section class="li-card" style="padding:14px 16px;">
      <div style="font-weight:700;font-size:.88rem;margin-bottom:10px;">Quick Actions</div>
      <a href="<?= APP_URL ?>/applicants" class="btn btn-primary w-100 mb-2" style="border-radius:20px;font-weight:600;font-size:.85rem;">
        <i class="bi bi-kanban me-1"></i>Manage Applications
      </a>
      <a href="<?= APP_URL ?>/recruiter-dashboard" class="btn btn-outline-secondary w-100 mb-2" style="border-radius:20px;font-weight:600;font-size:.85rem;">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
      </a>
      <a href="<?= APP_URL ?>/company-profile" class="btn btn-outline-secondary w-100" style="border-radius:20px;font-weight:600;font-size:.85rem;">
        <i class="bi bi-pencil me-1"></i>Edit Company Profile
      </a>
    </section>
    <section class="li-card" style="padding:14px 16px;">
      <div style="font-weight:700;font-size:.88rem;margin-bottom:12px;">Job Type Breakdown</div>
      <?php
        $typeLabels = [
          'full_time'  => 'Full Time',
          'part_time'  => 'Part Time',
          'remote'     => 'Remote',
          'contract'   => 'Contract',
          'internship' => 'Internship',
        ];
        $typeColors = [
          'full_time'  => '#0a66c2',
          'part_time'  => '#6f42c1',
          'remote'     => '#057642',
          'contract'   => '#e7a33e',
          'internship' => '#8f5849',
        ];
        $typeCount = [];
        foreach ($jobs as $j) {
          $t = $j['job_type'] ?? 'other';
          $typeCount[$t] = ($typeCount[$t] ?? 0) + 1;
        }
        $totalJobs = array_sum($typeCount);
        arsort($typeCount);
      ?>
      <?php if ($totalJobs === 0): ?>
        <div style="text-align:center;padding:18px 0 10px;">
          <i class="bi bi-bar-chart" style="font-size:2rem;color:#ccc;display:block;margin-bottom:8px;"></i>
          <p style="font-size:.82rem;color:#888;margin:0;">No jobs posted yet.<br>Post your first job to see the breakdown.</p>
        </div>
      <?php else: ?>
        <?php foreach ($typeCount as $type => $cnt):
          $label   = $typeLabels[$type] ?? ucwords(str_replace('_', ' ', $type));
          $color   = $typeColors[$type] ?? '#888';
          $pct     = round(($cnt / $totalJobs) * 100);
        ?>
        <div style="margin-bottom:10px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
            <span style="font-size:.8rem;color:#444;font-weight:600;display:flex;align-items:center;gap:6px;">
              <span style="width:8px;height:8px;border-radius:50%;background:<?= $color ?>;display:inline-block;flex-shrink:0;"></span>
              <?= e($label) ?>
            </span>
            <span style="font-size:.78rem;color:#666;font-weight:600;"><?= $cnt ?> <span style="color:#aaa;font-weight:400;">(<?= $pct ?>%)</span></span>
          </div>
          <div style="background:#f0f0f0;border-radius:20px;height:6px;overflow:hidden;">
            <div style="width:<?= $pct ?>%;background:<?= $color ?>;height:100%;border-radius:20px;transition:width .4s ease;"></div>
          </div>
        </div>
        <?php endforeach; ?>
        <div style="border-top:1px solid #f0f0f0;margin-top:10px;padding-top:8px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:.78rem;color:#888;">Total posted</span>
          <strong style="font-size:.88rem;color:#0a66c2;"><?= $totalJobs ?></strong>
        </div>
      <?php endif; ?>
    </section>
  </aside>
</div>

<script>
function resetJobForm() {
  document.getElementById('jobId').value = '';
  document.getElementById('jobForm').reset();
  document.getElementById('fCompany').value = '<?= e($company['name']) ?>';
  document.getElementById('jobFormTitle').innerHTML = '<i class="bi bi-plus-circle me-2" style="color:#0a66c2;"></i>Post a New Job';
  document.getElementById('jobSubmitBtn').innerHTML = '<i class="bi bi-send me-1"></i>Publish Job';
  document.getElementById('cancelEditBtn').style.display = 'none';
  document.getElementById('jobFormCard').scrollIntoView({behavior:'smooth'});
}

document.querySelectorAll('.edit-job-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const d = this.dataset;
    document.getElementById('jobId').value = d.id;
    document.getElementById('fTitle').value = d.title;
    document.getElementById('fLocation').value = d.location;
    document.getElementById('fJobType').value = d.jobType;
    document.getElementById('fExp').value = d.exp;
    document.getElementById('fSalMin').value = d.salMin;
    document.getElementById('fSalMax').value = d.salMax;
    document.getElementById('fReq').value = d.req;
    document.getElementById('fDesc').value = d.desc;
    document.getElementById('fBenefits').value = d.benefits;
    document.getElementById('fExpires').value = d.expires;
    document.getElementById('jobFormTitle').innerHTML = '<i class="bi bi-pencil-square me-2" style="color:#0a66c2;"></i>Edit Job';
    document.getElementById('jobSubmitBtn').innerHTML = '<i class="bi bi-check-lg me-1"></i>Update Job';
    document.getElementById('cancelEditBtn').style.display = 'inline-block';
    document.getElementById('jobFormCard').scrollIntoView({behavior:'smooth'});
  });
});

// Highlight job card if redirected from dashboard via anchor
if (location.hash) {
  const target = document.querySelector(location.hash);
  if (target) {
    setTimeout(() => {
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      target.style.transition = 'background 0.3s';
      target.style.background = '#e8f0fe';
      target.style.borderRadius = '10px';
      target.style.boxShadow = '0 0 0 2px #0a66c2';
      setTimeout(() => {
        target.style.background = '';
        target.style.boxShadow = '';
      }, 2500);
    }, 300);
  }
}

document.querySelectorAll('.delete-job-btn').forEach(btn => {
  btn.addEventListener('click', async function() {
    const id = this.dataset.id;
    const confirm = window.Swal
      ? await Swal.fire({title:'Delete Job?',text:'This will also delete all applications for this job.',icon:'warning',showCancelButton:true,confirmButtonText:'Yes, Delete',confirmButtonColor:'#cc1016',cancelButtonColor:'#0a66c2',reverseButtons:true})
      : {isConfirmed: window.confirm('Delete this job and all its applications?')};
    if (!confirm.isConfirmed) return;
    const res = await fetch(window.APP_URL+'/ajax?action=delete-job',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':window.CSRF_TOKEN},
      body:new URLSearchParams({job_id:id})
    }).then(r=>r.json());
    if(res.ok) {
      const card = document.querySelector(`.job-card[data-job-id="${id}"]`);
      if(card) { card.style.opacity='0'; card.style.transition='opacity .3s'; setTimeout(()=>card.remove(),300); }
      if(window.Swal) Swal.fire({title:'Deleted!',text:'Job has been removed.',icon:'success',timer:1500,showConfirmButton:false});
    } else {
      if(window.Swal) Swal.fire('Error','Could not delete job.','error');
    }
  });
});
</script>