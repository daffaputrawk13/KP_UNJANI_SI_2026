<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WADAN — SIBERAD</title>
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
      <div class="name">WADAN — Wakil Komandan</div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="laporan"><span class="dot"></span>Laporan Masuk</a>
      <a href="#" class="side-link" data-tab-link="koordinasi"><span class="dot"></span>Koordinasi Satlak</a>
      <a href="#" class="side-link" data-tab-link="diteruskan"><span class="dot"></span>Diteruskan ke DANPUS</a>
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
          <div class="topbar-sub">WADAN &middot; Penerima & verifikator laporan dari SDIR dan seluruh Satlak</div>
        </div>
      </div>
      <span class="badge">Pimpinan</span>
    </div>

    <div class="content">
      <div class="notice"><b>Catatan:</b> Halaman ini adalah prototype tampilan peran WADAN. Data di bawah masih contoh (mock) untuk keperluan demo alur koordinasi.</div>

      {{-- ===== RINGKASAN ===== --}}
      <section class="tab-panel active" data-tab-panel="ringkasan">
        <div class="section-head">
          <h2>Ringkasan Koordinasi</h2>
          <p>Status laporan yang masuk dari SDIR maupun langsung dari Satlak.</p>
        </div>
        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Menunggu Verifikasi</div>
            <div class="val" style="color:var(--amber);">{{ $stats['menunggu_verifikasi'] }}</div>
            <div class="sub">Perlu ditinjau WADAN</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Diteruskan ke DANPUS</div>
            <div class="val" style="color:var(--green-bright);">{{ $stats['diteruskan'] }}</div>
            <div class="sub">Bulan ini</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Aktif Melapor</div>
            <div class="val">{{ $stats['satuan_aktif'] }}/8</div>
            <div class="sub">Dari Satlak & Direktorat</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Permintaan Koordinasi</div>
            <div class="val">{{ $stats['permintaan_koordinasi'] }}</div>
            <div class="sub">Menunggu tindak lanjut</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div><h3>Aktivitas Terbaru</h3><p>Ringkasan laporan & koordinasi terbaru dari satuan pelaksana.</p></div>
          </div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal</th><th>Perihal</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($aktivitasTerbaru as $a)
                <tr>
                  <td>{{ $a['satuan'] }}</td>
                  <td>{{ $a['perihal'] }}</td>
                  <td>{{ $a['tanggal'] }}</td>
                  <td><span class="badge {{ $a['status_class'] }}">{{ $a['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== LAPORAN MASUK ===== --}}
      <section class="tab-panel" data-tab-panel="laporan">
        <div class="section-head">
          <h2>Laporan Masuk</h2>
          <p>Laporan dari SDIR dan seluruh Satlak yang perlu diverifikasi sebelum diteruskan ke DANPUS.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Tanggal</th><th>Prioritas</th><th>Status</th>
              @if(($user->jabatan ?? '') === 'Komandan')<th>Aksi</th>@endif
              </tr></thead>
              <tbody>
                @foreach($laporanMasuk as $l)
                <tr>
                  <td>{{ $l['satuan'] }}</td>
                  <td>{{ $l['perihal'] }}</td>
                  <td>{{ $l['tanggal'] }}</td>
                  <td><span class="status-dot {{ $l['prioritas_class'] }}">{{ $l['prioritas'] }}</span></td>
                  <td><span class="badge {{ $l['status_class'] }}">{{ $l['status'] }}</span></td>
                  @if(($user->jabatan ?? '') === 'Komandan')
                  <td>
                    <div class="btn-row">
                      <button class="btn btn-primary btn-sm" type="button">Teruskan ke DANPUS</button>
                      <button class="btn btn-sm" type="button">Kembalikan</button>
                    </div>
                  </td>
                  @endif
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
          <h2>Koordinasi dengan Satlak</h2>
          <p>Permintaan koordinasi antar satuan — misalnya permintaan pengiriman personel dari SDIR ke Satlak.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Satuan Tujuan</th><th>Perihal Koordinasi</th><th>Diminta Oleh</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($koordinasi as $k)
                <tr>
                  <td>{{ $k['satuan'] }}</td>
                  <td>{{ $k['perihal'] }}</td>
                  <td>{{ $k['diminta_oleh'] }}</td>
                  <td>{{ $k['tanggal'] }}</td>
                  <td><span class="badge {{ $k['status_class'] }}">{{ $k['status'] }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {{-- ===== DITERUSKAN KE DANPUS ===== --}}
      <section class="tab-panel" data-tab-panel="diteruskan">
        <div class="section-head">
          <h2>Riwayat Diteruskan ke DANPUS</h2>
          <p>Laporan yang sudah diverifikasi WADAN dan sudah dikirim ke DANPUS untuk persetujuan akhir.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Tanggal Diteruskan</th><th>Status di DANPUS</th></tr></thead>
              <tbody>
                @foreach($riwayatDiteruskan as $r)
                <tr>
                  <td>{{ $r['satuan'] }}</td>
                  <td>{{ $r['perihal'] }}</td>
                  <td>{{ $r['tanggal'] }}</td>
                  <td><span class="badge {{ $r['status_class'] }}">{{ $r['status'] }}</span></td>
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
