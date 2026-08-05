<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — SIBERAD</title>
<link rel="icon" type="image/jpeg" href="<?php echo e(asset('images/logo-pussiberad.jpg')); ?>">
<?php echo $__env->make('siberad.dashboards.partials.dash-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<style>
  .chart-box{margin-bottom:26px;}
  .chart-box-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
  .chart-mini{background:var(--panel-alt);border:1px solid var(--border-soft);border-radius:10px;padding:14px;}
  .chart-mini-head{margin-bottom:8px;}
  .chart-mini-head h4{font-family:var(--display);font-size:13px;font-weight:700;letter-spacing:.01em;line-height:1.3;}
  .chart-mini-head p{font-size:11px;color:var(--text-muted);margin-top:2px;}
  .chart-mini .chart-wrap{position:relative;height:210px;}
  @media(max-width:980px){.chart-box-grid{grid-template-columns:1fr;}.chart-mini .chart-wrap{height:230px;}}

  /* ===== toolbar cari & filter tabel ===== */
  .table-toolbar{display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;}
  .table-search-wrap{position:relative;flex:1;min-width:200px;}
  .table-search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;stroke:var(--text-dim);pointer-events:none;}
  .table-search{
    width:100%;box-sizing:border-box;background:var(--panel);border:1px solid var(--border);color:var(--text);
    font-family:var(--body);font-size:13px;border-radius:8px;padding:9px 12px 9px 34px;
  }
  .table-search::placeholder{color:var(--text-dim);}
  .table-search:focus{outline:none;border-color:var(--gold);}
  .table-filter{
    background:var(--panel);border:1px solid var(--border);color:var(--text);font-family:var(--mono);
    font-size:11.5px;letter-spacing:.02em;border-radius:8px;padding:0 10px;cursor:pointer;flex-shrink:0;
    min-width:170px;height:38px;
  }
  .table-filter:focus{outline:none;border-color:var(--gold);}
  .table-empty-row td{text-align:center;color:var(--text-dim);font-size:12.5px;padding:26px 12px !important;}
  @media(max-width:640px){.table-toolbar{flex-direction:column;}.table-filter{width:100%;}}
</style>
</head>
<body>
<div class="profile-modal-overlay" id="profileModalOverlay">
  <div class="profile-modal-card" id="profileModalCard" role="dialog" aria-modal="true" aria-label="Detail profil">
    <button type="button" class="profile-modal-close" id="profileModalCloseBtn" aria-label="Tutup">
      <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"></path></svg>
    </button>

    
    <div class="profile-dropdown-view" id="profilePhotoView" style="display:none;">
      <div class="profile-dropdown-head-lg">
        <div class="profile-dropdown-avatar-lg">
          <span class="profile-initial" id="profileInitialLarge"><?php echo e(strtoupper(mb_substr($user->name ?? 'U', 0, 1))); ?></span>
          <img class="profile-photo" id="profilePhotoLarge" alt="Foto profil <?php echo e($user->name); ?>">
        </div>
        <div class="profile-dropdown-name"><?php echo e($user->name); ?></div>
        <div class="profile-dropdown-role"><?php echo e($user->jabatan ?? 'Pengguna'); ?></div>
      </div>

      <button type="button" class="profile-dropdown-item" id="gantiFotoBtn" role="menuitem">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8.5A1.5 1.5 0 0 1 5.5 7h2l1-2h7l1 2h2A1.5 1.5 0 0 1 20 8.5v9A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5Z"></path><circle cx="12" cy="13" r="3.4"></circle></svg>
        <span id="gantiFotoLabel">Ganti Foto Profil</span>
      </button>
      <button type="button" class="profile-dropdown-item" id="hapusFotoBtn" role="menuitem" style="display:none;">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"></path><path d="M18 7l-.8 12.1a1.8 1.8 0 0 1-1.8 1.7H8.6a1.8 1.8 0 0 1-1.8-1.7L6 7"></path></svg>
        Hapus Foto Profil
      </button>
      <input type="file" id="fotoProfilInput" accept="image/png,image/jpeg,image/webp" hidden>
    </div>

    
    <div class="profile-dropdown-view" id="profileSettingsView" style="display:none;">
      <div class="profile-modal-title">Pengaturan Akun</div>

      <div class="profile-form-notice">
        Perubahan kata sandi tidak langsung berlaku. Permintaan akan dikirim ke <b>Admin</b> untuk diverifikasi terlebih dahulu.
      </div>

      <form class="profile-form" id="formGantiPassword" novalidate>
        <div class="profile-form-field">
          <label for="passBaru">Kata Sandi Baru</label>
          <input type="password" id="passBaru" minlength="8" required placeholder="Minimal 8 karakter">
        </div>
        <div class="profile-form-field">
          <label for="passKonfirmasi">Konfirmasi Kata Sandi Baru</label>
          <input type="password" id="passKonfirmasi" minlength="8" required placeholder="Ulangi kata sandi baru">
        </div>
        <div class="profile-form-field">
          <label for="passCatatan">Catatan untuk Admin (opsional)</label>
          <textarea id="passCatatan" rows="2" placeholder="Contoh: lupa kata sandi lama"></textarea>
        </div>
        <span class="profile-form-error" id="passError"></span>
        <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">Kirim Permintaan ke Admin</button>
      </form>
    </div>

    
    <div class="profile-dropdown-view" id="profileHelpView" style="display:none;">
      <div class="profile-modal-title">Bantuan &amp; Panduan</div>
      <p class="profile-help-text">
        Prototype — pusat bantuan belum tersambung. Kalau butuh bantuan seputar SIBERAD,
        silakan hubungi Admin Pussiberad melalui jalur koordinasi internal.
      </p>
    </div>

  </div>
