(function () {
  const qs = (s, root = document) => root.querySelector(s);
  const qsa = (s, root = document) => [...root.querySelectorAll(s)];

  const ajax = (action, data) =>
    fetch(`${window.APP_URL}/ajax?action=${action}`, {
      method: 'POST',
      headers: data instanceof FormData
        ? { 'X-CSRF-Token': window.CSRF_TOKEN }
        : { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': window.CSRF_TOKEN },
      body: data instanceof FormData ? data : new URLSearchParams(data)
    }).then(r => r.text().then(text => {
      try { return JSON.parse(text); }
      catch { return { ok: false, message: 'Server error. Please refresh and try again.' }; }
    }));

  const toast = msg => {
    const el = document.createElement('div');
    el.className = 'li-toast';
    el.textContent = msg;
    (qs('#toastWrap') || document.body).appendChild(el);
    setTimeout(() => el.remove(), 2800);
  };

  const currentUserId = String(window.CURRENT_USER_ID || '');

  document.querySelectorAll('a[href*="logout"]').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const href = this.href;

      if (window.Swal) {
        Swal.fire({
          title: 'Log Out',
          html: '<p style="color:#555;font-size:.95rem;margin:0;">You\'ll need to sign in again to access your account.</p>',
          icon: 'warning',
          iconColor: '#e7a33e',
          showCancelButton: true,
          confirmButtonText: '<i class="bi bi-box-arrow-right"></i> Log Out',
          cancelButtonText: 'Stay',
          confirmButtonColor: '#cc1016',
          cancelButtonColor: '#0a66c2',
          reverseButtons: true,
          focusCancel: true,
          customClass: {
            popup: 'li-swal-popup',
            title: 'li-swal-title',
            confirmButton: 'li-swal-confirm',
            cancelButton: 'li-swal-cancel'
          }
        }).then(r => {
          if (r.isConfirmed) window.location.href = href;
        });
      } else {
        if (confirm('Are you sure you want to log out?')) window.location.href = href;
      }
    });
  });

  qsa('.connect-btn').forEach(btn => btn.addEventListener('click', async () => {
    await ajax('connect', { user_id: btn.dataset.user });
    btn.textContent = 'Pending';
    btn.disabled = true;
    toast('Connection request sent');
  }));

  qsa('.connection-action').forEach(btn => btn.addEventListener('click', async (e) => {
    e.stopPropagation();
    e.preventDefault();
    const status = btn.dataset.status;
    const row = btn.closest('.people-row');

    // Disable buttons to prevent double-click
    row?.querySelectorAll('button').forEach(b => b.disabled = true);

    let res;
    try {
      res = await ajax('connection', { id: btn.dataset.id, status });
    } catch (e) {
      toast('Network error. Please try again.');
      row?.querySelectorAll('button').forEach(b => b.disabled = false);
      return;
    }

    if (!res?.ok) {
      toast(res?.message || 'Something went wrong. Please try again.');
      row?.querySelectorAll('button').forEach(b => b.disabled = false);
      return;
    }

    // Remove from Invitations only after confirmed success
    row?.remove();

    // Update Invitations count badge
    const invBadge = document.querySelector('.card-title .badge.bg-primary');
    if (invBadge) {
      const cur = parseInt(invBadge.textContent) || 0;
      invBadge.textContent = Math.max(0, cur - 1);
    }

    // Update nav Network badge
    const navNetworkLink = document.querySelector('a[href*="page=network"]');
    const navBadge = navNetworkLink?.querySelector('.nav-badge');
    if (navBadge) {
      const cur = parseInt(navBadge.textContent) || 0;
      const newVal = Math.max(0, cur - 1);
      if (newVal === 0) navBadge.remove();
      else navBadge.textContent = newVal;
    }

    if (status === 'accepted' && res?.user) {
      const u = res.user;
      const APP_URL = window.APP_URL || '';

      // Build avatar HTML
      const avatarHtml = u.avatar
        ? `<img src="${APP_URL}/${u.avatar}" alt="${u.name}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`
        : `<span style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#0a66c2;color:#fff;font-weight:700;font-size:.85rem;border-radius:50%;">${u.name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase()}</span>`;

      const newRow = document.createElement('div');
      newRow.className = 'people-row';
      newRow.innerHTML = `
        <a href="${APP_URL}/view-profile?id=${u.id}" style="display:contents;">
          <div class="mini-avatar" style="cursor:pointer;">${avatarHtml}</div>
          <div style="cursor:pointer;">
            <strong>${u.name}</strong>
            <span>${u.headline || 'LinkedIn member'}</span>
          </div>
        </a>
        <a class="btn btn-outline-primary btn-sm" href="${APP_URL}/messages?with=${u.id}">
          <i class="bi bi-chat-dots"></i> Message
        </a>`;

      // Append to My Connections section
      const connSection = document.querySelector('.card-title .badge.bg-secondary');
      if (connSection) {
        const section = connSection.closest('section');
        // Remove "no connections" placeholder if present
        section.querySelector('.muted')?.remove();
        section.appendChild(newRow);

        // Update My Connections count
        const connBadge = section.querySelector('.badge.bg-secondary');
        if (connBadge) {
          connBadge.textContent = (parseInt(connBadge.textContent) || 0) + 1;
        }
      }

      toast('Connection accepted!');
    } else {
      toast('Invitation updated');
    }
  }));

  // Post three-dots menu toggle
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.post-menu-btn');
    if (btn) {
      e.stopPropagation();
      const menu = document.getElementById('post-menu-' + btn.dataset.post);
      if (!menu) return;
      const isOpen = menu.style.display === 'block';
      document.querySelectorAll('.post-menu-dropdown').forEach(m => m.style.display = 'none');
      menu.style.display = isOpen ? 'none' : 'block';
      return;
    }
    // Close all menus on outside click
    if (!e.target.closest('.post-menu-wrap')) {
      document.querySelectorAll('.post-menu-dropdown').forEach(m => m.style.display = 'none');
    }
  });

  // Report post — open modal
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.post-report-btn');
    if (!btn) return;
    e.stopPropagation();
    document.querySelectorAll('.post-menu-dropdown').forEach(m => m.style.display = 'none');
    const modal = document.getElementById('report-modal-' + btn.dataset.post);
    if (!modal) return;
    modal.style.display = 'flex';
  });

  // Report modal — close
  document.addEventListener('click', function(e) {
    if (e.target.closest('.report-modal-close')) {
      const postId = e.target.closest('.report-modal-close').dataset.post;
      const modal = document.getElementById('report-modal-' + postId);
      if (modal) { modal.style.display = 'none'; modal.querySelector('.report-reason-input').value = ''; modal.querySelector('.report-error').style.display = 'none'; }
    }
    // Close on backdrop click
    if (e.target.classList.contains('report-modal-overlay')) {
      e.target.style.display = 'none';
    }
  });

  // Report modal — submit
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.report-submit-btn');
    if (!btn) return;
    const postId = btn.dataset.post;
    const modal = document.getElementById('report-modal-' + postId);
    const type = modal.querySelector('.report-type-select').value;
    const reason = modal.querySelector('.report-reason-input').value.trim();
    const errEl = modal.querySelector('.report-error');
    if (!reason) { errEl.textContent = 'Please describe the issue.'; errEl.style.display = 'block'; return; }
    errEl.style.display = 'none';
    btn.disabled = true; btn.textContent = 'Submitting...';
    try {
      await ajax('report-post', { post_id: postId, type, reason });
      modal.style.display = 'none';
      modal.querySelector('.report-reason-input').value = '';
      toast('Report submitted. We will review it shortly.');
    } catch {
      errEl.textContent = 'Could not submit report. Try again.';
      errEl.style.display = 'block';
    }
    btn.disabled = false; btn.textContent = 'Submit report';
  });

  // Post delete button
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.post-delete-btn');
    if (!btn) return;
    e.stopPropagation();
    if (!confirm('Delete this post?')) return;
    const postId = btn.dataset.post;
    try {
      await ajax('delete-post', { post_id: postId });
      const card = document.getElementById('post-' + postId);
      if (card) card.remove();
      toast('Post deleted.');
    } catch {
      toast('Could not delete post.');
    }
  });

  // Post feed: Connected button — remove connection
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.post-connected-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    if (!confirm('Do you want to remove connection?')) return;
    const userId = btn.dataset.user;
    try {
      await ajax('remove-connection', { user_id: userId });
      btn.classList.remove('post-connected-btn');
      btn.classList.add('post-connect-btn');
      btn.style.borderColor = '#0a66c2';
      btn.style.color = '#0a66c2';
      btn.innerHTML = '<i class="bi bi-person-plus-fill"></i> Connect';
      toast('Connection removed.');
    } catch {
      toast('Could not remove connection.');
    }
  });

  // Post feed: Connect button (for user posts)
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.post-connect-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    const userId = btn.dataset.user;
    try {
      await ajax('connect', { user_id: userId });
      btn.outerHTML = '<span style="font-size:.72rem;color:#888;font-weight:600;"><i class="bi bi-clock"></i> Pending</span>';
      toast('Connection request sent');
    } catch {
      toast('Could not send request.');
    }
  });

  // Post feed: Follow / Unfollow button (for company posts)
  document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.post-follow-company-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    const userId = btn.dataset.user;
    const isFollowing = btn.classList.contains('post-following-btn');

    if (isFollowing) {
      // Show unfollow confirm
      if (!confirm('Do you want to unfollow?')) return;
    }

    try {
      const res = await ajax('follow-user-company', { user_id: userId });
      if (res && res.following) {
        btn.classList.add('post-following-btn');
        btn.style.borderColor = '#057642';
        btn.style.color = '#057642';
        btn.innerHTML = '<i class="bi bi-check2"></i> Following';
        toast('You are now following this company');
      } else {
        btn.classList.remove('post-following-btn');
        btn.style.borderColor = '#0a66c2';
        btn.style.color = '#0a66c2';
        btn.innerHTML = '<i class="bi bi-plus-lg"></i> Follow';
        toast('Unfollowed successfully');
      }
    } catch {
      toast('Could not update follow status.');
    }
  });

  qsa('.like-btn').forEach(btn => {
    btn.innerHTML = btn.classList.contains('active')
      ? '<i class="bi bi-hand-thumbs-up-fill"></i> Unlike'
      : '<i class="bi bi-hand-thumbs-up"></i> Like';

    btn.addEventListener('click', async () => {
      const res = await ajax('like', { post_id: btn.dataset.id });

      btn.classList.toggle('active', !!res.liked);
      btn.innerHTML = res.liked
        ? '<i class="bi bi-hand-thumbs-up-fill"></i> Unlike'
        : '<i class="bi bi-hand-thumbs-up"></i> Like';

      const c = qs(`#like-count-${btn.dataset.id}`);
      if (c && res.count !== undefined) c.textContent = res.count + ' likes';
    });
  });

  function escHtml(s) {
    return String(s || '').replace(/[&<>"']/g, c => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[c]));
  }

  function timeAgo(dateStr) {
    if (!dateStr) return 'just now';

    const diff = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T')).getTime()) / 1000);

    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h';

    return Math.floor(diff / 86400) + 'd';
  }

  function renderComment(c, postId, isPostOwner) {
    const isMine = String(c.user_id) === currentUserId;
    const canDel = isMine || isPostOwner;

    const delBtn = canDel
      ? `<button class="delete-comment-btn" data-comment="${c.id}" data-post="${postId}"
           style="background:none;border:none;color:#dc3545;font-size:.75rem;padding:0 0 0 6px;cursor:pointer;opacity:.7;"
           title="Delete"><i class="bi bi-trash"></i></button>`
      : '';

    const replyBtn = !isMine
      ? `<button class="reply-comment-btn" data-comment="${c.id}" data-post="${postId}" data-name="${escHtml(c.name)}"
           style="background:none;border:none;color:#0a66c2;font-size:.75rem;padding:0 0 0 6px;cursor:pointer;"
           title="Reply"><i class="bi bi-reply"></i> Reply</button>`
      : '';

    const avatarSrc = isMine ? (window.CURRENT_USER_AVATAR || c.avatar) : c.avatar;
    const avatarName = isMine ? (window.CURRENT_USER_NAME || c.name || '?') : (c.name || '?');
    const avatar = avatarSrc
      ? `<img src="${window.APP_URL}/${escHtml(avatarSrc)}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">`
      : `<div style="width:32px;height:32px;border-radius:50%;background:#0a66c2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">${escHtml(avatarName.charAt(0).toUpperCase())}</div>`;

    const replies = (c.replies && c.replies.length)
      ? `<div style="padding-left:36px;margin-top:6px;">${c.replies.map(r => renderComment(r, postId, isPostOwner)).join('')}</div>`
      : '';

    return `<div class="comment-item" id="comment-${c.id}" style="display:flex;gap:8px;align-items:flex-start;margin-bottom:10px;">
      ${avatar}
      <div style="flex:1;">
        <div style="background:#f2f2f2;border-radius:12px;padding:8px 12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px;">
            <strong style="font-size:.83rem;">${escHtml(c.name)}</strong>
            <span style="display:flex;align-items:center;">${delBtn}${replyBtn}</span>
          </div>
          <p style="margin:2px 0 0;font-size:.85rem;line-height:1.4;">${escHtml(c.content)}</p>
        </div>
        <span style="font-size:.72rem;color:#888;padding-left:4px;">${timeAgo(c.created_at)}</span>
      </div>
    </div>${replies}`;
  }

  async function loadComments(postId, showAll) {
    const res = await fetch(`${window.APP_URL}/ajax?action=comments&post_id=${postId}${showAll ? '&all=1' : ''}`).then(r => r.json());
    const comments = res.comments || [];
    const list = qs(`#comments-list-${postId}`);

    if (!list) return comments.length;

    const isOwner = list.dataset.owner === '1';
    const preview = showAll ? comments : comments.slice(0, 2);

    list.innerHTML = preview.map(c => renderComment(c, postId, isOwner)).join('');
    list.dataset.loaded = '1';

    const saw = qs(`#see-all-${postId}`);
    if (saw) saw.style.display = (!showAll && comments.length > 2) ? 'block' : 'none';

    attachCommentActions(postId);
    return comments.length;
  }

  function attachCommentActions(postId) {
    const listEl = qs(`#comments-list-${postId}`);
    if (!listEl) return;

    qsa('.delete-comment-btn', listEl).forEach(btn => {
      btn.onclick = async () => {
        if (!confirm('Delete this comment?')) return;

        const res = await ajax('delete-comment', {
          comment_id: btn.dataset.comment,
          post_id: btn.dataset.post
        });

        if (res.ok) {
          qs(`#comment-${btn.dataset.comment}`)?.remove();

          const c = qs(`#comment-count-${postId}`);
          if (c && res.count !== undefined) c.textContent = res.count + ' comments';

          toast('Comment deleted');
        }
      };
    });

    qsa('.reply-comment-btn', listEl).forEach(btn => {
      btn.onclick = () => {
        const form = qs(`.comment-form[data-id="${postId}"]`);
        if (!form) return;

        const input = qs('input[name=content]', form);
        if (!input) return;

        input.value = `@${btn.dataset.name} `;
        input.focus();

        form.dataset.parentId = btn.dataset.comment;
      };
    });
  }

  qsa('.comment-toggle').forEach(btn => btn.addEventListener('click', () => {
    const postId = btn.dataset.id;
    const box = qs(`#comments-${postId}`);

    if (!box) return;

    const isVisible = box.style.display !== 'none';

    if (isVisible) {
      box.style.display = 'none';
      return;
    }

    box.style.display = 'block';

    const listEl = qs(`#comments-list-${postId}`);
    if (listEl && listEl.dataset.loaded === '0') {
      loadComments(postId, false);
    }

    const input = qs(`#comments-${postId} input[name=content]`);
    if (input) {
      input.focus();
      input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }));

  document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.see-all-btn');
    if (!btn) return;

    await loadComments(btn.dataset.id, true);
    btn.closest('.see-all-wrap').style.display = 'none';
  });

  document.addEventListener('submit', async function (e) {
    const form = e.target.closest('.comment-form');
    if (!form) return;

    e.preventDefault();

    const input = qs('input[name=content]', form);
    const content = input.value.trim();

    if (!content) return;

    const postId = form.dataset.id;
    const parentId = form.dataset.parentId || '';
    const box = qs(`#comments-${postId}`);

    input.value = '';
    delete form.dataset.parentId;

    const res = await ajax('comment', {
      post_id: postId,
      content,
      parent_id: parentId
    });

    if (res.ok && res.comment) {
      if (box) box.style.display = 'block';

      const listEl = qs(`#comments-list-${postId}`);
      const isOwner = listEl ? listEl.dataset.owner === '1' : false;

      if (listEl) {
        if (!res.comment.avatar && window.CURRENT_USER_AVATAR) res.comment.avatar = window.CURRENT_USER_AVATAR;
        if (!res.comment.name && window.CURRENT_USER_NAME) res.comment.name = window.CURRENT_USER_NAME;
        if (!res.comment.content) res.comment.content = content;
        if (!res.comment.user_id && window.CURRENT_USER_ID) res.comment.user_id = window.CURRENT_USER_ID;
        listEl.insertAdjacentHTML('afterbegin', renderComment(res.comment, postId, isOwner));
        listEl.dataset.loaded = '1';
        attachCommentActions(postId);
      } else {
        await loadComments(postId, true);
      }

      const countEl = qs(`#comment-count-${postId}`);
      if (countEl && res.count !== undefined) {
        countEl.textContent = res.count + ' comments';
      }

      const seeAll = qs(`#see-all-${postId}`);
      if (seeAll) seeAll.style.display = 'none';

      qs(`#comment-${res.comment.id}`)?.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      });

      toast('Comment posted!');
    } else {
      toast(res.message || 'Could not post comment.');
    }
  });

  const postModal = qs('#postModal');

  if (postModal) {
    const textarea = qs('#postTextarea');
    const submitBtn = qs('#postSubmitBtn');
    const mediaInput = qs('#postMediaInput');
    const emojiToggle = qs('#emojiToggleBtn');
    const emojiPanel = qs('#emojiPanel');
    const mediaPreviewArea = qs('#mediaPreviewArea');
    const mediaPreviewImg = qs('#mediaPreviewImg');
    const mediaPreviewVid = qs('#mediaPreviewVid');
    const mediaPreviewDoc = qs('#mediaPreviewDoc');
    const mediaPreviewDocName = qs('#mediaPreviewDocName');
    const removeMediaBtn = qs('#removeMediaBtn');

    const checkPostReady = () => {
      const hasText = textarea && textarea.value.trim().length > 0;
      const hasMedia = mediaInput && mediaInput.files && mediaInput.files.length > 0;

      if (submitBtn) submitBtn.disabled = !(hasText || hasMedia);
    };

    if (textarea) textarea.addEventListener('input', checkPostReady);

    if (emojiToggle && emojiPanel) {
      emojiToggle.addEventListener('click', e => {
        e.stopPropagation();
        emojiPanel.style.display = emojiPanel.style.display === 'none' ? 'flex' : 'none';
      });

      document.addEventListener('click', e => {
        if (!emojiPanel.contains(e.target) && e.target !== emojiToggle) {
          emojiPanel.style.display = 'none';
        }
      });
    }

    qsa('.emoji-pick').forEach(span => span.addEventListener('click', () => {
      if (!textarea) return;

      const s = textarea.selectionStart;
      const end = textarea.selectionEnd;

      textarea.value = textarea.value.slice(0, s) + span.textContent + textarea.value.slice(end);
      textarea.selectionStart = textarea.selectionEnd = s + span.textContent.length;

      textarea.focus();
      checkPostReady();
    }));

    const clearMediaPreview = () => {
      if (mediaInput) mediaInput.value = '';
      if (mediaPreviewArea) mediaPreviewArea.style.display = 'none';

      if (mediaPreviewImg) {
        mediaPreviewImg.style.display = 'none';
        mediaPreviewImg.src = '';
      }

      if (mediaPreviewVid) {
        mediaPreviewVid.style.display = 'none';
        mediaPreviewVid.src = '';
      }

      if (mediaPreviewDoc) mediaPreviewDoc.style.display = 'none';

      checkPostReady();
    };

    if (mediaInput) mediaInput.addEventListener('change', () => {
      const file = mediaInput.files[0];

      if (!file) {
        clearMediaPreview();
        return;
      }

      mediaPreviewArea.style.display = 'block';

      if (file.type.startsWith('image/')) {
        mediaPreviewImg.style.display = 'block';
        mediaPreviewVid.style.display = 'none';
        mediaPreviewDoc.style.display = 'none';

        const r = new FileReader();
        r.onload = e => {
          mediaPreviewImg.src = e.target.result;
        };
        r.readAsDataURL(file);
      } else if (file.type === 'video/mp4') {
        mediaPreviewImg.style.display = 'none';
        mediaPreviewVid.style.display = 'block';
        mediaPreviewDoc.style.display = 'none';
        mediaPreviewVid.src = URL.createObjectURL(file);
      } else {
        mediaPreviewImg.style.display = 'none';
        mediaPreviewVid.style.display = 'none';
        mediaPreviewDoc.style.display = 'flex';

        if (mediaPreviewDocName) mediaPreviewDocName.textContent = file.name;
      }

      checkPostReady();
    });

    if (removeMediaBtn) removeMediaBtn.addEventListener('click', clearMediaPreview);

    postModal.addEventListener('hidden.bs.modal', () => {
      if (textarea) textarea.value = '';
      clearMediaPreview();

      if (emojiPanel) emojiPanel.style.display = 'none';

      checkPostReady();
    });

    const postForm = qs('.ajax-post-form');

    if (postForm) postForm.addEventListener('submit', async e => {
      e.preventDefault();

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting...';
      }

      const fd = new FormData(postForm);

      if (mediaInput && mediaInput.files[0]) {
        fd.set('media', mediaInput.files[0]);
      }

      let res = null;
      try { res = await ajax('post', fd); } catch(err) {}

      const modalInstance = bootstrap.Modal.getInstance(postModal);
      if (modalInstance) modalInstance.hide();
      else { postModal.classList.remove('show'); postModal.style.display = 'none'; document.body.classList.remove('modal-open'); const bd = document.querySelector('.modal-backdrop'); if(bd) bd.remove(); }

      toast('Post published!');

      const postId = (res && res.post_id) ? res.post_id : Date.now();
      const avatar = window.CURRENT_USER_AVATAR
          ? `<img src="${window.APP_URL}/${escHtml(window.CURRENT_USER_AVATAR)}" alt="${escHtml(window.CURRENT_USER_NAME)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`
          : `<span style="font-weight:700;font-size:.85rem;">${escHtml((window.CURRENT_USER_NAME||'U').substring(0,2).toUpperCase())}</span>`;
        const content = fd.get('content') || '';
        const mediaFile = mediaInput && mediaInput.files[0];
        let mediaHtml = '';
        if (mediaFile) {
          if (mediaFile.type.startsWith('image/')) {
            mediaHtml = `<div class="post-media"><img src="${escHtml(URL.createObjectURL(mediaFile))}" alt="Post media"></div>`;
          } else if (mediaFile.type === 'video/mp4') {
            mediaHtml = `<div class="post-media"><video src="${escHtml(URL.createObjectURL(mediaFile))}" controls></video></div>`;
          } else {
            mediaHtml = `<div class="post-media"><a class="doc-preview" href="#"><i class="bi bi-file-earmark-pdf"></i> ${escHtml(mediaFile.name)}</a></div>`;
          }
        }
        const postHtml = `<article class="li-card post-card" data-post="${postId}" data-owner="1" id="post-${postId}">
          <header class="post-head">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
              <div class="mini-avatar" style="flex-shrink:0;">${avatar}</div>
              <div>
                <div><strong>${escHtml(window.CURRENT_USER_NAME)}</strong></div>
                <span>Just now</span>
              </div>
            </div>
            <div style="position:relative;margin-left:auto;" class="post-menu-wrap">
              <button class="icon-only post-menu-btn" data-post="${postId}"><i class="bi bi-three-dots"></i></button>
              <div class="post-menu-dropdown" id="post-menu-${postId}" style="display:none;position:absolute;right:0;top:36px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:180px;z-index:99;">
                <button class="post-delete-btn" data-post="${postId}" style="display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;border:none;background:none;color:#cc1016;font-size:.9rem;font-weight:600;cursor:pointer;border-radius:8px;"><i class="bi bi-trash3"></i> Delete post</button>
              </div>
            </div>
          </header>
          <p class="post-body">${escHtml(content).replace(/\n/g,'<br>')}</p>
          ${mediaHtml}
          <div class="post-counts" id="post-counts-${postId}"><span id="like-count-${postId}">0 likes</span><span id="comment-count-${postId}">0 comments</span></div>
          <div class="post-actions">
            <button class="like-btn" data-id="${postId}"><i class="bi bi-hand-thumbs-up"></i> Like</button>
            <button class="comment-toggle" data-id="${postId}"><i class="bi bi-chat-text"></i> Comment</button>
          </div>
          <div class="comment-box" id="comments-${postId}" style="display:none;">
            <form class="comment-form" data-id="${postId}">
              <div class="comment-input-wrap">
                <input class="form-control" name="content" placeholder="Add a comment..." autocomplete="off">
                <button type="submit" class="comment-post-btn">Post</button>
              </div>
            </form>
            <div class="comments-list" id="comments-list-${postId}" data-owner="1" data-loaded="0"></div>
          </div>
        </article>`;
        const feedList = qs('#feedList');
        if (feedList) feedList.insertAdjacentHTML('afterbegin', postHtml);
    });
  }

  document.addEventListener('click', function (e) {
    const card = e.target.closest('.job-card');
    if (card && !e.target.closest('.apply-btn, .withdraw-btn, .save-job-btn')) {
      const jobId = card.dataset.jobId;
      if (jobId && window.openJobDetail) window.openJobDetail(jobId);
    }
  });

  document.addEventListener('click', function (e) {
    const applyBtn = e.target.closest('.apply-btn');
    if (!applyBtn) return;

    e.preventDefault();
    e.stopPropagation();

    const jobId = applyBtn.dataset.job;
    const input = qs('#applyJobId');

    if (input) input.value = jobId;

    const modal = qs('#applyModal');
    if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
  });

  const applyForm = qs('#applyForm');

  if (applyForm) applyForm.addEventListener('submit', async e => {
    e.preventDefault();

    // Validate resume is provided
    const resumeInput = applyForm.querySelector('input[name="resume"]');
    const resumeError = document.getElementById('resumeError');
    if (resumeInput && (!resumeInput.files || resumeInput.files.length === 0)) {
      if (resumeError) resumeError.style.display = 'block';
      resumeInput.classList.add('is-invalid');
      resumeInput.focus();
      return;
    }
    if (resumeError) resumeError.style.display = 'none';
    if (resumeInput) resumeInput.classList.remove('is-invalid');

    const jobId = qs('#applyJobId')?.value;

    try {
      const res = await ajax('apply', new FormData(applyForm));

      bootstrap.Modal.getInstance(qs('#applyModal'))?.hide();

      if (res.ok) {
        setJobApplied(jobId, true);
        applyForm.reset();
        toast('Application submitted successfully!');
      } else {
        toast(res.message || 'Could not submit application.');
      }
    } catch (err) {
      bootstrap.Modal.getInstance(qs('#applyModal'))?.hide();
      toast('Network error. Please try again.');
    }
  });

  document.addEventListener('click', function (e) {
    const wBtn = e.target.closest('.withdraw-btn');
    if (!wBtn) return;

    e.preventDefault();
    e.stopPropagation();

    const jobId = wBtn.dataset.job || wBtn.dataset.jobId;
    if (!jobId) return;

    const run = () => doWithdraw(jobId);

    if (window.Swal) {
      Swal.fire({
        title: 'Withdraw Application?',
        text: 'Are you sure you want to withdraw your application for this job?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Withdraw',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#cc1016',
        cancelButtonColor: '#0a66c2',
        reverseButtons: true
      }).then(r => {
        if (r.isConfirmed) run();
      });
    } else {
      if (confirm('Withdraw your application for this job?')) run();
    }
  });

  function doWithdraw(jobId) {
    ajax('withdraw-application', { job_id: jobId })
      .then(data => {
        if (!data.ok) {
          toast(data.message || 'Could not withdraw. Please try again.');
          return;
        }

        bootstrap.Modal.getInstance(qs('#appliedJobDetailModal'))?.hide();
        bootstrap.Modal.getInstance(qs('#jobDetailModal'))?.hide();

        setJobApplied(jobId, false);
        removeAppliedJobRow(jobId);

        toast('Application withdrawn successfully.');
      })
      .catch(() => toast('Network error. Please try again.'));
  }

  function setJobApplied(jobId, applied) {
    qsa(`.apply-btn[data-job="${jobId}"], .withdraw-btn[data-job="${jobId}"]`).forEach(btn => {
      btn.className = applied ? 'btn btn-sm btn-success withdraw-btn' : 'btn btn-sm btn-primary apply-btn';
      btn.dataset.job = jobId;
      btn.innerHTML = applied
        ? '<i class="bi bi-check-circle-fill me-1"></i>Applied'
        : '<i class="bi bi-lightning-fill me-1"></i>Easy Apply';
    });

    // jobsData update
    var job = null;
    if (window.jobsData) {
      job = window.jobsData.find(j => j.id == jobId) || null;
      if (job) job.applied = applied ? 1 : 0;
    }

    // Applied Jobs sidebar update
    if (applied) {
      var jobTitle = (job && job.title) ? job.title : '';
      var jobCompany = (job && job.company) ? job.company : '';

      var appliedTitle = [...document.querySelectorAll('.card-title')]
        .find(el => el.textContent.trim() === 'Applied Jobs');

      if (appliedTitle) {
        var container = appliedTitle.closest('.li-card');
        if (container) {
          // "No applications yet." message hatao
          var emptyMsg = container.querySelector('.muted');
          if (emptyMsg) emptyMsg.remove();

          // Pehle se row hai toh dobara mat lagao
          if (!container.querySelector('[data-applied-job="' + jobId + '"]')) {
            var row = document.createElement('div');
            row.className = 'status-row';
            row.dataset.appliedJob = jobId;
            row.style.cssText = 'cursor:pointer;padding:10px 16px;border-bottom:1px solid #f0f0f0;';
            row.innerHTML = '<strong style="display:block;font-size:.85rem;">' + jobTitle + '</strong>'
              + '<span style="font-size:.78rem;color:#666;">' + jobCompany + '</span>';
            row.onclick = function () {
              if (window.openAppliedJobDetail) window.openAppliedJobDetail(jobId, jobTitle, jobCompany, 'applied');
            };
            container.appendChild(row);
          }
        }
      }

      // applicationsData update
      if (window.applicationsData && !window.applicationsData.find(function(a){ return a.job_id == jobId; })) {
        window.applicationsData.push({ job_id: jobId, title: jobTitle, company: jobCompany, status: 'applied' });
      }
    }

    if (!applied) {
      if (window.applicationsData) {
        window.applicationsData = window.applicationsData.filter(function(a){ return a.job_id != jobId; });
      }
    }
  }

  function removeAppliedJobRow(jobId) {
    const row = qs(`[data-applied-job="${jobId}"]`);
    if (row) row.remove();

    const appliedTitle = [...document.querySelectorAll('.li-card .card-title')]
      .find(el => el.textContent.trim() === 'Applied Jobs');

    if (!appliedTitle) return;

    const container = appliedTitle.closest('.li-card');
    if (!container) return;

    if (!container.querySelector('.status-row')) {
      const oldEmpty = container.querySelector('.muted');
      if (oldEmpty) return;

      const p = document.createElement('p');
      p.className = 'muted';
      p.style.cssText = 'font-size:.85rem;padding:0 16px 12px;';
      p.textContent = 'No applications yet.';
      container.appendChild(p);
    }
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.save-job-btn');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    if (btn.disabled) return;

    btn.disabled = true;

    const jobId = btn.dataset.job;

    ajax('save-job', { job_id: jobId })
      .then(data => {
        btn.disabled = false;

        if (!data.ok) {
          toast(data.message || 'Could not save job.');
          return;
        }

        const saved = !!data.saved;

        btn.className = 'btn btn-sm save-job-btn ' + (saved ? 'btn-primary' : 'btn-outline-secondary');
        btn.innerHTML = '<i class="bi ' + (saved ? 'bi-bookmark-fill' : 'bi-bookmark') + '"></i> ' + (saved ? 'Saved' : 'Save');
        btn.title = saved ? 'Saved' : 'Save job';

        if (window.jobsData) {
          const job = window.jobsData.find(j => j.id == jobId);
          if (job) job.saved = saved ? 1 : 0;
        }

        updateSavedJobsSidebar();

        toast(saved ? 'Job saved!' : 'Job removed from saved.');
      })
      .catch(() => {
        btn.disabled = false;
        toast('Network error. Please try again.');
      });
  });

  function updateSavedJobsSidebar() {
    const savedSection = [...document.querySelectorAll('.li-card .card-title')]
      .find(el => el.textContent.trim() === 'Saved Jobs');

    if (!savedSection) return;

    const container = savedSection.closest('.li-card');
    if (!container) return;

    const savedJobs = (window.jobsData || []).filter(j => j.saved == 1);

    [...container.querySelectorAll('.status-row')].forEach(el => el.remove());

    const emptyMsg = container.querySelector('.muted');
    if (emptyMsg) emptyMsg.remove();

    if (savedJobs.length === 0) {
      const p = document.createElement('p');
      p.className = 'muted';
      p.style.cssText = 'font-size:.85rem;padding:0 16px 12px;';
      p.textContent = 'No saved jobs.';
      container.appendChild(p);
      return;
    }

    savedJobs.forEach(job => {
      const div = document.createElement('div');
      div.className = 'status-row';
      div.style.cssText = 'cursor:pointer;padding:10px 16px;border-bottom:1px solid #f0f0f0;';
      div.onclick = () => window.openJobDetail && window.openJobDetail(job.id);
      div.innerHTML = `<strong style="display:block;font-size:.85rem;">${escHtml(job.title)}</strong>
        <span style="font-size:.78rem;color:#666;">${escHtml(job.company)}</span>`;
      container.appendChild(div);
    });
  }

  qsa('.app-status').forEach(sel => sel.addEventListener('change', async () => {
    const newStatus = sel.value;
    const appId = sel.dataset.id;
    const card = sel.closest('article.candidate');
    const oldCol = sel.closest('section.ats-col');

    await ajax('app-status', {
      application_id: appId,
      status: newStatus
    });

    toast('Candidate stage updated');

    // Move card to new column in real-time (no reload)
    const targetCol = document.querySelector(`.ats-col[data-stage="${newStatus}"]`);
    if (targetCol && card) {
      const wrap = targetCol.querySelector('.ats-cards-wrap');
      if (wrap) {
        // Remove empty placeholder if present
        const empty = wrap.querySelector('.ats-empty-col');
        if (empty) empty.remove();

        wrap.prepend(card);

        // Update column count badges
        [oldCol, targetCol].forEach(col => {
          if (!col) return;
          const countEl = col.querySelector('.ats-col-count');
          const cards = col.querySelectorAll('article.candidate').length;
          if (countEl) countEl.textContent = cards;
          // Restore empty placeholder if column is now empty
          const colWrap = col.querySelector('.ats-cards-wrap');
          if (colWrap && !colWrap.querySelector('article.candidate')) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'ats-empty-col';
            emptyDiv.innerHTML = '<i class="bi bi-inbox"></i><span>Empty</span>';
            colWrap.appendChild(emptyDiv);
          }
        });

        // Also update stage summary badges at top of page
        document.querySelectorAll('.ats-col').forEach(col => {
          const stage = col.dataset.stage;
          const count = col.querySelectorAll('article.candidate').length;
          // Update the top summary pill
          const summaryPills = document.querySelectorAll(`[data-stage-summary="${stage}"]`);
          summaryPills.forEach(pill => { pill.textContent = count; });
        });
      }
    }
  }));

  const msgForm = qs('#messageForm');

  if (msgForm) msgForm.addEventListener('submit', async e => {
    e.preventDefault();

    const bodyInput = qs('#msgBody');
    const fileInput = qs('#msgFile');
    const body = bodyInput ? bodyInput.value.trim() : '';
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

    if (!body && !hasFile) {
      toast('Please type a message or attach a file.');
      return;
    }

    const fd = new FormData(msgForm);

    if (bodyInput) fd.set('body', body);

    const res = await ajax('send-message', fd);

    if (res.ok) {
      appendMessage(res.message, true);
      msgForm.reset();
    }
  });

  const chat = qs('#chatMessages');

  if (chat) setInterval(async () => {
    const res = await fetch(`${window.APP_URL}/ajax?action=messages&with=${chat.dataset.with}`).then(r => r.json());

    chat.innerHTML = (res.messages || []).map(m => messageHtml(m, String(m.sender_id) !== chat.dataset.with)).join('');
    chat.scrollTop = chat.scrollHeight;
  }, 5000);

  function appendMessage(m, mine) {
    const el = qs('#chatMessages');
    if (!el) return;

    el.insertAdjacentHTML('beforeend', messageHtml(m, mine));
    el.scrollTop = el.scrollHeight;
  }

  function messageHtml(m, mine) {
    let att = '';

    if (m.attachment) {
      const isImg = /\.(jpg|jpeg|png|gif|webp)$/i.test(m.attachment);

      att = isImg
        ? `<a href="${window.APP_URL}/${escHtml(m.attachment)}" target="_blank"><img src="${window.APP_URL}/${escHtml(m.attachment)}" style="max-width:180px;border-radius:8px;margin-top:4px;display:block;"></a>`
        : `<a href="${window.APP_URL}/${escHtml(m.attachment)}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;background:#f0f0f0;padding:6px 10px;border-radius:8px;color:#0a66c2;font-size:.8rem;margin-top:4px;text-decoration:none;"><i class="bi bi-paperclip"></i> Attachment</a>`;
    }

    return `<div class="chat-msg ${mine ? 'mine' : ''}"><p>${escHtml(m.body || '')}${att}</p><span>now</span></div>`;
  }

  const search = qs('#globalSearch');

  if (search) {
    let timer;

    search.addEventListener('input', () => {
      clearTimeout(timer);

      const q = search.value.trim();
      const box = qs('#searchResults');

      if (!box) return;

      if (q.length < 2) {
        box.innerHTML = '';
        return;
      }

      // On jobs-board: show a quick "Search jobs for: ..." hint that applies filter on Enter/click
      const curPage = new URLSearchParams(window.location.search).get('page') || 'home';
      if (curPage === 'jobs-board') {
        box.innerHTML = `<div style="padding:10px 14px;cursor:pointer;font-size:.88rem;color:#0a66c2;display:flex;align-items:center;gap:8px;"
          onclick="window.location.href='${window.APP_URL}/jobs-board?q='+encodeURIComponent(document.getElementById('globalSearch').value.trim())">
          <i class="bi bi-search"></i> Search jobs for: <strong>${q.replace(/</g,'&lt;')}</strong>
        </div>`;
        return;
      }

      timer = setTimeout(async () => {
        const q2 = search.value.trim();

        if (!q2) return;

        const res = await fetch(`${window.APP_URL}/ajax?action=search&q=${encodeURIComponent(q2)}`).then(r => r.json());
        const results = res.results || {};

        const hasAny =
          (results.users && results.users.length) ||
          (results.companies && results.companies.length) ||
          (results.jobs && results.jobs.length) ||
          (results.posts && results.posts.length);

        box.innerHTML = renderSearch(results, q2);

        if (!hasAny) {
          toast(`No results found for "${q2}"`);
          box.innerHTML = '';
        }
      }, 300);
    });

    // On jobs-board: pressing Enter in global search applies as job filter
    search.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        const curPage = new URLSearchParams(window.location.search).get('page') || 'home';
        if (curPage === 'jobs-board') {
          e.preventDefault();
          const q = search.value.trim();
          if (q) window.location.href = window.APP_URL + '/jobs-board?q=' + encodeURIComponent(q);
        }
      }
    });

    document.addEventListener('click', e => {
      const box = qs('#searchResults');

      if (box && !search.contains(e.target) && !box.contains(e.target)) {
        box.innerHTML = '';
      }
    });
  }

  function renderSearch(r, q) {
    const curPage = new URLSearchParams(window.location.search).get('page') || 'home';
    const onNetwork = curPage === 'network';
    const onJobs = curPage === 'jobs-board';

    const hasUsers = !onJobs && r.users && r.users.length;
    const hasCompanies = !onNetwork && !onJobs && r.companies && r.companies.length;
    const hasJobs = !onNetwork && !onJobs && r.jobs && r.jobs.length;
    const hasPosts = !onNetwork && !onJobs && r.posts && r.posts.length;

    if (!hasUsers && !hasCompanies && !hasJobs && !hasPosts) {
      return `<div style="padding:14px 16px;color:#666;font-size:.88rem;text-align:center;">
        <i class="bi bi-search" style="font-size:1.1rem;opacity:.35;display:block;margin-bottom:5px;"></i>
        No results found for <strong>${escHtml(q)}</strong>
      </div>`;
    }

    let html = '';

    if (hasUsers) {
      html += `<strong>People</strong>`;
      r.users.forEach(x => {
        const isConnected = x.connected == 1 || x.connected === true;
        const avatarHtml = x.avatar
          ? `<img src="${window.APP_URL}/${escHtml(x.avatar)}" alt="${escHtml(x.name)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`
          : `<span style="font-weight:700;font-size:.8rem;">${escHtml((x.name||'U').substring(0,2).toUpperCase())}</span>`;
        html += `<div style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-bottom:1px solid #f0f0f0;">
          <a href="${window.APP_URL}/view-profile?id=${x.id}" style="flex:1;text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;">
            <div style="width:34px;height:34px;border-radius:50%;background:#0a66c2;color:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">${avatarHtml}</div>
            <div><div style="font-size:.88rem;font-weight:600;color:#191919;">${escHtml(x.name)}</div>${x.headline?`<div style="font-size:.76rem;color:#666;">${escHtml(x.headline)}</div>`:''}</div>
          </a>
          <div style="display:flex;gap:6px;flex-shrink:0;">
            ${(window.CURRENT_USER_ROLE === 'company' || isConnected) ? '' : `<button onclick="event.stopPropagation();connectUser(${x.id}, this)" style="font-size:.75rem;padding:4px 10px;border-radius:14px;border:1px solid #0a66c2;color:#0a66c2;background:#fff;cursor:pointer;font-weight:600;" title="Connect"><i class="bi bi-person-plus-fill"></i> Connect</button>`}
            <a href="${window.APP_URL}/messages?with=${x.id}" onclick="event.stopPropagation();document.getElementById('searchResults').innerHTML=''" style="font-size:.75rem;padding:4px 10px;border-radius:14px;border:1px solid #666;color:#444;background:#fff;cursor:pointer;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:3px;" title="Message"><i class="bi bi-chat-dots-fill"></i> Message</a>
          </div>
        </div>`;
      });
    }

    if (hasCompanies) {
      html += `<strong>Companies</strong>`;
      r.companies.forEach(x => {
        const isFollowed = x.followed == 1 || x.followed === true;
        const logoHtml = x.logo
          ? `<img src="${window.APP_URL}/${escHtml(x.logo)}" alt="${escHtml(x.name)}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">`
          : `<span style="font-weight:700;font-size:.8rem;">${escHtml((x.name||'C').substring(0,2).toUpperCase())}</span>`;
        const followBtn = isFollowed
          ? `<button onclick="event.stopPropagation();followCompany(${x.user_id},this)" style="font-size:.75rem;padding:4px 10px;border-radius:14px;border:1px solid #0a66c2;color:#fff;background:#0a66c2;cursor:pointer;font-weight:600;" title="Following"><i class="bi bi-check-lg"></i> Following</button>`
          : `<button onclick="event.stopPropagation();followCompany(${x.user_id},this)" style="font-size:.75rem;padding:4px 10px;border-radius:14px;border:1px solid #0a66c2;color:#0a66c2;background:#fff;cursor:pointer;font-weight:600;" title="Follow"><i class="bi bi-building-add"></i> Follow</button>`;
        html += `<div style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-bottom:1px solid #f0f0f0;">
          <a href="${window.APP_URL}/jobs-board?q=${encodeURIComponent(x.name)}" style="flex:1;text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;">
            <div style="width:34px;height:34px;border-radius:6px;background:#057642;color:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">${logoHtml}</div>
            <div><div style="font-size:.88rem;font-weight:600;color:#191919;">${escHtml(x.name)}</div>${x.recruiter_name?`<div style="font-size:.76rem;color:#666;">${escHtml(x.recruiter_name)}</div>`:''}</div>
          </a>
          <div style="display:flex;gap:6px;flex-shrink:0;">
            ${followBtn}
            <a href="${window.APP_URL}/messages?with=${x.id}" onclick="event.stopPropagation();document.getElementById('searchResults').innerHTML=''" style="font-size:.75rem;padding:4px 10px;border-radius:14px;border:1px solid #666;color:#444;background:#fff;cursor:pointer;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:3px;" title="Message"><i class="bi bi-chat-dots-fill"></i> Message</a>
          </div>
        </div>`;
      });
    }

    if (hasJobs) {
      html += `<strong>Jobs</strong>`;
      r.jobs.forEach(x => {
        html += `<a href="${window.APP_URL}/jobs-board?q=${encodeURIComponent(x.title)}">${escHtml(x.title)} - ${escHtml(x.company)}</a>`;
      });
    }

    if (hasPosts) {
      html += `<strong>Posts</strong>`;
      r.posts.forEach(x => {
        html += `<a href="${window.APP_URL}/home">${escHtml((x.content || '').slice(0, 80))}</a>`;
      });
    }

    return html;
  }

  // Connect to user from search
  window.connectUser = function(userId, btn) {
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-clock-fill"></i> Pending';
      btn.style.background = '#f3f2ef';
      btn.style.color = '#666';
      btn.style.borderColor = '#ccc';
    }
    ajax('connect', { user_id: userId, csrf_token: window.CSRF_TOKEN })
      .then(d => {
        if (d.ok) {
          toast('Connection request sent!');
        } else {
          toast(d.message || 'Could not send request.');
          if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-person-plus-fill"></i> Connect';
            btn.style.background = '#fff';
            btn.style.color = '#0a66c2';
            btn.style.borderColor = '#0a66c2';
          }
        }
      }).catch(() => {
        toast('Could not send request.');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-person-plus-fill"></i> Connect';
          btn.style.background = '#fff';
          btn.style.color = '#0a66c2';
          btn.style.borderColor = '#0a66c2';
        }
      });
  };

  // Follow company from search
  window.followCompany = function(companyId, btn) {
    ajax('follow-user-company', { user_id: companyId, csrf_token: window.CSRF_TOKEN })
      .then(d => {
        if (d.following) {
          btn.innerHTML = '<i class="bi bi-check-lg"></i> Following';
          btn.style.background = '#0a66c2';
          btn.style.color = '#fff';
        } else {
          btn.innerHTML = '<i class="bi bi-building-add"></i> Follow';
          btn.style.background = '#fff';
          btn.style.color = '#0a66c2';
        }
        toast(d.message || 'Done!');
      }).catch(() => toast('Could not follow.'));
  };

  const chartEl = qs('#hiringChart');

  if (chartEl && window.Chart) {
    new Chart(chartEl, {
      type: 'doughnut',
      data: {
        labels: ['Jobs', 'Active', 'Applicants', 'Interviews'],
        datasets: [{
          data: JSON.parse(chartEl.dataset.values || '[]'),
          backgroundColor: ['#0a66c2', '#057642', '#e7a33e', '#8f5849']
        }]
      }
    });
  }

  if (window.jQuery && jQuery.fn.DataTable) {
    jQuery('table').DataTable({
      pageLength: 10,
      responsive: true
    });
  }
})();

