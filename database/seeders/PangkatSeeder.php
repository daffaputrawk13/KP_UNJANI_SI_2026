<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use Illuminate\Database\Seeder;

class PangkatSeeder extends Seeder
{
    public function run(): void
    {
        $pangkats = [
            // Tamtama
            ['kode' => 'PRADA', 'nama' => 'Prajurit Dua', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 1],
            ['kode' => 'PRATU', 'nama' => 'Prajurit Satu', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 2],
            ['kode' => 'PRAKA', 'nama' => 'Prajurit Kepala', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 3],
            ['kode' => 'KOPDA', 'nama' => 'Kopral Dua', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 4],
            ['kode' => 'KOPTU', 'nama' => 'Kopral Satu', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 5],
            ['kode' => 'KOPKA', 'nama' => 'Kopral Kepala', 'kategori' => Pangkat::KATEGORI_TAMTAMA, 'urutan' => 6],
            // Bintara
            ['kode' => 'SERDA', 'nama' => 'Sersan Dua', 'kategori' => Pangkat::KATEGORI_BINTARA, 'urutan' => 7],
            ['kode' => 'SERTU', 'nama' => 'Sersan Satu', 'kategori' => Pangkat::KATEGORI_BINTARA, 'urutan' => 8],
            ['kode' => 'SERKA', 'nama' => 'Sersan Kepala', 'kategori' => Pangkat::KATEGORI_BINTARA, 'urutan' => 9],
            ['kode' => 'SERMA', 'nama' => 'Sersan Mayor', 'kategori' => Pangkat::KATEGORI_BINTARA, 'urutan' => 10],
            // Perwira
            ['kode' => 'LETDA', 'nama' => 'Letnan Dua', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 11],
            ['kode' => 'LETTU', 'nama' => 'Letnan Satu', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 12],
            ['kode' => 'KAPTEN', 'nama' => 'Kapten', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 13],
            ['kode' => 'MAYOR', 'nama' => 'Mayor', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 14],
            ['kode' => 'LETKOL', 'nama' => 'Letnan Kolonel', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 15],
            ['kode' => 'KOLONEL', 'nama' => 'Kolonel', 'kategori' => Pangkat::KATEGORI_PERWIRA, 'urutan' => 16],
        ];

        foreach ($pangkats as $p) {
            Pangkat::updateOrCreate(['kode' => $p['kode']], $p);
        }
    }
}
