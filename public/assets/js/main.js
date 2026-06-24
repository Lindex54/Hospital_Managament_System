document.addEventListener('DOMContentLoaded', () => {
  const flashSource = document.querySelector('[data-flash-message]');
  const sidebar = document.querySelector('[data-sidebar]');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  const sidebarStorageKey = 'hms.sidebar.scrollTop';

  if (flashSource instanceof HTMLElement) {
    const flashType = flashSource.dataset.flashType || 'notice';
    const flashText = flashSource.dataset.flashText || '';

    if (flashText.trim() !== '') {
      const stack = document.createElement('div');
      stack.className = 'toast-stack';

      const toast = document.createElement('article');
      toast.className = `toast-card toast-${flashType === 'success' ? 'success' : 'error'}`;
      toast.setAttribute('role', 'status');
      toast.setAttribute('aria-live', 'polite');

      const icon = document.createElement('div');
      icon.className = 'toast-icon';
      icon.innerHTML = flashType === 'success'
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>';

      const body = document.createElement('div');
      body.className = 'toast-body';

      const title = document.createElement('h4');
      title.className = 'toast-title';
      title.textContent = flashType === 'success' ? 'Successful' : 'Action Failed';

      const message = document.createElement('p');
      message.className = 'toast-copy';
      message.textContent = flashText;

      const closeButton = document.createElement('button');
      closeButton.className = 'toast-close';
      closeButton.type = 'button';
      closeButton.setAttribute('aria-label', 'Close notification');
      closeButton.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12"/><path d="M18 6l-12 12"/></svg>';

      const dismissToast = () => {
        toast.classList.add('toast-leaving');
        window.setTimeout(() => {
          stack.remove();
        }, 240);
      };

      closeButton.addEventListener('click', dismissToast);

      body.append(title, message);
      toast.append(icon, body, closeButton);
      stack.appendChild(toast);
      document.body.appendChild(stack);

      window.setTimeout(dismissToast, 5000);
    }
  }

  if (sidebar) {
    const restoreSidebarScroll = () => {
      const savedSidebarScroll = window.sessionStorage.getItem(sidebarStorageKey);

      if (savedSidebarScroll === null) {
        return;
      }

      sidebar.scrollTop = Number.parseInt(savedSidebarScroll, 10) || 0;
    };

    const persistSidebarScroll = () => {
      window.sessionStorage.setItem(sidebarStorageKey, String(sidebar.scrollTop));
    };

    restoreSidebarScroll();
    window.requestAnimationFrame(restoreSidebarScroll);
    window.setTimeout(restoreSidebarScroll, 0);
    window.setTimeout(restoreSidebarScroll, 150);

    sidebar.addEventListener('scroll', persistSidebarScroll, { passive: true });
    window.addEventListener('beforeunload', persistSidebarScroll);
    window.addEventListener('pagehide', persistSidebarScroll);

    sidebar.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof Element)) {
        return;
      }

      const link = target.closest('a[href]');

      if (!link) {
        return;
      }

      persistSidebarScroll();
    });
  }

  if (sidebar && toggle) {
    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('hidden');
    });
  }
});
