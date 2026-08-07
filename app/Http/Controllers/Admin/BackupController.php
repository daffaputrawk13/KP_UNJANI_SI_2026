<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    private const DISK = 'local';

    private const FOLDER = 'backups';

    /**
     * Daftar file backup yang sudah pernah dibuat, dipakai tab "Backup
     * Database" untuk menampilkan riwayat + tombol unduh.
     */
    public function index()
    {
        $files = collect(Storage::disk(self::DISK)->files(self::FOLDER))
            ->filter(fn ($f) => str_ends_with($f, '.sqlite') || str_ends_with($f, '.sql'))
            ->map(fn ($f) => [
                'nama' => basename($f),
                'path' => $f,
                'ukuran' => round(Storage::disk(self::DISK)->size($f) / 1024, 1).' KB',
                'tanggal' => \Illuminate\Support\Carbon::createFromTimestamp(Storage::disk(self::DISK)->lastModified($f))->translatedFormat('d M Y H:i'),
            ])
            ->sortByDesc('tanggal')
            ->values();

        return $files;
    }

    /**
     * Buat backup baru — untuk koneksi sqlite, salin file database; untuk
     * mysql/pgsql, jalankan mysqldump/pg_dump lewat proses shell kalau
     * tersedia di server.
     */
    public function store(): RedirectResponse
    {
        $connection = Config::get('database.default');
        $timestamp = now()->format('Y-m-d_His');

        try {
            if ($connection === 'sqlite') {
                $dbPath = Config::get('database.connections.sqlite.database');
                $filename = self::FOLDER."/backup_{$timestamp}.sqlite";
                Storage::disk(self::DISK)->put($filename, file_get_contents($dbPath));
            } elseif ($connection === 'mysql') {
                $cfg = Config::get('database.connections.mysql');
                $filename = self::FOLDER."/backup_{$timestamp}.sql";
                $fullPath = Storage::disk(self::DISK)->path($filename);
                Storage::disk(self::DISK)->makeDirectory(self::FOLDER);

                $process = new Process([
                    'mysqldump',
                    '-h', $cfg['host'],
                    '-P', (string) ($cfg['port'] ?? 3306),
                    '-u', $cfg['username'],
                    '--password='.$cfg['password'],
                    $cfg['database'],
                ]);
                $process->setTimeout(300);
                $process->run();

                if (! $process->isSuccessful()) {
                    throw new \RuntimeException('mysqldump gagal dijalankan di server ini.');
                }

                file_put_contents($fullPath, $process->getOutput());
            } else {
                return back()->with('error', "Backup otomatis belum didukung untuk koneksi database \"{$connection}\".");
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat backup: '.$e->getMessage());
        }

        ActivityLog::catat('backup.create', "Membuat backup database ({$connection}).");

        return back()->with('status', 'Backup database berhasil dibuat.');
    }

    /**
     * Unduh salah satu file backup.
     */
    public function download(string $filename): Response
    {
        $path = self::FOLDER.'/'.basename($filename);

        abort_unless(Storage::disk(self::DISK)->exists($path), 404);

        ActivityLog::catat('backup.download', "Mengunduh backup database \"{$filename}\".");

        return Storage::disk(self::DISK)->download($path);
    }
}
