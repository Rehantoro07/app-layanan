<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokumen_permohonan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('permohonan_data_id')->constrained('permohonan_data')->cascadeOnDelete();
            $table->foreignUuid('diunggah_oleh')->constrained('users')->cascadeOnDelete();
            $table->string('jenis')->index(); // format_data | hasil
            $table->string('nama_file');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('ukuran'); // bytes
            $table->jsonb('metadata')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_permohonan');
    }
};
