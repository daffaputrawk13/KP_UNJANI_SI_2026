<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Binmat — SIBERAD</title>
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
      <div class="name">Binmat</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="inventaris"><span class="dot"></span>Data Inventaris</a>
      <a href="#" class="side-link" data-tab-link="pengadaan"><span class="dot"></span>Permintaan Pengadaan</a>
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
          <div class="topbar-sub">Binmat (Pembinaan Materiil) &middot; Material/perlengkapan satuan</div>
        </div>
      </div>
      <span class="badge">Direktorat</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran Binmat. Sesuai rapat KP Pussiberad, tugas satuan ini adalah mengurus <b>material/perlengkapan</b> seluruh satuan Pussiberad.</div>

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Materiil</h2>
          <p>Status inventaris dan pengadaan material yang ditangani Binmat.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Item</div>
            <div class="val">{{ $stats['total_item'] }}</div>
            <div class="sub">Seluruh kategori</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Permintaan Pending</div>
            <div class="val" style="color:var(--amber);">{{ $stats['permintaan_pending'] }}</div>
            <div class="sub">Menunggu persetujuan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Kondisi Kritis</div>
            <div class="val" style="color:var(--red);">{{ $stats['kondisi_kritis'] }}</div>
            <div class="sub">Butuh tindakan segera</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Pengadaan Selesai</div>
            <div class="val" style="color:var(--green);">{{ $stats['pengadaan_selesai_bulan_ini'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Kondisi material yang baru dilaporkan atau diperbarui.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Item</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $i)
                <tr>
                  <td>{{ $i['item'] }}</td>
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

      {{-- ===== DATA INVENTARIS ===== --}}
      <section class="tab-panel" data-tab-panel="inventaris">
        <div class="section-head">
          <h2>Data Inventaris / Material</h2>
          <p>Daftar material dan perlengkapan yang dikelola beserta kondisi terkininya.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Item</th><th>Kategori</th><th>Jumlah</th><th>Kondisi</th><th>Update Terakhir</th><th>Aksi</th></tr></thead>
              <tbody>
                @foreach($inventaris as $inv)
                <tr>
                  <td>{{ $inv['nama'] }}</td>
                  <td style="color:var(--text-muted);">{{ $inv['kategori'] }}</td>
                  <td style="font-family:var(--mono);">{{ $inv['jumlah'] }}</td>
                  <td><span class="status-dot {{ $inv['kondisi_class'] }}">{{ $inv['kondisi'] }}</span></td>
                  <td>{{ $inv['update'] }}</td>
                  <td>
                    <div class="btn-row">
                      @if($inv['kondisi_class'] === 'bad')
                        <button class="btn btn-primary btn-sm" type="button">Ajukan Perbaikan</button>
                      @else
                        <button class="btn btn-sm" type="button">Lihat Detail</button>
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

      {{-- ===== PERMINTAAN PENGADAAN ===== --}}
      <section class="tab-panel" data-tab-panel="pengadaan">
        <div class="section-head">
          <h2>Permintaan Pengadaan</h2>
          <p>Daftar permintaan pengadaan material dari satuan-satuan Pussiberad.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Item</th><th>Diajukan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($permintaanPengadaan as $pg)
                <tr>
                  <td>{{ $pg['item'] }}</td>
                  <td>{{ $pg['diajukan_oleh'] }}</td>
                  <td>{{ $pg['tanggal'] }}</td>
                  <td><span class="status-dot {{ $pg['prioritas_class'] }}">{{ $pg['prioritas'] }}</span></td>
                  <td><span class="badge {{ $pg['status_class'] }}">{{ $pg['status'] }}</span></td>
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
            <h2>Buat Laporan Material</h2>
            <p>Form untuk Piket melaporkan kondisi material atau mengajukan pengadaan ke Komandan Binmat.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="itemLapor">Item Terkait</label>
                <select id="itemLapor">
                  @foreach($inventaris as $inv)
                    <option>{{ $inv['nama'] }}</option>
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
                <input id="perihalLapor" type="text" placeholder="Contoh: Kerusakan hardware pada server">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Deskripsi</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan kondisi material dan kebutuhan penanganan..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (foto / dokumentasi)</label>
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
            <p>Laporan kondisi material yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Item</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['item'] }}</td>
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
