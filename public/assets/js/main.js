document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('[data-sidebar]');
  const toggle = document.querySelector('[data-sidebar-toggle]');
  const sidebarStorageKey = 'hms.sidebar.scrollTop';

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
