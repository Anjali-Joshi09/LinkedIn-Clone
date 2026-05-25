<?php /* app/views/jobs/index.php */ $pageTitle = 'Job Listings'; ?>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-briefcase" style="color:#e65100"></i> Job Listings</span>
    <form method="GET" action="<?= APP_URL ?>/" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="page" value="jobs">
      <select class="filter-input" name="status" onchange="this.form.submit()">
        <option value="">All Status</option>
        <?php foreach (['pending','approved','rejected','expired','closed','hidden'] as $s): ?>
          <option <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Job Title</th>
          <th>Company</th>
          <th>Type</th>
          <th>Applications</th>
          <th>Posted</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($jobs)): ?>
          <tr><td colspan="8"><div class="empty-state"><i class="ti ti-briefcase"></i><p>No job listings found.</p></div></td></tr>
        <?php else: foreach ($jobs as $i => $j):
          $status   = strtolower($j['status'] ?? 'pending');
          $badgeMap = ['pending'=>'yellow','approved'=>'green','rejected'=>'red','expired'=>'gray','closed'=>'gray','hidden'=>'gray'];
          $jobData  = htmlspecialchars(json_encode([
            'id'                => $j['id'],
            'title'             => $j['title'],
            'company'           => $j['company'] ?? null,
            'location'          => $j['location'] ?? null,
            'status'            => $j['status'],
            'job_type'          => $j['job_type'] ?? null,
            'experience_level'  => $j['experience_level'] ?? null,
            'salary_min'        => $j['salary_min'] ?? null,
            'salary_max'        => $j['salary_max'] ?? null,
            'salary_currency'   => $j['salary_currency'] ?? 'USD',
            'applications_count'=> $j['applications_count'] ?? 0,
            'views_count'       => $j['views_count'] ?? 0,
            'requirements'      => $j['requirements'] ?? null,
            'benefits'          => $j['benefits'] ?? null,
            'description'       => $j['description'] ?? null,
            'created_at'        => $j['created_at'],
            'expires_at'        => $j['expires_at'] ?? null,
          ]), ENT_QUOTES);
        ?>
        <tr style="cursor:pointer" onclick="markSeenModal(this,'admin_seen_jobs','[data-admin-badge=jobs]');openJobModal(<?= $jobData ?>)" title="Click to view details" data-new-id="j<?= $j['id'] ?>">
          <td style="color:#aaa;font-size:12px"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:#191919"><?= htmlspecialchars($j['title'] ?? '') ?></div>
            <?php if (!empty($j['location'])): ?>
            <div style="font-size:11px;color:#aaa"><i class="ti ti-map-pin" style="font-size:11px"></i> <?= htmlspecialchars($j['location']) ?></div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($j['company'] ?? '') ?></td>
          <td>
            <?php if (!empty($j['job_type'])): ?>
            <span style="font-size:12px;color:#555"><?= ucfirst(str_replace('_', ' ', $j['job_type'])) ?></span>
            <?php endif; ?>
          </td>
          <td>
            <span style="font-size:13px;font-weight:600"><?= number_format((int)($j['applications_count'] ?? 0)) ?></span>
            <span style="font-size:11px;color:#aaa"> apps</span>
          </td>
          <td style="font-size:12px;color:#666"><?= date('M d, Y', strtotime($j['created_at'] ?? 'now')) ?></td>
          <td>
            <span class="badge badge-<?= $badgeMap[$status] ?? 'blue' ?>">
              <?= htmlspecialchars(ucfirst($j['status'] ?? 'pending')) ?>
            </span>
          </td>
          <td>
            <div class="act-btns" onclick="event.stopPropagation()">
              <?php if ($status !== 'approved'): ?>
              <a href="<?= APP_URL ?>/jobs?action=approve&id=<?= $j['id'] ?>"
                 class="act-btn approve" title="Approve"
                 onclick="return confirm('Approve this job listing?')"><i class="ti ti-circle-check"></i></a>
              <?php endif; ?>

              <?php if ($status !== 'rejected'): ?>
              <a href="<?= APP_URL ?>/jobs?action=reject&id=<?= $j['id'] ?>"
                 class="act-btn block" title="Reject"
                 onclick="return confirm('Reject this job listing?')"><i class="ti ti-circle-x"></i></a>
              <?php endif; ?>

              <?php if ($status === 'hidden'): ?>
              <a href="<?= APP_URL ?>/jobs?action=unhide&id=<?= $j['id'] ?>"
                 class="act-btn approve" title="Restore to Pending"
                 onclick="return confirm('Restore this job listing?')" style="background:#e8f5e9"><i class="ti ti-eye"></i></a>
              <?php else: ?>
              <a href="<?= APP_URL ?>/jobs?action=hide&id=<?= $j['id'] ?>"
                 class="act-btn block" title="Hide Job"
                 onclick="return confirm('Hide this job from public view?')" style="background:#fff3e0"><i class="ti ti-eye-off"></i></a>
              <?php endif; ?>

              <a href="<?= APP_URL ?>/jobs?action=delete&id=<?= $j['id'] ?>"
                 class="act-btn del" title="Delete"
                 onclick="return confirm('Permanently delete this job?')"><i class="ti ti-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
