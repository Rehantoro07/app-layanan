<?php

namespace Tests\Feature;

use App\Models\PermohonanData;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_each_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/pimpinan')->assertRedirect('/pimpinan/login');
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    }

    public function test_petugas_can_access_admin_panel_but_not_pimpinan_or_portal(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)
            ->get('/admin')
            ->assertSuccessful();

        $this->actingAs($petugas)
            ->get('/pimpinan')
            ->assertForbidden();

        $this->actingAs($petugas)
            ->get('/portal')
            ->assertForbidden();
    }

    public function test_pimpinan_can_access_pimpinan_panel_but_not_admin_or_portal(): void
    {
        $pimpinan = User::factory()->pimpinan()->create();

        $this->actingAs($pimpinan)
            ->get('/pimpinan')
            ->assertSuccessful();

        $this->actingAs($pimpinan)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($pimpinan)
            ->get('/portal')
            ->assertForbidden();
    }

    public function test_pemohon_can_access_portal_panel_but_not_admin_or_pimpinan(): void
    {
        $pemohon = User::factory()->pemohon()->create();

        $this->actingAs($pemohon)
            ->get('/portal')
            ->assertSuccessful();

        $this->actingAs($pemohon)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($pemohon)
            ->get('/pimpinan')
            ->assertForbidden();
    }

    public function test_inactive_user_cannot_access_any_panel(): void
    {
        $inactiveAdmin = User::factory()->admin()->inactive()->create();

        $this->actingAs($inactiveAdmin)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_view_permohonan_data_resource(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/permohonan-data')
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get('/admin/unit-kerjas')
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertSuccessful();
    }

    public function test_admin_can_view_create_and_edit_permohonan_data_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $unitKerja = UnitKerja::factory()->create();
        $permohonan = PermohonanData::factory()->create([
            'pemohon_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/permohonan-data/create')
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get("/admin/permohonan-data/{$permohonan->id}")
            ->assertSuccessful();

        $this->actingAs($admin)
            ->get("/admin/permohonan-data/{$permohonan->id}/edit")
            ->assertSuccessful();
    }
}
