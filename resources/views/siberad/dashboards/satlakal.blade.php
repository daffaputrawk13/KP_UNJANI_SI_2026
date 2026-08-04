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
      @if(($user->jabatan ?? '') === 'Piket')
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Buat Laporan</a>
      @else
      <a href="#" class="side-link" data-tab-link="lapor"><span class="dot"></span>Verifikasi Laporan</a>
      @endif
      <a href="#" class="side-link" data-tab-link="danpus"><span class="dot"></span>Lapor ke DANPUS</a>
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

        <div class="profile-menu" id="profileMenu">
          <button type="button" class="profile-menu-btn" id="profileMenuBtn" aria-haspopup="menu" aria-expanded="false" aria-label="Menu profil">
            <span class="profile-initial" id="profileInitial">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
            <img class="profile-photo" id="profilePhotoBtn" alt="Foto profil {{ $user->name }}">
          </button>

          <div class="profile-dropdown" id="profileDropdown" role="menu" aria-label="Menu profil">

            {{-- ===== VIEW UTAMA ===== --}}
            <div class="profile-dropdown-view" id="profileMainView">
              <div class="profile-dropdown-head">
                <div class="profile-dropdown-avatar">
                  <span class="profile-initial" id="profileInitialDropdown">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                  <img class="profile-photo" id="profilePhotoDropdown" alt="Foto profil {{ $user->name }}">
                </div>
                <div>
                  <div class="profile-dropdown-name">{{ $user->name }}</div>
                  <div class="profile-dropdown-role">{{ $user->jabatan ?? 'Pengguna' }}</div>
                </div>
              </div>

              <button type="button" class="profile-dropdown-item" id="openProfilSayaBtn" role="menuitem">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"></circle><path d="M5 20c1.2-4 4.2-6 7-6s5.8 2 7 6"></path></svg>
                Profil Saya
              </button>
              <a href="#" class="profile-dropdown-item" role="menuitem" onclick="alert('Prototype — pengaturan akun belum tersambung.'); return false;">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 13.5a7.6 7.6 0 0 0 0-3l2-1.5-2-3.4-2.3.9a7.6 7.6 0 0 0-2.6-1.5L14 2.5h-4l-.5 2.5a7.6 7.6 0 0 0-2.6 1.5l-2.3-.9-2 3.4 2 1.5a7.6 7.6 0 0 0 0 3l-2 1.5 2 3.4 2.3-.9a7.6 7.6 0 0 0 2.6 1.5l.5 2.5h4l.5-2.5a7.6 7.6 0 0 0 2.6-1.5l2.3.9 2-3.4Z"></path></svg>
                Pengaturan Akun
              </a>
              <a href="#" class="profile-dropdown-item" role="menuitem" onclick="alert('Prototype — pusat bantuan belum tersambung.'); return false;">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"></circle><path d="M9.2 9.2a2.8 2.8 0 1 1 3.9 2.6c-.8.4-1.1 1-1.1 1.9"></path><path d="M12 17.2h.01"></path></svg>
                Bantuan &amp; Panduan
              </a>

              <div class="profile-dropdown-divider"></div>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="profile-dropdown-item danger" role="menuitem">
                  <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5"></path><path d="M21 12H9"></path></svg>
                  Keluar
                </button>
              </form>
            </div>

            {{-- ===== VIEW PROFIL SAYA ===== --}}
            <div class="profile-dropdown-view" id="profilePhotoView" style="display:none;">
              <button type="button" class="profile-dropdown-back" id="backToMainBtn">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"></path></svg>
                Profil Saya
              </button>

              <div class="profile-dropdown-head-lg">
                <div class="profile-dropdown-avatar-lg">
                  <span class="profile-initial" id="profileInitialLarge">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                  <img class="profile-photo" id="profilePhotoLarge" alt="Foto profil {{ $user->name }}">
                </div>
                <div class="profile-dropdown-name">{{ $user->name }}</div>
                <div class="profile-dropdown-role">{{ $user->jabatan ?? 'Pengguna' }}</div>
              </div>

              <button type="button" class="profile-dropdown-item" id="gantiFotoBtn" role="menuitem">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8.5A1.5 1.5 0 0 1 5.5 7h2l1-2h7l1 2h2A1.5 1.5 0 0 1 20 8.5v9A1.5 1.5 0 0 1 18.5 19h-13A1.5 1.5 0 0 1 4 17.5Z"></path><circle cx="12" cy="13" r="3.4"></circle></svg>
                <span id="gantiFotoLabel">Ganti Foto Profil</span>
              </button>
              <button type="button" class="profile-dropdown-item" id="hapusFotoBtn" role="menuitem" style="display:none;">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"></path><path d="M18 7l-.8 12.1a1.8 1.8 0 0 1-1.8 1.7H8.6a1.8 1.8 0 0 1-1.8-1.7L6 7"></path></svg>
                Hapus Foto Profil
              </button>
              <input type="file" id="fotoProfilInput" accept="image/png,image/jpeg,image/webp" hidden>
            </div>

          </div>
        </div>
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

      {{-- ===== LAPOR LANGSUNG KE DANPUS ===== --}}
      <section class="tab-panel" data-tab-panel="danpus">
        <div class="section-head">
          <h2>Lapor ke DANPUS</h2>
          <p>Laporan insiden yang dikirim langsung ke DANPUS, lengkap dengan lampiran bukti dalam format PDF.</p>
        </div>
        <div class="panel">
          <form class="form-grid" id="formDanpus" novalidate>
            <div class="form-field">
              <label for="asetDanpus">Aset / Website Terdampak</label>
              <select id="asetDanpus" required>
                @foreach($asetMonitoring as $a)
                  <option>{{ $a['nama'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label for="prioritasDanpus">Prioritas</label>
              <select id="prioritasDanpus" required>
                <option>Tinggi</option><option>Sedang</option><option>Rendah</option>
              </select>
            </div>
            <div class="form-field full">
              <label for="perihalDanpus">Perihal</label>
              <input id="perihalDanpus" type="text" placeholder="Contoh: Website diserang DDoS" required>
            </div>
            <div class="form-field full">
              <label for="deskripsiDanpus">Deskripsi Kejadian</label>
              <textarea id="deskripsiDanpus" rows="4" placeholder="Jelaskan kronologi dan dampak insiden..." required></textarea>
            </div>
            <div class="form-field full">
              <label for="lampiranDanpus">Lampiran (bukti / dokumentasi)</label>
              <input id="lampiranDanpus" type="file" accept="application/pdf,.pdf">
              <span class="form-hint">Format PDF, maksimal 20 MB, dikirim langsung ke DANPUS.</span>
              <span class="form-hint" id="lampiranDanpusInfo" style="display:none;"></span>
              <span class="form-hint" id="lampiranDanpusError" style="display:none;color:var(--red);"></span>
            </div>
            <div class="form-field full">
              <button class="btn btn-primary" type="submit">Kirim Laporan ke DANPUS</button>
            </div>
          </form>
        </div>
      </section>

      <script>
      (function () {
        var menuBtn = document.getElementById('profileMenuBtn');
        var dropdown = document.getElementById('profileDropdown');
        var wrapper = document.getElementById('profileMenu');
        var mainView = document.getElementById('profileMainView');
        var photoView = document.getElementById('profilePhotoView');
        var openProfilBtn = document.getElementById('openProfilSayaBtn');
        var backBtn = document.getElementById('backToMainBtn');
        if (!menuBtn || !dropdown || !wrapper) return;

        function showMainView() {
          if (mainView) mainView.style.display = 'block';
          if (photoView) photoView.style.display = 'none';
        }

        function showPhotoView() {
          if (mainView) mainView.style.display = 'none';
          if (photoView) photoView.style.display = 'block';
        }

        function closeMenu() {
          dropdown.classList.remove('open');
          menuBtn.classList.remove('open');
          menuBtn.setAttribute('aria-expanded', 'false');
          showMainView();
        }

        function openMenu() {
          dropdown.classList.add('open');
          menuBtn.classList.add('open');
          menuBtn.setAttribute('aria-expanded', 'true');
        }

        menuBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          if (dropdown.classList.contains('open')) {
            closeMenu();
          } else {
            openMenu();
          }
        });

        if (openProfilBtn) {
          openProfilBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            showPhotoView();
          });
        }

        if (backBtn) {
          backBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            showMainView();
          });
        }

        document.addEventListener('click', function (e) {
          if (!wrapper.contains(e.target)) closeMenu();
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') closeMenu();
        });
      })();
      </script>

      <script>
      (function () {
        var MAX_PHOTO_MB = 5;
        var MAX_PHOTO_BYTES = MAX_PHOTO_MB * 1024 * 1024;
        var ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
        var STORAGE_KEY = 'siberad-profile-photo-{{ $user->id ?? "default" }}';

        var fileInput = document.getElementById('fotoProfilInput');
        var gantiBtn = document.getElementById('gantiFotoBtn');
        var gantiLabel = document.getElementById('gantiFotoLabel');
        var hapusBtn = document.getElementById('hapusFotoBtn');

        var photoBtn = document.getElementById('profilePhotoBtn');
        var photoDropdown = document.getElementById('profilePhotoDropdown');
        var photoLarge = document.getElementById('profilePhotoLarge');
        var initialBtn = document.getElementById('profileInitial');
        var initialDropdown = document.getElementById('profileInitialDropdown');
        var initialLarge = document.getElementById('profileInitialLarge');

        if (!fileInput || !gantiBtn || !hapusBtn) return;

        function showPhoto(dataUrl) {
          photoBtn.src = dataUrl;
          photoDropdown.src = dataUrl;
          photoLarge.src = dataUrl;
          photoBtn.classList.add('visible');
          photoDropdown.classList.add('visible');
          photoLarge.classList.add('visible');
          initialBtn.classList.add('hidden');
          initialDropdown.classList.add('hidden');
          initialLarge.classList.add('hidden');
          hapusBtn.style.display = 'flex';
        }

        function clearPhoto() {
          photoBtn.classList.remove('visible');
          photoDropdown.classList.remove('visible');
          photoLarge.classList.remove('visible');
          photoBtn.removeAttribute('src');
          photoDropdown.removeAttribute('src');
          photoLarge.removeAttribute('src');
          initialBtn.classList.remove('hidden');
          initialDropdown.classList.remove('hidden');
          initialLarge.classList.remove('hidden');
          hapusBtn.style.display = 'none';
        }

        // Muat foto tersimpan (jika ada) saat halaman dibuka
        try {
          var saved = localStorage.getItem(STORAGE_KEY);
          if (saved) showPhoto(saved);
        } catch (e) {}

        gantiBtn.addEventListener('click', function () {
          fileInput.click();
        });

        hapusBtn.addEventListener('click', function () {
          clearPhoto();
          try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
        });

        fileInput.addEventListener('change', function () {
          var file = fileInput.files && fileInput.files[0];
          if (!file) return;

          if (ALLOWED_TYPES.indexOf(file.type) === -1) {
            alert('File "' + file.name + '" ditolak: hanya format JPG, PNG, atau WEBP yang diperbolehkan.');
            fileInput.value = '';
            return;
          }

          if (file.size > MAX_PHOTO_BYTES) {
            alert('File "' + file.name + '" (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB) melebihi batas maksimal ' + MAX_PHOTO_MB + ' MB.');
            fileInput.value = '';
            return;
          }

          gantiLabel.textContent = 'Memproses...';
          gantiBtn.setAttribute('disabled', 'disabled');

          var reader = new FileReader();
          reader.onload = function (e) {
            var dataUrl = e.target.result;
            showPhoto(dataUrl);
            try {
              localStorage.setItem(STORAGE_KEY, dataUrl);
            } catch (err) {
              alert('Foto berhasil ditampilkan, tetapi gagal disimpan secara lokal (kemungkinan ukuran terlalu besar untuk penyimpanan browser).');
            }
            gantiLabel.textContent = 'Ganti Foto Profil';
            gantiBtn.removeAttribute('disabled');
            fileInput.value = '';
          };
          reader.onerror = function () {
            alert('Gagal membaca file gambar. Silakan coba file lain.');
            gantiLabel.textContent = 'Ganti Foto Profil';
            gantiBtn.removeAttribute('disabled');
            fileInput.value = '';
          };
          reader.readAsDataURL(file);
        });
      })();
      </script>

      <script>
      (function () {
        var MAX_SIZE_MB = 20;
        var MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

        var form = document.getElementById('formDanpus');
        var fileInput = document.getElementById('lampiranDanpus');
        var infoEl = document.getElementById('lampiranDanpusInfo');
        var errorEl = document.getElementById('lampiranDanpusError');

        if (!form || !fileInput) return;

        function resetFileMessages() {
          infoEl.style.display = 'none';
          infoEl.textContent = '';
          errorEl.style.display = 'none';
          errorEl.textContent = '';
        }

        function formatSize(bytes) {
          return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function validateFile() {
          resetFileMessages();

          var file = fileInput.files && fileInput.files[0];
          if (!file) return null;

          var isPdfType = file.type === 'application/pdf';
          var isPdfExt = /\.pdf$/i.test(file.name);

          if (!isPdfType && !isPdfExt) {
            errorEl.textContent = 'File "' + file.name + '" ditolak: hanya format PDF yang diperbolehkan.';
            errorEl.style.display = 'block';
            fileInput.value = '';
            return null;
          }

          if (file.size > MAX_SIZE_BYTES) {
            errorEl.textContent = 'File "' + file.name + '" (' + formatSize(file.size) + ') melebihi batas maksimal ' + MAX_SIZE_MB + ' MB.';
            errorEl.style.display = 'block';
            fileInput.value = '';
            return null;
          }

          infoEl.textContent = 'Lampiran dipilih: ' + file.name + ' (' + formatSize(file.size) + ')';
          infoEl.style.display = 'block';
          return file;
        }

        fileInput.addEventListener('change', validateFile);

        form.addEventListener('submit', function (e) {
          e.preventDefault();

          if (!form.checkValidity()) {
            form.reportValidity();
            return;
          }

          var file = validateFile();
          if (fileInput.files.length > 0 && !file) {
            // File dipilih tapi tidak lolos validasi
            return;
          }

          alert('Prototype — form ini belum tersambung ke database. ' + (file ? 'Lampiran "' + file.name + '" siap dikirim.' : 'Tidak ada lampiran.'));
        });
      })();
      </script>

    </div>
  </main>
</div>
@include('siberad.dashboards.partials.dash-script')
</body>
</html>