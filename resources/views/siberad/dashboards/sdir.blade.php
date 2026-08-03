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
    <div class="side-unit">
      <div class="eyebrow">Login sebagai</div>
      <div class="name">SDIR</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="koordinasi"><span class="dot"></span>Koordinasi Satlak</a>
      <a href="#" class="side-link" data-tab-link="diteruskan"><span class="dot"></span>Laporan ke DANPUS</a>
      @if(($user->jabatan ?? '') === 'Piket')
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Buat Laporan</a>
      @else
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
      @endif
    </nav>
    <div class="side-foot">
      <div class="side-user">
        <div class="side-avatar">{{ strtoupper(substr($user->name,0,2)) }}</div>
        <div>
          <div class="n">{{ $user->name }}</div>
          <div class="j">{{ $user->jabatan ?? '-' }}</div>
        </div>
      </div>
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
          <div class="topbar-sub">SDIR (Sekretaris Direktorat) &middot; Koordinasi antar Satlak, pelaporan ke DANPUS</div>
        </div>
      </div>
      <span class="badge">Pimpinan</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran SDIR. Sesuai rapat KP Pussiberad, tugas satuan ini adalah <b>berkoordinasi dengan Satlak</b> dan meneruskan laporan ke <b>DANPUS</b>.</div>

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
        @if(($user->jabatan ?? '') === 'Piket')
          <div class="section-head">
            <h2>Buat Laporan Koordinasi</h2>
            <p>Form untuk Piket mengajukan permintaan koordinasi ke Komandan SDIR.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="satuanLapor">Satuan Terkait</label>
                <select id="satuanLapor">
                  @foreach($koordinasiSatlak as $k)
                    <option>{{ $k['satuan'] }}</option>
                  @endforeach
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Permintaan koordinasi eskalasi insiden">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan konteks koordinasi dan satuan yang terlibat..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (dokumen pendukung)</label>
                <input id="lampiranLapor" type="file" accept=".pdf,.jpg,.png">
                <span class="form-hint">Format PDF/JPG/PNG, maksimal 25 MB sesuai ketentuan rapat.</span>
              </div>
              <div class="form-field full">
                <button class="btn btn-primary" type="submit">Kirim Laporan ke Komandan</button>
              </div>
            </form>
          </div>
        @else
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Permintaan koordinasi yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
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
        @endif
      </section>

    </div>
  </main>
</div>
@include('siberad.dashboards.partials.dash-script')
</body>
</html>
