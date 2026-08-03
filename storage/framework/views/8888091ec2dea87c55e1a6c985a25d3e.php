<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlakal (Penangkalan) — SIBERAD</title>
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
      <div class="name">Satlakal (Penangkalan)</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="monitoring"><span class="dot"></span>Monitoring Aset</a>
      <a href="#" class="side-link" data-tab-link="insiden"><span class="dot"></span>Log Insiden</a>
      <?php if(($user->jabatan ?? '') === 'Piket'): ?>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Buat Laporan</a>
      <?php else: ?>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
      <?php endif; ?>
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
          <div class="topbar-sub">Satlakal (Penangkalan) &middot; Pemantauan & pemulihan website/aset digital</div>
        </div>
      </div>
      <span class="badge">Satlak</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran Satlakal (Penangkalan). Sesuai rapat KP Pussiberad, tugas satuan ini adalah <b>memantau</b> dan <b>memulihkan</b> website yang terserang.</div>

      
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Pemantauan</h2>
          <p>Status aset/website yang dipantau Satlakal (Penangkalan) hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Aset Dipantau</div>
            <div class="val"><?php echo e($stats['total_aset']); ?></div>
            <div class="sub">Website & layanan digital</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Status Normal</div>
            <div class="val" style="color:var(--green);"><?php echo e($stats['normal']); ?></div>
            <div class="sub">Berjalan baik</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Sedang Diserang</div>
            <div class="val" style="color:var(--red);"><?php echo e($stats['diserang']); ?></div>
            <div class="sub">Butuh penanganan segera</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Dalam Pemulihan</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['pemulihan']); ?></div>
            <div class="sub">Sedang ditangani</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Insiden Terbaru</h3><p>Serangan atau gangguan yang baru terdeteksi.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Gangguan</th><th>Terdeteksi</th><th>Status</th></tr></thead>
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

      
      <section class="tab-panel" data-tab-panel="monitoring">
        <div class="section-head">
          <h2>Monitoring Aset / Website</h2>
          <p>Daftar seluruh aset digital yang dipantau beserta status terkininya.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Aset</th><th>URL</th><th>Status</th><th>Pengecekan Terakhir</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $asetMonitoring; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($a['nama']); ?></td>
                  <td style="font-family:var(--mono);font-size:12px;color:var(--text-muted);"><?php echo e($a['url']); ?></td>
                  <td><span class="status-dot <?php echo e($a['status_class']); ?>"><?php echo e($a['status']); ?></span></td>
                  <td><?php echo e($a['cek_terakhir']); ?></td>
                  <td>
                    <div class="btn-row">
                      <?php if($a['status_class'] === 'bad'): ?>
                        <button class="btn btn-primary btn-sm" type="button">Tandai Dipulihkan</button>
                      <?php else: ?>
                        <button class="btn btn-sm" type="button">Cek Ulang</button>
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

      
      <section class="tab-panel" data-tab-panel="insiden">
        <div class="section-head">
          <h2>Log Insiden & Pemulihan</h2>
          <p>Riwayat lengkap serangan dan tindakan pemulihan yang sudah dilakukan.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Gangguan</th><th>Waktu Insiden</th><th>Tindakan Pemulihan</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $logInsiden; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
            <p>Form untuk Piket melaporkan insiden ke Komandan Satlakal (Penangkalan).</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="asetLapor">Aset / Website Terdampak</label>
                <select id="asetLapor">
                  <?php $__currentLoopData = $asetMonitoring; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Website diserang DDoS">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi Kejadian</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kronologi dan dampak insiden..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (bukti / dokumentasi)</label>
                <input id="lampiranLapor" type="file" accept=".pdf,.jpg,.png">
                <span class="form-hint">Format PDF/JPG/PNG, maksimal 25 MB sesuai ketentuan rapat.</span>
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
</html>
<?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\KP_UNJANI_SI_2026\resources\views/siberad/dashboards/satlakal.blade.php ENDPATH**/ ?>