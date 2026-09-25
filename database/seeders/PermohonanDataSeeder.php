<?php

namespace Database\Seeders;

use App\Enums\JenisDokumen;
use App\Enums\StatusPermohonan;
use App\Models\DokumenPermohonan;
use App\Models\PermohonanData;
use App\Models\RiwayatStatus;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PermohonanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'asd@gmail.com')->first();
        $petugas1 = User::where('email', 'petugas@bapeten.go.id')->first();
        $petugas2 = User::where('email', 'siti.rahmawati@bapeten.go.id')->first();
        $pimpinan = User::where('email', 'pimpinan@bapeten.go.id')->first();

        $pemohonDfrzr = User::where('email', 'dewi.lestari@bapeten.go.id')->first();
        $pemohonDifrzr = User::where('email', 'pemohon@bapeten.go.id')->first();
        $pemohonBpik = User::where('email', 'rian.hidayat@bapeten.go.id')->first();
        $pemohonDp2frzr = User::where('email', 'nurul.hidayati@bapeten.go.id')->first();

        $dpfrzr = UnitKerja::where('kode', 'DPFRZR')->first();
        $difrzr = UnitKerja::where('kode', 'DIFRZR')->first();
        $bpik = UnitKerja::where('kode', 'BPIK')->first();
        $dp2frzr = UnitKerja::where('kode', 'DP2FRZR')->first();

        // ---------------------------------------------------------------------------------
        // 1. Kasus SELESAI (On-Time): Data Riwayat Dosis Radiasi Pekerja 2025
        // ---------------------------------------------------------------------------------
        $tglKirim1 = Carbon::now()->subDays(6);
        $batasSla1 = $tglKirim1->copy()->addDays(3);
        $tglSelesai1 = $tglKirim1->copy()->addDays(2); // Selesai dalam 2 hari (SLA terpenuhi)

        $p1 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0001',
            'pemohon_id' => $pemohonDifrzr->id,
            'unit_kerja_id' => $difrzr->id,
            'petugas_id' => $petugas1->id,
            'pimpinan_id' => $pimpinan->id,
            'tanggal_permohonan' => $tglKirim1->toDateString(),
            'nama_data' => 'Data Riwayat Dosis Radiasi Pekerja Radiologi Tahun 2025',
            'sumber_data' => 'Sistem Informasi Dosis Pasien & Pekerja (Si-INTAN)',
            'periode_data' => '1 Januari 2025 - 31 Desember 2025',
            'status' => StatusPermohonan::Selesai,
            'catatan_hasil' => 'Data riwayat dosis telah diekstraksi dari database Si-INTAN sebanyak 4.820 rekor dan disesuaikan dengan template kolom permintaan.',
            'dikirim_pada' => $tglKirim1,
            'batas_sla' => $batasSla1,
            'dikonfirmasi_pada' => $tglSelesai1,
            'selesai_pada' => $tglSelesai1,
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p1->id,
            'diunggah_oleh' => $pemohonDifrzr->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Format_Kebutuhan_Tabel_Dosis_Pekerja.xlsx',
            'path' => 'dokumen/format_data_0001.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 38400,
            'metadata' => [
                'kolom' => ['ID_Pekerja', 'Instansi', 'Jenis_Pekerjaan', 'Dosis_Hp10', 'Dosis_Hp07', 'Periode'],
                'format' => 'xlsx',
            ],
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p1->id,
            'diunggah_oleh' => $petugas1->id,
            'jenis' => JenisDokumen::Hasil,
            'nama_file' => 'Hasil_Ekstraksi_Dosis_Radiasi_2025_Final.xlsx',
            'path' => 'dokumen/hasil_pengolahan_0001.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 285400,
            'metadata' => [
                'total_baris' => 4820,
                'waktu_generate' => $tglSelesai1->toIso8601String(),
                'status_verifikasi' => 'Valid',
            ],
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p1->id,
            'user_id' => $pemohonDifrzr->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Draf permohonan data dibuat oleh pemohon',
            'created_at' => $tglKirim1->copy()->subHours(2),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p1->id,
            'user_id' => $pemohonDifrzr->id,
            'status_dari' => StatusPermohonan::Draft->value,
            'status_ke' => StatusPermohonan::Diajukan->value,
            'catatan' => 'Permohonan resmi diajukan ke Pusat Data',
            'created_at' => $tglKirim1,
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p1->id,
            'user_id' => $petugas1->id,
            'status_dari' => StatusPermohonan::Diajukan->value,
            'status_ke' => StatusPermohonan::Diproses->value,
            'catatan' => 'Petugas memulai proses query dan ekstraksi data',
            'created_at' => $tglKirim1->copy()->addHours(6),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p1->id,
            'user_id' => $petugas1->id,
            'status_dari' => StatusPermohonan::Diproses->value,
            'status_ke' => StatusPermohonan::MenungguKonfirmasi->value,
            'catatan' => 'Hasil pengolahan selesai dan dikirim ke Kepala Pusat Data untuk konfirmasi',
            'created_at' => $tglSelesai1->copy()->subHours(4),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p1->id,
            'user_id' => $pimpinan->id,
            'status_dari' => StatusPermohonan::MenungguKonfirmasi->value,
            'status_ke' => StatusPermohonan::Selesai->value,
            'catatan' => 'Hasil pengolahan data dikonfirmasi dan disetujui. Layanan selesai.',
            'created_at' => $tglSelesai1,
        ]);

        // ---------------------------------------------------------------------------------
        // 2. Kasus MENUNGGU KONFIRMASI PIMPINAN: Data Izin Sumber Radiasi Pengion Jawa Barat
        // ---------------------------------------------------------------------------------
        $tglKirim2 = Carbon::now()->subDays(2);
        $batasSla2 = $tglKirim2->copy()->addDays(3);

        $p2 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0002',
            'pemohon_id' => $pemohonDfrzr->id,
            'unit_kerja_id' => $dpfrzr->id,
            'petugas_id' => $petugas1->id,
            'pimpinan_id' => $pimpinan->id,
            'tanggal_permohonan' => $tglKirim2->toDateString(),
            'nama_data' => 'Data Rekapitulasi Izin Pemanfaatan Sumber Radiasi Pengion Provinsi Jawa Barat',
            'sumber_data' => 'Balis Online (BAPETEN Licensing and Inspection System)',
            'periode_data' => 'Tahun 2024 - 2025',
            'status' => StatusPermohonan::MenungguKonfirmasi,
            'catatan_hasil' => 'Data perizinan aktif dan kadaluarsa untuk fasilitas medik dan industri di Jawa Barat telah diekstrak dan siap diverifikasi pimpinan.',
            'dikirim_pada' => $tglKirim2,
            'batas_sla' => $batasSla2,
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p2->id,
            'diunggah_oleh' => $pemohonDfrzr->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Spesifikasi_Kolom_Izin_Jabar.xlsx',
            'path' => 'dokumen/format_data_0002.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 24500,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p2->id,
            'diunggah_oleh' => $petugas1->id,
            'jenis' => JenisDokumen::Hasil,
            'nama_file' => 'Hasil_Olah_Data_Izin_Jabar_2024_2025.xlsx',
            'path' => 'dokumen/hasil_pengolahan_0002.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 195200,
            'metadata' => [
                'total_izin' => 1240,
                'status_izin' => ['Berlaku' => 980, 'Perpanjangan' => 140, 'Kadaluarsa' => 120],
            ],
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p2->id,
            'user_id' => $pemohonDfrzr->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Draf permohonan disiapkan',
            'created_at' => $tglKirim2->copy()->subHours(3),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p2->id,
            'user_id' => $pemohonDfrzr->id,
            'status_dari' => StatusPermohonan::Draft->value,
            'status_ke' => StatusPermohonan::Diajukan->value,
            'catatan' => 'Permohonan data diajukan untuk ditindaklanjuti',
            'created_at' => $tglKirim2,
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p2->id,
            'user_id' => $petugas1->id,
            'status_dari' => StatusPermohonan::Diajukan->value,
            'status_ke' => StatusPermohonan::Diproses->value,
            'catatan' => 'Petugas memproses query database PostgreSQL Balis',
            'created_at' => $tglKirim2->copy()->addHours(8),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p2->id,
            'user_id' => $petugas1->id,
            'status_dari' => StatusPermohonan::Diproses->value,
            'status_ke' => StatusPermohonan::MenungguKonfirmasi->value,
            'catatan' => 'File hasil pengolahan telah diunggah dan diajukan ke Pimpinan Pusat Data',
            'created_at' => Carbon::now()->subHours(5),
        ]);

        // ---------------------------------------------------------------------------------
        // 3. Kasus MELEBIHI SLA (> 3 Hari): Data Integrasi Tarif PNBP Fasilitas Radiasi
        // ---------------------------------------------------------------------------------
        $tglKirim3 = Carbon::now()->subDays(6);
        $batasSla3 = $tglKirim3->copy()->addDays(3); // Batas SLA 3 hari yang lalu

        $p3 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0003',
            'pemohon_id' => $pemohonBpik->id,
            'unit_kerja_id' => $bpik->id,
            'petugas_id' => $petugas2->id,
            'tanggal_permohonan' => $tglKirim3->toDateString(),
            'nama_data' => 'Data Integrasi Tarif Realisasi PNBP dan Jenis Fasilitas Radiasi Nasional',
            'sumber_data' => 'Database SIMPONI Kemenkeu & Balis Perizinan',
            'periode_data' => 'Tahun Anggaran 2023 - 2025',
            'status' => StatusPermohonan::Diproses,
            'dikirim_pada' => $tglKirim3,
            'batas_sla' => $batasSla3,
            'melebihi_sla' => true, // Menandai permohonan telah melewati SLA 3 hari
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p3->id,
            'diunggah_oleh' => $pemohonBpik->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Template_Rekon_PNBP_BAPETEN.xlsx',
            'path' => 'dokumen/format_data_0003.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 54100,
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p3->id,
            'user_id' => $pemohonBpik->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Penyusunan draf data PNBP',
            'created_at' => $tglKirim3->copy()->subHours(1),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p3->id,
            'user_id' => $pemohonBpik->id,
            'status_dari' => StatusPermohonan::Draft->value,
            'status_ke' => StatusPermohonan::Diajukan->value,
            'catatan' => 'Permohonan dikirim ke Pusat Data',
            'created_at' => $tglKirim3,
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p3->id,
            'user_id' => $petugas2->id,
            'status_dari' => StatusPermohonan::Diajukan->value,
            'status_ke' => StatusPermohonan::Diproses->value,
            'catatan' => 'Sedang dilakukan pencocokan NTPN SIMPONI dengan kode billing Balis (mengalami kendala inkonsistensi nomor tagihan)',
            'created_at' => $tglKirim3->copy()->addDay(),
        ]);

        // ---------------------------------------------------------------------------------
        // 4. Kasus SEDANG DIPROSES (On-Time): Rekapitulasi Temuan Inspeksi Kedokteran Nuklir
        // ---------------------------------------------------------------------------------
        $tglKirim4 = Carbon::now()->subDay();
        $batasSla4 = $tglKirim4->copy()->addDays(3);

        $p4 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0004',
            'pemohon_id' => $pemohonDifrzr->id,
            'unit_kerja_id' => $difrzr->id,
            'petugas_id' => $petugas1->id,
            'tanggal_permohonan' => $tglKirim4->toDateString(),
            'nama_data' => 'Rekapitulasi Kategori Temuan Inspeksi Fasilitas Kedokteran Nuklir',
            'sumber_data' => 'Aplikasi E-Inspeksi Keselamatan Radiasi',
            'periode_data' => 'Tahun 2024 - Semester I 2026',
            'status' => StatusPermohonan::Diproses,
            'dikirim_pada' => $tglKirim4,
            'batas_sla' => $batasSla4,
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p4->id,
            'diunggah_oleh' => $pemohonDifrzr->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Format_Klasifikasi_Temuan_Inspeksi.xlsx',
            'path' => 'dokumen/format_data_0004.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 29800,
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p4->id,
            'user_id' => $pemohonDifrzr->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Draf permohonan disiapkan',
            'created_at' => $tglKirim4->copy()->subHours(2),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p4->id,
            'user_id' => $pemohonDifrzr->id,
            'status_dari' => StatusPermohonan::Draft->value,
            'status_ke' => StatusPermohonan::Diajukan->value,
            'catatan' => 'Permohonan diajukan ke Pusat Data',
            'created_at' => $tglKirim4,
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p4->id,
            'user_id' => $petugas1->id,
            'status_dari' => StatusPermohonan::Diajukan->value,
            'status_ke' => StatusPermohonan::Diproses->value,
            'catatan' => 'Petugas memulai kompilasi data laporan inspeksi keselamatan',
            'created_at' => Carbon::now()->subHours(10),
        ]);

        // ---------------------------------------------------------------------------------
        // 5. Kasus DIAJUKAN (Menunggu Diambil Petugas)
        // ---------------------------------------------------------------------------------
        $tglKirim5 = Carbon::now()->subHours(8);
        $batasSla5 = $tglKirim5->copy()->addDays(3);

        $p5 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0005',
            'pemohon_id' => $pemohonDp2frzr->id,
            'unit_kerja_id' => $dp2frzr->id,
            'petugas_id' => null,
            'pimpinan_id' => null,
            'tanggal_permohonan' => $tglKirim5->toDateString(),
            'nama_data' => 'Data Inventaris Sumber Radioaktif Terbungkus Kategori 1 dan 2 Seluruh Indonesia',
            'sumber_data' => 'Balis Infotuk & Database Sumber Radioaktif',
            'periode_data' => 'Kondisi per September 2026',
            'status' => StatusPermohonan::Diajukan,
            'dikirim_pada' => $tglKirim5,
            'batas_sla' => $batasSla5,
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p5->id,
            'diunggah_oleh' => $pemohonDp2frzr->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Template_Sumber_Radioaktif_Kat1_Kat2.xlsx',
            'path' => 'dokumen/format_data_0005.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 31200,
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p5->id,
            'user_id' => $pemohonDp2frzr->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Draf dibuat oleh pemohon DP2FRZR',
            'created_at' => $tglKirim5->copy()->subHours(1),
        ]);
        RiwayatStatus::create([
            'permohonan_data_id' => $p5->id,
            'user_id' => $pemohonDp2frzr->id,
            'status_dari' => StatusPermohonan::Draft->value,
            'status_ke' => StatusPermohonan::Diajukan->value,
            'catatan' => 'Permohonan dikirimkan ke Pusat Data, menunggu penugasan petugas',
            'created_at' => $tglKirim5,
        ]);

        // ---------------------------------------------------------------------------------
        // 6. Kasus DRAFT (Masih Disiapkan Pemohon)
        // ---------------------------------------------------------------------------------
        $p6 = PermohonanData::create([
            'nomor_permohonan' => 'PD/2026/09/0006',
            'pemohon_id' => $pemohonDfrzr->id,
            'unit_kerja_id' => $dpfrzr->id,
            'petugas_id' => null,
            'pimpinan_id' => null,
            'tanggal_permohonan' => Carbon::today()->toDateString(),
            'nama_data' => 'Data Sebaran Fasilitas Radioterapi dan Akselerator Medik Nasional',
            'sumber_data' => 'Balis Online Medik',
            'periode_data' => 'Tahun 2026',
            'status' => StatusPermohonan::Draft,
            'dikirim_pada' => null,
            'batas_sla' => null,
            'melebihi_sla' => false,
        ]);

        DokumenPermohonan::create([
            'permohonan_data_id' => $p6->id,
            'diunggah_oleh' => $pemohonDfrzr->id,
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'Draft_Kebutuhan_Kolom_Radioterapi.xlsx',
            'path' => 'dokumen/format_data_0006.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => 18900,
        ]);

        RiwayatStatus::create([
            'permohonan_data_id' => $p6->id,
            'user_id' => $pemohonDfrzr->id,
            'status_dari' => null,
            'status_ke' => StatusPermohonan::Draft->value,
            'catatan' => 'Draf permohonan data baru disimpan oleh pemohon',
            'created_at' => Carbon::now()->subHours(2),
        ]);
    }
}
