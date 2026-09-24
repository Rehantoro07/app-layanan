<?php

namespace Database\Factories;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanData;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermohonanData>
 */
class PermohonanDataFactory extends Factory
{
    protected $model = PermohonanData::class;

    public function definition(): array
    {
        $status = fake()->randomElement(StatusPermohonan::cases());
        $dikirimPada = in_array($status, [StatusPermohonan::Diajukan, StatusPermohonan::Diproses, StatusPermohonan::MenungguKonfirmasi, StatusPermohonan::Selesai])
            ? fake()->dateTimeBetween('-10 days', 'now')
            : null;

        $batasSla = $dikirimPada ? (clone $dikirimPada)->modify('+3 days') : null;
        $isSelesai = $status === StatusPermohonan::Selesai;
        $selesaiPada = $isSelesai ? fake()->dateTimeBetween($dikirimPada, 'now') : null;
        $melebihiSla = $batasSla && now() > $batasSla && ! $isSelesai;

        return [
            'nomor_permohonan' => PermohonanData::generateNomorPermohonan(),
            'pemohon_id' => User::factory()->pemohon(),
            'unit_kerja_id' => UnitKerja::factory(),
            'petugas_id' => in_array($status, [StatusPermohonan::Diproses, StatusPermohonan::MenungguKonfirmasi, StatusPermohonan::Selesai])
                ? User::factory()->petugas()
                : null,
            'pimpinan_id' => in_array($status, [StatusPermohonan::MenungguKonfirmasi, StatusPermohonan::Selesai])
                ? User::factory()->pimpinan()
                : null,
            'tanggal_permohonan' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'nama_data' => fake()->randomElement([
                'Data Riwayat Dosis Radiasi Pekerja 2025',
                'Data Izin Pemanfaatan Sumber Radiasi Pengion Jawa Barat',
                'Rekapitulasi Temuan Inspeksi Keselamatan Nuklir',
                'Data Inventaris Sumber Radioaktif Terbungkus',
                'Data Fasilitas Radioterapi dan Kedokteran Nuklir Nasional',
            ]),
            'sumber_data' => fake()->randomElement([
                'BAPETEN Licensing and Inspection System (Balis Online)',
                'Sistem Informasi Dosis Pasien (Si-INTAN)',
                'Aplikasi Inspeksi Keselamatan Radiasi',
                'Database Perizinan Fasilitas Radiasi',
            ]),
            'periode_data' => fake()->randomElement([
                'Januari - Desember 2025',
                'Triwulan I 2026',
                'Semester II 2025',
                'Tahun 2024 - 2025',
            ]),
            'status' => $status,
            'catatan_hasil' => $isSelesai ? 'Data telah diolah dan diekstrak sesuai format yang diminta.' : null,
            'dikirim_pada' => $dikirimPada,
            'batas_sla' => $batasSla,
            'dikonfirmasi_pada' => $isSelesai ? $selesaiPada : null,
            'selesai_pada' => $selesaiPada,
            'melebihi_sla' => $melebihiSla,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => StatusPermohonan::Draft,
            'petugas_id' => null,
            'pimpinan_id' => null,
            'dikirim_pada' => null,
            'batas_sla' => null,
            'dikonfirmasi_pada' => null,
            'selesai_pada' => null,
            'melebihi_sla' => false,
        ]);
    }

    public function diajukan(): static
    {
        $dikirim = now();

        return $this->state(fn () => [
            'status' => StatusPermohonan::Diajukan,
            'petugas_id' => null,
            'pimpinan_id' => null,
            'dikirim_pada' => $dikirim,
            'batas_sla' => $dikirim->copy()->addDays(3),
            'dikonfirmasi_pada' => null,
            'selesai_pada' => null,
            'melebihi_sla' => false,
        ]);
    }

    public function diproses(): static
    {
        $dikirim = now()->subDay();

        return $this->state(fn () => [
            'status' => StatusPermohonan::Diproses,
            'petugas_id' => User::factory()->petugas(),
            'dikirim_pada' => $dikirim,
            'batas_sla' => $dikirim->copy()->addDays(3),
            'dikonfirmasi_pada' => null,
            'selesai_pada' => null,
            'melebihi_sla' => false,
        ]);
    }

    public function selesai(): static
    {
        $dikirim = now()->subDays(2);

        return $this->state(fn () => [
            'status' => StatusPermohonan::Selesai,
            'petugas_id' => User::factory()->petugas(),
            'pimpinan_id' => User::factory()->pimpinan(),
            'dikirim_pada' => $dikirim,
            'batas_sla' => $dikirim->copy()->addDays(3),
            'dikonfirmasi_pada' => now(),
            'selesai_pada' => now(),
            'catatan_hasil' => 'Permohonan telah diselesaikan dan dokumen hasil telah diunggah.',
            'melebihi_sla' => false,
        ]);
    }

    public function melebihiSla(): static
    {
        $dikirim = now()->subDays(5);

        return $this->state(fn () => [
            'status' => StatusPermohonan::Diproses,
            'petugas_id' => User::factory()->petugas(),
            'dikirim_pada' => $dikirim,
            'batas_sla' => $dikirim->copy()->addDays(3),
            'melebihi_sla' => true,
        ]);
    }
}
