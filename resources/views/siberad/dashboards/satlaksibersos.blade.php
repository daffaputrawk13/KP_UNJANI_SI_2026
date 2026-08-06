<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlak Sibersos — SIBERAD</title>
<link rel="icon" type="image/jpeg" href="{{ asset('images/logo-pussiberad.jpg') }}">
@include('siberad.dashboards.partials.dash-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<style>
  .chart-box{margin-bottom:26px;}
  .chart-box-head-row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
  .chart-filter-group{display:flex;gap:8px;flex-wrap:wrap;flex-shrink:0;}
  .chart-type-select{background:var(--panel);border:1px solid var(--border);color:var(--text);font-family:var(--mono);font-size:11px;border-radius:6px;padding:5px 8px;cursor:pointer;flex-shrink:0;}
  .chart-type-select:focus{outline:none;border-color:var(--gold);}
  .chart-box-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;transition:all .2s ease;}
  .chart-mini{background:var(--panel-alt);border:1px solid var(--border-soft);border-radius:10px;padding:14px;}
  .chart-mini-head{margin-bottom:8px;}
  .chart-mini-head h4{font-family:var(--display);font-size:13px;font-weight:700;letter-spacing:.01em;line-height:1.3;}
  .chart-mini-head p{font-size:11px;color:var(--text-muted);margin-top:2px;}
  .chart-mini .chart-wrap{position:relative;height:210px;transition:height .2s ease;}
  .chart-box-grid.split-mode{grid-template-columns:1fr;gap:18px;}
  .chart-box-grid.split-mode .chart-mini{padding:18px 20px;border-color:var(--border);}
  .chart-box-grid.split-mode .chart-mini .chart-wrap{height:320px;}
  .chart-legend-note{font-size:11px;color:var(--text-dim);margin-top:14px;line-height:1.5;}
  @media(max-width:980px){.chart-box-grid{grid-template-columns:1fr;}.chart-mini .chart-wrap{height:230px;}}
