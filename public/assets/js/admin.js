/* ============================================================
   LinkedIn Admin — Main JavaScript
   Extracted from app/views/layouts/admin.php <script> blocks
   ============================================================ */

// ── PROFILE MENU ─────────────────────────────────────────────
function toggleProfileMenu() {
  const menu = document.getElementById('profile-menu');
  menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function (e) {
  const wrap = document.querySelector('.profile-wrap');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('profile-menu').style.display = 'none';
  }
});


// ── ADMIN PROFILE MODAL ───────────────────────────────────────
window.openAdminProfileModal = function () {
  const alertEl = document.getElementById('ap-alert');
  alertEl.style.display = 'none';
  alertEl.className = '';
  document.getElementById('ap-password').value = '';
  document.getElementById('ap-confirm').value = '';
  document.getElementById('admin-profile-modal').classList.add('show');
};

window.closeAdminProfileModal = function () {
  document.getElementById('admin-profile-modal').classList.remove('show');
};

document.addEventListener('DOMContentLoaded', function () {
  const apModal = document.getElementById('admin-profile-modal');
  if (apModal) apModal.addEventListener('click', function (e) {
    if (e.target === this) closeAdminProfileModal();
  });
});

function showApAlert(msg, type) {
  const el = document.getElementById('ap-alert');
  el.style.display = 'block';
  el.className = 'ap-alert-' + type;
  el.innerHTML = (type === 'success'
    ? '<i class="ti ti-circle-check"></i> '
    : '<i class="ti ti-alert-circle"></i> ') + msg;
}

window.saveAdminProfile = function () {
  const name     = document.getElementById('ap-name').value.trim();
  const email    = document.getElementById('ap-email').value.trim();
  const password = document.getElementById('ap-password').value;
  const confirm  = document.getElementById('ap-confirm').value;
  const saveBtn  = document.getElementById('ap-save-btn');

  if (!name || !email) { showApAlert('Name and email are required.', 'error'); return; }
  if (password && password.length < 8) { showApAlert('Password must be at least 8 characters.', 'error'); return; }
  if (password && password !== confirm) { showApAlert('Passwords do not match.', 'error'); return; }

  saveBtn.disabled = true;
  saveBtn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Saving...';

  const formData = new FormData();
  formData.append('name', name);
  formData.append('email', email);
  if (password) formData.append('password', password);

  fetch(window.APP_URL + '/admin-profile?action=save', {
    method: 'POST',
    body: formData
  })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (data.success) {
        const sName = document.querySelector('.s-name');
        const sAv   = document.querySelector('.s-av');
        const apAv  = document.getElementById('ap-av');
        if (sName) sName.textContent = name;
        if (sAv)   sAv.textContent   = name.substring(0, 2).toUpperCase();
        if (apAv)  apAv.textContent  = name.substring(0, 2).toUpperCase();
        closeAdminProfileModal();
        showToast('Profile updated successfully!', 'success');
        setTimeout(function () { location.reload(); }, 1000);
      } else {
        showApAlert(data.error || 'Something went wrong. Please try again.', 'error');
      }
    })
    .catch(function () {
      showApAlert('Network error. Please try again.', 'error');
    })
    .finally(function () {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="ti ti-device-floppy"></i> Save Changes';
    });
};

// ── TOAST SYSTEM ──────────────────────────────────────────────
function showToast(msg, type, dur) {
  type = type || 'success';
  dur  = dur  || 4000;
  const icons = { success: 'ti-circle-check', error: 'ti-alert-circle', info: 'ti-info-circle' };
  const wrap = document.getElementById('toast-wrap');
  const t = document.createElement('div');
  t.className = 'toast toast-' + type;
  t.innerHTML =
    '<i class="ti ' + (icons[type] || icons.info) + '" style="flex-shrink:0"></i>' +
    '<span class="toast-msg">' + msg + '</span>' +
    '<button class="toast-close" onclick="this.parentElement.remove()"><i class="ti ti-x"></i></button>';
  wrap.appendChild(t);
  setTimeout(function () {
    t.classList.add('hiding');
    setTimeout(function () { t.remove(); }, 400);
  }, dur);
}

// Expose globally so PHP-generated flash messages can call it
window.showToast = showToast;

