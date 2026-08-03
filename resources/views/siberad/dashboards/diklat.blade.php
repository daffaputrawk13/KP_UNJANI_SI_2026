<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Diklat — SIBERAD</title>
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
      <div class="name">Diklat</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="program"><span class="dot"></span>Program Diklat</a>
      <a href="#" class="side-link" data-tab-link="jadwal"><span class="dot"></span>Jadwal Latihan</a>
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
          <div class="topbar-sub">Diklat (Pendidikan & Latihan) &middot; Pendidikan dan latihan-latihan satuan</div>
        </div>
      </div>
      <div class="topbar-actions">
        <button type="button" class="btn-icon-toggle" id="themeToggleBtn" aria-pressed="false" aria-label="Ganti tema">
          <svg class="icon-moon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"></path></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4.2"></circle><path d="M12 2.5v2.4M12 19.1v2.4M4.4 4.4l1.7 1.7M17.9 17.9l1.7 1.7M2.5 12h2.4M19.1 12h2.4M4.4 19.6l1.7-1.7M17.9 6.1l1.7-1.7"></path></svg>
        </button>
      <span class="badge">Direktorat</span>
      </div>
    </div>

    <div class="content">

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Pendidikan & Latihan</h2>
          <p>Status program diklat yang berjalan saat ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Program Aktif</div>
            <div class="val">{{ $stats['program_aktif'] }}</div>
            <div class="sub">Sedang berjalan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Total Peserta</div>
            <div class="val" style="color:var(--green);">{{ $stats['total_peserta'] }}</div>
            <div class="sub">Seluruh program</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Latihan Terjadwal</div>
            <div class="val" style="color:var(--amber);">{{ $stats['latihan_terjadwal'] }}</div>
            <div class="sub">Belum berlangsung</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Lulus Bulan Ini</div>
            <div class="val" style="color:var(--green);">{{ $stats['lulus_bulan_ini'] }}</div>
            <div class="sub">Personel bersertifikat</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Kegiatan pendidikan & latihan yang baru berlangsung.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Program</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $i)
                <tr>
                  <td>{{ $i['program'] }}</td>
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

      {{-- ===== PROGRAM DIKLAT ===== --}}
      <section class="tab-panel" data-tab-panel="program">
        <div class="section-head">
          <h2>Program Pendidikan & Latihan</h2>
          <p>Daftar seluruh program diklat beserta progres dan jumlah peserta.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Program</th><th>Kategori</th><th>Peserta</th><th>Progres</th><th>Status</th><th>Target Selesai</th></tr></thead>
              <tbody>
                @foreach($programDiklat as $p)
                <tr>
                  <td>{{ $p['nama'] }}</td>
                  <td style="color:var(--text-muted);">{{ $p['kategori'] }}</td>
                  <td style="font-family:var(--mono);">{{ $p['peserta'] }}</td>
                  <td style="font-family:var(--mono);">{{ $p['progres'] }}%</td>
                  <td><span class="status-dot {{ $p['status_class'] }}">{{ $p['status'] }}</span></td>
                  <td>{{ $p['selesai'] }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== JADWAL LATIHAN ===== --}}
      <section class="tab-panel" data-tab-panel="jadwal">
        <div class="section-head">
          <h2>Jadwal Latihan</h2>
          <p>Rencana dan jadwal latihan yang akan atau sedang berlangsung.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Latihan</th><th>Satuan Terlibat</th><th>Lokasi</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($jadwalLatihan as $j)
                <tr>
                  <td>{{ $j['nama'] }}</td>
                  <td style="color:var(--text-muted);">{{ $j['satuan_terlibat'] }}</td>
                  <td>{{ $j['lokasi'] }}</td>
                  <td>{{ $j['tanggal'] }}</td>
                  <td><span class="badge {{ $j['status_class'] }}">{{ $j['status'] }}</span></td>
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
            <p>Form untuk Piket melaporkan kendala atau mengajukan kebutuhan program ke Komandan Diklat.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="programLapor">Program Terkait</label>
                <select id="programLapor">
                  @foreach($programDiklat as $p)
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Permintaan tambahan modul praktik lab">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kendala atau kebutuhan terkait program diklat..."></textarea>
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
            <p>Laporan atau pengajuan yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Program</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['program'] }}</td>
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