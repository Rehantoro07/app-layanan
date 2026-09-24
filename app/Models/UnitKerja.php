<?php

namespace App\Models;

use Database\Factories\UnitKerjaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode', 'nama'])]
class UnitKerja extends Model
{
    /** @use HasFactory<UnitKerjaFactory> */
    use HasFactory, HasUuids;

    protected $table = 'unit_kerja';

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }

    public function permohonanData(): HasMany
    {
        return $this->hasMany(PermohonanData::class, 'unit_kerja_id');
    }
}
