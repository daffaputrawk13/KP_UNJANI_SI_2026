<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Binfung — SIBERAD</title>
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
      <div class="name">Binfung</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="penempatan"><span class="dot"></span>Data Penempatan</a>
      <a href="#" class="side-link" data-tab-link="riwayat"><span class="dot"></span>Riwayat Penempatan</a>
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
          <div class="topbar-sub">Binfung (Pembinaan Fungsi) &middot; Penempatan personel yang masuk</div>
        </div>
      </div>
      <span class="badge">Direktorat</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran Binfung. Sesuai rapat KP Pussiberad, tugas satuan ini adalah mengurus <b>penempatan personel</b> yang baru masuk ke satuan-satuan Pussiberad.</div>

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Penempatan Personel</h2>
          <p>Status penempatan personel yang ditangani Binfung bulan ini.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Personel Masuk</div>
            <div class="val">{{ $stats['personel_masuk_bulan_ini'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Menunggu SK</div>
            <div class="val" style="color:var(--amber);">{{ $stats['menunggu_sk'] }}</div>
            <div class="sub">Perlu ditindaklanjuti</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Terisi</div>
            <div class="val" style="color:var(--green);">{{ $stats['satuan_terisi'] }}</div>
            <div class="sub">Dari seluruh satuan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Total Personel</div>
            <div class="val">{{ $stats['total_personel'] }}</div>
            <div class="sub">Terdata di sistem</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><div><h3>Aktivitas Terbaru</h3><p>Proses penempatan personel yang baru berlangsung.</p></div></div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama</th><th>Kegiatan</th><th>Waktu</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $i)
                <tr>
                  <td>{{ $i['nama'] }}</td>
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

      {{-- ===== DATA PENEMPATAN ===== --}}
      <section class="tab-panel" data-tab-panel="penempatan">
        <div class="section-head">
          <h2>Data Penempatan Personel</h2>
          <p>Daftar personel yang sedang diproses penempatannya ke satuan tujuan.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Personel</th><th>Satuan Tujuan</th><th>Jabatan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                @foreach($penempatan as $p)
                <tr>
                  <td>{{ $p['nama'] }}</td>
                  <td>{{ $p['satuan_tujuan'] }}</td>
                  <td style="color:var(--text-muted);">{{ $p['jabatan'] }}</td>
                  <td>{{ $p['tanggal'] }}</td>
                  <td><span class="status-dot {{ $p['status_class'] }}">{{ $p['status'] }}</span></td>
                  <td>
                    <div class="btn-row">
                      @if($p['status_class'] === 'warn')
                        <button class="btn btn-primary btn-sm" type="button">Terbitkan SK</button>
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

      {{-- ===== RIWAYAT PENEMPATAN ===== --}}
      <section class="tab-panel" data-tab-panel="riwayat">
        <div class="section-head">
          <h2>Riwayat Penempatan</h2>
          <p>Riwayat lengkap personel yang sudah selesai ditempatkan ke satuan.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Nama Personel</th><th>Satuan Tujuan</th><th>Jabatan</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($riwayatPenempatan as $r)
                <tr>
                  <td>{{ $r['nama'] }}</td>
                  <td>{{ $r['satuan_tujuan'] }}</td>
                  <td style="color:var(--text-muted);">{{ $r['jabatan'] }}</td>
                  <td>{{ $r['tanggal'] }}</td>
                  <td><span class="badge {{ $r['status_class'] }}">{{ $r['status'] }}</span></td>
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
            <h2>Buat Laporan Penempatan</h2>
            <p>Form untuk Piket mengajukan penempatan personel baru ke Komandan Binfung.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="satuanLapor">Satuan Tujuan</label>
                <select id="satuanLapor">
                  @foreach($penempatan as $p)
                    <option>{{ $p['satuan_tujuan'] }}</option>
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
                <label for="namaLapor">Nama Personel</label>
                <input id="namaLapor" type="text" placeholder="Contoh: Serda Ahmad Fauzi">
              </div>
              <div class="form-field full">
                <label for="deskripsiLapor">Keterangan</label>
                <textarea id="deskripsiLapor" rows="4" placeholder="Jelaskan jabatan yang diajukan, latar belakang, dan pertimbangan penempatan..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLapor">Lampiran (dokumen personel)</label>
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
            <p>Pengajuan penempatan personel yang dikirim Piket dan menunggu verifikasi Komandan sebelum diteruskan ke WADAN.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Nama</th><th>Perihal</th><th>Dilaporkan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['nama'] }}</td>
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