</div>

<div class="shell">

  <aside class="sidebar" id="sidebar">
    <div class="side-brand">
      <img src="<?php echo e(asset('images/logo-pussiberad.jpg')); ?>" alt="Lambang Pussiberad">
      <div class="logo">SIBER<span>AD</span></div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="dashboard"><span class="dot"></span>Dashboard</a>

      <div class="side-dropdown" id="penggunaDropdown">
        <button type="button" class="side-link side-dropdown-toggle" id="penggunaToggle" aria-expanded="false" aria-controls="penggunaSubmenu">
          <span class="dot"></span>
          <span class="side-link-label">Kelola Pengguna</span>
          <svg class="side-dropdown-arrow" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg>
        </button>
        <div class="side-dropdown-menu" id="penggunaSubmenu">
          <a href="#" class="side-link side-sublink" data-tab-link="pengguna">Daftar Pengguna</a>
          <a href="#" class="side-link side-sublink" data-tab-link="reset-password">Permintaan Reset Password</a>
        </div>
      </div>

      <div class="side-dropdown" id="sistemDropdown">
        <button type="button" class="side-link side-dropdown-toggle" id="sistemToggle" aria-expanded="false" aria-controls="sistemSubmenu">
          <span class="dot"></span>
          <span class="side-link-label">Kelola Sistem</span>
          <svg class="side-dropdown-arrow" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg>
        </button>
        <div class="side-dropdown-menu" id="sistemSubmenu">
          <a href="#" class="side-link side-sublink" data-tab-link="pengaturan-umum">Pengaturan Umum</a>
        </div>
      </div>
    </nav>
    <div class="side-foot">
      <form class="logout logout-form" method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit">Keluar</button>
      </form>
    </div>
  </aside>

  <script>
  (function () {
    var dropdowns = [
      { wrap: 'penggunaDropdown', toggle: 'penggunaToggle' },
      { wrap: 'sistemDropdown', toggle: 'sistemToggle' }
    ];
    dropdowns.forEach(function (cfg) {
      var dropdown = document.getElementById(cfg.wrap);
      var toggle = document.getElementById(cfg.toggle);
      if (!dropdown || !toggle) return;

      var subActive = dropdown.querySelector('.side-sublink.active');
      if (subActive) dropdown.classList.add('open');

      toggle.addEventListener('click', function (e) {
        e.preventDefault();
        var isOpen = dropdown.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    });
  })();
  </script>

  <main class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="menu-btn" id="menuBtn">☰</button>
      </div>
      <div class="topbar-actions">
        <button type="button" class="btn-icon-toggle" id="themeToggleBtn" aria-pressed="false" aria-label="Ganti tema">
          <svg class="icon-moon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"></path></svg>
        </button>

        <div class="profile-menu" id="notifMenu">
          <button type="button" class="btn-icon-toggle" id="notifBtn" aria-label="Notifikasi" aria-haspopup="menu" aria-expanded="false" style="position:relative;">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="stroke:var(--gold-bright) !important;color:var(--gold-bright) !important;">
              <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" style="fill:var(--gold-dim) !important;stroke:var(--gold-bright) !important;"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0" style="fill:none !important;stroke:var(--gold-bright) !important;"></path>
            </svg>
            <span style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:var(--red);box-shadow:0 0 0 2px var(--panel,#0c2417);"></span>
          </button>

          <div class="profile-dropdown" id="notifDropdown" role="menu" aria-label="Notifikasi">
            <div class="profile-dropdown-head" style="border-bottom:1px solid var(--border-soft);">
              <div class="profile-dropdown-name" style="font-size:14px;">Notifikasi</div>
            </div>
            <div style="text-align:center;padding:20px 6px 8px;">
              <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="var(--text-dim)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 14px;display:block;">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
              </svg>
              <p style="margin:0;font-size:12.5px;line-height:1.6;color:var(--text-muted);">Belum ada notifikasi saat ini.<br>Fitur pusat notifikasi masih prototype dan belum tersambung ke database.</p>
            </div>
          </div>
        </div>
        <div class="profile-menu" id="profileMenu">
          <button type="button" class="profile-menu-btn" id="profileMenuBtn" aria-haspopup="menu" aria-expanded="false" aria-label="Menu profil">
            <span class="profile-initial" id="profileInitial"><?php echo e(strtoupper(mb_substr($user->name ?? 'U', 0, 1))); ?></span>
            <img class="profile-photo" id="profilePhotoBtn" alt="Foto profil <?php echo e($user->name); ?>">
          </button>

          <div class="profile-dropdown" id="profileDropdown" role="menu" aria-label="Menu profil">

            <div class="profile-dropdown-head">
              <div class="profile-dropdown-avatar">
                <span class="profile-initial" id="profileInitialDropdown"><?php echo e(strtoupper(mb_substr($user->name ?? 'U', 0, 1))); ?></span>
                <img class="profile-photo" id="profilePhotoDropdown" alt="Foto profil <?php echo e($user->name); ?>">
              </div>
              <div>
                <div class="profile-dropdown-name"><?php echo e($user->name); ?></div>
                <div class="profile-dropdown-role"><?php echo e($user->jabatan ?? 'Pengguna'); ?></div>
              </div>
            </div>

            <button type="button" class="profile-dropdown-item" id="openProfilSayaBtn" role="menuitem">
              <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"></circle><path d="M5 20c1.2-4 4.2-6 7-6s5.8 2 7 6"></path></svg>
              Profil Saya
            </button>
            <button type="button" class="profile-dropdown-item" id="openPengaturanBtn" role="menuitem">
              <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 13.5a7.6 7.6 0 0 0 0-3l2-1.5-2-3.4-2.3.9a7.6 7.6 0 0 0-2.6-1.5L14 2.5h-4l-.5 2.5a7.6 7.6 0 0 0-2.6 1.5l-2.3-.9-2 3.4 2 1.5a7.6 7.6 0 0 0 0 3l-2 1.5 2 3.4 2.3-.9a7.6 7.6 0 0 0 2.6 1.5l.5 2.5h4l.5-2.5a7.6 7.6 0 0 0 2.6-1.5l2.3.9 2-3.4Z"></path></svg>
              Pengaturan Akun
            </button>
            <button type="button" class="profile-dropdown-item" id="openBantuanBtn" role="menuitem">
              <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"></circle><path d="M9.2 9.2a2.8 2.8 0 1 1 3.9 2.6c-.8.4-1.1 1-1.1 1.9"></path><path d="M12 17.2h.01"></path></svg>
              Bantuan &amp; Panduan
            </button>

            <div class="profile-dropdown-divider"></div>

            <form class="logout-form" method="POST" action="<?php echo e(route('logout')); ?>">
              <?php echo csrf_field(); ?>
              <button type="submit" class="profile-dropdown-item danger" role="menuitem">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
                Keluar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    
    <div class="content">

      
      <section class="tab-panel active" data-tab-panel="dashboard">
        <div class="section-head">
          <h2>Ringkasan Sistem</h2>
          <p>Kondisi akun pengguna dan satuan yang terdaftar di SIBERAD.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Pengguna</div>
            <div class="val"><?php echo e($stats['total_pengguna']); ?></div>
            <div class="sub">Akun terdaftar di sistem</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Total Satuan</div>
            <div class="val"><?php echo e($stats['total_satuan']); ?></div>
            <div class="sub">Termasuk Admin</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Permintaan Reset Password</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['reset_password_pending']); ?></div>
            <div class="sub">Menunggu diverifikasi</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Tanpa Pengguna</div>
            <div class="val" style="color:<?php echo e($stats['satuan_tanpa_pengguna'] > 0 ? 'var(--red)' : 'var(--green)'); ?>;"><?php echo e($stats['satuan_tanpa_pengguna']); ?></div>
            <div class="sub">Perlu dibuatkan akun</div>
          </div>
        </div>

        <div class="panel chart-box">
          <div class="panel-head"><div><h3>Statistik Sistem</h3><p>Sebaran akun, satuan, dan permintaan reset password.</p></div></div>
          <div class="chart-box-grid">

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Pengguna per Kategori Satuan</h4><p>Sebaran akun berdasarkan kategori.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartKategoriSatuan"></canvas></div>
            </div>

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Status Reset Password</h4><p>Proporsi permintaan yang masuk.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartStatusReset"></canvas></div>
            </div>

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Kelengkapan Akun Satuan</h4><p>Satuan yang sudah vs belum punya akun.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartKelengkapanSatuan"></canvas></div>
            </div>

          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Aktivitas seputar akun dan data satuan.</p></div></div>
          <div class="table-toolbar">
            <div class="table-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
              <input type="text" class="table-search" data-table-search="tblAktivitas" placeholder="Cari kegiatan...">
            </div>
            <select class="table-filter" data-table-filter="tblAktivitas">
              <option value="">Semua Status</option>
              <option value="Menunggu">Menunggu</option>
              <option value="Info">Info</option>
              <option value="Selesai">Selesai</option>
            </select>
          </div>
          <div class="tbl-wrap" data-row-limit="5">
            <table class="dtbl" id="tblAktivitas">
              <thead><tr><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr data-filter-value="<?php echo e($a['status']); ?>">
                  <td><?php echo e($a['kegiatan']); ?></td>
                  <td><?php echo e($a['waktu']); ?></td>
                  <td><span class="status-dot <?php echo e($a['status_class']); ?>"><?php echo e($a['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="pengguna">
        <div class="section-head">
          <h2>Kelola Pengguna</h2>
          <p>Seluruh akun yang terdaftar, satu akun per satuan.</p>
        </div>
        <div class="panel">
          <div class="panel-head">
            <button type="button" class="btn btn-primary btn-sm" style="margin-left:auto;" onclick="alert('Prototype — form tambah pengguna belum tersambung ke database.')">+ Tambah Pengguna</button>
          </div>
          <div class="table-toolbar">
            <div class="table-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
              <input type="text" class="table-search" data-table-search="tblPengguna" placeholder="Cari nama, username, atau email...">
            </div>
            <select class="table-filter" data-table-filter="tblPengguna">
              <option value="">Semua Satuan</option>
              <?php $__currentLoopData = $semuaSatuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($s->nama); ?>"><?php echo e($s->nama); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="tbl-wrap" data-row-limit="5">
            <table class="dtbl" id="tblPengguna">
              <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Satuan</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $semuaPengguna; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr data-filter-value="<?php echo e($p->satuan->nama ?? ''); ?>">
                  <td><?php echo e($p->name); ?></td>
                  <td><span class="badge"><?php echo e($p->username); ?></span></td>
                  <td style="color:var(--text-muted);"><?php echo e($p->email); ?></td>
                  <td><?php echo e($p->satuan->nama ?? '-'); ?></td>
                  <td>
                    <div class="btn-row">
                      <button class="btn btn-sm" type="button" onclick="alert('Prototype — reset password untuk &quot;<?php echo e($p->name); ?>&quot; belum tersambung ke database.')">Reset Password</button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="pengaturan-umum">
        <div class="section-head">
          <h2>Pengaturan Umum</h2>
          <p>Konfigurasi umum aplikasi SIBERAD.</p>
        </div>
        <div class="panel">
          <div class="panel-head"><div><h3>Identitas Aplikasi</h3><p>Nama, logo, dan informasi dasar sistem.</p></div></div>
          <div style="padding:24px;text-align:center;">
            <p style="margin:0;font-size:12.5px;line-height:1.6;color:var(--text-muted);">Prototype — pengaturan umum belum tersambung ke database.<br>Nantinya menu ini dipakai untuk mengatur nama instansi, logo, dan preferensi sistem lainnya.</p>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="reset-password">
        <div class="section-head">
          <h2>Permintaan Reset Password</h2>
          <p>Permintaan ganti kata sandi yang dikirim pengguna lewat menu "Pengaturan Akun".</p>
        </div>
        <div class="panel">
          <div class="table-toolbar">
            <div class="table-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M21 21l-4.3-4.3"></path></svg>
              <input type="text" class="table-search" data-table-search="tblResetPassword" placeholder="Cari satuan atau catatan...">
            </div>
            <select class="table-filter" data-table-filter="tblResetPassword">
              <option value="">Semua Status</option>
              <option value="Menunggu">Menunggu</option>
              <option value="Selesai">Selesai</option>
              <option value="Ditolak">Ditolak</option>
            </select>
          </div>
          <div class="tbl-wrap" data-row-limit="5">
            <table class="dtbl" id="tblResetPassword">
              <thead><tr><th>Satuan</th><th>Catatan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $permintaanResetPassword; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id="rowReset<?php echo e($i); ?>" data-filter-value="<?php echo e($r['status']); ?>">
                  <td><?php echo e($r['satuan']); ?></td>
                  <td style="color:var(--text-muted);"><?php echo e($r['catatan']); ?></td>
                  <td><?php echo e($r['tanggal']); ?></td>
                  <td id="statusReset<?php echo e($i); ?>"><span class="badge <?php echo e($r['status_class']); ?>"><?php echo e($r['status']); ?></span></td>
                  <td>
                    <?php if($r['status_class'] === 'amber'): ?>
                    <div class="btn-row">
                      <button class="btn btn-primary btn-sm" type="button" onclick="setujuiResetPassword(<?php echo e($i); ?>)">Setujui</button>
                      <button class="btn btn-ghost-red btn-sm" type="button" onclick="tolakResetPassword(<?php echo e($i); ?>)">Tolak</button>
                    </div>
                    <?php else: ?>
                      <span style="font-size:11.5px;color:var(--text-dim);">Sudah diproses</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>

      <script>
        function setujuiResetPassword(i) {
          document.getElementById('statusReset' + i).innerHTML = '<span class="badge green">Selesai</span>';
          var row = document.getElementById('rowReset' + i);
          if (row) {
            row.children[4].innerHTML = '<span style="font-size:11.5px;color:var(--text-dim);">Sudah diproses</span>';
            row.setAttribute('data-filter-value', 'Selesai');
            if (window.terapkanTabelFilter) window.terapkanTabelFilter('tblResetPassword');
          }
        }
        function tolakResetPassword(i) {
          document.getElementById('statusReset' + i).innerHTML = '<span class="badge red">Ditolak</span>';
          var row = document.getElementById('rowReset' + i);
          if (row) {
            row.children[4].innerHTML = '<span style="font-size:11.5px;color:var(--text-dim);">Sudah diproses</span>';
            row.setAttribute('data-filter-value', 'Ditolak');
            if (window.terapkanTabelFilter) window.terapkanTabelFilter('tblResetPassword');
          }
        }
      </script>

      <script>
      (function () {
        var menuBtn = document.getElementById('profileMenuBtn');
        var dropdown = document.getElementById('profileDropdown');
        var wrapper = document.getElementById('profileMenu');
        var openProfilBtn = document.getElementById('openProfilSayaBtn');
        var openPengaturanBtn = document.getElementById('openPengaturanBtn');
        var openBantuanBtn = document.getElementById('openBantuanBtn');
        if (!menuBtn || !dropdown || !wrapper) return;

        function closeMenu() {
          dropdown.classList.remove('open');
          menuBtn.classList.remove('open');
          menuBtn.setAttribute('aria-expanded', 'false');
        }

        function openMenu() {
          // Tutup dropdown notifikasi kalau lagi kebuka, biar cuma satu yang tampil
          var notifDropdown = document.getElementById('notifDropdown');
          var notifBtnEl = document.getElementById('notifBtn');
          if (notifDropdown && notifDropdown.classList.contains('open')) {
            notifDropdown.classList.remove('open');
            if (notifBtnEl) {
              notifBtnEl.classList.remove('open');
              notifBtnEl.setAttribute('aria-expanded', 'false');
            }
          }
          dropdown.classList.add('open');
          menuBtn.classList.add('open');
          menuBtn.setAttribute('aria-expanded', 'true');
        }

        menuBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          if (dropdown.classList.contains('open')) {
            closeMenu();
          } else {
            openMenu();
          }
        });

        // Item di dropdown kecil membuka popup besar di tengah layar
        // (fungsi openProfileModal didefinisikan di script popup di bawah)
        if (openProfilBtn) {
          openProfilBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeMenu();
            if (window.openProfileModal) window.openProfileModal('profilePhotoView');
          });
        }
        if (openPengaturanBtn) {
          openPengaturanBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeMenu();
            if (window.openProfileModal) window.openProfileModal('profileSettingsView');
          });
        }
        if (openBantuanBtn) {
          openBantuanBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeMenu();
            if (window.openProfileModal) window.openProfileModal('profileHelpView');
          });
        }

        document.addEventListener('click', function (e) {
          if (!wrapper.contains(e.target)) closeMenu();
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') closeMenu();
        });
      })();
      </script>

      <script>
      (function () {
        var overlay = document.getElementById('profileModalOverlay');
        var card = document.getElementById('profileModalCard');
        var closeBtn = document.getElementById('profileModalCloseBtn');
        var views = document.querySelectorAll('#profileModalOverlay .profile-dropdown-view');
        if (!overlay || !card) return;

        function showView(id) {
          views.forEach(function (v) {
            v.style.display = (v.id === id) ? 'block' : 'none';
          });
        }

        window.openProfileModal = function (viewId) {
          showView(viewId);
          overlay.classList.add('open');
          document.body.style.overflow = 'hidden';
        };

        function closeModal() {
          overlay.classList.remove('open');
          document.body.style.overflow = '';
        }

        if (closeBtn) {
          closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeModal();
          });
        }

        // Klik di backdrop (di luar kartu popup) menutup popup
        overlay.addEventListener('click', function (e) {
          if (e.target === overlay) closeModal();
        });

        // Klik di dalam kartu popup tidak boleh menutupnya
        card.addEventListener('click', function (e) {
          e.stopPropagation();
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
        });
      })();
      </script>

      <script>
      (function () {
        var MAX_PHOTO_MB = 5;
        var MAX_PHOTO_BYTES = MAX_PHOTO_MB * 1024 * 1024;
        var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
        var STORAGE_KEY = 'siberad-profile-photo-<?php echo e($user->id ?? "default"); ?>';

        var fileInput = document.getElementById('fotoProfilInput');
        var gantiBtn = document.getElementById('gantiFotoBtn');
        var gantiLabel = document.getElementById('gantiFotoLabel');
        var hapusBtn = document.getElementById('hapusFotoBtn');

        var photoBtn = document.getElementById('profilePhotoBtn');
        var photoDropdown = document.getElementById('profilePhotoDropdown');
        var photoLarge = document.getElementById('profilePhotoLarge');
        var initialBtn = document.getElementById('profileInitial');
        var initialDropdown = document.getElementById('profileInitialDropdown');
        var initialLarge = document.getElementById('profileInitialLarge');

        if (!fileInput || !gantiBtn || !hapusBtn) return;

        function showPhoto(dataUrl) {
          photoBtn.src = dataUrl;
          photoDropdown.src = dataUrl;
          photoLarge.src = dataUrl;
          photoBtn.classList.add('visible');
          photoDropdown.classList.add('visible');
          photoLarge.classList.add('visible');
          initialBtn.classList.add('hidden');
          initialDropdown.classList.add('hidden');
          initialLarge.classList.add('hidden');
          hapusBtn.style.display = 'flex';
        }

        function clearPhoto() {
          photoBtn.classList.remove('visible');
          photoDropdown.classList.remove('visible');
          photoLarge.classList.remove('visible');
          photoBtn.removeAttribute('src');
          photoDropdown.removeAttribute('src');
          photoLarge.removeAttribute('src');
          initialBtn.classList.remove('hidden');
          initialDropdown.classList.remove('hidden');
          initialLarge.classList.remove('hidden');
          hapusBtn.style.display = 'none';
        }

        // Muat foto tersimpan (jika ada) saat halaman dibuka
        try {
          var saved = localStorage.getItem(STORAGE_KEY);
          if (saved) showPhoto(saved);
        } catch (e) {}

        gantiBtn.addEventListener('click', function () {
          fileInput.click();
        });

        hapusBtn.addEventListener('click', function () {
          clearPhoto();
          try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
        });

        fileInput.addEventListener('change', function () {
          var file = fileInput.files && fileInput.files[0];
          if (!file) return;

          if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            alert('File "' + file.name + '" ditolak: hanya format JPG, PNG, atau WEBP yang diperbolehkan.');
            fileInput.value = '';
            return;
          }

          if (file.size > MAX_PHOTO_BYTES) {
            alert('File "' + file.name + '" (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB) melebihi batas maksimal ' + MAX_PHOTO_MB + ' MB.');
            fileInput.value = '';
            return;
          }

          gantiLabel.textContent = 'Memproses...';
          gantiBtn.setAttribute('disabled', 'disabled');

          var reader = new FileReader();
          reader.onload = function (e) {
            var dataUrl = e.target.result;
            showPhoto(dataUrl);
            try {
              localStorage.setItem(STORAGE_KEY, dataUrl);
            } catch (err) {
              alert('Foto berhasil ditampilkan, tetapi gagal disimpan secara lokal (kemungkinan ukuran terlalu besar untuk penyimpanan browser).');
            }
            gantiLabel.textContent = 'Ganti Foto Profil';
            gantiBtn.removeAttribute('disabled');
            fileInput.value = '';
          };
          reader.onerror = function () {
            alert('Gagal membaca file gambar. Silakan coba file lain.');
            gantiLabel.textContent = 'Ganti Foto Profil';
            gantiBtn.removeAttribute('disabled');
            fileInput.value = '';
          };
          reader.readAsDataURL(file);
        });
      })();
      </script>

      <script>
      (function () {
        var notifBtn = document.getElementById('notifBtn');
        var dropdown = document.getElementById('notifDropdown');
        var wrapper = document.getElementById('notifMenu');
        if (!notifBtn || !dropdown || !wrapper) return;

        function closeNotif() {
          dropdown.classList.remove('open');
          notifBtn.classList.remove('open');
          notifBtn.setAttribute('aria-expanded', 'false');
        }

        function openNotif() {
          // Tutup dropdown profil kalau lagi kebuka, biar cuma satu yang tampil
          var profileDropdown = document.getElementById('profileDropdown');
          var profileMenuBtn = document.getElementById('profileMenuBtn');
          if (profileDropdown && profileDropdown.classList.contains('open')) {
            profileDropdown.classList.remove('open');
            if (profileMenuBtn) {
              profileMenuBtn.classList.remove('open');
              profileMenuBtn.setAttribute('aria-expanded', 'false');
            }
          }
          dropdown.classList.add('open');
          notifBtn.classList.add('open');
          notifBtn.setAttribute('aria-expanded', 'true');
        }

        notifBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          if (dropdown.classList.contains('open')) {
            closeNotif();
          } else {
            openNotif();
          }
        });

        document.addEventListener('click', function (e) {
          if (!wrapper.contains(e.target)) closeNotif();
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') closeNotif();
        });
      })();
      </script>

  </main>

  
  <div class="confirm-overlay" id="logoutConfirmOverlay">
    <div class="confirm-box" role="alertdialog" aria-modal="true" aria-labelledby="logoutConfirmTitle">
      <div class="confirm-icon">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
      </div>
      <h3 id="logoutConfirmTitle">Keluar dari akun?</h3>
      <p>Sesi kamu akan diakhiri dan kamu perlu login kembali untuk mengakses SIBERAD.</p>
      <div class="confirm-actions">
        <button type="button" class="btn" id="logoutCancelBtn">Batal</button>
        <button type="button" class="btn btn-ghost-red" id="logoutConfirmBtn">Ya, Keluar</button>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var overlay = document.getElementById('logoutConfirmOverlay');
  var cancelBtn = document.getElementById('logoutCancelBtn');
  var confirmBtn = document.getElementById('logoutConfirmBtn');
  var pendingForm = null;

  if (!overlay || !cancelBtn || !confirmBtn) return;

  function openConfirm(targetForm) {
    pendingForm = targetForm;
    overlay.classList.add('open');
  }
  function closeConfirm() {
    overlay.classList.remove('open');
    pendingForm = null;
  }

  document.querySelectorAll('.logout-form').forEach(function (logoutForm) {
    logoutForm.addEventListener('submit', function (e) {
      e.preventDefault();
      openConfirm(logoutForm);
    });
  });

  cancelBtn.addEventListener('click', closeConfirm);
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) closeConfirm();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeConfirm();
  });
  confirmBtn.addEventListener('click', function () {
    if (pendingForm) pendingForm.submit();
  });
})();
</script>

  <script>
  (function () {
    if (typeof Chart === 'undefined') return;

    var root = getComputedStyle(document.documentElement);
    var cGold = root.getPropertyValue('--gold-bright').trim() || '#f3cd5c';
    var cGreen = root.getPropertyValue('--green-bright').trim() || '#3fc27d';
    var cAmber = root.getPropertyValue('--amber').trim() || '#e0a83a';
    var cRed = root.getPropertyValue('--red').trim() || '#c62828';
    var cMuted = root.getPropertyValue('--text-muted').trim() || '#9fb3a5';
    var cText = root.getPropertyValue('--text').trim() || '#f4f1e6';

    Chart.defaults.color = cMuted;
    Chart.defaults.font.family = "'JetBrains Mono', monospace";
    Chart.defaults.font.size = 11;

    var doughnutOptions = {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '62%',
      plugins: { legend: { position: 'bottom', labels: { color: cText, boxWidth: 10, padding: 12 } } }
    };

    function renderDoughnut(canvasId, labels, values, colors) {
      var el = document.getElementById(canvasId);
      if (!el) return;
      new Chart(el, {
        type: 'doughnut',
        data: { labels: labels, datasets: [{ data: values, backgroundColor: colors, borderColor: 'transparent' }] },
        options: doughnutOptions
      });
    }

    // ===== Grafik 1: Pengguna per Kategori Satuan =====
    var distribusiKategori = <?php echo json_encode($distribusiPenggunaKategori, 15, 512) ?>;
    renderDoughnut(
      'chartKategoriSatuan',
      distribusiKategori.map(function (d) { return d.kategori; }),
      distribusiKategori.map(function (d) { return d.jumlah; }),
      [cGold, cGreen, cAmber, cMuted]
    );

    // ===== Grafik 2: Status Permintaan Reset Password =====
    var statusReset = <?php echo json_encode($statusResetPassword, 15, 512) ?>;
    var warnaStatus = { 'Menunggu': cAmber, 'Selesai': cGreen, 'Ditolak': cRed };
    renderDoughnut(
      'chartStatusReset',
      statusReset.map(function (s) { return s.status; }),
      statusReset.map(function (s) { return s.jumlah; }),
      statusReset.map(function (s) { return warnaStatus[s.status] || cMuted; })
    );

    // ===== Grafik 3: Kelengkapan Akun Satuan =====
    var kelengkapan = <?php echo json_encode($kelengkapanAkunSatuan, 15, 512) ?>;
    renderDoughnut(
      'chartKelengkapanSatuan',
      ['Sudah Punya Akun', 'Belum Punya Akun'],
      [kelengkapan.sudah, kelengkapan.belum],
      [cGreen, cRed]
    );
  })();
  </script>

  <script>
  (function () {
    function collectRows(table) {
      return Array.prototype.slice.call(table.querySelectorAll('tbody tr:not(.table-empty-row)'));
    }

    function buatBarisKosong(table) {
      var colCount = table.querySelectorAll('thead th').length || 1;
      var tr = document.createElement('tr');
      tr.className = 'table-empty-row';
      var td = document.createElement('td');
      td.colSpan = colCount;
      td.textContent = 'Tidak ada data yang cocok.';
      tr.appendChild(td);
      return tr;
    }

    // Terapkan pencarian teks + filter dropdown untuk satu tabel tertentu
    // (dipanggil lewat id tabelnya). Diekspos ke window supaya bisa dipanggil
    // ulang dari tempat lain (mis. setelah status baris berubah).
    function terapkanTabelFilter(tableId) {
      var table = document.getElementById(tableId);
      if (!table) return;
      var wrap = table.closest('.tbl-wrap');
      var searchInput = document.querySelector('[data-table-search="' + tableId + '"]');
      var filterSelect = document.querySelector('[data-table-filter="' + tableId + '"]');
      var q = searchInput ? searchInput.value.trim().toLowerCase() : '';
      var f = filterSelect ? filterSelect.value : '';
      var rows = collectRows(table);
      var visibleCount = 0;

      rows.forEach(function (row) {
        var cocokCari = !q || row.textContent.toLowerCase().indexOf(q) !== -1;
        var cocokFilter = !f || row.getAttribute('data-filter-value') === f;
        var tampil = cocokCari && cocokFilter;
        row.style.display = tampil ? '' : 'none';
        if (tampil) visibleCount++;
      });

      var tbody = table.querySelector('tbody');
      var existingEmpty = tbody.querySelector('.table-empty-row');
      if (visibleCount === 0) {
        if (!existingEmpty) tbody.appendChild(buatBarisKosong(table));
      } else if (existingEmpty) {
        existingEmpty.remove();
      }

      // Hitung ulang batas 5 baris hanya berdasarkan baris yang sedang tampil.
      if (window.terapkanRowLimitWrap) window.terapkanRowLimitWrap(wrap);
    }

    window.terapkanTabelFilter = terapkanTabelFilter;

    document.querySelectorAll('[data-table-search]').forEach(function (input) {
      input.addEventListener('input', function () {
        terapkanTabelFilter(input.getAttribute('data-table-search'));
      });
    });
    document.querySelectorAll('[data-table-filter]').forEach(function (select) {
      select.addEventListener('change', function () {
        terapkanTabelFilter(select.getAttribute('data-table-filter'));
      });
    });
  })();
  </script>

<?php echo $__env->make('siberad.dashboards.partials.dash-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html><?php /**PATH D:\Unjani\Kerja Praktek\kelompok5\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/admin.blade.php ENDPATH**/ ?>