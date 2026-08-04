<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlak Penindakan — SIBERAD</title>
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
      <a href="#" class="side-link" data-tab-link="ancaman"><span class="dot"></span>Deteksi Ancaman</a>
      <a href="#" class="side-link" data-tab-link="penanganan"><span class="dot"></span>Log Penanganan</a>
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
          <h2>Ringkasan Penanganan Ancaman</h2>
          <p>Status ancaman siber yang ditangani Satlak Penindakan hari ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Ancaman Aktif</div>
            <div class="val" style="color:var(--red);">{{ $stats['ancaman_aktif'] }}</div>
            <div class="sub">Sedang ditangani</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Ransomware</div>
            <div class="val" style="color:var(--red);">{{ $stats['ransomware'] }}</div>
            <div class="sub">Kasus berjalan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Malware Dikarantina</div>
            <div class="val" style="color:var(--amber);">{{ $stats['malware_dikarantina'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Insiden Selesai</div>
            <div class="val" style="color:var(--green);">{{ $stats['insiden_selesai_bulan_ini'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Insiden Terbaru</h3><p>Ancaman siber yang baru terdeteksi dan sedang ditangani.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Aset</th><th>Jenis Ancaman</th><th>Terdeteksi</th><th>Status</th></tr></thead>
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

      {{-- ===== DETEKSI ANCAMAN ===== --}}
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
                @foreach($ancamanTerdeteksi as $a)
                <tr>
                  <td>{{ $a['nama'] }}</td>
                  <td style="color:var(--text-muted);">{{ $a['jenis'] }}</td>
                  <td><span class="status-dot {{ $a['tingkat_class'] }}">{{ $a['tingkat'] }}</span></td>
                  <td>{{ $a['terdeteksi'] }}</td>
                  <td>
                    <div class="btn-row">
                      @if($a['tingkat_class'] === 'bad')
                        <button class="btn btn-primary btn-sm" type="button">Isolasi & Tangani</button>
                      @else
                        <button class="btn btn-sm" type="button">Investigasi</button>
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

      {{-- ===== LOG PENANGANAN ===== --}}
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
                @foreach($logPenanganan as $l)
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
            <p>Form untuk Piket melaporkan insiden ke Komandan Satlak Penindakan.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="asetLapor">Aset Terdampak</label>
                <select id="asetLapor">
                  @foreach($ancamanTerdeteksi as $a)
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Ransomware mengenkripsi file server">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi Kejadian</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kronologi, indikator kompromi, dan dampak insiden..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (log / bukti forensik)</label>
                <input id="lampiranLapor" type="file" accept=".pdf">
                <span class="form-hint">Format PDF, maksimal 20 MB, dikirim langsung ke DANPUS.</span>
              </div>
              <div class="form-field full">
                <button class="btn btn-primary" type="submit">Kirim Laporan ke Komandan</button>
              </div>
            </form>
          </div>
        @else
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Laporan insiden yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan langsung ke DANPUS.</p>
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
                        <button class="btn btn-primary btn-sm" type="button">Verifikasi & Teruskan ke DANPUS</button>
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
@include('siberad.dashboards.partials.dash-script')
</body>
</html>