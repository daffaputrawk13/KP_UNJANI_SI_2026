<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlakal (Penangkalan) — SIBERAD</title>
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
      <a href="#" class="side-link" data-tab-link="monitoring"><span class="dot"></span>Monitoring Aset</a>
      <a href="#" class="side-link" data-tab-link="insiden"><span class="dot"></span>Log Insiden</a>
      @if(($user->jabatan ?? '') === 'Piket')
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Buat Laporan</a>
      @else
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
      @endif
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
          <h2>Ringkasan Pemantauan</h2>
          <p>Status aset/website yang dipantau Satlakal (Penangkalan) hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Aset Dipantau</div>
            <div class="val">{{ $stats['total_aset'] }}</div>
            <div class="sub">Website & layanan digital</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Status Normal</div>
            <div class="val" style="color:var(--green);">{{ $stats['normal'] }}</div>
            <div class="sub">Berjalan baik</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Sedang Diserang</div>
            <div class="val" style="color:var(--red);">{{ $stats['diserang'] }}</div>
            <div class="sub">Butuh penanganan segera</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Dalam Pemulihan</div>
            <div class="val" style="color:var(--amber);">{{ $stats['pemulihan'] }}</div>
            <div class="sub">Sedang ditangani</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Insiden Terbaru</h3><p>Serangan atau gangguan yang baru terdeteksi.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Gangguan</th><th>Terdeteksi</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($insidenTerbaru as $i)
                <tr>
                  <td>{{ $i['aset'] }}</td>
                  <td>{{ $i['jenis'] }}</td>
                  <td>{{ $i['waktu'] }}</td>
                  <td><span class="status-dot {{ $i['status_class'] }}">{{ $i['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== MONITORING ASET ===== --}}
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
                @foreach($asetMonitoring as $a)
                <tr>
                  <td>{{ $a['nama'] }}</td>
                  <td style="font-family:var(--mono);font-size:12px;color:var(--text-muted);">{{ $a['url'] }}</td>
                  <td><span class="status-dot {{ $a['status_class'] }}">{{ $a['status'] }}</span></td>
                  <td>{{ $a['cek_terakhir'] }}</td>
                  <td>
                    <div class="btn-row">
                      @if($a['status_class'] === 'bad')
                        <button class="btn btn-primary btn-sm" type="button">Tandai Dipulihkan</button>
                      @else
                        <button class="btn btn-sm" type="button">Cek Ulang</button>
                      @endif
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LOG INSIDEN ===== --}}
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
                @foreach($logInsiden as $l)
                <tr>
                  <td>{{ $l['aset'] }}</td>
                  <td>{{ $l['jenis'] }}</td>
                  <td>{{ $l['waktu'] }}</td>
                  <td>{{ $l['tindakan'] }}</td>
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
            <h2>Buat Laporan Insiden</h2>
            <p>Form untuk Piket melaporkan insiden ke Komandan Satlakal (Penangkalan).</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="asetLapor">Aset / Website Terdampak</label>
                <select id="asetLapor">
                  @foreach($asetMonitoring as $a)
                    <option>{{ $a['nama'] }}</option>
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
        @else
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Laporan insiden yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Aset</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['aset'] }}</td>
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