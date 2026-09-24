<?php

namespace Database\Factories;

use App\Models\UnitKerja;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitKerja>
 */
class UnitKerjaFactory extends Factory
{
    protected $model = UnitKerja::class;

    public function definition(): array
    {
        return [
            'kode' => 'UK-'.fake()->unique()->numerify('###'),
            'nama' => fake()->randomElement([
                'Pusat Data dan Informasi Standardisasi',
                'Direktorat Pengaturan Pengawasan Fasilitas Radiasi',
                'Direktorat Perizinan Fasilitas Radiasi dan Zat Radioaktif',
                'Direktorat Inspeksi Fasilitas Radiasi dan Zat Radioaktif',
                'Biro Perencanaan, Informasi dan Keuangan',
                'Biro Hukum, Kerjasama dan Komunikasi Publik',
                'Biro Organisasi dan Umum',
                'Inspektorat BAPETEN',
            ]),
        ];
    }
}
