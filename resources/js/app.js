// ============================================================
// SULUH — Scroll Reveal Engine (Intersection Observer)
// Elemen dengan atribut [data-reveal] akan muncul saat di-scroll
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const delay = entry.target.dataset.revealDelay || 0;
          setTimeout(() => {
            entry.target.classList.add('is-revealed');
          }, delay);
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: '0px 0px -40px 0px',
    }
  );

  document.querySelectorAll('[data-reveal]').forEach((el) => {
    observer.observe(el);
  });
});

// ============================================================
// SULUH — Premium Custom Dialog/Alert/Confirm System
// ============================================================

window.showCustomAlert = function(message, title = 'Notifikasi') {
  return new Promise((resolve) => {
    const overlay = document.createElement('div');
    overlay.className = 'custom-dialog-overlay';
    
    overlay.innerHTML = `
      <div class="custom-dialog-box" role="dialog" aria-modal="true" aria-labelledby="dialog-title">
        <div class="custom-dialog-header">
          <div class="custom-dialog-icon custom-dialog-icon-alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
          </div>
          <div>
            <h4 id="dialog-title" class="custom-dialog-title">${title}</h4>
            <p class="custom-dialog-message">${message}</p>
          </div>
        </div>
        <div class="custom-dialog-actions">
          <button type="button" class="btn btn-primary btn-sm" id="custom-dialog-ok">OK</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(overlay);
    const okBtn = overlay.querySelector('#custom-dialog-ok');
    if (okBtn) okBtn.focus();
    
    const close = () => {
      overlay.style.opacity = '0';
      overlay.style.transition = 'opacity 0.15s ease';
      setTimeout(() => {
        overlay.remove();
        resolve();
      }, 150);
    };
    
    if (okBtn) okBtn.addEventListener('click', close);
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) close();
    });
  });
};

window.showCustomConfirm = function(message, onConfirm, onCancel, title = 'Konfirmasi') {
  return new Promise((resolve) => {
    const overlay = document.createElement('div');
    overlay.className = 'custom-dialog-overlay';
    
    overlay.innerHTML = `
      <div class="custom-dialog-box" role="dialog" aria-modal="true" aria-labelledby="dialog-title">
        <div class="custom-dialog-header">
          <div class="custom-dialog-icon custom-dialog-icon-confirm">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
          <div>
            <h4 id="dialog-title" class="custom-dialog-title">${title}</h4>
            <p class="custom-dialog-message">${message}</p>
          </div>
        </div>
        <div class="custom-dialog-actions">
          <button type="button" class="btn btn-ghost btn-sm" id="custom-confirm-cancel">Batal</button>
          <button type="button" class="btn btn-primary btn-sm" id="custom-confirm-ok">Yakin</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(overlay);
    const okBtn = overlay.querySelector('#custom-confirm-ok');
    const cancelBtn = overlay.querySelector('#custom-confirm-cancel');
    if (okBtn) okBtn.focus();
    
    const close = (confirmed) => {
      overlay.style.opacity = '0';
      overlay.style.transition = 'opacity 0.15s ease';
      setTimeout(() => {
        overlay.remove();
        if (confirmed) {
          if (onConfirm) onConfirm();
          resolve(true);
        } else {
          if (onCancel) onCancel();
          resolve(false);
        }
      }, 150);
    };
    
    if (okBtn) okBtn.addEventListener('click', () => close(true));
    if (cancelBtn) cancelBtn.addEventListener('click', () => close(false));
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) close(false);
    });
  });
};

// Override window.alert
window.alert = function(message) {
  window.showCustomAlert(message);
};

// Intercept all submit events that use inline return confirm(...)
document.addEventListener('submit', function(event) {
  const form = event.target;
  const onsubmitAttr = form.getAttribute('onsubmit');
  if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
    event.preventDefault();
    event.stopImmediatePropagation();
    
    let message = 'Apakah Anda yakin?';
    const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
    if (match && match[1]) {
      message = match[1];
    }
    
    window.showCustomConfirm(message, function() {
      form.removeAttribute('onsubmit');
      form.submit();
    });
  }
}, true);

