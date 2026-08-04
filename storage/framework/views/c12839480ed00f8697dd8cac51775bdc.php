<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlak Sibersos — SIBERAD</title>
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
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="monitoring"><span class="dot"></span>Monitoring Medsos</a>
      <a href="#" class="side-link" data-tab-link="isu"><span class="dot"></span>Isu Terdeteksi</a>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
    </nav>
    <div class="side-foot">
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
        </div>
      </div>
      <div class="topbar-actions">
        <button type="button" class="btn-icon-toggle" id="themeToggleBtn" aria-pressed="false" aria-label="Ganti tema">
          <svg class="icon-moon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"></path></svg>
        </button>
      </div>
    </div>

    <div class="content">

      
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Pemantauan Medsos</h2>
          <p>Kondisi pemantauan media sosial daerah hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Akun Dipantau</div>
            <div class="val"><?php echo e($stats['akun_dipantau']); ?></div>
            <div class="sub">Seluruh platform</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Isu Aktif</div>
            <div class="val" style="color:var(--red);"><?php echo e($stats['isu_aktif']); ?></div>
            <div class="sub">Perlu ditindaklanjuti</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Wilayah Terpantau</div>
            <div class="val" style="color:var(--green-bright);"><?php echo e($stats['wilayah']); ?></div>
            <div class="sub">Cakupan daerah</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Laporan Bulan Ini</div>
            <div class="val"><?php echo e($stats['laporan_bulan_ini']); ?></div>
            <div class="sub">Sudah tercatat bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Isu Terbaru</h3><p>Isu atau konten mencurigakan yang baru terdeteksi.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan Isu</th><th>Terdeteksi</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $isuTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($i['platform']); ?></td>
                  <td><?php echo e($i['wilayah']); ?></td>
                  <td><?php echo e($i['ringkasan']); ?></td>
                  <td><?php echo e($i['waktu']); ?></td>
                  <td><span class="status-dot <?php echo e($i['status_class']); ?>"><?php echo e($i['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="monitoring">
        <div class="section-head">
          <h2>Monitoring Akun Media Sosial</h2>
          <p>Daftar akun/kanal media sosial daerah yang dipantau.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Akun</th><th>Platform</th><th>Wilayah</th><th>Status</th><th>Pantauan Terakhir</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $akunMonitoring; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($a['nama']); ?></td>
                  <td><?php echo e($a['platform']); ?></td>
                  <td><?php echo e($a['wilayah']); ?></td>
                  <td><span class="status-dot <?php echo e($a['status_class']); ?>"><?php echo e($a['status']); ?></span></td>
                  <td><?php echo e($a['terakhir']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="isu">
        <div class="section-head">
          <h2>Isu Terdeteksi</h2>
          <p>Riwayat isu, hoaks, atau konten negatif yang terpantau di media sosial daerah.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan Isu</th><th>Prioritas</th><th>Status Penanganan</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $riwayatIsu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($r['platform']); ?></td>
                  <td><?php echo e($r['wilayah']); ?></td>
                  <td><?php echo e($r['ringkasan']); ?></td>
                  <td><span class="status-dot <?php echo e($r['prioritas_class']); ?>"><?php echo e($r['prioritas']); ?></span></td>
                  <td><span class="badge <?php echo e($r['status_class']); ?>"><?php echo e($r['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="lapor">
          <div class="section-head">
            <h2>Verifikasi &amp; Teruskan Laporan</h2>
            <p>Laporan isu media sosial yang menunggu diteruskan.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan</th><th>Dilaporkan Oleh</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  <?php $__currentLoopData = $laporanPiket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($l['platform']); ?></td>
                    <td><?php echo e($l['wilayah']); ?></td>
                    <td><?php echo e($l['ringkasan']); ?></td>
                    <td><?php echo e($l['pelapor']); ?></td>
                    <td><span class="status-dot <?php echo e($l['prioritas_class']); ?>"><?php echo e($l['prioritas']); ?></span></td>
                    <td>
                      <div class="btn-row">
                        <button class="btn btn-primary btn-sm" type="button">Verifikasi & Teruskan ke DANPUS</button>
                        <button class="btn btn-ghost-red btn-sm" type="button">Tolak</button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
        <div class="panel" style="margin-top:20px;">
          <div class="panel-head">
            <div><h3>Koordinasi dengan SDIR</h3><p>Ajukan permintaan koordinasi (bukan laporan insiden) ke SDIR bila diperlukan.</p></div>
          </div>
          <div class="form-grid" style="padding:0 22px 22px;">
            <div class="form-field full">
              <button class="btn btn-sm" type="button" onclick="alert('Prototype — form koordinasi ke SDIR belum tersambung ke database.')">Ajukan Koordinasi ke SDIR</button>
            </div>
          </div>
        </div>

      </section>

    </div>
  </main>
</div>
<?php echo $__env->make('siberad.dashboards.partials.dash-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html><?php /**PATH D:\Unjani\Kerja Praktek\kelompok5\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/satlaksibersos.blade.php ENDPATH**/ ?>