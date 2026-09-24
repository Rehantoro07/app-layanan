<?php

namespace Database\Factories;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanData;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiwayatStatus>
 */
class RiwayatStatusFactory extends Factory
{
    protected $model = RiwayatStatus::class;

    public function definition(): array
    {
        return [
            'permohonan_data_id' => PermohonanData::factory(),
            'user_id' => User::factory(),
            'status_dari' => StatusPermohonan::Draft,
            'status_ke' => StatusPermohonan::Diajukan,
            'catatan' => 'Permohonan diajukan ke Pusat Data',
            'created_at' => now(),
        ];
    }
}
