<?php

namespace Tests\Feature;

use App\Enums\JenisDokumen;
use App\Enums\StatusPermohonan;
use App\Enums\UserRole;
use App\Models\DokumenPermohonan;
use App\Models\PermohonanData;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermohonanDataModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_unit_kerja_with_uuid(): void
    {
        $unitKerja = UnitKerja::factory()->create([
            'kode' => 'UK-TEST',
            'nama' => 'Pusat Data dan Informasi',
        ]);

        $this->assertNotEmpty($unitKerja->id);
        $this->assertIsString($unitKerja->id);
        $this->assertDatabaseHas('unit_kerja', [
            'kode' => 'UK-TEST',
            'nama' => 'Pusat Data dan Informasi',
        ]);
    }

    public function test_can_create_user_with_roles_and_unit_kerja(): void
    {
        $unitKerja = UnitKerja::factory()->create();

        $admin = User::factory()->admin()->withUnitKerja($unitKerja)->create();
        $this->assertTrue($admin->isAdmin());
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertEquals($unitKerja->id, $admin->unitKerja->id);
        $this->assertEquals($admin->nama, $admin->name);

        $petugas = User::factory()->petugas()->create();
        $this->assertTrue($petugas->isPetugas());

        $pimpinan = User::factory()->pimpinan()->create();
        $this->assertTrue($pimpinan->isPimpinan());

        $pemohon = User::factory()->pemohon()->create();
        $this->assertTrue($pemohon->isPemohon());
    }

    public function test_can_create_permohonan_data_with_automatic_numbering_and_sla(): void
    {
        $pemohon = User::factory()->pemohon()->create();
        $unitKerja = UnitKerja::factory()->create();

        $permohonan = PermohonanData::create([
            'pemohon_id' => $pemohon->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama_data' => 'Data Pemantauan Dosis',
            'sumber_data' => 'Si-INTAN',
            'periode_data' => '2025',
            'status' => StatusPermohonan::Draft,
        ]);

        $this->assertNotEmpty($permohonan->nomor_permohonan);
        $this->assertStringStartsWith('PD/', $permohonan->nomor_permohonan);
        $this->assertEquals(StatusPermohonan::Draft, $permohonan->status);

        // Transition to Diajukan calculates SLA (3 days)
        $permohonan->update(['status' => StatusPermohonan::Diajukan]);
        $this->assertNotNull($permohonan->fresh()->dikirim_pada);
        $this->assertNotNull($permohonan->fresh()->batas_sla);
        $this->assertEquals(
            $permohonan->fresh()->dikirim_pada->addDays(3)->format('Y-m-d'),
            $permohonan->fresh()->batas_sla->format('Y-m-d')
        );
    }

    public function test_permohonan_relationships(): void
    {
        $pemohon = User::factory()->pemohon()->create();
        $petugas = User::factory()->petugas()->create();
        $unitKerja = UnitKerja::factory()->create();

        $permohonan = PermohonanData::factory()->create([
            'pemohon_id' => $pemohon->id,
            'petugas_id' => $petugas->id,
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $dokumen = DokumenPermohonan::factory()->formatData()->create([
            'permohonan_data_id' => $permohonan->id,
            'diunggah_oleh' => $pemohon->id,
        ]);

        $riwayat = $permohonan->catatRiwayat(StatusPermohonan::Diajukan, $pemohon, 'Mengajukan dokumen');

        $this->assertEquals($pemohon->id, $permohonan->pemohon->id);
        $this->assertEquals($petugas->id, $permohonan->petugas->id);
        $this->assertEquals($unitKerja->id, $permohonan->unitKerja->id);
        $this->assertCount(1, $permohonan->dokumenPermohonan);
        $this->assertCount(1, $permohonan->formatDataDocuments);
        $this->assertCount(1, $permohonan->riwayatStatus);
        $this->assertEquals(JenisDokumen::FormatData, $dokumen->fresh()->jenis);
        $this->assertEquals(StatusPermohonan::Diajukan, $riwayat->fresh()->status_ke);
    }
}
