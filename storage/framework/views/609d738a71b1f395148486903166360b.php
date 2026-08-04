<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlak Penindakan — SIBERAD</title>
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
      <a href="#" class="side-link" data-tab-link="ancaman"><span class="dot"></span>Deteksi Ancaman</a>
      <a href="#" class="side-link" data-tab-link="penanganan"><span class="dot"></span>Log Penanganan</a>
      <?php if(($user->jabatan ?? '') === 'Piket'): ?>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Buat Laporan</a>
      <?php else: ?>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
      <?php endif; ?>
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
          <h2>Ringkasan Penanganan Ancaman</h2>
          <p>Status ancaman siber yang ditangani Satlak Penindakan hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Ancaman Aktif</div>
            <div class="val" style="color:var(--red);"><?php echo e($stats['ancaman_aktif']); ?></div>
            <div class="sub">Sedang ditangani</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Ransomware</div>
            <div class="val" style="color:var(--red);"><?php echo e($stats['ransomware']); ?></div>
            <div class="sub">Kasus berjalan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Malware Dikarantina</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['malware_dikarantina']); ?></div>
            <div class="sub">Bulan ini</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Insiden Selesai</div>
            <div class="val" style="color:var(--green);"><?php echo e($stats['insiden_selesai_bulan_ini']); ?></div>
            <div class="sub">Bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Insiden Terbaru</h3><p>Ancaman siber yang baru terdeteksi dan sedang ditangani.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Ancaman</th><th>Terdeteksi</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $insidenTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($i['aset']); ?></td>
                  <td><?php echo e($i['jenis']); ?></td>
                  <td><?php echo e($i['waktu']); ?></td>
                  <td><span class="status-dot <?php echo e($i['status_class']); ?>"><?php echo e($i['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="ancaman">
        <div class="section-head">
          <h2>Deteksi Ancaman</h2>
          <p>Daftar ancaman siber yang terdeteksi sistem monitoring beserta tingkat keparahannya.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset Terdampak</th><th>Jenis Ancaman</th><th>Tingkat</th><th>Terdeteksi</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $ancamanTerdeteksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($a['nama']); ?></td>
                  <td style="color:var(--text-muted);"><?php echo e($a['jenis']); ?></td>
                  <td><span class="status-dot <?php echo e($a['tingkat_class']); ?>"><?php echo e($a['tingkat']); ?></span></td>
                  <td><?php echo e($a['terdeteksi']); ?></td>
                  <td>
                    <div class="btn-row">
                      <?php if($a['tingkat_class'] === 'bad'): ?>
                        <button class="btn btn-primary btn-sm" type="button">Isolasi & Tangani</button>
                      <?php else: ?>
                        <button class="btn btn-sm" type="button">Investigasi</button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="penanganan">
        <div class="section-head">
          <h2>Log Penanganan Insiden</h2>
          <p>Riwayat lengkap ancaman siber dan tindakan penanganan yang sudah dilakukan.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Ancaman</th><th>Waktu Insiden</th><th>Tindakan</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $logPenanganan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($l['aset']); ?></td>
                  <td><?php echo e($l['jenis']); ?></td>
                  <td><?php echo e($l['waktu']); ?></td>
                  <td><?php echo e($l['tindakan']); ?></td>
                  <td><span class="badge <?php echo e($l['status_class']); ?>"><?php echo e($l['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="lapor">
        <?php if(($user->jabatan ?? '') === 'Piket'): ?>
          <div class="section-head">
            <h2>Buat Laporan Insiden</h2>
            <p>Form untuk Piket melaporkan insiden ke Komandan Satlak Penindakan.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="asetLapor">Aset Terdampak</label>
                <select id="asetLapor">
                  <?php $__currentLoopData = $ancamanTerdeteksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option><?php echo e($a['nama']); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="form-field">
                <label for="prioritasLapor">Prioritas</label>
                <select id="prioritasLapor">
                  <option>Tinggi</option><option>Sedang</option><option>Rendah</option>
                </select>
              </div>
              <div class="form-field full">
                <label for="perihalLapor">Perihal</label>
                <input id="perihalLapor" type="text" placeholder="Contoh: Ransomware mengenkripsi file server">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi Kejadian</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kronologi, indikator kompromi, dan dampak insiden..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (log / bukti forensik)</label>
                <input id="lampiranLapor" type="file" accept=".pdf,.jpg,.png,.zip">
                <span class="form-hint">Format PDF/JPG/PNG/ZIP, maksimal 25 MB sesuai ketentuan rapat.</span>
              </div>
              <div class="form-field full">
                <button class="btn btn-primary" type="submit">Kirim Laporan ke Komandan</button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Laporan insiden yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Aset</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  <?php $__currentLoopData = $laporanPiket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($l['aset']); ?></td>
                    <td><?php echo e($l['perihal']); ?></td>
                    <td><?php echo e($l['pelapor']); ?></td>
                    <td><?php echo e($l['tanggal']); ?></td>
                    <td><span class="status-dot <?php echo e($l['prioritas_class']); ?>"><?php echo e($l['prioritas']); ?></span></td>
                    <td>
                      <div class="btn-row">
                        <button class="btn btn-primary btn-sm" type="button">Verifikasi & Teruskan</button>
                        <button class="btn btn-ghost-red btn-sm" type="button">Tolak</button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>
      </section>

    </div>
  </main>
</div>
<?php echo $__env->make('siberad.dashboards.partials.dash-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html><?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/satlakrindak.blade.php ENDPATH**/ ?>