// ── IDLE AUTO-LOGOUT ──────────────────────────────────────────
(function () {
  const IDLE_MINS = 15;
  const WARN_SECS = 60;
  let idleTimer, countTimer, secs;
  const modal   = document.getElementById('idle-modal');
  const counter = document.getElementById('idle-count');
  const logoutUrl = window.APP_URL + '/logout';

  function startIdle() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(showWarning, IDLE_MINS * 60 * 1000);
  }

  function showWarning() {
    secs = WARN_SECS;
    counter.textContent = secs;
    modal.classList.add('show');
    countTimer = setInterval(function () {
      secs--;
      counter.textContent = secs;
      if (secs <= 0) { clearInterval(countTimer); window.location.href = logoutUrl; }
    }, 1000);
  }

  window.resetIdle = function () {
    clearInterval(countTimer);
    modal.classList.remove('show');
    startIdle();
  };

  ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (ev) {
    document.addEventListener(ev, function () {
      if (!modal.classList.contains('show')) startIdle();
    }, { passive: true });
  });

  startIdle();
})();

// ── MISC HELPERS ──────────────────────────────────────────────
function toggleFeatured(id, state) {
  fetch(window.APP_URL + '/jobs?action=toggle-featured', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + id + '&featured=' + (state ? 1 : 0)
  });
}

function selType(btn) {
  document.querySelectorAll('.type-btn').forEach(function (b) { b.classList.remove('sel'); });
  btn.classList.add('sel');
}

