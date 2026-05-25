/* ============================================================
   LinkedIn Admin — Auth Pages JavaScript
   Used by: login.php, reset_password.php
   ============================================================ */

// Apply saved theme before paint (also inlined as tiny <script> in <head> for FOUC prevention)
(function () {
  var t = localStorage.getItem('li_theme') || 'light';
  document.documentElement.setAttribute('data-theme', t);
})();

// ── THEME TOGGLE ──────────────────────────────────────────────
function toggleTheme() {
  var html = document.documentElement;
  var next = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
  html.setAttribute('data-theme', next);
  localStorage.setItem('li_theme', next);
}

// ── PASSWORD SHOW/HIDE (single-field, login page) ─────────────
function togglePw() {
  var inp = document.getElementById('password');
  var btn = inp ? inp.nextElementSibling : null;
  if (!inp || !btn) return;
  if (inp.type === 'password') { inp.type = 'text';     btn.textContent = 'Hide'; }
  else                         { inp.type = 'password'; btn.textContent = 'Show'; }
}

// ── PASSWORD SHOW/HIDE (multi-field, reset page) ─────────────
function togglePwById(id, btn) {
  var inp = document.getElementById(id);
  if (!inp) return;
  var isText = inp.type === 'text';
  inp.type = isText ? 'password' : 'text';
  btn.textContent = isText ? 'Show' : 'Hide';
}

// ── FORGOT PASSWORD MODAL ─────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
  var forgotModal = document.getElementById('forgot-modal');
  if (forgotModal) {
    forgotModal.addEventListener('click', function (e) {
      if (e.target === this) this.classList.remove('open');
    });
  }
});

function refreshCaptcha(imageId) {
  var img = document.getElementById(imageId);
  if (!img) return;
  var base = img.src.split('?')[0];
  img.onload = function () {
    var input = document.querySelector('input[name="captcha"]');
    if (input) input.value = '';
  };
  img.src = base + '?refresh=' + Date.now() + '&r=' + Math.random().toString(36).slice(2);
}
