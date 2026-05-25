<?php /* app/views/support/index.php */ $pageTitle = 'Support Tickets'; ?>

<?php if (empty($tickets)): ?>
  <div class="panel">
    <div class="empty-state">
      <i class="ti ti-headset"></i>
      <p>No support tickets yet.</p>
    </div>
  </div>
<?php else: ?>

<div class="panel" style="padding:0;overflow:hidden">
  <div class="panel-head" style="padding:16px 20px;margin-bottom:0;border-bottom:1px solid #f0f0f0">
    <span class="panel-title"><i class="ti ti-headset" style="color:#0a66c2"></i> Support Tickets</span>
    <span style="font-size:12px;color:#888"><?= count($tickets) ?> tickets</span>
  </div>
</div>

<?php foreach ($tickets as $t): ?>
<div class="ticket">
  <div class="ticket-head">
    <span class="badge badge-blue">Ticket #<?= htmlspecialchars($t['id'] ?? '') ?></span>
    <span class="badge badge-<?= ['open'=>'yellow','in_progress'=>'blue','resolved'=>'green','closed'=>'gray'][strtolower($t['status']??'open')]??'blue' ?>">
      <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $t['status'] ?? 'open'))) ?>
    </span>
    <?php if (!empty($t['priority']) && $t['priority'] !== 'normal'): ?>
    <span class="badge badge-<?= ['high'=>'red','urgent'=>'red','low'=>'gray'][$t['priority']]??'blue' ?>">
      <?= ucfirst($t['priority']) ?> Priority
    </span>
    <?php endif; ?>
    <button class="act-btn view" style="margin-left:auto" title="View Details" onclick='openTicketModal(<?= htmlspecialchars(json_encode(["id"=>$t["id"],"name"=>$t["name"]??null,"email"=>$t["email"]??null,"subject"=>$t["subject"]??null,"message"=>$t["message"]??null,"status"=>$t["status"],"priority"=>$t["priority"]??"normal","created_at"=>$t["created_at"]]), ENT_QUOTES) ?>)'><i class="ti ti-eye"></i></button>
    <span style="font-size:12px;color:#aaa;margin-left:8px">
      From: <strong><?= htmlspecialchars($t['name'] ?? $t['email'] ?? '') ?></strong>
      &lt;<?= htmlspecialchars($t['email'] ?? '') ?>&gt;
      &bull; <?= date('M d, Y', strtotime($t['created_at'] ?? 'now')) ?>
    </span>
  </div>
  <div class="ticket-subject"><?= htmlspecialchars($t['subject'] ?? '') ?></div>
  <div class="ticket-body"><?= nl2br(htmlspecialchars($t['message'] ?? '')) ?></div>

  <?php if ($t['status'] !== 'resolved' && $t['status'] !== 'closed'): ?>
  <form method="POST" action="<?= APP_URL ?>/support?action=reply" style="margin-bottom:10px">
    <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
    <textarea class="form-control" name="message" placeholder="Write your reply to the user..." rows="2" style="margin-bottom:8px" required></textarea>
    <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-send"></i> Send Reply</button>
  </form>
  <?php endif; ?>

  <div class="ticket-foot">
    <?php if ($t['status'] !== 'resolved' && $t['status'] !== 'closed'): ?>
    <a href="<?= APP_URL ?>/support?action=resolve&id=<?= $t['id'] ?>"
       class="btn btn-success btn-sm"
       onclick="return confirm('Mark this ticket as resolved?')">
      <i class="ti ti-circle-check"></i> Mark Resolved
    </a>
    <?php else: ?>
    <span class="badge badge-green"><i class="ti ti-circle-check"></i> Resolved</span>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>

<?php endif; ?>