// ── DETAIL MODAL ENGINE ────────────────────────────────────────
(function () {
  const modal = document.getElementById('detail-modal');

  window.closeDetailModal = function () {
    modal.classList.remove('show');
  };

  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeDetailModal();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDetailModal();
  });

  function field(label, value, full) {
    if (!value && value !== 0) value = '<span style="color:#ccc">—</span>';
    return '<div class="detail-field' + (full ? ' full' : '') + '">' +
      '<div class="detail-field-label">' + label + '</div>' +
      '<div class="detail-field-value">' + value + '</div>' +
      '</div>';
  }

  function section(title, fields) {
    return '<div class="detail-section">' +
      '<div class="detail-section-title">' + title + '</div>' +
      '<div class="detail-grid">' + fields + '</div>' +
      '</div>';
  }

  function badge(text, color) {
    return '<span class="badge badge-' + (color || 'blue') + '">' + (text || '—') + '</span>';
  }

  function statusBadge(s) {
    const map = {
      active: 'green', verified: 'green', approved: 'green', resolved: 'green',
      blocked: 'red', pending: 'yellow', suspended: 'red', rejected: 'red',
      open: 'yellow', in_progress: 'blue', closed: 'gray', expired: 'gray',
      hidden: 'gray', reported: 'yellow', offensive: 'red', deleted: 'red'
    };
    return badge(s ? (s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')) : '—', map[s] || 'blue');
  }

  function fmtDate(d) {
    if (!d) return '—';
    try {
      return new Date(d).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    } catch (e) { return d; }
  }

  // ── USER MODAL ──
  function esc(value) {
    if (value === null || value === undefined) return '';
    return String(value).replace(/[&<>"']/g, function (ch) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
  }

  function mediaPreview(media, type) {
    if (!media) return null;
    var url = String(media);
    var fullUrl = /^https?:\/\//i.test(url) ? url : (window.APP_URL + '/' + url.replace(/^\/+/, ''));
    var safeUrl = esc(fullUrl);
    var isImage = (type === 'image') || /\.(jpe?g|png|gif|webp)$/i.test(url);
    if (isImage) {
      return '<a href="' + safeUrl + '" target="_blank"><img src="' + safeUrl + '" alt="Reported post media" style="max-width:100%;max-height:260px;border-radius:6px;border:1px solid #e0e0e0;object-fit:contain"></a>';
    }
    return '<a href="' + safeUrl + '" target="_blank">View uploaded media/file</a>';
  }

  window.openUserModal = function (data) {
    document.getElementById('dm-icon-box').textContent = (data.name || 'U').slice(0, 2).toUpperCase();
    document.getElementById('dm-icon-box').style.background = '#0a66c2';
    document.getElementById('dm-name').textContent = data.name || 'Unknown User';
    document.getElementById('dm-sub').innerHTML = '<i class="ti ti-mail" style="font-size:11px"></i> ' + (data.email || '');
    document.getElementById('dm-id-chip').textContent = 'User #' + data.id;

    document.getElementById('dm-body').innerHTML =
      section('Basic Info',
        field('Full Name', data.name) +
        field('Email', data.email) +
        field('Phone', data.phone) +
        field('Status', statusBadge(data.status))
      ) +
      section('Profile',
        field('Headline', data.headline, true) +
        field('Bio', data.bio, true) +
        field('Location', data.location) +
        field('Website', data.website ? '<a href="' + data.website + '" target="_blank">' + data.website + '</a>' : null)
      ) +
      section('Account',
        field('Role', data.role ? badge(data.role, 'blue') : '—') +
        field('Email Verified', data.email_verified == 1 ? badge('Yes', 'green') : badge('No', 'yellow')) +
        field('Joined', fmtDate(data.created_at)) +
        field('Last Login', fmtDate(data.last_login))
      );

    modal.classList.add('show');
  };

  // ── COMPANY MODAL ──
  window.openCompanyModal = function (data) {
    document.getElementById('dm-icon-box').textContent = (data.name || 'C').slice(0, 2).toUpperCase();
    document.getElementById('dm-icon-box').style.background = '#057642';
    document.getElementById('dm-name').textContent = data.name || 'Unknown Company';
    document.getElementById('dm-sub').innerHTML = '<i class="ti ti-mail" style="font-size:11px"></i> ' + (data.email || '');
    document.getElementById('dm-id-chip').textContent = 'Company #' + data.id;

    document.getElementById('dm-body').innerHTML =
      section('Basic Info',
        field('Company Name', data.name) +
        field('Email', data.email) +
        field('Phone', data.phone) +
        field('Status', statusBadge(data.status))
      ) +
      section('Company Details',
        field('Industry', data.industry) +
        field('Company Size', data.company_size) +
        field('Founded Year', data.founded_year) +
        field('Location', data.location) +
        field('Website', data.website ? '<a href="' + data.website + '" target="_blank">' + data.website + '</a>' : null) +
        field('LinkedIn', data.linkedin_url ? '<a href="' + data.linkedin_url + '" target="_blank">' + data.linkedin_url + '</a>' : null) +
        field('Description', data.description, true)
      ) +
      section('Stats',
        field('Total Jobs', data.jobs_count || '0') +
        field('Registered On', fmtDate(data.created_at))
      );

    modal.classList.add('show');
  };

  // ── JOB MODAL ──
  window.openJobModal = function (data) {
    document.getElementById('dm-icon-box').innerHTML = '<i class="ti ti-briefcase"></i>';
    document.getElementById('dm-icon-box').style.background = '#e65100';
    document.getElementById('dm-name').textContent = data.title || 'Unknown Job';
    document.getElementById('dm-sub').textContent = data.company || '';
    document.getElementById('dm-id-chip').textContent = 'Job #' + data.id;

    const salary = (data.salary_min && data.salary_max)
      ? (data.salary_currency || 'USD') + ' ' + Number(data.salary_min).toLocaleString() + ' – ' + Number(data.salary_max).toLocaleString()
      : (data.salary_min ? (data.salary_currency || 'USD') + ' ' + Number(data.salary_min).toLocaleString() + '+' : null);

    document.getElementById('dm-body').innerHTML =
      section('Job Info',
        field('Title', data.title) +
        field('Company', data.company) +
        field('Location', data.location) +
        field('Status', statusBadge(data.status)) +
        field('Job Type', data.job_type ? badge(data.job_type.replace('_', ' '), 'blue') : null) +
        field('Experience', data.experience_level ? badge(data.experience_level, 'purple') : null) +
        field('Salary', salary) +
        field('Featured', data.is_featured == 1 ? badge('Yes', 'green') : badge('No', 'gray'))
      ) +
      section('Activity',
        field('Applications', data.applications_count || '0') +
        field('Views', data.views_count || '0') +
        field('Posted On', fmtDate(data.created_at)) +
        field('Expires On', data.expires_at ? fmtDate(data.expires_at) : null)
      ) +
      section('Description',
        field('Requirements', data.requirements, true) +
        field('Benefits', data.benefits, true) +
        field('Description', data.description ? data.description.substring(0, 400) + (data.description.length > 400 ? '…' : '') : null, true)
      );

    modal.classList.add('show');
  };

  // ── POST MODAL ──
  window.openPostModal = function (data) {
    document.getElementById('dm-icon-box').innerHTML = '<i class="ti ti-file-text"></i>';
    document.getElementById('dm-icon-box').style.background = '#7b1fa2';
    document.getElementById('dm-name').textContent = data.author || 'Unknown Author';
    document.getElementById('dm-sub').textContent = 'Post / Content';
    document.getElementById('dm-id-chip').textContent = 'Post #' + data.id;

    document.getElementById('dm-body').innerHTML =
      section('Post Details',
        field('Author', data.author) +
        field('Status', statusBadge(data.status)) +
        field('Visibility', data.visibility ? badge(data.visibility, 'blue') : null) +
        field('Media Type', data.media_type && data.media_type !== 'none' ? badge(data.media_type, 'purple') : badge('Text only', 'gray')) +
        field('Posted On', fmtDate(data.created_at))
      ) +
      section('Engagement',
        field('Likes', '<i class="ti ti-heart" style="color:#cc1016"></i> ' + Number(data.likes || 0).toLocaleString()) +
        field('Comments', '<i class="ti ti-message" style="color:#0a66c2"></i> ' + Number(data.comments_count || 0).toLocaleString())
      ) +
      section('Content',
        field('Full Content', data.content ? data.content.replace(/</g, '&lt;') : null, true) +
        (data.media ? field('Media URL', '<a href="' + data.media + '" target="_blank">View Media</a>', true) : '')
      );

    modal.classList.add('show');
  };

  // ── TICKET MODAL ──
  window.openReportModal = function (data) {
    var post = data.post || {};
    var creator = data.creator || {};
    var company = data.company || {};
    var comments = Array.isArray(data.comments) ? data.comments : [];
    var reporter = data.reporter_name || data.reporter_email || 'Anonymous';

    document.getElementById('dm-icon-box').innerHTML = '<i class="ti ti-flag"></i>';
    document.getElementById('dm-icon-box').style.background = '#cc1016';
    document.getElementById('dm-name').textContent = 'Report #' + (data.id || '');
    document.getElementById('dm-sub').textContent = post.id ? ('Post #' + post.id + ' / ' + (post.author || 'Unknown author')) : ('Target: ' + (data.target_type || 'Unknown') + ' #' + (data.target_id || ''));
    document.getElementById('dm-id-chip').textContent = 'Reports: ' + Number(data.total_report_count || 1).toLocaleString();

    var companyFields = company && company.id
      ? section('Creator Company',
          field('Company', esc(company.name)) +
          field('Company Email', esc(company.email)) +
          field('Industry', esc(company.industry)) +
          field('Company Size', esc(company.company_size)) +
          field('Location', esc(company.location)) +
          field('Status', statusBadge(company.status)) +
          field('Website', company.website ? '<a href="' + esc(company.website) + '" target="_blank">' + esc(company.website) + '</a>' : null, true)
        )
      : '';

    var commentsHtml = comments.length
      ? '<div style="display:flex;flex-direction:column;gap:10px">' + comments.map(function (c) {
          return '<div style="border:1px solid #eee;border-radius:6px;padding:10px;background:#fafafa">' +
            '<div style="font-size:12px;color:#888;margin-bottom:5px"><strong style="color:#333">' + esc(c.author_name || 'Unknown') + '</strong>' +
            (c.author_email ? ' &lt;' + esc(c.author_email) + '&gt;' : '') +
            ' &bull; ' + fmtDate(c.created_at) +
            (c.status ? ' &bull; ' + esc(c.status) : '') +
            '</div>' +
            '<div style="font-size:13px;color:#333;white-space:pre-wrap">' + esc(c.content || '') + '</div>' +
          '</div>';
        }).join('') + '</div>'
      : '<span style="color:#ccc">No comments found.</span>';

    document.getElementById('dm-body').innerHTML =
      section('Report Details',
        field('Category', data.type ? badge(esc(data.type), 'red') : null) +
        field('Report Status', statusBadge(data.status)) +
        field('Reported By', esc(reporter)) +
        field('Reporter ID', data.reporter_id ? '#' + esc(data.reporter_id) : null) +
        field('Total Reports', Number(data.total_report_count || 1).toLocaleString()) +
        field('Report Submitted', fmtDate(data.created_at)) +
        field('Reason', esc(data.reason), true)
      ) +
      section('Post Details',
        field('Post ID', post.id ? '#' + esc(post.id) : null) +
        field('Author', esc(post.author || creator.name)) +
        field('Post Status', statusBadge(post.status)) +
        field('Visibility', post.visibility ? badge(esc(post.visibility), 'blue') : null) +
        field('Media Type', post.media_type ? badge(esc(post.media_type), 'purple') : null) +
        field('Created At', fmtDate(post.created_at)) +
        field('Content', esc(post.content), true) +
        field('Uploaded Media/File', mediaPreview(post.media, post.media_type), true)
      ) +
      section('Engagement',
        field('Likes / Reactions', '<i class="ti ti-heart" style="color:#cc1016"></i> ' + Number(post.likes || 0).toLocaleString()) +
        field('Comments', '<i class="ti ti-message" style="color:#0a66c2"></i> ' + Number(post.comments_count || comments.length || 0).toLocaleString())
      ) +
      section('Creator Profile',
        field('Name', esc(creator.name)) +
        field('Email', esc(creator.email)) +
        field('Phone', esc(creator.phone)) +
        field('Role', creator.role ? badge(esc(creator.role), 'blue') : null) +
        field('Status', statusBadge(creator.status)) +
        field('Joined', fmtDate(creator.created_at)) +
        field('Headline', esc(creator.headline), true) +
        field('Bio', esc(creator.bio), true) +
        field('Location', esc(creator.location)) +
        field('Website', creator.website ? '<a href="' + esc(creator.website) + '" target="_blank">' + esc(creator.website) + '</a>' : null)
      ) +
      companyFields +
      section('Comments', field('Full Comments List', commentsHtml, true));

    modal.classList.add('show');
  };

  window.openTicketModal = function (data) {
    document.getElementById('dm-icon-box').innerHTML = '<i class="ti ti-headset"></i>';
    document.getElementById('dm-icon-box').style.background = '#0a66c2';
    document.getElementById('dm-name').textContent = data.name || data.email || 'Unknown';
    document.getElementById('dm-sub').innerHTML = 'Ticket #' + data.id + ' &bull; ' + (data.email || '');
    document.getElementById('dm-id-chip').textContent = 'Ticket #' + data.id;

    const priorityColor = { low: 'gray', normal: 'blue', high: 'red', urgent: 'red' };

    document.getElementById('dm-body').innerHTML =
      section('Ticket Info',
        field('Name', data.name) +
        field('Email', data.email) +
        field('Status', statusBadge(data.status)) +
        field('Priority', data.priority ? badge(data.priority, priorityColor[data.priority] || 'blue') : null) +
        field('Submitted On', fmtDate(data.created_at))
      ) +
      section('Message',
        field('Subject', data.subject, true) +
        field('Message', data.message ? data.message.replace(/\n/g, '<br>') : null, true)
      );

    modal.classList.add('show');
  };

  // ── AGENT APPROVAL MODAL ──
  window.openAgentModal = function (data) {
    document.getElementById('dm-icon-box').textContent = (data.name || 'A').slice(0, 2).toUpperCase();
    document.getElementById('dm-icon-box').style.background = '#7b1fa2';
    document.getElementById('dm-name').textContent = data.name || 'Unknown Agent';
    document.getElementById('dm-sub').innerHTML = '<i class="ti ti-mail" style="font-size:11px"></i> ' + (data.email || '');
    document.getElementById('dm-id-chip').textContent = 'Approval #' + data.id;

    document.getElementById('dm-body').innerHTML =
      section('Agent Info',
        field('Full Name', data.name) +
        field('Email', data.email) +
        field('Phone', data.phone) +
        field('Status', statusBadge(data.status)) +
        field('Location', data.location) +
        field('Website', data.website ? '<a href="' + data.website + '" target="_blank">' + data.website + '</a>' : null) +
        field('Headline', data.headline, true) +
        field('Bio', data.bio, true)
      ) +
      section('Review Info',
        field('Submitted On', fmtDate(data.created_at)) +
        field('Reviewed On', fmtDate(data.reviewed_at)) +
        (data.admin_note ? field('Admin Note', data.admin_note, true) : '')
      );

    modal.classList.add('show');
  };
})();

// ── AGENT PAGE ────────────────────────────────────────────────
function toggleRejectForm(id) {
  const form = document.getElementById('reject-form-' + id);
  if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// ── USER PAGE ─────────────────────────────────────────────────
var _searchTimer = null;
function debounceSearch(input) {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(function () {
    input.closest('form').submit();
  }, 500);
}

// ── NEW ITEM HIGHLIGHT & BADGE (localStorage based) ──────────
(function () {
  var PAGES = {
    users:   { key: 'admin_seen_users',   badgeSel: '[data-admin-badge="users"]'   },
    content: { key: 'admin_seen_content', badgeSel: '[data-admin-badge="content"]' },
    reports: { key: 'admin_seen_reports', badgeSel: '[data-admin-badge="reports"]' },
    agents:  { key: 'admin_seen_agents',  badgeSel: '[data-admin-badge="agents"]'  },
    jobs:    { key: 'admin_seen_jobs',    badgeSel: '[data-admin-badge="jobs"]'    },
  };

  function getSeen(key) {
    try { return new Set(JSON.parse(localStorage.getItem(key) || '[]')); }
    catch (e) { return new Set(); }
  }
  function saveSeen(key, set) {
    try { localStorage.setItem(key, JSON.stringify(Array.from(set))); }
    catch (e) {}
  }
  function hasSeenList(key) {
    return localStorage.getItem(key) !== null;
  }
  function ensureSeenList(pageKey) {
    var cfg = PAGES[pageKey];
    if (!cfg || hasSeenList(cfg.key)) return;
    var ids = (window.BADGE_IDS && window.BADGE_IDS[pageKey]) ? window.BADGE_IDS[pageKey] : [];
    saveSeen(cfg.key, new Set(ids.map(String)));
  }

  // Calculate unseen count from BADGE_IDS (PHP-provided IDs) minus localStorage seen
  function unseenCount(pageKey) {
    var cfg = PAGES[pageKey];
    if (!cfg) return 0;
    var ids = (window.BADGE_IDS && window.BADGE_IDS[pageKey]) ? window.BADGE_IDS[pageKey] : [];
    var seen = getSeen(cfg.key);
    return ids.filter(function (id) { return !seen.has(String(id)); }).length;
  }

  // Update one sidebar badge
  function refreshBadge(pageKey) {
    var cfg = PAGES[pageKey];
    if (!cfg) return;
    var badge = document.querySelector(cfg.badgeSel);
    if (!badge) return;
    var n = unseenCount(pageKey);
    if (n <= 0) { badge.style.display = 'none'; }
    else { badge.style.display = ''; badge.textContent = n; }
  }

  // Update all sidebar badges on every page load
  function refreshAllBadges() {
    Object.keys(PAGES).forEach(ensureSeenList);
    Object.keys(PAGES).forEach(refreshBadge);
  }

  function currentPageKey() {
    var params = new URLSearchParams(window.location.search);
    var queryPage = params.get('page');
    if (queryPage) return queryPage;
    var path = window.location.pathname.replace(/\/+$/, '');
    var last = path.split('/').pop() || 'dashboard';
    var map = {
      users: 'users',
      companies: 'users',
      jobs: 'jobs',
      content: 'content',
      reports: 'reports',
      agents: 'agents',
      dashboard: 'dashboard'
    };
    return map[last] || 'dashboard';
  }

  // Highlight unseen rows/cards on current page
  function highlightUnseen() {
    var curPage = currentPageKey();
    var cfg = PAGES[curPage];
    if (!cfg) return;
    var seen = getSeen(cfg.key);
    document.querySelectorAll('[data-new-id]').forEach(function (el) {
      if (!seen.has(String(el.getAttribute('data-new-id')))) {
        el.classList.add(el.tagName === 'TR' ? 'is-new' : 'card-is-new');
      }
    });
  }

  // Shared: mark one item as seen and refresh its badge
  function markSeen(el, pageKey) {
    var cfg = PAGES[pageKey];
    if (!cfg) return;
    var id = String(el.getAttribute('data-new-id'));
    if (!id) return;
    var seen = getSeen(cfg.key);
    if (seen.has(id)) return;
    seen.add(id);
    saveSeen(cfg.key, seen);
    el.classList.remove('is-new', 'card-is-new');
    refreshBadge(pageKey);
  }

  // Detect current page key from URL
  function curPageKey() {
    return currentPageKey();
  }

  // For rows that navigate on click
  window.markSeenAndGo = function (el, storageKey, badgeSel, url) {
    markSeen(el, curPageKey());
    window.location = url;
  };

  // For rows that open a modal (no navigation)
  window.markSeenModal = function (el, storageKey, badgeSel) {
    markSeen(el, curPageKey());
  };

  document.addEventListener('DOMContentLoaded', function () {
    refreshAllBadges();
    highlightUnseen();
    pollBadgeIds();
    setInterval(pollBadgeIds, 15000);
  });

  function pollBadgeIds() {
    if (!window.APP_URL) return;
    fetch(window.APP_URL + '/admin-badges', {
      headers: { 'Accept': 'application/json' },
      cache: 'no-store'
    })
      .then(function (res) { return res.ok ? res.json() : null; })
      .then(function (data) {
        if (!data || !data.ok || !data.ids) return;
        window.BADGE_IDS = data.ids;
        refreshAllBadges();
        highlightUnseen();
      })
      .catch(function () {});
  }
})();
