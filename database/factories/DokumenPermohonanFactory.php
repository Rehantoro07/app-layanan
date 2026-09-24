<?php

namespace Database\Factories;

use App\Enums\JenisDokumen;
use App\Models\DokumenPermohonan;
use App\Models\PermohonanData;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenPermohonan>
 */
class DokumenPermohonanFactory extends Factory
{
    protected $model = DokumenPermohonan::class;

    public function definition(): array
    {
        $jenis = fake()->randomElement(JenisDokumen::cases());

        return [
            'permohonan_data_id' => PermohonanData::factory(),
            'diunggah_oleh' => User::factory(),
            'jenis' => $jenis,
            'nama_file' => fake()->randomElement([
                'template_format_data.xlsx',
                'spesifikasi_kebutuhan_data.pdf',
                'hasil_ekstraksi_data.xlsx',
                'laporan_rekapitulasi_data.pdf',
            ]),
            'path' => 'dokumen/'.fake()->uuid().'.xlsx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran' => fake()->numberBetween(10240, 5242880), // 10 KB - 5 MB
            'metadata' => [
                'checksum' => fake()->sha256(),
                'original_name' => 'dokumen_'.fake()->word().'.xlsx',
            ],
        ];
    }

    public function formatData(): static
    {
        return $this->state(fn () => [
            'jenis' => JenisDokumen::FormatData,
            'nama_file' => 'format_kebutuhan_data_'.fake()->lexify('????').'.xlsx',
        ]);
    }

    public function hasil(): static
    {
        return $this->state(fn () => [
            'jenis' => JenisDokumen::Hasil,
            'nama_file' => 'hasil_pengolahan_data_'.fake()->lexify('????').'.xlsx',
        ]);
    }
}
