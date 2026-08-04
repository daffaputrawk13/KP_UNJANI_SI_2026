<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SDIR — SIBERAD</title>
<link rel="icon" type="image/jpeg" href="{{ asset('images/logo-pussiberad.jpg') }}">
@include('siberad.dashboards.partials.dash-styles')
</head>
<body>
<div class="shell">

  <aside class="sidebar" id="sidebar">
    <div class="side-brand">
      <img src="{{ asset('images/logo-pussiberad.jpg') }}" alt="Lambang Pussiberad">
      <div class="logo">SIBER<span>AD</span></div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="koordinasi"><span class="dot"></span>Koordinasi Satlak</a>
      <a href="#" class="side-link" data-tab-link="diteruskan"><span class="dot"></span>Laporan ke DANPUS</a>
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
    </nav>
    <div class="side-foot">
      <form class="logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Keluar</button>
      </form>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="menu-btn" id="menuBtn">☰</button>
        <div>
          <div class="topbar-title">Selamat datang, {{ $user->name }}</div>
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

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Koordinasi</h2>
          <p>Status koordinasi antar Satlak dan pelaporan yang ditangani SDIR saat ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Satlak Dikoordinasikan</div>
            <div class="val">{{ $stats['satlak_dikoordinasikan'] }}</div>
            <div class="sub">Satuan pelaksana</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Laporan Diteruskan</div>
            <div class="val" style="color:var(--green);">{{ $stats['laporan_diteruskan'] }}</div>
            <div class="sub">Ke WADAN/DANPUS</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Koordinasi Aktif</div>
            <div class="val" style="color:var(--amber);">{{ $stats['permintaan_koordinasi_aktif'] }}</div>
            <div class="sub">Sedang berjalan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Menunggu WADAN</div>
            <div class="val" style="color:var(--amber);">{{ $stats['menunggu_wadan'] }}</div>
            <div class="sub">Belum ditindaklanjuti</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Kegiatan koordinasi yang baru berlangsung.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Satuan</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $i)
                <tr>
                  <td>{{ $i['satuan'] }}</td>
                  <td>{{ $i['kegiatan'] }}</td>
                  <td>{{ $i['waktu'] }}</td>
                  <td><span class="status-dot {{ $i['status_class'] }}">{{ $i['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== KOORDINASI SATLAK ===== --}}
      <section class="tab-panel" data-tab-panel="koordinasi">
        <div class="section-head">
          <h2>Koordinasi Satlak</h2>
          <p>Daftar permintaan koordinasi antar Satlak yang sedang ditangani SDIR.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Satuan</th><th>Perihal</th><th>Jenis</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($koordinasiSatlak as $k)
                <tr>
                  <td>{{ $k['satuan'] }}</td>
                  <td>{{ $k['perihal'] }}</td>
                  <td style="color:var(--text-muted);">{{ $k['jenis'] }}</td>
                  <td>{{ $k['tanggal'] }}</td>
                  <td><span class="status-dot {{ $k['status_class'] }}">{{ $k['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LAPORAN DITERUSKAN ===== --}}
      <section class="tab-panel" data-tab-panel="diteruskan">
        <div class="section-head">
          <h2>Laporan Diteruskan ke DANPUS</h2>
          <p>Riwayat laporan dari Satlak yang diteruskan SDIR ke WADAN/DANPUS.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Satuan</th><th>Perihal</th><th>Tanggal</th><th>Diteruskan Ke</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($laporanDiteruskan as $l)
                <tr>
                  <td>{{ $l['satuan'] }}</td>
                  <td>{{ $l['perihal'] }}</td>
                  <td>{{ $l['tanggal'] }}</td>
                  <td style="color:var(--text-muted);">{{ $l['diteruskan_ke'] }}</td>
                  <td><span class="badge {{ $l['status_class'] }}">{{ $l['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LAPOR / VERIFIKASI ===== --}}
      <section class="tab-panel" data-tab-panel="lapor">
          <div class="section-head">
            <h2>Verifikasi &amp; Teruskan Laporan</h2>
            <p>Permintaan koordinasi yang menunggu diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Satuan</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['satuan'] }}</td>
                    <td>{{ $l['perihal'] }}</td>
                    <td>{{ $l['pelapor'] }}</td>
                    <td>{{ $l['tanggal'] }}</td>
                    <td><span class="status-dot {{ $l['prioritas_class'] }}">{{ $l['prioritas'] }}</span></td>
                    <td>
                      <div class="btn-row">
                        <button class="btn btn-primary btn-sm" type="button">Verifikasi & Teruskan</button>
                        <button class="btn btn-ghost-red btn-sm" type="button">Tolak</button>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
      </section>

    </div>
  </main>
</div>
@include('siberad.dashboards.partials.dash-script')
</body>
</html>