</style>
</head>
<body>
<div class="profile-modal-overlay" id="profileModalOverlay">
  <div class="profile-modal-card" id="profileModalCard" role="dialog" aria-modal="true" aria-label="Detail profil">
    <button type="button" class="profile-modal-close" id="profileModalCloseBtn" aria-label="Tutup">
      <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"></path></svg>
    </button>

    {{-- ===== VIEW PROFIL SAYA ===== --}}
    <div class="profile-dropdown-view" id="profilePhotoView" style="display:none;">
      <div class="profile-dropdown-head-lg">
        <div class="profile-dropdown-avatar-lg">
          <span class="profile-initial" id="profileInitialLarge">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
          <img class="profile-photo" id="profilePhotoLarge" alt="Foto profil {{ $user->name }}">
        </div>
        <div class="profile-dropdown-name">{{ $user->name }}</div>
        <div class="profile-dropdown-role">{{ $user->jabatan ?? 'Pengguna' }}</div>
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

    {{-- ===== VIEW PENGATURAN AKUN ===== --}}
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

    {{-- ===== VIEW BANTUAN & PANDUAN ===== --}}
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
      <img src="{{ asset('images/logo-pussiberad.jpg') }}" alt="Lambang Pussiberad">
      <div class="logo">SIBER<span>AD</span></div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="dashboard"><span class="dot"></span>Dashboard</a>

      <div class="side-dropdown" id="laporanDropdown">
        <button type="button" class="side-link side-dropdown-toggle" id="laporanToggle" aria-expanded="false" aria-controls="laporanSubmenu">
          <span class="dot"></span>
          <span class="side-link-label">Laporan</span>
          <svg class="side-dropdown-arrow" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg>
        </button>
        <div class="side-dropdown-menu" id="laporanSubmenu">
          <a href="#" class="side-link side-sublink" data-tab-link="tambah-laporan">Tambah Laporan</a>
          <a href="#" class="side-link side-sublink" data-tab-link="status-laporan">Status Laporan</a>
          <a href="#" class="side-link side-sublink" data-tab-link="riwayat-laporan">Riwayat Laporan</a>
        </div>
      </div>

      <div class="side-dropdown" id="medsosDropdown">
        <button type="button" class="side-link side-dropdown-toggle" id="medsosToggle" aria-expanded="false" aria-controls="medsosSubmenu">
          <span class="dot"></span>
          <span class="side-link-label">Media Sosial</span>
          <svg class="side-dropdown-arrow" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg>
        </button>
        <div class="side-dropdown-menu" id="medsosSubmenu">
          <a href="#" class="side-link side-sublink" data-tab-link="akun-medsos">Manajemen Akun</a>
          <a href="#" class="side-link side-sublink" data-tab-link="buat-posting">Buat &amp; Jadwalkan Posting</a>
          <a href="#" class="side-link side-sublink" data-tab-link="kalender-konten">Kalender Konten</a>
          <a href="#" class="side-link side-sublink" data-tab-link="monitoring-engagement">Monitoring Engagement</a>
          <a href="#" class="side-link side-sublink" data-tab-link="statistik-performa">Statistik Performa</a>
          <a href="#" class="side-link side-sublink" data-tab-link="arsip-posting">Arsip Posting</a>
        </div>
      </div>
    </nav>
    <div class="side-foot">
      <form class="logout logout-form" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Keluar</button>
      </form>
    </div>
  </aside>

  <script>
  (function () {
    // Berlaku untuk semua dropdown sidebar (Laporan, Media Sosial, dst),
    // bukan cuma satu — supaya dropdown baru tidak perlu skrip terpisah.
    document.querySelectorAll('.side-dropdown').forEach(function (dropdown) {
      var toggle = dropdown.querySelector('.side-dropdown-toggle');
      if (!toggle) return;

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
            <span class="profile-initial" id="profileInitial">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
            <img class="profile-photo" id="profilePhotoBtn" alt="Foto profil {{ $user->name }}">
          </button>

          <div class="profile-dropdown" id="profileDropdown" role="menu" aria-label="Menu profil">

            <div class="profile-dropdown-head">
              <div class="profile-dropdown-avatar">
                <span class="profile-initial" id="profileInitialDropdown">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                <img class="profile-photo" id="profilePhotoDropdown" alt="Foto profil {{ $user->name }}">
              </div>
              <div>
                <div class="profile-dropdown-name">{{ $user->name }}</div>
                <div class="profile-dropdown-role">{{ $user->jabatan ?? 'Pengguna' }}</div>
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

            <form class="logout-form" method="POST" action="{{ route('logout') }}">
              @csrf
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

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="dashboard">
        <div class="section-head">
          <h2>Ringkasan Pemantauan Medsos</h2>
          <p>Kondisi pemantauan media sosial daerah hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Akun Dipantau</div>
            <div class="val">{{ $stats['akun_dipantau'] }}</div>
            <div class="sub">Seluruh platform</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Isu Aktif</div>
            <div class="val" style="color:var(--red);">{{ $stats['isu_aktif'] }}</div>
            <div class="sub">Perlu ditindaklanjuti</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Wilayah Terpantau</div>
            <div class="val" style="color:var(--green-bright);">{{ $stats['wilayah'] }}</div>
            <div class="sub">Cakupan daerah</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Laporan Bulan Ini</div>
            <div class="val">{{ $stats['laporan_bulan_ini'] }}</div>
            <div class="sub">Sudah tercatat bulan ini</div>
          </div>
        </div>


        <div class="panel chart-box">
          <div class="chart-box-head-row">
            <div><h3 style="font-family:var(--display);font-size:17px;font-weight:700;">Analitik Pemantauan Media Sosial</h3><p style="font-size:12px;color:var(--text-muted);margin-top:2px;">Sebaran akun yang dipantau per platform, status akun, dan tingkat prioritas isu.</p></div>
            <div class="chart-filter-group">
              <select class="chart-type-select" id="chartDateFilterGlobal">
                <option value="7d">7 Hari Terakhir</option>
                <option value="30d">30 Hari Terakhir</option>
                <option value="90d">3 Bulan Terakhir</option>
                <option value="all" selected>Semua Waktu</option>
              </select>
              <select class="chart-type-select" id="chartTypeFilterGlobal">
                <option value="bar" selected>Grafik Batang</option>
                <option value="line">Grafik Garis</option>
                <option value="radar">Grafik Radar</option>
              </select>
            </div>
          </div>

          <div class="chart-box-grid" id="chartBoxGrid">

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Akun per Platform</h4><p>Instagram, Facebook, X, TikTok, dsb.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartAkunPlatform"></canvas></div>
            </div>

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Status Akun Dipantau</h4><p>Normal vs Terpantau Isu.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartStatusAkun"></canvas></div>
            </div>

            <div class="chart-mini">
              <div class="chart-mini-head">
                <h4>Isu per Prioritas</h4><p>Tingkat urgensi isu yang tercatat.</p>
              </div>
              <div class="chart-wrap"><canvas id="chartIsuPrioritas"></canvas></div>
            </div>
          </div>
          <p class="chart-legend-note">Merah = prioritas tinggi (perlu ditindaklanjuti segera). Ganti jenis grafik lewat dropdown di kanan atas — pilihan selain "Batang" akan otomatis memisah tiap grafik menjadi tampilan yang lebih besar. Filter tanggal masih simulasi proporsional karena histori isu per tanggal belum tersambung ke database.</p>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Isu Terbaru</h3><p>Isu atau konten mencurigakan yang baru terdeteksi.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan Isu</th><th>Terdeteksi</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($isuTerbaru as $i)
                <tr>
                  <td>{{ $i['platform'] }}</td>
                  <td>{{ $i['wilayah'] }}</td>
                  <td>{{ $i['ringkasan'] }}</td>
                  <td>{{ $i['waktu'] }}</td>
                  <td><span class="status-dot {{ $i['status_class'] }}">{{ $i['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== MONITORING MEDSOS ===== --}}
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
                @foreach($akunMonitoring as $a)
                <tr>
                  <td>{{ $a['nama'] }}</td>
                  <td>{{ $a['platform'] }}</td>
                  <td>{{ $a['wilayah'] }}</td>
                  <td><span class="status-dot {{ $a['status_class'] }}">{{ $a['status'] }}</span></td>
                  <td>{{ $a['terakhir'] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== ISU TERDETEKSI ===== --}}
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
                @foreach($riwayatIsu as $r)
                <tr>
                  <td>{{ $r['platform'] }}</td>
                  <td>{{ $r['wilayah'] }}</td>
                  <td>{{ $r['ringkasan'] }}</td>
                  <td><span class="status-dot {{ $r['prioritas_class'] }}">{{ $r['prioritas'] }}</span></td>
                  <td><span class="badge {{ $r['status_class'] }}">{{ $r['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LAPORAN › TAMBAH LAPORAN ===== --}}
      <section class="tab-panel" data-tab-panel="tambah-laporan">
        <div class="section-head">
          <h2>Tambah Laporan</h2>
          <p>Catat isu, hoaks, atau konten negatif baru yang terpantau di media sosial daerah.</p>
        </div>
        <div class="panel">
          <form class="form-grid" id="formTambahLaporan" style="padding:22px;" novalidate>
            <div class="form-field">
              <label for="akunTambahLaporan">Akun / Platform Terdampak</label>
              <select id="akunTambahLaporan" required>
                @foreach($akunMonitoring as $a)
                  <option>{{ $a['nama'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label for="prioritasTambahLaporan">Prioritas</label>
              <select id="prioritasTambahLaporan" required>
                <option>Tinggi</option><option>Sedang</option><option>Rendah</option>
              </select>
            </div>
            <div class="form-field full">
              <label for="perihalTambahLaporan">Perihal</label>
              <input id="perihalTambahLaporan" type="text" placeholder="Contoh: Hoaks rekrutmen mengatasnamakan TNI AD" required>
            </div>
            <div class="form-field full">
              <label for="deskripsiTambahLaporan">Deskripsi Kejadian</label>
              <textarea id="deskripsiTambahLaporan" rows="4" placeholder="Jelaskan kronologi dan dampak isu..." required></textarea>
            </div>
            <div class="form-field full">
              <label for="lampiranTambahLaporan">Lampiran (tangkapan layar / dokumentasi)</label>
              <input id="lampiranTambahLaporan" type="file" accept="application/pdf,.pdf">
              <span class="form-hint">Format PDF, maksimal 20 MB.</span>
            </div>
            <div class="form-field full">
              <button class="btn btn-primary" type="button" onclick="alert('Prototype — form Tambah Laporan belum tersambung ke database.')">Simpan Laporan</button>
            </div>
          </form>
        </div>
      </section>

      {{-- ===== LAPORAN › STATUS LAPORAN ===== --}}
      <section class="tab-panel" data-tab-panel="status-laporan">
        <div class="section-head">
          <h2>Status Laporan</h2>
          <p>Pantau progres laporan yang sudah diajukan oleh Satlak Sibersos.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Platform</th><th>Wilayah</th><th>Perihal</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                <tr>
                  <td>Facebook</td>
                  <td>Jawa Barat</td>
                  <td>Hoaks rekrutmen mengatasnamakan TNI AD</td>
                  <td>02 Agu 2026</td>
                  <td><span class="status-dot amber">Menunggu Verifikasi</span></td>
                </tr>
                <tr>
                  <td>X (Twitter)</td>
                  <td>Nasional</td>
                  <td>Narasi provokasi soal latihan gabungan</td>
                  <td>01 Agu 2026</td>
                  <td><span class="status-dot warn">Diteruskan ke DANPUS</span></td>
                </tr>
                <tr>
                  <td>Instagram</td>
                  <td>Kodim 0612/Bandung</td>
                  <td>Akun tiruan mengatasnamakan satuan</td>
                  <td>28 Jul 2026</td>
                  <td><span class="status-dot green">Disetujui DANPUS</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LAPORAN › RIWAYAT LAPORAN ===== --}}
      <section class="tab-panel" data-tab-panel="riwayat-laporan">
        <div class="section-head">
          <h2>Riwayat Laporan</h2>
          <p>Log lengkap isu dan tindak lanjut yang pernah ditangani Satlak Sibersos.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan Isu</th><th>Prioritas</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($riwayatIsu as $r)
                <tr>
                  <td>{{ $r['platform'] }}</td>
                  <td>{{ $r['wilayah'] }}</td>
                  <td>{{ $r['ringkasan'] }}</td>
                  <td><span class="status-dot {{ $r['prioritas_class'] }}">{{ $r['prioritas'] }}</span></td>
                  <td><span class="badge {{ $r['status_class'] }}">{{ $r['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== MEDIA SOSIAL › MANAJEMEN AKUN ===== --}}
      <section class="tab-panel" data-tab-panel="akun-medsos">
        <div class="section-head">
          <h2>Manajemen Akun Media Sosial</h2>
          <p>Kelola akun media sosial resmi yang dipegang langsung oleh Satlak Sibersos.</p>
        </div>

        @if (session('status'))
          <div class="notice">{{ session('status') }}</div>
        @endif

        <div class="panel">
          <div class="panel-head"><div><h3>Tambah Akun Baru</h3><p>Daftarkan akun media sosial resmi satuan.</p></div></div>
          <form class="form-grid" method="POST" action="{{ route('akun-medsos.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-field">
              <label for="namaAkunBaru">Nama Akun</label>
              <input id="namaAkunBaru" name="nama_akun" type="text" placeholder="Contoh: Instagram Resmi Satlak Sibersos" required>
            </div>
            <div class="form-field">
              <label for="platformAkunBaru">Platform</label>
              <select id="platformAkunBaru" name="platform" required>
                <option value="Instagram">Instagram</option>
                <option value="Facebook">Facebook</option>
                <option value="X (Twitter)">X (Twitter)</option>
                <option value="TikTok">TikTok</option>
                <option value="YouTube">YouTube</option>
              </select>
            </div>
            <div class="form-field">
              <label for="usernameAkunBaru">Username / Handle</label>
              <input id="usernameAkunBaru" name="username_platform" type="text" placeholder="@satlaksibersos" required>
            </div>
            <div class="form-field">
              <label for="urlAkunBaru">URL Profil (opsional)</label>
              <input id="urlAkunBaru" name="url_profil" type="url" placeholder="https://instagram.com/satlaksibersos">
            </div>
            <div class="form-field full">
              <label for="fotoAkunBaru">Foto Profil (opsional)</label>
              <input id="fotoAkunBaru" name="foto_profil" type="file" accept="image/png,image/jpeg,image/webp">
              <span class="form-hint">Format gambar, maksimal 5 MB.</span>
            </div>
            <div class="form-field full">
              <button class="btn btn-primary" type="submit">Simpan Akun</button>
            </div>
          </form>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Daftar Akun Terdaftar</h3><p>Seluruh akun resmi yang dikelola satuan ini.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Akun</th><th>Platform</th><th>Username</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($akunMedsosList as $a)
                <tr>
                  <td>{{ $a->nama_akun }}</td>
                  <td>{{ $a->platform }}</td>
                  <td>{{ $a->username_platform }}</td>
                  <td><span class="status-dot {{ $a->status === 'Aktif' ? 'ok' : 'warn' }}">{{ $a->status }}</span></td>
                  <td>
                    <div class="btn-row">
                      <form method="POST" action="{{ route('akun-medsos.update', $a) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $a->status === 'Aktif' ? 'Nonaktif' : 'Aktif' }}">
                        <button class="btn btn-sm" type="submit">{{ $a->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                      </form>
                      <form method="POST" action="{{ route('akun-medsos.destroy', $a) }}" onsubmit="return confirm('Hapus akun ini beserta seluruh postingannya?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-ghost-red" type="submit">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" style="color:var(--text-dim);text-align:center;padding:24px;">Belum ada akun media sosial terdaftar.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== MEDIA SOSIAL › BUAT & JADWALKAN POSTING ===== --}}
      <section class="tab-panel" data-tab-panel="buat-posting">
        <div class="section-head">
          <h2>Buat &amp; Jadwalkan Posting</h2>
          <p>Buat konten baru — simpan sebagai draft, jadwalkan tayang, atau terbitkan langsung.</p>
        </div>

        @if (session('status'))
          <div class="notice">{{ session('status') }}</div>
        @endif

        <div class="panel">
          @if ($akunMedsosList->isEmpty())
            <p style="color:var(--text-muted);font-size:13px;">Tambahkan akun media sosial terlebih dahulu di menu <b>Manajemen Akun</b> sebelum membuat postingan.</p>
          @else
          <form class="form-grid" method="POST" action="{{ route('posting.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-field">
              <label for="akunPosting">Akun Tujuan</label>
              <select id="akunPosting" name="akun_medsos_id" required>
                @foreach($akunMedsosList as $a)
                  <option value="{{ $a->id }}">{{ $a->nama_akun }} ({{ $a->platform }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label for="jenisPosting">Jenis Konten</label>
              <select id="jenisPosting" name="jenis_konten" required>
                <option value="Feed">Feed / Foto</option>
                <option value="Reels/Video">Reels / Video</option>
                <option value="Story">Story</option>
                <option value="Carousel">Carousel</option>
              </select>
            </div>
            <div class="form-field full">
              <label for="judulPosting">Judul Internal</label>
              <input id="judulPosting" name="judul" type="text" placeholder="Contoh: Edukasi Anti-Hoaks Minggu Ini" required>
            </div>
            <div class="form-field full">
              <label for="captionPosting">Caption</label>
              <textarea id="captionPosting" name="caption" rows="4" placeholder="Tulis caption postingan di sini..." required></textarea>
            </div>
            <div class="form-field full">
              <label for="mediaPosting">Upload Foto / Video</label>
              <input id="mediaPosting" name="media" type="file" accept="image/png,image/jpeg,image/webp,video/mp4,video/quicktime">
              <span class="form-hint">Foto (JPG/PNG/WEBP) atau video (MP4/MOV), maksimal 50 MB.</span>
            </div>
            <div class="form-field full">
              <label for="jadwalPosting">Jadwalkan Tayang (opsional)</label>
              <input id="jadwalPosting" name="scheduled_at" type="datetime-local">
              <span class="form-hint">Isi kalau ingin dijadwalkan. Kosongkan kalau mau disimpan draft atau langsung diterbitkan.</span>
            </div>
            <div class="form-field full btn-row">
              <button class="btn" type="submit" name="aksi" value="simpan_draft">Simpan Draft</button>
              <button class="btn" type="submit" name="aksi" value="jadwalkan">Jadwalkan</button>
              <button class="btn btn-primary" type="submit" name="aksi" value="terbitkan">Terbitkan Sekarang</button>
            </div>
          </form>
          @endif
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Draft &amp; Terjadwal</h3><p>Postingan yang belum tayang.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Judul</th><th>Akun</th><th>Jenis</th><th>Status</th><th>Jadwal Tayang</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($postinganDraftJadwal as $p)
                <tr>
                  <td>{{ $p->judul }}</td>
                  <td>{{ $p->akunMedsos->nama_akun ?? '-' }}</td>
                  <td>{{ $p->jenis_konten }}</td>
                  <td><span class="status-dot {{ $p->status === 'Terjadwal' ? 'warn' : 'ok' }}">{{ $p->status }}</span></td>
                  <td>{{ $p->scheduled_at?->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                  <td>
                    <div class="btn-row">
                      <form method="POST" action="{{ route('posting.terbitkan', $p) }}">
                        @csrf
                        <button class="btn btn-sm btn-primary" type="submit">Terbitkan</button>
                      </form>
                      <form method="POST" action="{{ route('posting.destroy', $p) }}" onsubmit="return confirm('Hapus postingan ini?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-ghost-red" type="submit">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" style="color:var(--text-dim);text-align:center;padding:24px;">Belum ada draft atau postingan terjadwal.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== MEDIA SOSIAL › KALENDER KONTEN ===== --}}
      <section class="tab-panel" data-tab-panel="kalender-konten">
        <div class="section-head">
          <h2>Kalender Konten</h2>
          <p>Jadwal tayang seluruh konten, dikelompokkan per tanggal.</p>
        </div>
        <div class="panel">
          @forelse($kalenderKonten as $tanggal => $daftar)
            <div style="margin-bottom:22px;">
              <div style="font-family:var(--mono);font-size:11.5px;letter-spacing:.06em;color:var(--gold-bright);text-transform:uppercase;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border-soft);">
                {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
              </div>
              <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($daftar as $p)
                  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 14px;background:var(--panel-alt);border:1px solid var(--border-soft);border-radius:9px;flex-wrap:wrap;">
                    <div>
                      <div style="font-weight:600;font-size:13.5px;">{{ $p->judul }}</div>
                      <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">{{ $p->akunMedsos->nama_akun ?? '-' }} · {{ $p->jenis_konten }}</div>
                    </div>
                    <span class="status-dot {{ $p->status === 'Terjadwal' ? 'warn' : 'ok' }}">
                      {{ $p->status === 'Terjadwal' ? 'Terjadwal ' . $p->scheduled_at?->translatedFormat('H:i') : 'Terbit ' . $p->published_at?->translatedFormat('H:i') }}
                    </span>
                  </div>
                @endforeach
              </div>
            </div>
          @empty
            <p style="color:var(--text-dim);text-align:center;padding:24px;">Belum ada konten terjadwal maupun terbit.</p>
          @endforelse
        </div>
      </section>

      {{-- ===== MEDIA SOSIAL › MONITORING ENGAGEMENT ===== --}}
      <section class="tab-panel" data-tab-panel="monitoring-engagement">
        <div class="section-head">
          <h2>Monitoring Engagement</h2>
          <p>Pantau dan perbarui angka like, komentar, dan share tiap postingan yang sudah tayang.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Postingan</th><th>Akun</th><th>Tayang</th><th>Like</th><th>Komentar</th><th>Share</th><th>Dilihat</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($postinganTerbit as $p)
                <tr>
                  <td>{{ $p->judul }}</td>
                  <td>{{ $p->akunMedsos->nama_akun ?? '-' }}</td>
                  <td>{{ $p->published_at?->translatedFormat('d M Y') }}</td>
                  <td colspan="4">
                    <form method="POST" action="{{ route('posting.engagement', $p) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                      @csrf @method('PATCH')
                      <input type="number" name="likes" value="{{ $p->likes }}" min="0" style="width:80px;border:1px solid var(--border);border-radius:6px;padding:6px 8px;background:var(--bg-deep);color:var(--text);font-size:12.5px;">
                      <input type="number" name="komentar" value="{{ $p->komentar }}" min="0" style="width:80px;border:1px solid var(--border);border-radius:6px;padding:6px 8px;background:var(--bg-deep);color:var(--text);font-size:12.5px;">
                      <input type="number" name="share" value="{{ $p->share }}" min="0" style="width:80px;border:1px solid var(--border);border-radius:6px;padding:6px 8px;background:var(--bg-deep);color:var(--text);font-size:12.5px;">
                      <input type="number" name="dilihat" value="{{ $p->dilihat }}" min="0" style="width:90px;border:1px solid var(--border);border-radius:6px;padding:6px 8px;background:var(--bg-deep);color:var(--text);font-size:12.5px;">
                      <button class="btn btn-sm" type="submit">Simpan</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="8" style="color:var(--text-dim);text-align:center;padding:24px;">Belum ada postingan yang tayang.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <p class="chart-legend-note">Kolom Like / Komentar / Share / Dilihat bisa diedit langsung lalu klik "Simpan" — dipakai untuk mencatat hasil pantauan manual karena sistem belum tersambung API resmi tiap platform.</p>
        </div>
      </section>

      {{-- ===== MEDIA SOSIAL › STATISTIK PERFORMA ===== --}}
      <section class="tab-panel" data-tab-panel="statistik-performa">
        <div class="section-head">
          <h2>Statistik Performa Posting</h2>
          <p>Ringkasan performa seluruh konten yang sudah diterbitkan.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Akun Dikelola</div>
            <div class="val">{{ $statsMedsos['total_akun'] }}</div>
            <div class="sub">Akun media sosial resmi</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Total Postingan</div>
            <div class="val">{{ $statsMedsos['total_posting'] }}</div>
            <div class="sub">Draft, terjadwal &amp; terbit</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Sudah Terbit</div>
            <div class="val" style="color:var(--green-bright);">{{ $statsMedsos['sudah_terbit'] }}</div>
            <div class="sub">{{ $statsMedsos['terjadwal'] }} menunggu jadwal tayang</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Total Engagement</div>
            <div class="val">{{ number_format($statsMedsos['total_engagement'], 0, ',', '.') }}</div>
            <div class="sub">Like + komentar + share</div>
          </div>
        </div>

        <div class="panel chart-box">
          <div class="panel-head"><div><h3>Engagement per Postingan</h3><p>Perbandingan like, komentar, dan share tiap konten yang sudah tayang.</p></div></div>
          <div class="chart-wrap" style="height:280px;"><canvas id="chartEngagementPosting"></canvas></div>
        </div>

        @if($statsMedsos['postingan_terbaik'])
        <div class="panel">
          <div class="panel-head"><div><h3>Postingan Paling Engaging</h3><p>Konten dengan total interaksi tertinggi.</p></div></div>
          <div style="padding:4px 2px;">
            <div style="font-weight:700;font-size:15px;">{{ $statsMedsos['postingan_terbaik']->judul }}</div>
            <div style="font-size:12.5px;color:var(--text-muted);margin-top:4px;">{{ $statsMedsos['postingan_terbaik']->akunMedsos->nama_akun ?? '-' }} · Tayang {{ $statsMedsos['postingan_terbaik']->published_at?->translatedFormat('d M Y') }}</div>
            <div class="btn-row" style="margin-top:12px;">
              <span class="badge green">{{ $statsMedsos['postingan_terbaik']->likes }} Like</span>
              <span class="badge">{{ $statsMedsos['postingan_terbaik']->komentar }} Komentar</span>
              <span class="badge amber">{{ $statsMedsos['postingan_terbaik']->share }} Share</span>
            </div>
          </div>
        </div>
        @endif
      </section>

      {{-- ===== MEDIA SOSIAL › ARSIP POSTING ===== --}}
      <section class="tab-panel" data-tab-panel="arsip-posting">
        <div class="section-head">
          <h2>Arsip Seluruh Posting</h2>
          <p>Riwayat lengkap seluruh konten yang pernah dibuat, baik draft, terjadwal, maupun sudah tayang.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap" data-row-limit="8">
            <table class="dtbl">
              <thead><tr><th>Judul</th><th>Akun</th><th>Jenis</th><th>Dibuat Oleh</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
              <tbody>
                @forelse($postinganDraftJadwal->concat($postinganTerbit)->sortByDesc('created_at') as $p)
                <tr>
                  <td>{{ $p->judul }}</td>
                  <td>{{ $p->akunMedsos->nama_akun ?? '-' }}</td>
                  <td>{{ $p->jenis_konten }}</td>
                  <td>{{ $p->user->name ?? '-' }}</td>
                  <td>
                    <span class="badge {{ match($p->status) { 'Terbit' => 'green', 'Terjadwal' => 'amber', default => '' } }}">{{ $p->status }}</span>
                  </td>
                  <td>{{ $p->created_at->translatedFormat('d M Y') }}</td>
                  <td>
                    <form method="POST" action="{{ route('posting.destroy', $p) }}" onsubmit="return confirm('Hapus postingan ini dari arsip?');">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-ghost-red" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="7" style="color:var(--text-dim);text-align:center;padding:24px;">Arsip masih kosong.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </div>

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
        var STORAGE_KEY = 'siberad-profile-photo-{{ $user->id ?? "default" }}';

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

  {{-- ===== KONFIRMASI KELUAR ===== --}}
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

{{-- ===== ANALITIK PEMANTAUAN MEDIA SOSIAL (RINGKASAN) ===== --}}
<script>
(function () {
  var canvasCheck = document.getElementById('chartAkunPlatform');
  if (!canvasCheck || typeof Chart === 'undefined') return;

  var css = getComputedStyle(document.documentElement);
  var cGold = css.getPropertyValue('--gold-bright').trim() || '#f2c14e';
  var cGreen = css.getPropertyValue('--green').trim() || '#3ddc84';
  var cAmber = css.getPropertyValue('--amber').trim() || '#f2a93b';
  var cRed = css.getPropertyValue('--red').trim() || '#e5484d';
  var cMuted = css.getPropertyValue('--text-dim').trim() || '#7d8f87';
  var cText = css.getPropertyValue('--text').trim() || '#e8efe9';
  var cBorder = css.getPropertyValue('--border-soft').trim() || '#22302a';

  var akunPlatform = @json($akunPerPlatform ?? []);
  var statusAkun = @json($statusAkunDistribusi ?? []);
  var isuPrioritas = @json($isuPerPrioritas ?? []);

  var registry = {};

  function wrapLabel(text, maxCharsPerLine) {
    maxCharsPerLine = maxCharsPerLine || 14;
    var words = String(text).split(' ');
    var lines = [];
    var current = '';
    words.forEach(function (w) {
      var test = current ? current + ' ' + w : w;
      if (test.length > maxCharsPerLine && current) {
        lines.push(current);
        current = w;
      } else {
        current = test;
      }
    });
    if (current) lines.push(current);
    return lines;
  }

  function buildOptions(type, opts) {
    opts = opts || {};
    if (type === 'radar') {
      return {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: 0 },
        plugins: { legend: { display: false, position: 'bottom', labels: { color: cText, boxWidth: 9, padding: 10 } } },
        scales: {
          r: {
            min: 0, max: opts.max || 100,
            grid: { color: cBorder }, angleLines: { color: cBorder },
            pointLabels: { color: cMuted, font: { size: 10 } },
            ticks: { display: false, backdropColor: 'transparent' }
          }
        }
      };
    }
    if (type === 'doughnut') {
      return {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: { legend: { position: 'bottom', labels: { color: cText, boxWidth: 10, padding: 12 } } }
      };
    }
    return {
      indexAxis: opts.horizontal ? 'y' : 'x',
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { left: 4, right: 12, top: 8, bottom: 0 } },
      plugins: { legend: { display: false } },
      scales: {
        x: opts.horizontal
          ? { min: 0, grid: { color: cBorder }, ticks: { precision: 0 } }
          : { offset: false, grid: { display: false }, ticks: { maxRotation: 0, minRotation: 0, autoSkip: false, font: { size: 10 } } },
        y: opts.horizontal
          ? { offset: false, grid: { display: false }, ticks: { autoSkip: false } }
          : { min: 0, grid: { color: cBorder }, ticks: { precision: 0 } }
      }
    };
  }

  function renderChart(canvasId, type, rawLabels, values, colors, opts) {
    var el = document.getElementById(canvasId);
    if (!el) return;
    if (registry[canvasId]) registry[canvasId].destroy();

    var labels = (type !== 'doughnut' && !opts.horizontal)
      ? rawLabels.map(function (l) { return wrapLabel(l, 14); })
      : rawLabels;

    var isFillType = (type === 'doughnut' || type === 'radar');
    var fillColor = (type === 'radar') ? hexToRgba(opts.lineColor || cGold, 0.28) : colors;

    var dataset = {
      label: opts.label || '',
      data: values,
      backgroundColor: isFillType ? fillColor : colors,
      borderColor: type === 'line' ? (opts.lineColor || cGold) : (type === 'radar' ? (opts.lineColor || cGold) : 'transparent'),
      borderWidth: (type === 'line' || type === 'radar') ? 2 : 0,
      borderRadius: (type === 'bar') ? 4 : 0,
      maxBarThickness: opts.horizontal ? 34 : 44,
      fill: type === 'radar' ? true : (type === 'line' ? false : undefined),
      tension: 0,
      pointBackgroundColor: (type === 'line' || type === 'radar') ? (opts.lineColor || cGold) : undefined,
      pointRadius: type === 'line' ? 3 : undefined,
    };

    registry[canvasId] = new Chart(el, {
      type: type,
      data: { labels: labels, datasets: [dataset] },
      options: buildOptions(type, opts)
    });
  }

  function hexToRgba(hex, alpha) {
    hex = (hex || '').replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(function (c) { return c + c; }).join('');
    var r = parseInt(hex.substring(0, 2), 16) || 0;
    var g = parseInt(hex.substring(2, 4), 16) || 0;
    var b = parseInt(hex.substring(4, 6), 16) || 0;
    return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
  }

  function drawAkunPlatform(type, data) {
    var t = type === 'bar' ? 'doughnut' : type;
    renderChart(
      'chartAkunPlatform', t,
      data.map(function (s) { return s.platform; }),
      data.map(function (s) { return s.jumlah; }),
      [cGold, cGreen, cAmber, cRed],
      { label: 'Jumlah Akun' }
    );
  }

  function drawStatusAkun(type, data) {
    renderChart(
      'chartStatusAkun', type,
      data.map(function (s) { return s.label; }),
      data.map(function (s) { return s.jumlah; }),
      type === 'line' || type === 'radar' ? cGold : [cGreen, cAmber],
      { label: 'Jumlah Akun', lineColor: cGold }
    );
  }

  function drawIsuPrioritas(type, data) {
    renderChart(
      'chartIsuPrioritas', type,
      data.map(function (p) { return p.label; }),
      data.map(function (p) { return p.jumlah; }),
      type === 'line' || type === 'radar' ? cGold : [cRed, cAmber, cMuted],
      { label: 'Jumlah Isu', lineColor: cGold }
    );
  }

  var DATE_RANGE_FACTOR = { '7d': 0.35, '30d': 0.7, '90d': 0.9, 'all': 1 };

  function scaleFactor(arr, factor, keyValue) {
    return arr.map(function (d) {
      var c = Object.assign({}, d);
      c[keyValue] = Math.max(0, Math.round(d[keyValue] * factor));
      return c;
    });
  }

  var typeFilterEl = document.getElementById('chartTypeFilterGlobal');
  var dateFilterEl = document.getElementById('chartDateFilterGlobal');
  var gridEl = document.getElementById('chartBoxGrid');

  function redrawAll() {
    var type = typeFilterEl ? typeFilterEl.value : 'bar';
    var factor = DATE_RANGE_FACTOR[dateFilterEl ? dateFilterEl.value : 'all'] || 1;
    if (gridEl) gridEl.classList.toggle('split-mode', type !== 'bar');

    drawAkunPlatform(type, scaleFactor(akunPlatform, factor, 'jumlah'));
    drawStatusAkun(type, scaleFactor(statusAkun, factor, 'jumlah'));
    drawIsuPrioritas(type, scaleFactor(isuPrioritas, factor, 'jumlah'));
  }

  redrawAll();

  if (typeFilterEl) typeFilterEl.addEventListener('change', redrawAll);
  if (dateFilterEl) dateFilterEl.addEventListener('change', redrawAll);
})();
</script>

{{-- ===== STATISTIK PERFORMA POSTING (tab Media Sosial) ===== --}}
<script>
(function () {
  var canvas = document.getElementById('chartEngagementPosting');
  if (!canvas || typeof Chart === 'undefined') return;

  var css = getComputedStyle(document.documentElement);
  var cGold = css.getPropertyValue('--gold-bright').trim() || '#f2c14e';
  var cGreen = css.getPropertyValue('--green-bright').trim() || '#3ddc84';
  var cAmber = css.getPropertyValue('--amber').trim() || '#f2a93b';
  var cText = css.getPropertyValue('--text').trim() || '#e8efe9';
  var cBorder = css.getPropertyValue('--border-soft').trim() || '#22302a';

  var postingan = @json($postinganTerbit->values()->map(function ($p) {
      return ['judul' => $p->judul, 'likes' => $p->likes, 'komentar' => $p->komentar, 'share' => $p->share];
  }));

  if (!postingan.length) return;

  new Chart(canvas, {
    type: 'bar',
    data: {
      labels: postingan.map(function (p) { return p.judul; }),
      datasets: [
        { label: 'Like', data: postingan.map(function (p) { return p.likes; }), backgroundColor: cGold, borderRadius: 4, maxBarThickness: 34 },
        { label: 'Komentar', data: postingan.map(function (p) { return p.komentar; }), backgroundColor: cGreen, borderRadius: 4, maxBarThickness: 34 },
        { label: 'Share', data: postingan.map(function (p) { return p.share; }), backgroundColor: cAmber, borderRadius: 4, maxBarThickness: 34 }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { color: cText, boxWidth: 10, padding: 12 } } },
      scales: {
        x: { grid: { display: false }, ticks: { color: cText, font: { size: 10 }, maxRotation: 0, autoSkip: false } },
        y: { min: 0, grid: { color: cBorder }, ticks: { color: cText, precision: 0 } }
      }
    }
  });
})();
</script>

@include('siberad.dashboards.partials.dash-script')
</body>
</html>