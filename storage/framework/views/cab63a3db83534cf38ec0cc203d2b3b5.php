<script>
  // Toggle sidebar di mobile
  const menuBtn = document.getElementById('menuBtn');
  const sidebar = document.getElementById('sidebar');
  if (menuBtn && sidebar) {
    menuBtn.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', (e) => {
      if (window.innerWidth <= 900 && sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) && e.target !== menuBtn) {
        sidebar.classList.remove('open');
      }
    });
  }

  // Tab switching sederhana (prototype — belum tersambung ke backend laporan)
  const links = document.querySelectorAll('[data-tab-link]');
  const panels = document.querySelectorAll('[data-tab-panel]');
  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const target = link.getAttribute('data-tab-link');
      links.forEach(l => l.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      link.classList.add('active');
      document.querySelector(`[data-tab-panel="${target}"]`).classList.add('active');
      if (window.innerWidth <= 900) sidebar.classList.remove('open');
      window.scrollTo({top:0, behavior:'smooth'});
    });
  });

  // Ganti tema (dark / light) — 1 tombol, tersimpan di localStorage, berlaku di semua halaman
  (function(){
    var THEME_KEY = 'siberad-theme';
    var btn = document.getElementById('themeToggleBtn');

    function applyTheme(theme){
      if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
      } else {
        document.documentElement.removeAttribute('data-theme');
      }
      if (btn) {
        var icon = btn.querySelector('.theme-toggle-icon');
        var label = btn.querySelector('.theme-toggle-label');
        if (theme === 'light') {
          if (icon) icon.textContent = '☀️';
          if (label) label.textContent = 'Mode Terang';
          btn.setAttribute('aria-pressed', 'true');
        } else {
          if (icon) icon.textContent = '🌙';
          if (label) label.textContent = 'Mode Gelap';
          btn.setAttribute('aria-pressed', 'false');
        }
      }
    }

    var saved = 'dark';
    try { saved = localStorage.getItem(THEME_KEY) || 'dark'; } catch (e) {}
    applyTheme(saved);

    if (btn) {
      btn.addEventListener('click', function(){
        var current = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
        var next = current === 'light' ? 'dark' : 'light';
        try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
        applyTheme(next);
      });
    }
  })();
</script><?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/partials/dash-script.blade.php ENDPATH**/ ?>