(function () {
  var hash = window.location.hash;

  if (!hash) return;

  var postMatch = hash.match(/^#post-(\d+)$/);
  var commentMatch = hash.match(/^#comment-section-(\d+)$/);

  function escHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function (c) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[c];
    });
  }

  if (postMatch) {
    var el = document.getElementById('post-' + postMatch[1]);

    if (el) {
      setTimeout(function () {
        el.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });

        el.classList.add('notif-highlight');

        setTimeout(function () {
          el.classList.remove('notif-highlight');
        }, 2500);
      }, 350);
    }

    return;
  }

  if (commentMatch) {
    var postId = commentMatch[1];
    var postEl = document.getElementById('post-' + postId);
    var listEl = document.getElementById('comments-list-' + postId);
    var boxEl = document.getElementById('comments-' + postId);

    if (boxEl) boxEl.style.display = 'block';

    if (!postEl || !listEl) return;

    fetch(window.APP_URL + '/ajax?action=comments&post_id=' + postId + '&all=1')
      .then(function (r) {
        return r.json();
      })
      .then(function (res) {
        var comments = res.comments || [];

        listEl.innerHTML = comments.map(function (c) {
          var av = c.avatar
            ? '<img src="' + window.APP_URL + '/' + escHtml(c.avatar) + '" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">'
            : '<div style="width:32px;height:32px;border-radius:50%;background:#0a66c2;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">' + escHtml((c.name || '?').charAt(0).toUpperCase()) + '</div>';

          return '<div class="comment-item" id="comment-' + c.id + '" style="display:flex;gap:8px;align-items:flex-start;margin-bottom:10px;">'
            + av
            + '<div style="flex:1;"><div style="background:#f2f2f2;border-radius:12px;padding:8px 12px;">'
            + '<strong style="font-size:.83rem;">' + escHtml(c.name || '') + '</strong>'
            + '<p style="margin:2px 0 0;font-size:.85rem;line-height:1.4;">' + escHtml(c.content || '') + '</p>'
            + '</div></div></div>';
        }).join('');

        listEl.dataset.loaded = '1';

        setTimeout(function () {
          postEl.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
        }, 400);

        setTimeout(function () {
          var items = listEl.querySelectorAll('.comment-item');

          if (items.length) {
            var last = items[items.length - 1];

            last.classList.add('notif-highlight');
            last.scrollIntoView({
              behavior: 'smooth',
              block: 'nearest'
            });

            setTimeout(function () {
              last.classList.remove('notif-highlight');
            }, 2500);
          }
        }, 900);
      });
  }
})();
// ── buildJobCardHTML — used by the live job poller in jobs.php ───────────
function buildJobCardHTML(job) {
  function esc(v) {
    return String(v || '').replace(/[&<>"']/g, function(c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];
    });
  }
  var appUrl = window.APP_URL || '';
  var saved   = job.saved == 1 || job.saved === true;
  var applied = job.applied == 1 || job.applied === true;
  var isNew   = (window.newJobIds || []).indexOf(job.id) !== -1 || true; // just-polled = always new
  var logoHtml = job.logo
    ? '<img src="' + appUrl + '/' + esc(job.logo) + '" alt="' + esc(job.company) + '" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">'
    : '<div style="width:48px;height:48px;border-radius:8px;background:#e8f0fe;color:#0a66c2;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1rem;">' + esc((job.company||'??').substring(0,2).toUpperCase()) + '</div>';

  var salaryHtml = (job.salary_min)
    ? '&#x20B9;' + Number(job.salary_min).toLocaleString() + ' - &#x20B9;' + Number(job.salary_max).toLocaleString()
    : 'Salary not disclosed';

  var saveBtn = '<button class="btn btn-sm ' + (saved ? 'btn-primary' : 'btn-outline-secondary') + ' save-job-btn" data-job="' + job.id + '" title="' + (saved ? 'Saved' : 'Save job') + '" style="font-size:.78rem;padding:4px 10px;">'
    + '<i class="bi ' + (saved ? 'bi-bookmark-fill' : 'bi-bookmark') + '"></i> ' + (saved ? 'Saved' : 'Save') + '</button>';

  var actionBtn = applied
    ? '<button class="btn btn-sm btn-success withdraw-btn" data-job="' + job.id + '" style="font-size:.78rem;padding:4px 12px;"><i class="bi bi-check-circle-fill me-1"></i>Applied</button>'
    : '<button class="btn btn-sm btn-primary apply-btn" data-job="' + job.id + '" style="font-size:.78rem;padding:4px 12px;"><i class="bi bi-lightning-fill me-1"></i>Easy Apply</button>';

  return '<article class="li-card job-card job-card--new" data-job-id="' + job.id + '" style="cursor:pointer;">'
    + '<div class="job-logo">' + logoHtml + '</div>'
    + '<div class="job-content" style="flex:1;">'
    +   '<h2 style="font-size:1rem;font-weight:600;margin-bottom:2px;display:flex;align-items:center;gap:6px;">'
    +     esc(job.title) + ' <span class="new-job-badge">NEW</span>'
    +   '</h2>'
    +   '<p style="color:#555;font-size:.875rem;margin-bottom:4px;">'
    +     esc(job.company) + ' &middot; ' + esc(job.location) + ' &middot; '
    +     '<span class="badge bg-light text-dark border" style="font-size:.75rem;">' + esc(String(job.job_type||'').replace(/_/g,' ')) + '</span>'
    +   '</p>'
    +   '<p style="color:#777;font-size:.82rem;margin-bottom:6px;">'
    +     esc(String(job.description||'').replace(/<[^>]+>/g,'').substring(0,140)) + '...'
    +   '</p>'
    +   '<span class="salary" style="font-size:.82rem;color:#0a66c2;font-weight:500;">' + salaryHtml + '</span>'
    + '</div>'
    + '<div class="job-actions" style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;min-width:110px;">'
    +   saveBtn + actionBtn
    + '</div>'
    + '</article>';
}
window.buildJobCardHTML = buildJobCardHTML;