<?php

namespace App\Models;

use App\Enums\JenisDokumen;
use Database\Factories\DokumenPermohonanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'permohonan_data_id',
    'diunggah_oleh',
    'jenis',
    'nama_file',
    'path',
    'mime_type',
    'ukuran',
    'metadata',
])]
class DokumenPermohonan extends Model
{
    /** @use HasFactory<DokumenPermohonanFactory> */
    use HasFactory, HasUuids;

    protected $table = 'dokumen_permohonan';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jenis' => JenisDokumen::class,
            'ukuran' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * Human-readable formatted file size.
     */
    protected function ukuranFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = (int) $this->ukuran;
                if ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 2).' MB';
                }

                if ($bytes >= 1024) {
                    return number_format($bytes / 1024, 2).' KB';
                }

                return $bytes.' B';
            }
        );
    }

    public function permohonanData(): BelongsTo
    {
        return $this->belongsTo(PermohonanData::class, 'permohonan_data_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
