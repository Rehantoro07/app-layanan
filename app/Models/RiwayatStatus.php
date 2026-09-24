<?php

namespace App\Models;

use App\Enums\StatusPermohonan;
use Database\Factories\RiwayatStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'permohonan_data_id',
    'user_id',
    'status_dari',
    'status_ke',
    'catatan',
    'created_at',
])]
class RiwayatStatus extends Model
{
    /** @use HasFactory<RiwayatStatusFactory> */
    use HasFactory, HasUuids;

    public const UPDATED_AT = null;

    protected $table = 'riwayat_status';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_dari' => StatusPermohonan::class,
            'status_ke' => StatusPermohonan::class,
            'created_at' => 'datetime',
        ];
    }

    public function permohonanData(): BelongsTo
    {
        return $this->belongsTo(PermohonanData::class, 'permohonan_data_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
