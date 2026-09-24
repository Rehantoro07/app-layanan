<?php

namespace Database\Seeders;

use App\Enums\JenisDokumen;
use App\Enums\StatusPermohonan;
use App\Enums\UserRole;
use App\Models\DokumenPermohonan;
use App\Models\PermohonanData;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Master Unit Kerja
        $pusdatin = UnitKerja::firstOrCreate(
            ['kode' => 'UK-001'],
            ['nama' => 'Pusat Data dan Informasi Standardisasi']
        );

        $dpfrzr = UnitKerja::firstOrCreate(
            ['kode' => 'UK-002'],
            ['nama' => 'Direktorat Perizinan Fasilitas Radiasi dan Zat Radioaktif']
        );

        $difrzr = UnitKerja::firstOrCreate(
            ['kode' => 'UK-003'],
            ['nama' => 'Direktorat Inspeksi Fasilitas Radiasi dan Zat Radioaktif']
        );

        $bpik = UnitKerja::firstOrCreate(
            ['kode' => 'UK-004'],
            ['nama' => 'Biro Perencanaan, Informasi dan Keuangan']
        );

        // 2. Seed Users for each role
        $password = Hash::make('password');

        $admin = User::firstOrCreate(
            ['email' => 'asd@gmail.com'],
            [
                'nama' => 'Robby Rehantoro',
                'nip' => '199505122022031001',
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin->id,
            ]
        );

        $adminAlt = User::firstOrCreate(
            ['email' => 'admin@bapeten.go.id'],
            [
                'nama' => 'Administrator TI',
                'nip' => '199001012015031001',
                'password' => $password,
                'role' => UserRole::Admin,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin->id,
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@bapeten.go.id'],
            [
                'nama' => 'Petugas Pusat Data',
                'nip' => '199203152018011002',
                'password' => $password,
                'role' => UserRole::Petugas,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin->id,
            ]
        );

        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@bapeten.go.id'],
            [
                'nama' => 'Kepala Pusat Data',
                'nip' => '197508201998031001',
                'password' => $password,
                'role' => UserRole::Pimpinan,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin->id,
            ]
        );

        $pemohon = User::firstOrCreate(
            ['email' => 'pemohon@bapeten.go.id'],
            [
                'nama' => 'Pegawai Pemohon BAPETEN',
                'nip' => '199411052020121003',
                'password' => $password,
                'role' => UserRole::Pemohon,
                'is_active' => true,
                'unit_kerja_id' => $dpfrzr->id,
            ]
        );

        // 3. Seed Sample Permohonan Data with different workflows

        // Sample 1: Selesai
        $permohonan1 = PermohonanData::create([
            'nomor_permohonan' => PermohonanData::generateNomorPermohonan(),
            'pemohon_id' => $pemohon->id,
            'unit_kerja_id' => $dpfrzr->id,
            'petugas_id' => $petugas->id,
            'pimpinan_id' => $pimpinan->id,
            'tanggal_permohonan' => now()->subDays(5)->toDateString(),
            'nama_data' => 'Data Riwayat Dosis Radiasi Pekerja Tahun 2025',
            'sumber_data' => 'Sistem Informasi Dosis Pasien (Si-INTAN)',
            'periode_data' => 'Januari - Desember 2025',
            'status' => StatusPermohonan::Selesai,
            'catatan_hasil' => 'Data riwayat dosis telah berhasil diekstrak dan disesuaikan dengan format tabel Excel.',
            'dikirim_pada' => now()->subDays(5),
            'batas_sla' => now()->subDays(2),
            'dikonfirmasi_pada' => now()->subDays(2),
            'selesai_pada' => now()->subDays(2),
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $permohonan1->id,
            'diunggah_oleh' => $pemohon->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'format_kebutuhan_dosis_2025.xlsx',
            'path' => 'dokumen/sample_format_1.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 45056,
            'metadata' => ['keterangan' => 'Template kolom yang dibutuhkan'],
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $permohonan1->id,
            'diunggah_oleh' => $petugas->id,
            'jenis' => JenisDokumen::Hasil,
            'nama_file' => 'hasil_rekap_dosis_radiasi_2025.xlsx',
            'path' => 'dokumen/sample_hasil_1.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 125829,
            'metadata' => ['total_baris' => 1250],
        ]);

        $permohonan1->catatRiwayat(StatusPermohonan::Draft, $pemohon, 'Permohonan dibuat dalam bentuk draft');
        $permohonan1->catatRiwayat(StatusPermohonan::Diajukan, $pemohon, 'Permohonan dikirim ke Pusat Data', StatusPermohonan::Draft);
        $permohonan1->catatRiwayat(StatusPermohonan::Diproses, $petugas, 'Petugas Pusat Data menerima dan memproses data', StatusPermohonan::Diajukan);
        $permohonan1->catatRiwayat(StatusPermohonan::MenungguKonfirmasi, $petugas, 'Hasil pengolahan selesai dan diteruskan ke Kepala Pusat Data', StatusPermohonan::Diproses);
        $permohonan1->catatRiwayat(StatusPermohonan::Selesai, $pimpinan, 'Hasil pengolahan data dikonfirmasi dan disetujui', StatusPermohonan::MenungguKonfirmasi);

        // Sample 2: Menunggu Konfirmasi Pimpinan
        $permohonan2 = PermohonanData::create([
            'nomor_permohonan' => PermohonanData::generateNomorPermohonan(),
            'pemohon_id' => $pemohon->id,
            'unit_kerja_id' => $dpfrzr->id,
            'petugas_id' => $petugas->id,
            'pimpinan_id' => $pimpinan->id,
            'tanggal_permohonan' => now()->subDays(2)->toDateString(),
            'nama_data' => 'Data Izin Fasilitas Radiasi Provinsi Jawa Timur',
            'sumber_data' => 'Balis Online (BAPETEN Licensing)',
            'periode_data' => 'Semester I 2026',
            'status' => StatusPermohonan::MenungguKonfirmasi,
            'catatan_hasil' => 'Query database telah diekspor dan difilter berdasarkan kode wilayah Jawa Timur.',
            'dikirim_pada' => now()->subDays(2),
            'batas_sla' => now()->addDay(),
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $permohonan2->id,
            'diunggah_oleh' => $petugas->id,
            'jenis' => JenisDokumen::Hasil,
            'nama_file' => 'hasil_izin_radiasi_jatim_2026.xlsx',
            'path' => 'dokumen/sample_hasil_2.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 210944,
        ]);

        $permohonan2->catatRiwayat(StatusPermohonan::Draft, $pemohon, 'Permohonan dibuat');
        $permohonan2->catatRiwayat(StatusPermohonan::Diajukan, $pemohon, 'Permohonan dikirim ke Pusat Data', StatusPermohonan::Draft);
        $permohonan2->catatRiwayat(StatusPermohonan::Diproses, $petugas, 'Sedang diproses ekstraksi SQL', StatusPermohonan::Diajukan);
        $permohonan2->catatRiwayat(StatusPermohonan::MenungguKonfirmasi, $petugas, 'Menunggu persetujuan pimpinan', StatusPermohonan::Diproses);

        // Sample 3: Sedang Diproses Petugas
        $permohonan3 = PermohonanData::create([
            'nomor_permohonan' => PermohonanData::generateNomorPermohonan(),
            'pemohon_id' => $pemohon->id,
            'unit_kerja_id' => $difrzr->id,
            'petugas_id' => $petugas->id,
            'tanggal_permohonan' => now()->subDay()->toDateString(),
            'nama_data' => 'Rekapitulasi Temuan Inspeksi Fasilitas Kedokteran Nuklir',
            'sumber_data' => 'Aplikasi Inspeksi Keselamatan Radiasi',
            'periode_data' => 'Tahun 2024 - 2025',
            'status' => StatusPermohonan::Diproses,
            'dikirim_pada' => now()->subDay(),
            'batas_sla' => now()->addDays(2),
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $permohonan3->id,
            'diunggah_oleh' => $pemohon->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'format_kategori_temuan.xlsx',
            'path' => 'dokumen/sample_format_3.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 32768,
        ]);

        $permohonan3->catatRiwayat(StatusPermohonan::Draft, $pemohon, 'Draf dibuat');
        $permohonan3->catatRiwayat(StatusPermohonan::Diajukan, $pemohon, 'Permohonan dikirim', StatusPermohonan::Draft);
        $permohonan3->catatRiwayat(StatusPermohonan::Diproses, $petugas, 'Petugas mulai memproses data', StatusPermohonan::Diajukan);

        // Sample 4: Melebihi SLA (> 3 hari belum selesai)
        $permohonan4 = PermohonanData::create([
            'nomor_permohonan' => PermohonanData::generateNomorPermohonan(),
            'pemohon_id' => $pemohon->id,
            'unit_kerja_id' => $bpik->id,
            'petugas_id' => $petugas->id,
            'tanggal_permohonan' => now()->subDays(6)->toDateString(),
            'nama_data' => 'Data Integrasi PNBP Fasilitas Radiasi Nasional',
            'sumber_data' => 'Database SIMPONI & Balis',
            'periode_data' => 'Tahun 2023 - 2025',
            'status' => StatusPermohonan::Diproses,
            'dikirim_pada' => now()->subDays(6),
            'batas_sla' => now()->subDays(3),
            'melebihi_sla' => true,
        ]);

        $permohonan4->catatRiwayat(StatusPermohonan::Draft, $pemohon, 'Permohonan dibuat');
        $permohonan4->catatRiwayat(StatusPermohonan::Diajukan, $pemohon, 'Permohonan diajukan', StatusPermohonan::Draft);
        $permohonan4->catatRiwayat(StatusPermohonan::Diproses, $petugas, 'Proses konsolidasi data PNBP', StatusPermohonan::Diajukan);
    }
}
