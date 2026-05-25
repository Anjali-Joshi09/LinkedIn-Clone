<?php $pageTitle = 'Company Approvals'; ?>
<?php
function statusBadge(string $s): string {
    $map = ['pending' => 'yellow', 'approved' => 'green', 'rejected' => 'red', 'blocked' => 'red'];
    $c   = $map[$s] ?? 'blue';
    return "<span class='badge badge-{$c}'>" . ucfirst($s) . "</span>";
}
?>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-user-check" style="color:#7b1fa2"></i> Agent / Company Approval Requests</span>
    <div class="filters">
      <a href="<?= APP_URL ?>/agents" class="btn btn-sm <?= $status === '' ? 'btn-primary' : 'btn-outline' ?>">All</a>
      <a href="<?= APP_URL ?>/agents?status=pending"  class="btn btn-sm <?= $status === 'pending'  ? 'btn-primary' : 'btn-outline' ?>">Pending</a>
      <a href="<?= APP_URL ?>/agents?status=approved" class="btn btn-sm <?= $status === 'approved' ? 'btn-primary' : 'btn-outline' ?>">Approved</a>
      <a href="<?= APP_URL ?>/agents?status=rejected" class="btn btn-sm <?= $status === 'rejected' ? 'btn-primary' : 'btn-outline' ?>">Rejected</a>
    </div>
  </div>

  <?php if (empty($approvals)): ?>
    <div class="empty-state">
      <i class="ti ti-user-check"></i>
      <p>No approval requests<?= $status ? " with status <strong>{$status}</strong>" : '' ?>.</p>
    </div>
  <?php else: ?>
    <?php foreach ($approvals as $a): ?>
    <div class="ticket" style="margin-bottom:16px" data-new-id="a<?= $a['id'] ?>" onclick="markSeenModal(this,'admin_seen_agents','[data-admin-badge=agents]')">
      <div class="ticket-head">
        <div class="av" style="background:#7b1fa2"><?= strtoupper(substr($a['name'] ?? 'A', 0, 2)) ?></div>
        <div style="flex:1">
          <div style="font-size:14px;font-weight:700;color:#191919"><?= htmlspecialchars($a['name']) ?></div>
          <div style="font-size:12px;color:#888"><?= htmlspecialchars($a['email']) ?> &bull; Submitted <?= date('M d, Y', strtotime($a['created_at'])) ?></div>
        </div>
        <?= statusBadge($a['status']) ?>
        <button class="act-btn view" title="View Full Details" onclick='event.stopPropagation();markSeenModal(this.closest("[data-new-id]"),"admin_seen_agents","[data-admin-badge=agents]");openAgentModal(<?= htmlspecialchars(json_encode(["id"=>$a["id"],"name"=>$a["name"],"email"=>$a["email"],"phone"=>$a["phone"]??null,"headline"=>$a["headline"]??null,"bio"=>$a["bio"]??null,"location"=>$a["location"]??null,"website"=>$a["website"]??null,"status"=>$a["status"],"admin_note"=>$a["admin_note"]??null,"created_at"=>$a["created_at"],"reviewed_at"=>$a["reviewed_at"]??null]), ENT_QUOTES) ?>)'><i class="ti ti-eye"></i></button>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;background:#f8f8f8;border:1px solid #eee;border-radius:6px;padding:14px;margin-bottom:14px;font-size:13px">
        <div><span style="color:#888">Phone:</span> <strong><?= htmlspecialchars($a['phone'] ?? 'N/A') ?></strong></div>
        <div><span style="color:#888">Location:</span> <strong><?= htmlspecialchars($a['location'] ?? 'N/A') ?></strong></div>
        <div><span style="color:#888">Headline:</span> <strong><?= htmlspecialchars($a['headline'] ?? 'N/A') ?></strong></div>
        <div><span style="color:#888">Website:</span>
          <?php if (!empty($a['website'])): ?>
            <a href="<?= htmlspecialchars($a['website']) ?>" target="_blank" style="color:#0a66c2"><?= htmlspecialchars($a['website']) ?></a>
          <?php else: ?><strong>N/A</strong><?php endif; ?>
        </div>
        <?php if (!empty($a['bio'])): ?>
        <div style="grid-column:1/-1"><span style="color:#888">Bio:</span> <?= htmlspecialchars($a['bio']) ?></div>
        <?php endif; ?>
        <?php if (!empty($a['admin_note'])): ?>
        <div style="grid-column:1/-1;background:#fff8e1;border-radius:4px;padding:8px"><span style="color:#888">Admin Note:</span> <?= htmlspecialchars($a['admin_note']) ?></div>
        <?php endif; ?>
      </div>

      <!-- Action Buttons -->
      <?php
        $isBlocked = ($a['status'] === 'rejected' && ($a['admin_note'] ?? '') === 'Blocked by admin');
      ?>
      <div class="ticket-foot" style="flex-wrap:wrap;gap:8px;">
        <?php if ($a['status'] === 'pending'): ?>
          <a href="<?= APP_URL ?>/agents?action=approve&id=<?= $a['id'] ?>"
             class="btn btn-success btn-sm"
             onclick="return confirm('Approve this agent? An approval email will be sent.')">
            <i class="ti ti-circle-check"></i> Approve &amp; Notify Agent
          </a>
          <button class="btn btn-danger btn-sm" onclick="toggleRejectForm(<?= $a['id'] ?>)">
            <i class="ti ti-circle-x"></i> Reject
          </button>
        <?php endif; ?>

        <?php if ($isBlocked): ?>
          <!-- Show Unblock when blocked -->
          <a href="<?= APP_URL ?>/agents?action=unblock&id=<?= $a['id'] ?>"
             class="btn btn-sm" style="background:#e8f5e9;color:#057642;border:1px solid #a5d6a7;"
             onclick="return confirm('Unblock this company/agent account?')">
            <i class="ti ti-lock-open"></i> Unblock
          </a>
        <?php else: ?>
          <!-- Show Block when not blocked -->
          <a href="<?= APP_URL ?>/agents?action=block&id=<?= $a['id'] ?>"
             class="btn btn-sm" style="background:#fff3e0;color:#e65100;border:1px solid #ffcc80;"
             onclick="return confirm('Block this company/agent account?')">
            <i class="ti ti-ban"></i> Block
          </a>
        <?php endif; ?>

        <a href="<?= APP_URL ?>/agents?action=delete&id=<?= $a['id'] ?>"
           class="btn btn-sm" style="background:#fdecea;color:#cc1016;border:1px solid #f5c6c6;"
           onclick="return confirm('Permanently delete this company/agent? This cannot be undone.')">
          <i class="ti ti-trash"></i> Delete
        </a>
      </div>

      <!-- Reject Form (hidden by default) -->
      <div id="reject-form-<?= $a['id'] ?>" style="display:none;margin-top:12px;background:#fff5f5;border:1px solid #f5c6c6;border-radius:6px;padding:14px">
        <form method="POST" action="<?= APP_URL ?>/agents?action=reject&id=<?= $a['id'] ?>">
          <label style="font-size:13px;font-weight:600;color:#444;display:block;margin-bottom:6px">
            Rejection Reason <span style="font-weight:400;color:#888">(optional — will be emailed to agent)</span>
          </label>
          <textarea name="note" class="form-control" rows="3" placeholder="e.g. Incomplete profile, missing credentials..."></textarea>
          <div style="margin-top:10px;display:flex;gap:8px">
            <button type="submit" class="btn btn-danger btn-sm"
              onclick="return confirm('Reject this agent? A rejection email will be sent.')">
              <i class="ti ti-send"></i> Confirm Rejection
            </button>
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleRejectForm(<?= $a['id'] ?>)">Cancel</button>
          </div>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
