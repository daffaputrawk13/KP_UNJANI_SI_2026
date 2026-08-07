<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Pengguna & Aktivitas — {{ $pengaturan->singkatan ?? 'SIBERAD' }}</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0c1210;color:#e7efe9;margin:0;padding:32px;}
  h1{font-size:20px;margin:0 0 4px;}
  p.sub{color:#9fb0a6;font-size:13px;margin:0 0 24px;}
  .toolbar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:end;}
  .toolbar form{display:flex;gap:10px;flex-wrap:wrap;align-items:end;}
  .field{display:flex;flex-direction:column;gap:4px;font-size:12px;}
  input,button,a.btn{font-family:inherit;font-size:12.5px;}
  input{background:#111a17;border:1px solid #24352d;color:#e7efe9;border-radius:6px;padding:8px 10px;}
  .btn{display:inline-flex;align-items:center;gap:6px;background:#1c2c25;border:1px solid #2c4438;color:#e7efe9;border-radius:6px;padding:8px 14px;text-decoration:none;cursor:pointer;}
  .btn.primary{background:#d4a94f;color:#1a1206;border-color:#d4a94f;font-weight:600;}
  table{width:100%;border-collapse:collapse;margin-top:8px;font-size:12.5px;}
  th,td{text-align:left;padding:9px 10px;border-bottom:1px solid #202f27;}
  th{color:#9fb0a6;font-weight:600;text-transform:uppercase;font-size:10.5px;letter-spacing:.04em;}
  .panel{background:#111a17;border:1px solid #202f27;border-radius:10px;padding:18px;margin-bottom:24px;}
</style>
</head>
<body>
  <h1>Laporan Pengguna &amp; Aktivitas</h1>
  <p class="sub">{{ $pengaturan->nama_instansi }} — SIBERAD</p>

  <div class="toolbar">
    <a class="btn primary" href="{{ route('admin.laporan.export-pengguna') }}">Export Pengguna (Excel/CSV)</a>
    <a class="btn primary" href="{{ route('admin.laporan.export-aktivitas') }}">Export Aktivitas (Excel/CSV)</a>
    <a class="btn" href="{{ route('admin.laporan.cetak') }}" target="_blank">Cetak / Simpan PDF</a>
    <form method="GET" action="{{ route('admin.laporan.index') }}">
      <div class="field"><label>Dari</label><input type="date" name="dari" value="{{ $dari }}"></div>
      <div class="field"><label>Sampai</label><input type="date" name="sampai" value="{{ $sampai }}"></div>
      <button class="btn" type="submit">Terapkan</button>
    </form>
  </div>

  <div class="panel">
    <h3>Daftar Pengguna ({{ $semuaPengguna->count() }})</h3>
    <table>
      <thead><tr><th>Nama</th><th>Username</th><th>Satuan</th><th>Jabatan</th></tr></thead>
      <tbody>
        @foreach($semuaPengguna as $u)
        <tr><td>{{ $u->name }}</td><td>{{ $u->username }}</td><td>{{ $u->satuan->nama ?? '-' }}</td><td>{{ $u->jabatan ?? '-' }}</td></tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="panel">
    <h3>Log Aktivitas ({{ $log->count() }})</h3>
    <table>
      <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead>
      <tbody>
        @foreach($log as $l)
        <tr>
          <td>{{ $l->created_at?->translatedFormat('d M Y H:i') }}</td>
          <td>{{ $l->nama_pengguna ?? '-' }}</td>
          <td>{{ $l->aksi }}</td>
          <td>{{ $l->deskripsi }}</td>
          <td>{{ $l->ip_address }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</body>
</html>
