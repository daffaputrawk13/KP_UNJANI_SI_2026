<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WADAN — SIBERAD</title>
<link rel="icon" type="image/jpeg" href="<?php echo e(asset('images/logo-pussiberad.jpg')); ?>">
<?php echo $__env->make('siberad.dashboards.partials.dash-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body>
<div class="shell">

  <aside class="sidebar" id="sidebar">
    <div class="side-brand">
      <img src="<?php echo e(asset('images/logo-pussiberad.jpg')); ?>" alt="Lambang Pussiberad">
      <div class="logo">SIBER<span>AD</span></div>
    </div>
    <div class="side-unit">
      <div class="eyebrow">Login sebagai</div>
      <div class="name">WADAN — Wakil Komandan</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="laporan"><span class="dot"></span>Laporan Masuk</a>
      <a href="#" class="side-link" data-tab-link="koordinasi"><span class="dot"></span>Koordinasi Satlak</a>
      <a href="#" class="side-link" data-tab-link="diteruskan"><span class="dot"></span>Diteruskan ke DANPUS</a>
    </nav>
    <div class="side-foot">
      <div class="side-user">
        <div class="side-avatar"><?php echo e(strtoupper(substr($user->name,0,2))); ?></div>
        <div>
          <div class="n"><?php echo e($user->name); ?></div>
          <div class="j"><?php echo e($user->jabatan ?? '-'); ?></div>
        </div>
      </div>
      <form class="logout" method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit">Keluar</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="menu-btn" id="menuBtn">☰</button>
        <div>
          <div class="topbar-title">Selamat datang, <?php echo e($user->name); ?></div>
          <div class="topbar-sub">WADAN &middot; Penerima & verifikator laporan dari SDIR dan seluruh Satlak</div>
        </div>
      </div>
      <div class="topbar-actions">
        <button type="button" class="btn-icon-toggle" id="themeToggleBtn" aria-pressed="false" aria-label="Ganti tema">
          <svg class="icon-moon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"></path></svg>
        </button>
      <span class="badge">Pimpinan</span>
      </div>
    </div>

    <div class="content">

      
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Koordinasi</h2>
          <p>Status laporan yang masuk dari SDIR maupun langsung dari Satlak.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Menunggu Verifikasi</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['menunggu_verifikasi']); ?></div>
            <div class="sub">Perlu ditinjau WADAN</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Diteruskan ke DANPUS</div>
            <div class="val" style="color:var(--green-bright);"><?php echo e($stats['diteruskan']); ?></div>
            <div class="sub">Bulan ini</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Aktif Melapor</div>
            <div class="val"><?php echo e($stats['satuan_aktif']); ?>/8</div>
            <div class="sub">Dari Satlak & Direktorat</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Permintaan Koordinasi</div>
            <div class="val"><?php echo e($stats['permintaan_koordinasi']); ?></div>
            <div class="sub">Menunggu tindak lanjut</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div><h3>Aktivitas Terbaru</h3><p>Ringkasan laporan & koordinasi terbaru dari satuan pelaksana.</p></div>
          </div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal</th><th>Perihal</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($a['satuan']); ?></td>
                  <td><?php echo e($a['perihal']); ?></td>
                  <td><?php echo e($a['tanggal']); ?></td>
                  <td><span class="badge <?php echo e($a['status_class']); ?>"><?php echo e($a['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="laporan">
        <div class="section-head">
          <h2>Laporan Masuk</h2>
          <p>Laporan dari SDIR dan seluruh Satlak yang perlu diverifikasi sebelum diteruskan ke DANPUS.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Tanggal</th><th>Prioritas</th><th>Status</th>
              <?php if(($user->jabatan ?? '') === 'Komandan'): ?><th>Aksi</th><?php endif; ?>
              </tr></thead>
              <tbody>
                <?php $__currentLoopData = $laporanMasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($l['satuan']); ?></td>
                  <td><?php echo e($l['perihal']); ?></td>
                  <td><?php echo e($l['tanggal']); ?></td>
                  <td><span class="status-dot <?php echo e($l['prioritas_class']); ?>"><?php echo e($l['prioritas']); ?></span></td>
                  <td><span class="badge <?php echo e($l['status_class']); ?>"><?php echo e($l['status']); ?></span></td>
                  <?php if(($user->jabatan ?? '') === 'Komandan'): ?>
                  <td>
                    <div class="btn-row">
                      <button class="btn btn-primary btn-sm" type="button">Teruskan ke DANPUS</button>
                      <button class="btn btn-sm" type="button">Kembalikan</button>
                    </div>
                  </td>
                  <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="koordinasi">
        <div class="section-head">
          <h2>Koordinasi dengan Satlak</h2>
          <p>Permintaan koordinasi antar satuan — misalnya permintaan pengiriman personel dari SDIR ke Satlak.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Satuan Tujuan</th><th>Perihal Koordinasi</th><th>Diminta Oleh</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $koordinasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($k['satuan']); ?></td>
                  <td><?php echo e($k['perihal']); ?></td>
                  <td><?php echo e($k['diminta_oleh']); ?></td>
                  <td><?php echo e($k['tanggal']); ?></td>
                  <td><span class="badge <?php echo e($k['status_class']); ?>"><?php echo e($k['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="diteruskan">
        <div class="section-head">
          <h2>Riwayat Diteruskan ke DANPUS</h2>
          <p>Laporan yang sudah diverifikasi WADAN dan sudah dikirim ke DANPUS untuk persetujuan akhir.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Tanggal Diteruskan</th><th>Status di DANPUS</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $riwayatDiteruskan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($r['satuan']); ?></td>
                  <td><?php echo e($r['perihal']); ?></td>
                  <td><?php echo e($r['tanggal']); ?></td>
                  <td><span class="badge <?php echo e($r['status_class']); ?>"><?php echo e($r['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>
  </main>
</div>
<?php echo $__env->make('siberad.dashboards.partials.dash-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html><?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/wadan.blade.php ENDPATH**/ ?>