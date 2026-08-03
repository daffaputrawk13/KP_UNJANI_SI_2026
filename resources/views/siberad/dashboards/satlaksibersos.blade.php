<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satlak Sibersos — SIBERAD</title>
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
      <div class="name">Satlak Sibersos</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="monitoring"><span class="dot"></span>Monitoring Medsos</a>
      <a href="#" class="side-link" data-tab-link="isu"><span class="dot"></span>Isu Terdeteksi</a>
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
          <div class="topbar-sub">Satlak Sibersos &middot; Pengelolaan & pemantauan media sosial di daerah</div>
        </div>
      </div>
      <span class="badge">Satlak</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran Satlak Sibersos. Sesuai rapat KP Pussiberad, satuan ini menangani <b>seluruh aktivitas media sosial di daerah</b>.</div>

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
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
            <div class="sub">Sudah dikirim ke Komandan</div>
          </div>
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

      {{-- ===== LAPOR / VERIFIKASI ===== --}}
      <section class="tab-panel" data-tab-panel="lapor">
        @if(($user->jabatan ?? '') === 'Piket')
          <div class="section-head">
            <h2>Buat Laporan Isu</h2>
            <p>Form untuk Piket melaporkan isu media sosial ke Komandan Satlak Sibersos.</p>
          </div>
          <div class="panel">
            <form class="form-grid" onsubmit="event.preventDefault(); alert('Prototype — form ini belum tersambung ke database.');">
              <div class="form-field">
                <label for="platformLapor">Platform</label>
                <select id="platformLapor">
                  <option>Facebook</option><option>Instagram</option><option>X (Twitter)</option><option>TikTok</option><option>YouTube</option>
                </select>
              </div>
              <div class="form-field">
                <label for="wilayahLapor">Wilayah</label>
                <input id="wilayahLapor" type="text" placeholder="Contoh: Kodim 0612/Bandung">
              </div>
              <div class="form-field">
                <label for="prioritasLaporSos">Prioritas</label>
                <select id="prioritasLaporSos">
                  <option>Tinggi</option><option>Sedang</option><option>Rendah</option>
                </select>
              </div>
              <div class="form-field">
                <label for="linkLapor">Tautan Konten (opsional)</label>
                <input id="linkLapor" type="url" placeholder="https://...">
              </div>
              <div class="form-field full">
                <label for="ringkasanLapor">Ringkasan Isu</label>
                <input id="ringkasanLapor" type="text" placeholder="Contoh: Hoaks rekrutmen mengatasnamakan TNI AD">
              </div>
              <div class="form-field full">
                <label for="deskripsiLaporSos">Deskripsi & Analisis</label>
                <textarea id="deskripsiLaporSos" rows="4" placeholder="Jelaskan konteks isu dan dampaknya..."></textarea>
              </div>
              <div class="form-field full">
                <label for="lampiranLaporSos">Lampiran (tangkapan layar)</label>
                <input id="lampiranLaporSos" type="file" accept=".pdf,.jpg,.png">
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
            <p>Laporan isu media sosial yang dikirim Piket dan menunggu verifikasi Komandan.</p>
          </div>
          <div class="panel">
            <div class="tbl-wrap">
              <table class="dtbl">
                <thead><tr><th>Platform</th><th>Wilayah</th><th>Ringkasan</th><th>Dilaporkan Oleh</th><th>Prioritas</th><th>Aksi</th></tr></thead>
                <tbody>
                  @foreach($laporanPiket as $l)
                  <tr>
                    <td>{{ $l['platform'] }}</td>
                    <td>{{ $l['wilayah'] }}</td>
                    <td>{{ $l['ringkasan'] }}</td>
                    <td>{{ $l['pelapor'] }}</td>
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
