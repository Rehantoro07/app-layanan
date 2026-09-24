<?php

use App\Enums\StatusPermohonan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permohonan_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_permohonan')->unique();
            $table->foreignUuid('pemohon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('unit_kerja_id')->constrained('unit_kerja')->cascadeOnDelete();
            $table->foreignUuid('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('pimpinan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_permohonan');
            $table->string('nama_data');
            $table->string('sumber_data'); // nama aplikasi / sumber data
            $table->string('periode_data');
            $table->string('status')->default(StatusPermohonan::Draft->value)->index();
            $table->text('catatan_hasil')->nullable();
            $table->timestampTz('dikirim_pada')->nullable();
            $table->timestampTz('batas_sla')->nullable();
            $table->timestampTz('dikonfirmasi_pada')->nullable();
            $table->timestampTz('selesai_pada')->nullable();
            $table->boolean('melebihi_sla')->default(false)->index();
            $table->timestampsTz();
        });

        // PostgreSQL specific optimizations: tsvector with GIN index & partial index for SLA monitoring
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE permohonan_data ADD COLUMN search_vector tsvector GENERATED ALWAYS AS (to_tsvector('simple', coalesce(nama_data, '') || ' ' || coalesce(sumber_data, '') || ' ' || coalesce(nomor_permohonan, ''))) STORED");
            DB::statement('CREATE INDEX permohonan_data_search_vector_idx ON permohonan_data USING GIN (search_vector)');
            DB::statement("CREATE INDEX permohonan_data_sla_monitoring_idx ON permohonan_data (status, batas_sla) WHERE status != 'selesai'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_data');
    }
};
