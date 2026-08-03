<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlok Duktek (Dukungan Teknologi) — SIBERAD</title>
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
      <a href="#" class="side-link" data-tab-link="proyek"><span class="dot"></span>Proyek Riset</a>
      <a href="#" class="side-link" data-tab-link="uji"><span class="dot"></span>Log Uji & Pengembangan</a>
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
          <h2>Ringkasan Riset & Pengembangan</h2>
          <p>Status proyek teknologi yang dikerjakan Satlok Duktek (Dukungan Teknologi) saat ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Proyek Aktif</div>
            <div class="val">{{ $stats['proyek_aktif'] }}</div>
            <div class="sub">Sedang dikerjakan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Proyek AI</div>
            <div class="val" style="color:var(--green);">{{ $stats['proyek_ai'] }}</div>
            <div class="sub">Machine learning & NLP</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Unit Drone Diuji</div>
            <div class="val" style="color:var(--amber);">{{ $stats['unit_drone_uji'] }}</div>
            <div class="sub">Tahap uji lapangan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Prototipe Selesai</div>
            <div class="val" style="color:var(--green);">{{ $stats['prototipe_selesai'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Kegiatan riset & pengembangan yang baru berlangsung.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Proyek</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $i)
                <tr>
                  <td>{{ $i['proyek'] }}</td>
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

      {{-- ===== PROYEK RISET ===== --}}
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
                @foreach($proyekRiset as $p)
                <tr>
                  <td>{{ $p['nama'] }}</td>
                  <td style="color:var(--text-muted);">{{ $p['kategori'] }}</td>
                  <td style="font-family:var(--mono);">{{ $p['progres'] }}%</td>
                  <td><span class="status-dot {{ $p['status_class'] }}">{{ $p['status'] }}</span></td>
                  <td>{{ $p['target'] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LOG UJI & PENGEMBANGAN ===== --}}
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
                @foreach($logUji as $l)
                <tr>
                  <td>{{ $l['proyek'] }}</td>
                  <td>{{ $l['kegiatan'] }}</td>
                  <td>{{ $l['waktu'] }}</td>
                  <td>{{ $l['hasil'] }}</td>
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
            <h2>Buat Laporan / Pengajuan</h2>
            <p>Form untuk Piket melaporkan kendala atau mengajukan kebutuhan proyek ke Komandan Satlok Duktek (Dukungan Teknologi).</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="proyekLapor">Proyek Terkait</label>
                <select id="proyekLapor">
                  @foreach($proyekRiset as $p)
                    <option>{{ $p['nama'] }}</option>
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Pengajuan anggaran komponen drone">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kebutuhan, kendala, atau hasil uji terkait..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (dokumentasi / data uji)</label>
                <input id="lampiranLapor" type="file" accept=".pdf,.jpg,.png,.zip">
                <span class="form-hint">Format PDF/JPG/PNG/ZIP, maksimal 25 MB sesuai ketentuan rapat.</span>
              </div>
              <div class="form-field full">
                <button class="btn btn-primary" type="submit">Kirim ke Komandan</button>
              </div>
            </form>
          </div>
        @else
          <div class="section-head">
            <h2>Verifikasi Laporan dari Piket</h2>
            <p>Laporan atau pengajuan yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Proyek</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['proyek'] }}</td>
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