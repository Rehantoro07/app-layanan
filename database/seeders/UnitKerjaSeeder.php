<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'kode' => 'PUSDATIN',
                'nama' => 'Pusat Data dan Informasi Standardisasi',
            ],
            [
                'kode' => 'DPFRZR',
                'nama' => 'Direktorat Perizinan Fasilitas Radiasi dan Zat Radioaktif',
            ],
            [
                'kode' => 'DIFRZR',
                'nama' => 'Direktorat Inspeksi Fasilitas Radiasi dan Zat Radioaktif',
            ],
            [
                'kode' => 'DP2FRZR',
                'nama' => 'Direktorat Pengaturan Pengawasan Fasilitas Radiasi dan Zat Radioaktif',
            ],
            [
                'kode' => 'DPIBN',
                'nama' => 'Direktorat Perizinan Instalasi dan Bahan Nuklir',
            ],
            [
                'kode' => 'DIIBN',
                'nama' => 'Direktorat Inspeksi Instalasi dan Bahan Nuklir',
            ],
            [
                'kode' => 'BPIK',
                'nama' => 'Biro Perencanaan, Informasi dan Keuangan',
            ],
            [
                'kode' => 'BHKK',
                'nama' => 'Biro Hukum, Kerjasama dan Komunikasi Publik',
            ],
            [
                'kode' => 'BOU',
                'nama' => 'Biro Organisasi dan Umum',
            ],
            [
                'kode' => 'INSPEKTORAT',
                'nama' => 'Inspektorat BAPETEN',
            ],
        ];

        foreach ($units as $unit) {
            UnitKerja::firstOrCreate(
                ['kode' => $unit['kode']],
                ['nama' => $unit['nama']]
            );
        }
    }
}
