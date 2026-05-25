<?php /* app/views/content/index.php */ $pageTitle = 'Content Management'; ?>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-file-text" style="color:#7b1fa2"></i> Posts &amp; Content</span>
    <form method="GET" action="<?= APP_URL ?>/" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="page" value="content">
      <select class="filter-input" name="status" onchange="this.form.submit()">
        <option value="">All Posts</option>
        <?php foreach (['active','hidden','reported','offensive','deleted'] as $s): ?>
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
          <th>Author</th>
          <th>Content Preview</th>
          <th>Likes</th>
          <th>Comments</th>
          <th>Posted</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($posts)): ?>
          <tr><td colspan="8"><div class="empty-state"><i class="ti ti-file-text"></i><p>No posts found.</p></div></td></tr>
        <?php else: foreach ($posts as $i => $p):
          $postData = htmlspecialchars(json_encode([
            'id'            => $p['id'],
            'author'        => $p['author']         ?? null,
            'content'       => $p['content']        ?? null,
            'status'        => $p['status'],
            'visibility'    => $p['visibility']     ?? null,
            'media_type'    => $p['media_type']     ?? null,
            'media'         => $p['media']          ?? null,
            'likes'         => $p['likes']          ?? 0,
            'comments_count'=> $p['comments_count'] ?? 0,
            'shares_count'  => $p['shares_count']   ?? 0,
            'created_at'    => $p['created_at'],
          ]), ENT_QUOTES);
        ?>
        <tr style="cursor:pointer" onclick="markSeenModal(this,'admin_seen_content','a[href*=\'page=content\'] .nav-badge');openPostModal(<?= $postData ?>)" title="Click to view details" data-new-id="p<?= $p['id'] ?>">
          <td style="color:#aaa;font-size:12px"><?= $i + 1 ?></td>
          <td>
            <div style="font-weight:600;color:#191919"><?= htmlspecialchars($p['author'] ?? 'Unknown') ?></div>
          </td>
          <td style="max-width:220px">
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:#555">
              <?= htmlspecialchars($p['content'] ?? '') ?>
            </div>
          </td>
          <td>
            <span style="font-size:13px"><i class="ti ti-heart" style="color:#cc1016;font-size:13px"></i> <?= number_format((int)($p['likes'] ?? 0)) ?></span>
          </td>
          <td>
            <span style="font-size:13px"><i class="ti ti-message" style="color:#0a66c2;font-size:13px"></i> <?= number_format((int)($p['comments_count'] ?? 0)) ?></span>
          </td>
          <td style="font-size:12px;color:#666"><?= date('M d, Y', strtotime($p['created_at'] ?? 'now')) ?></td>
          <td>
            <span class="badge badge-<?= ['active'=>'green','hidden'=>'gray','reported'=>'yellow','offensive'=>'red','deleted'=>'red'][strtolower($p['status'] ?? 'active')] ?? 'blue' ?>">
              <?= htmlspecialchars(ucfirst($p['status'] ?? 'active')) ?>
            </span>
          </td>
          <td>
            <div class="act-btns" onclick="event.stopPropagation()">
              <?php if (strtolower($p['status'] ?? '') === 'hidden'): ?>
              <a href="<?= APP_URL ?>/content?action=unhide&id=<?= $p['id'] ?>"
                 class="act-btn approve" title="Unhide Post"
                 onclick="return confirm('Restore this post?')" style="background:#e8f5e9"><i class="ti ti-eye"></i></a>
              <?php else: ?>
              <a href="<?= APP_URL ?>/content?action=hide&id=<?= $p['id'] ?>"
                 class="act-btn block" title="Hide Post"
                 onclick="return confirm('Hide this post?')" style="background:#fff3e0"><i class="ti ti-eye-off"></i></a>
              <?php endif; ?>
              <a href="<?= APP_URL ?>/content?action=delete&id=<?= $p['id'] ?>"
                 class="act-btn del" title="Delete Post"
                 onclick="return confirm('Delete this post?')"><i class="ti ti-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>