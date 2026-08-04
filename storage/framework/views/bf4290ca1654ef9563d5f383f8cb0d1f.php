<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlok Duktek (Dukungan Teknologi) — SIBERAD</title>
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
      <a href="#" class="side-link" data-tab-link="proyek"><span class="dot"></span>Proyek Riset</a>
      <a href="#" class="side-link" data-tab-link="uji"><span class="dot"></span>Log Uji & Pengembangan</a>
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
          <h2>Ringkasan Riset & Pengembangan</h2>
          <p>Status proyek teknologi yang dikerjakan Satlok Duktek (Dukungan Teknologi) saat ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Proyek Aktif</div>
            <div class="val"><?php echo e($stats['proyek_aktif']); ?></div>
            <div class="sub">Sedang dikerjakan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Proyek AI</div>
            <div class="val" style="color:var(--green);"><?php echo e($stats['proyek_ai']); ?></div>
            <div class="sub">Machine learning & NLP</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Unit Drone Diuji</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['unit_drone_uji']); ?></div>
            <div class="sub">Tahap uji lapangan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Prototipe Selesai</div>
            <div class="val" style="color:var(--green);"><?php echo e($stats['prototipe_selesai']); ?></div>
            <div class="sub">Bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Kegiatan riset & pengembangan yang baru berlangsung.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Proyek</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($i['proyek']); ?></td>
                  <td><?php echo e($i['kegiatan']); ?></td>
                  <td><?php echo e($i['waktu']); ?></td>
                  <td><span class="status-dot <?php echo e($i['status_class']); ?>"><?php echo e($i['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="proyek">
        <div class="section-head">
          <h2>Proyek Riset & Pengembangan</h2>
          <p>Daftar proyek teknologi beserta progres dan target penyelesaian.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Proyek</th><th>Kategori</th><th>Progres</th><th>Status</th><th>Target</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $proyekRiset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($p['nama']); ?></td>
                  <td style="color:var(--text-muted);"><?php echo e($p['kategori']); ?></td>
                  <td style="font-family:var(--mono);"><?php echo e($p['progres']); ?>%</td>
                  <td><span class="status-dot <?php echo e($p['status_class']); ?>"><?php echo e($p['status']); ?></span></td>
                  <td><?php echo e($p['target']); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="uji">
        <div class="section-head">
          <h2>Log Uji & Pengembangan</h2>
          <p>Riwayat pengujian prototipe dan hasil yang didapat.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Proyek</th><th>Kegiatan Uji</th><th>Waktu</th><th>Hasil</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $logUji; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($l['proyek']); ?></td>
                  <td><?php echo e($l['kegiatan']); ?></td>
                  <td><?php echo e($l['waktu']); ?></td>
                  <td><?php echo e($l['hasil']); ?></td>
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
            <h2>Buat Laporan / Pengajuan</h2>
            <p>Form untuk Piket melaporkan kendala atau mengajukan kebutuhan proyek ke Komandan Satlok Duktek (Dukungan Teknologi).</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="proyekLapor">Proyek Terkait</label>
                <select id="proyekLapor">
                  <?php $__currentLoopData = $proyekRiset; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option><?php echo e($p['nama']); ?></option>
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Pengajuan anggaran komponen drone">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kebutuhan, kendala, atau hasil uji terkait..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (dokumentasi / data uji)</label>
                <input id="lampiranLapor" type="file" accept=".pdf">
                <span class="form-hint">Format PDF, maksimal 20 MB, dikirim langsung ke DANPUS.</span>
              </div>
              <div class="form-field full">
                <button class="btn btn-primary" type="submit">Kirim ke Komandan</button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Laporan atau pengajuan yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan langsung ke DANPUS.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Proyek</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  <?php $__currentLoopData = $laporanPiket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($l['proyek']); ?></td>
                    <td><?php echo e($l['perihal']); ?></td>
                    <td><?php echo e($l['pelapor']); ?></td>
                    <td><?php echo e($l['tanggal']); ?></td>
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
        <?php endif; ?>
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
</html><?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/satlakbangtek.blade.php ENDPATH**/ ?>