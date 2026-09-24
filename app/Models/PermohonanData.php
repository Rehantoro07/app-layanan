<?php

namespace App\Models;

use App\Enums\JenisDokumen;
use App\Enums\StatusPermohonan;
use Database\Factories\PermohonanDataFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'nomor_permohonan',
    'pemohon_id',
    'unit_kerja_id',
    'petugas_id',
    'pimpinan_id',
    'tanggal_permohonan',
    'nama_data',
    'sumber_data',
    'periode_data',
    'status',
    'catatan_hasil',
    'dikirim_pada',
    'batas_sla',
    'dikonfirmasi_pada',
    'selesai_pada',
    'melebihi_sla',
])]
class PermohonanData extends Model
{
    /** @use HasFactory<PermohonanDataFactory> */
    use HasFactory, HasUuids;

    protected $table = 'permohonan_data';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_permohonan' => 'date',
            'status' => StatusPermohonan::class,
            'dikirim_pada' => 'datetime',
            'batas_sla' => 'datetime',
            'dikonfirmasi_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'melebihi_sla' => 'boolean',
        ];
    }

    /**
     * Boot model events for automatic numbering and SLA calculation.
     */
    protected static function booted(): void
    {
        static::creating(function (PermohonanData $permohonan) {
            if (empty($permohonan->nomor_permohonan)) {
                $permohonan->nomor_permohonan = static::generateNomorPermohonan();
            }

            if (empty($permohonan->tanggal_permohonan)) {
                $permohonan->tanggal_permohonan = now()->toDateString();
            }
        });

        static::updating(function (PermohonanData $permohonan) {
            // When status changes to Diajukan for the first time, set dikirim_pada & batas_sla (3 days)
            if ($permohonan->isDirty('status') && $permohonan->status === StatusPermohonan::Diajukan && empty($permohonan->dikirim_pada)) {
                $permohonan->dikirim_pada = now();
                $permohonan->batas_sla = now()->addDays(3);
            }

            // When status changes to Selesai, record selesai_pada
            if ($permohonan->isDirty('status') && $permohonan->status === StatusPermohonan::Selesai && empty($permohonan->selesai_pada)) {
                $permohonan->selesai_pada = now();
            }
        });
    }

    /**
     * Generate unique sequential request number: PD/{YYYY}/{MM}/{4-digit-sequence}.
     */
    public static function generateNomorPermohonan(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $prefix = "PD/{$year}/{$month}/";

        $latest = static::query()
            ->where('nomor_permohonan', 'like', "{$prefix}%")
            ->orderBy('nomor_permohonan', 'desc')
            ->value('nomor_permohonan');

        if ($latest) {
            $lastNumber = (int) substr($latest, strrpos($latest, '/') + 1);
            $nextNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}{$nextNumber}";
    }

    /**
     * Record a status transition in the audit history.
     */
    public function catatRiwayat(
        StatusPermohonan|string $ke,
        ?User $user = null,
        ?string $catatan = null,
        StatusPermohonan|string|null $dari = null
    ): RiwayatStatus {
        $statusDari = $dari ?? $this->status;
        $dariValue = $statusDari instanceof StatusPermohonan ? $statusDari->value : $statusDari;
        $keValue = $ke instanceof StatusPermohonan ? $ke->value : $ke;

        return $this->riwayatStatus()->create([
            'user_id' => $user?->id ?? auth()->id(),
            'status_dari' => $dariValue,
            'status_ke' => $keValue,
            'catatan' => $catatan,
            'created_at' => now(),
        ]);
    }

    /**
     * Check if the SLA has been breached and update the column if necessary.
     */
    public function cekDanUpdateMelebihiSla(): bool
    {
        if ($this->status === StatusPermohonan::Selesai) {
            return $this->melebihi_sla;
        }

        if ($this->batas_sla && now()->isAfter($this->batas_sla)) {
            if (! $this->melebihi_sla) {
                $this->update(['melebihi_sla' => true]);
            }

            return true;
        }

        return false;
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }

    public function dokumenPermohonan(): HasMany
    {
        return $this->hasMany(DokumenPermohonan::class, 'permohonan_data_id');
    }

    public function formatDataDocuments(): HasMany
    {
        return $this->hasMany(DokumenPermohonan::class, 'permohonan_data_id')
            ->where('jenis', JenisDokumen::FormatData);
    }

    public function hasilDocuments(): HasMany
    {
        return $this->hasMany(DokumenPermohonan::class, 'permohonan_data_id')
            ->where('jenis', JenisDokumen::Hasil);
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class, 'permohonan_data_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for requests that have not been completed.
     */
    public function scopeBelumSelesai(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusPermohonan::Selesai);
    }

    /**
     * Scope for requests that have exceeded their SLA.
     */
    public function scopeMelebihiSla(Builder $query): Builder
    {
        return $query->where('melebihi_sla', true);
    }

    /**
     * Scope for search matching full-text search vector on PostgreSQL or ILIKE fallback.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if (empty(trim($term))) {
            return $query;
        }

        if (DB::getDriverName() === 'pgsql') {
            return $query->whereRaw("search_vector @@ plainto_tsquery('simple', ?)", [$term]);
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('nama_data', 'like', "%{$term}%")
                ->orWhere('sumber_data', 'like', "%{$term}%")
                ->orWhere('nomor_permohonan', 'like', "%{$term}%");
        });
    }
}
