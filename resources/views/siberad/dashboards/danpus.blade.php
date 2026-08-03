<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DANPUS — SIBERAD</title>
<link rel="icon" type="image/jpeg" href="{{ asset('images/logo-pussiberad.jpg') }}">
@include('siberad.dashboards.partials.dash-styles')
</head>
<body>
@php
  $laporanBySatuan = collect($laporanMasuk ?? [])->groupBy('satuan');
@endphp
<div class="shell">

  {{-- ===== SIDEBAR ===== --}}
  <aside class="sidebar" id="sidebar">
    <div class="side-brand">
      <img src="{{ asset('images/logo-pussiberad.jpg') }}" alt="Lambang Pussiberad">
      <div class="logo">SIBER<span>AD</span></div>
    </div>
    <nav class="side-nav">
      <div class="side-nav-label">Menu</div>
      <a href="#" class="side-link active" data-tab-link="ringkasan"><span class="dot"></span>Ringkasan</a>
      <a href="#" class="side-link" data-tab-link="laporan"><span class="dot"></span>Laporan Masuk</a>
      <a href="#" class="side-link" data-tab-link="status-satuan"><span class="dot"></span>Status Seluruh Satuan</a>
    </nav>

    <div class="side-foot">
      <form class="logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Keluar</button>
      </form>
    </div>
  </aside>

  {{-- ===== MAIN ===== --}}
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
          <h2>Ringkasan Organisasi</h2>
          <p>Kondisi seluruh satuan Pussiberad secara garis besar, hari ini.</p>
        </div>

        <div class="stat-grid">
          <div class="stat-card">
            <div class="lbl">Total Satuan</div>
            <div class="val">{{ $stats['total_satuan'] }}</div>
            <div class="sub">4 Satlak &middot; 4 Direktorat &middot; 2 Pimpinan</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Insiden Aktif</div>
            <div class="val" style="color:var(--red);">{{ $stats['insiden_aktif'] }}</div>
            <div class="sub">Ditangani Satlakal (Penangkalan)</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Laporan Menunggu Persetujuan</div>
            <div class="val" style="color:var(--amber);">{{ $stats['laporan_pending'] }}</div>
            <div class="sub">Diteruskan dari WADAN</div>
          </div>
          <div class="stat-card">
            <div class="lbl">Satuan Status Siaga Hijau</div>
            <div class="val" style="color:var(--green);">{{ $stats['siaga_hijau'] }}/{{ $stats['total_satuan'] }}</div>
            <div class="sub">Kondisi normal</div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head">
            <div>
              <h3>Laporan Prioritas Tinggi</h3>
              <p>Ringkasan laporan yang butuh perhatian DANPUS segera.</p>
            </div>
          </div>
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Prioritas</th><th>Tanggal</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($laporanPrioritas as $l)
                <tr>
                  <td>{{ $l['satuan'] }}</td>
                  <td>{{ $l['perihal'] }}</td>
                  <td><span class="status-dot {{ $l['prioritas_class'] }}">{{ $l['prioritas'] }}</span></td>
                  <td>{{ $l['tanggal'] }}</td>
                  <td><span class="badge {{ $l['status_class'] }}">{{ $l['status'] }}</span></td>
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
          <h2>Laporan Masuk dari WADAN</h2>
          <p>Laporan yang sudah diverifikasi WADAN dan menunggu persetujuan akhir DANPUS.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap">
            <table class="dtbl">
              <thead><tr><th>Asal Satuan</th><th>Perihal</th><th>Diteruskan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Status</th>
              @if(($user->jabatan ?? '') === 'Komandan')<th>Aksi</th>@endif
              </tr></thead>
              <tbody>
                @foreach($laporanMasuk as $i => $l)
                <tr id="rowLaporan{{ $i }}">
                  <td>{{ $l['satuan'] }}</td>
                  <td>{{ $l['perihal'] }}</td>
                  <td>{{ $l['diteruskan_oleh'] }}</td>
                  <td>{{ $l['tanggal'] }}</td>
                  <td><span class="status-dot {{ $l['prioritas_class'] }}">{{ $l['prioritas'] }}</span></td>
                  <td id="statusLaporan{{ $i }}"><span class="badge {{ $l['status_class'] }}">{{ $l['status'] }}</span></td>
                  @if(($user->jabatan ?? '') === 'Komandan')
                  <td id="aksiLaporan{{ $i }}">
                    <div class="btn-row">
                      <button class="btn btn-primary btn-sm" type="button" onclick="bukaKonfirmasiLaporan({{ $i }}, 'setuju', '{{ addslashes($l['satuan']) }}', '{{ addslashes($l['perihal']) }}')">Setujui</button>
                      <button class="btn btn-ghost-red btn-sm" type="button" onclick="bukaKonfirmasiLaporan({{ $i }}, 'tolak', '{{ addslashes($l['satuan']) }}', '{{ addslashes($l['perihal']) }}')">Tolak</button>
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

      {{-- ===== STATUS SELURUH SATUAN ===== --}}
      <section class="tab-panel" data-tab-panel="status-satuan">
        <div class="section-head">
          <h2>Status Seluruh Satuan</h2>
          <p>Pemantauan kondisi setiap Satlak dan Direktorat di bawah Pussiberad.</p>
        </div>
        <div class="panel">
          <div class="tbl-wrap" data-row-limit="5">
            <table class="dtbl">
              <thead><tr><th>Kode</th><th>Nama Satuan</th><th>Kategori</th><th>Status</th><th>Update Terakhir</th><th>Detail</th></tr></thead>
              <tbody>
                @foreach($semuaSatuan as $s)
                <tr>
                  <td><span class="badge">{{ $s->kode }}</span></td>
                  <td>{{ $s->nama }}</td>
                  <td style="text-transform:capitalize;">{{ $s->kategori }}</td>
                  <td><span class="status-dot {{ $statusSatuan[$s->kode]['class'] ?? 'ok' }}">{{ $statusSatuan[$s->kode]['label'] ?? 'Normal' }}</span></td>
                  <td>{{ $statusSatuan[$s->kode]['update'] ?? '-' }}</td>
                  <td>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="bukaDetailSatuan('{{ $s->nama }}', '{{ $s->kode }}', '{{ $s->kategori }}', '{{ $statusSatuan[$s->kode]['label'] ?? 'Normal' }}', '{{ $statusSatuan[$s->kode]['class'] ?? 'ok' }}')">Lihat Detail</button>
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

  {{-- ===== MODAL KONFIRMASI SETUJUI / TOLAK ===== --}}
  <div class="modal-overlay" id="modalKonfirmasiLaporan">
    <div class="modal-box" style="max-width:480px;">
      <div class="modal-head">
        <div>
          <h3 id="konfirmasiJudul">Konfirmasi</h3>
          <p id="konfirmasiSub" style="margin:2px 0 0;font-size:12.5px;color:var(--text-muted);">-</p>
        </div>
        <button type="button" class="modal-close" onclick="tutupKonfirmasiLaporan()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-field full" style="margin-bottom:16px;">
          <label for="konfirmasiCatatan">Catatan (opsional)</label>
          <textarea id="konfirmasiCatatan" rows="3" placeholder="Tulis catatan terkait keputusan ini..."></textarea>
        </div>
        <div class="btn-row" style="justify-content:flex-end;">
          <button type="button" class="btn" onclick="tutupKonfirmasiLaporan()">Batal</button>
          <button type="button" class="btn btn-primary" id="konfirmasiBtnAksi" onclick="konfirmasiLaporanSubmit()">Konfirmasi</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let laporanAktif = null;

    function bukaKonfirmasiLaporan(index, aksi, satuan, perihal){
      laporanAktif = { index, aksi };
      const judul = aksi === 'setuju' ? 'Setujui Laporan' : 'Tolak Laporan';
      document.getElementById('konfirmasiJudul').textContent = judul;
      document.getElementById('konfirmasiSub').textContent = satuan + ' \u2014 ' + perihal;
      document.getElementById('konfirmasiCatatan').value = '';

      const btnAksi = document.getElementById('konfirmasiBtnAksi');
      btnAksi.textContent = aksi === 'setuju' ? 'Ya, Setujui' : 'Ya, Tolak';
      btnAksi.className = aksi === 'setuju' ? 'btn btn-primary' : 'btn btn-ghost-red';

      document.getElementById('modalKonfirmasiLaporan').classList.add('open');
    }

    function tutupKonfirmasiLaporan(){
      document.getElementById('modalKonfirmasiLaporan').classList.remove('open');
      laporanAktif = null;
    }

    function konfirmasiLaporanSubmit(){
      if(!laporanAktif) return;
      const { index, aksi } = laporanAktif;

      const statusCell = document.getElementById('statusLaporan' + index);
      const aksiCell = document.getElementById('aksiLaporan' + index);

      if(aksi === 'setuju'){
        statusCell.innerHTML = '<span class="badge green">Disetujui</span>';
      } else {
        statusCell.innerHTML = '<span class="badge red">Ditolak</span>';
      }

      if(aksiCell){
        aksiCell.innerHTML = '<span style="font-size:11.5px;color:var(--text-dim);">Sudah diproses</span>';
      }

      // Catatan (jika ada) saat ini baru tersimpan sementara di sisi tampilan.
      // Kalau nanti mau disimpan permanen ke database, tinggal kirim nilai
      // document.getElementById('konfirmasiCatatan').value beserta index-nya ke route backend di sini.

      tutupKonfirmasiLaporan();
    }

    document.getElementById('modalKonfirmasiLaporan').addEventListener('click', function(e){
      if(e.target === this) tutupKonfirmasiLaporan();
    });
  </script>

  {{-- ===== MODAL DETAIL SATUAN (VIEW ONLY) ===== --}}
  <div class="modal-overlay" id="modalDetailSatuan">
    <div class="modal-box">
      <div class="modal-head">
        <div>
          <h3 id="modalSatuanNama">-</h3>
          <p id="modalSatuanSub" style="margin:2px 0 0;font-size:12.5px;color:var(--text-muted);">-</p>
        </div>
        <button type="button" class="modal-close" onclick="tutupDetailSatuan()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="tbl-wrap">
          <table class="dtbl">
            <thead><tr><th>Perihal</th><th>Diteruskan Oleh</th><th>Tanggal</th><th>Prioritas</th><th>Status</th></tr></thead>
            <tbody id="modalSatuanTbody">
              <tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Tidak ada data laporan.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <style>
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:200;align-items:center;justify-content:center;padding:20px;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--panel,#0f1a14);border:1px solid var(--border-strong,#2a3a30);border-radius:12px;max-width:720px;width:100%;max-height:80vh;display:flex;flex-direction:column;}
    .modal-head{display:flex;align-items:flex-start;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border-soft,#22302a);}
    .modal-head h3{margin:0;font-size:16px;}
    .modal-close{background:none;border:none;color:var(--text-muted,#9fb0a8);font-size:22px;line-height:1;cursor:pointer;}
    .modal-close:hover{color:var(--gold-bright,#f2c14e);}
    .modal-body{padding:16px 20px 20px;overflow-y:auto;}
  </style>

  <script>
    const laporanBySatuan = @json($laporanBySatuan);

    function bukaDetailSatuan(nama, kode, kategori, statusLabel, statusClass){
      document.getElementById('modalSatuanNama').textContent = nama;
      document.getElementById('modalSatuanSub').textContent = kode + ' \u00b7 ' + kategori + ' \u00b7 Status: ' + statusLabel;

      const tbody = document.getElementById('modalSatuanTbody');
      const data = laporanBySatuan[nama] || [];

      if(data.length === 0){
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:var(--text-muted);">Tidak ada data laporan.</td></tr>';
      } else {
        tbody.innerHTML = data.map(l => `
          <tr>
            <td>${l.perihal ?? '-'}</td>
            <td>${l.diteruskan_oleh ?? '-'}</td>
            <td>${l.tanggal ?? '-'}</td>
            <td><span class="status-dot ${l.prioritas_class ?? ''}">${l.prioritas ?? '-'}</span></td>
            <td><span class="badge ${l.status_class ?? ''}">${l.status ?? '-'}</span></td>
          </tr>
        `).join('');
      }

      document.getElementById('modalDetailSatuan').classList.add('open');
    }

    function tutupDetailSatuan(){
      document.getElementById('modalDetailSatuan').classList.remove('open');
    }

    document.getElementById('modalDetailSatuan').addEventListener('click', function(e){
      if(e.target === this) tutupDetailSatuan();
    });
  </script>
</div>
@include('siberad.dashboards.partials.dash-script')
</body>
</html>