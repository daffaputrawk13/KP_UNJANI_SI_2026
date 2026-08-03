<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DANPUS — SIBERAD</title>
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
      <div class="name">DANPUS — Komandan Pussiberad</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="laporan"><span class="dot"></span>Laporan Masuk</a>
      <a href="#" class="side-link" data-tab-link="status-satuan"><span class="dot"></span>Status Seluruh Satuan</a>
      <a href="#" class="side-link" data-tab-link="struktur"><span class="dot"></span>Struktur Organisasi</a>
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
          <div class="topbar-sub">DANPUS &middot; Penerima laporan tertinggi dari seluruh satuan Pussiberad</div>
        </div>
      </div>
      <span class="badge">Pimpinan</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran DANPUS. Data di bawah masih contoh (mock) untuk keperluan demo alur kerja, mengikuti hasil rapat KP Pussiberad.</div>

      
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Organisasi</h2>
          <p>Kondisi seluruh satuan Pussiberad secara garis besar, hari ini.</p>
        </div>

        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Satuan</div>
            <div class="val"><?php echo e($stats['total_satuan']); ?></div>
            <div class="sub">4 Satlak &middot; 4 Direktorat &middot; 2 Pimpinan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Insiden Aktif</div>
            <div class="val" style="color:var(--red);"><?php echo e($stats['insiden_aktif']); ?></div>
            <div class="sub">Ditangani Satlakal (Penangkalan)</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Laporan Menunggu Persetujuan</div>
            <div class="val" style="color:var(--amber);"><?php echo e($stats['laporan_pending']); ?></div>
            <div class="sub">Diteruskan dari WADAN</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Status Siaga Hijau</div>
            <div class="val" style="color:var(--green);"><?php echo e($stats['siaga_hijau']); ?>/<?php echo e($stats['total_satuan']); ?></div>
            <div class="sub">Kondisi normal</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>Laporan Prioritas Tinggi</h3>
              <p>Ringkasan laporan yang butuh perhatian DANPUS segera.</p>
            </div>
          </div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Prioritas</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $laporanPrioritas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($l['satuan']); ?></td>
                  <td><?php echo e($l['perihal']); ?></td>
                  <td><span class="status-dot <?php echo e($l['prioritas_class']); ?>"><?php echo e($l['prioritas']); ?></span></td>
                  <td><?php echo e($l['tanggal']); ?></td>
                  <td><span class="badge <?php echo e($l['status_class']); ?>"><?php echo e($l['status']); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="laporan">
        <div class="section-head">
          <h2>Laporan Masuk dari WADAN</h2>
          <p>Laporan yang sudah diverifikasi WADAN dan menunggu persetujuan akhir DANPUS.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Diteruskan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Status</th>
              <?php if(($user->jabatan ?? '') === 'Komandan'): ?><th>Aksi</th><?php endif; ?>
              </tr></thead>
              <tbody>
                <?php $__currentLoopData = $laporanMasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($l['satuan']); ?></td>
                  <td><?php echo e($l['perihal']); ?></td>
                  <td><?php echo e($l['diteruskan_oleh']); ?></td>
                  <td><?php echo e($l['tanggal']); ?></td>
                  <td><span class="status-dot <?php echo e($l['prioritas_class']); ?>"><?php echo e($l['prioritas']); ?></span></td>
                  <td><span class="badge <?php echo e($l['status_class']); ?>"><?php echo e($l['status']); ?></span></td>
                  <?php if(($user->jabatan ?? '') === 'Komandan'): ?>
                  <td>
                    <div class="btn-row">
                      <button class="btn btn-primary btn-sm" type="button">Setujui</button>
                      <button class="btn btn-ghost-red btn-sm" type="button">Tolak</button>
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

      
      <section class="tab-panel" data-tab-panel="status-satuan">
        <div class="section-head">
          <h2>Status Seluruh Satuan</h2>
          <p>Pemantauan kondisi setiap Satlak dan Direktorat di bawah Pussiberad.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Kode</th><th>Nama Satuan</th><th>Kategori</th><th>Status</th><th>Update Terakhir</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $semuaSatuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><span class="badge"><?php echo e($s->kode); ?></span></td>
                  <td><?php echo e($s->nama); ?></td>
                  <td style="text-transform:capitalize;"><?php echo e($s->kategori); ?></td>
                  <td><span class="status-dot <?php echo e($statusSatuan[$s->kode]['class'] ?? 'ok'); ?>"><?php echo e($statusSatuan[$s->kode]['label'] ?? 'Normal'); ?></span></td>
                  <td><?php echo e($statusSatuan[$s->kode]['update'] ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      
      <section class="tab-panel" data-tab-panel="struktur">
        <div class="section-head">
          <h2>Struktur Organisasi & Alur Koordinasi</h2>
          <p>Sesuai hasil rapat KP Pussiberad — alur laporan berjenjang dari Satlak ke DANPUS.</p>
        </div>
        <div class="panel">
          <ul class="org-list">
            <li class="lvl1">DANPUS — Komandan Pussiberad <span class="badge">Persetujuan akhir</span></li>
            <li class="lvl2">WADAN — Wakil Komandan <span class="badge green">Verifikasi</span></li>
            <li class="lvl2">SDIR — Sekretaris Direktorat <span class="badge">Koordinasi</span></li>
            <li class="lvl3">Satlakal (Penangkalan) — Pemantauan & pemulihan website</li>
            <li class="lvl3">Satlak Sibersos — Media sosial daerah</li>
            <li class="lvl3">Satlak Penindakan — Penindakan aksi cyber (malware, ransomware)</li>
            <li class="lvl3">Satlok Duktek (Dukungan Teknologi) — Pengembangan teknologi (AI, drone, dll.)</li>
            <li class="lvl3">Binfung — Penempatan personel</li>
            <li class="lvl3">Binkum — Pengawasan satuan & personel baru</li>
            <li class="lvl3">Diklat — Pendidikan & latihan satuan</li>
            <li class="lvl3">Binmat — Pengurusan materiil satuan</li>
          </ul>
        </div>
      </section>

    </div>
  </main>
</div>
<?php echo $__env->make('siberad.dashboards.partials.dash-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH D:\SEMESTER 6\KP PUSSIBERAD\SISTEM SIMULASI\SISTEM_SIBERAD_updated\SISTEM_SIBERAD\SISTEM\resources\views/siberad/dashboards/danpus.blade.php ENDPATH**/ ?>