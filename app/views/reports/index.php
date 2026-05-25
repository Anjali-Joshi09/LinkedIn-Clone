<?php /* app/views/reports/index.php */ $pageTitle = 'Reports'; ?>

<?php if (empty($reports)): ?>
  <div class="panel">
    <div class="empty-state">
      <i class="ti ti-circle-check" style="color:#057642"></i>
      <p>No pending reports — everything looks clean!</p>
    </div>
  </div>
<?php else: ?>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-flag" style="color:#cc1016"></i> Pending Reports</span>
    <span style="font-size:12px;color:#888"><?= count($reports) ?> pending</span>
  </div>

  <?php foreach ($reports as $r):
    $reportData = htmlspecialchars(json_encode([
      'id' => $r['id'],
      'type' => $r['type'] ?? null,
      'reason' => $r['reason'] ?? null,
      'status' => $r['status'] ?? null,
      'target_type' => $r['target_type'] ?? null,
      'target_id' => $r['target_id'] ?? null,
      'created_at' => $r['created_at'] ?? null,
      'reporter_name' => $r['reporter_name'] ?? null,
      'reporter_email' => $r['reporter_user_email'] ?? $r['reporter_email'] ?? null,
      'reporter_id' => $r['reporter_id'] ?? null,
      'total_report_count' => $r['total_report_count'] ?? 1,
      'post' => [
        'id' => $r['post_id'] ?? null,
        'author' => $r['post_author'] ?? $r['creator_name'] ?? null,
        'content' => $r['post_content'] ?? null,
        'media' => $r['post_media'] ?? null,
        'media_type' => $r['post_media_type'] ?? null,
        'visibility' => $r['post_visibility'] ?? null,
        'likes' => $r['live_likes_count'] ?? $r['post_likes'] ?? 0,
        'comments_count' => $r['live_comments_count'] ?? $r['post_comments_count'] ?? 0,
        'shares_count' => $r['post_shares_count'] ?? 0,
        'status' => $r['post_status'] ?? null,
        'created_at' => $r['post_created_at'] ?? null,
      ],
      'creator' => [
        'id' => $r['post_user_id'] ?? null,
        'name' => $r['creator_name'] ?? null,
        'email' => $r['creator_email'] ?? null,
        'phone' => $r['creator_phone'] ?? null,
        'role' => $r['creator_role'] ?? null,
        'headline' => $r['creator_headline'] ?? null,
        'bio' => $r['creator_bio'] ?? null,
        'location' => $r['creator_location'] ?? null,
        'website' => $r['creator_website'] ?? null,
        'status' => $r['creator_status'] ?? null,
        'avatar' => $r['creator_avatar'] ?? null,
        'created_at' => $r['creator_created_at'] ?? null,
      ],
      'company' => [
        'id' => $r['creator_company_id'] ?? null,
        'name' => $r['creator_company_name'] ?? null,
        'email' => $r['creator_company_email'] ?? null,
        'industry' => $r['creator_company_industry'] ?? null,
        'company_size' => $r['creator_company_size'] ?? null,
        'location' => $r['creator_company_location'] ?? null,
        'website' => $r['creator_company_website'] ?? null,
        'status' => $r['creator_company_status'] ?? null,
      ],
      'comments' => $r['comments'] ?? [],
    ]), ENT_QUOTES);
  ?>
  <div class="report-card" data-new-id="r<?= $r['id'] ?>" onclick="markSeenModal(this,'admin_seen_reports','[data-admin-badge=reports]');openReportModal(<?= $reportData ?>)">
    <div class="report-head">
      <span class="badge badge-red"><?= htmlspecialchars(ucfirst($r['type'] ?? 'Report')) ?></span>
      <span class="badge badge-yellow">Pending</span>
      <span style="font-size:12px;color:#aaa">
        Reported by: <?= htmlspecialchars($r['reporter_email'] ?? 'Anonymous') ?>
        &nbsp;&bull;&nbsp; <?= date('M d, Y', strtotime($r['created_at'] ?? 'now')) ?>
      </span>
      <span class="badge badge-gray" style="margin-left:auto">Target: <?= htmlspecialchars(ucfirst($r['target_type'] ?? '')) ?> #<?= (int)($r['target_id'] ?? 0) ?></span>
    </div>
    <div class="report-reason"><strong>Reason:</strong> <?= htmlspecialchars($r['reason'] ?? '') ?></div>
    <div class="act-btns" onclick="event.stopPropagation()">
      <a href="<?= APP_URL ?>/reports?action=resolve&id=<?= $r['id'] ?>&status=resolved"
         class="btn btn-success btn-sm"
         onclick="return confirm('Mark this report as resolved?')">
        <i class="ti ti-circle-check"></i> Resolve
      </a>
      <a href="<?= APP_URL ?>/reports?action=resolve&id=<?= $r['id'] ?>&status=dismissed"
         class="btn btn-outline btn-sm"
         onclick="return confirm('Dismiss this report?')">
        <i class="ti ti-x"></i> Dismiss
      </a>
      <?php if (($r['target_type'] ?? '') === 'post'): ?>
      <a href="<?= APP_URL ?>/reports?action=delete-post&id=<?= $r['id'] ?>"
         class="btn btn-danger btn-sm"
         onclick="return confirm('Permanently delete this reported post and all related report records?')">
        <i class="ti ti-trash"></i> Delete Post
      </a>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>
