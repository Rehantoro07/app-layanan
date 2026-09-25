<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pusdatin = UnitKerja::where('kode', 'PUSDATIN')->first();
        $dpfrzr = UnitKerja::where('kode', 'DPFRZR')->first();
        $difrzr = UnitKerja::where('kode', 'DIFRZR')->first();
        $bpik = UnitKerja::where('kode', 'BPIK')->first();
        $dp2frzr = UnitKerja::where('kode', 'DP2FRZR')->first();

        $defaultPassword = Hash::make('password');

        $users = [
            // 1. Admin (Staf TI)
            [
                'nama' => 'Robby Rehantoro',
                'nip' => '199505122022031001',
                'email' => 'asd@gmail.com',
                'password' => $defaultPassword,
                'role' => UserRole::Admin,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin?->id,
            ],
            [
                'nama' => 'Administrator TI Pusdatin',
                'nip' => '199001012015031001',
                'email' => 'admin@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Admin,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin?->id,
            ],

            // 2. Petugas (Staf Pusat Data)
            [
                'nama' => 'Budi Santoso, S.Kom',
                'nip' => '199203152018011002',
                'email' => 'petugas@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Petugas,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin?->id,
            ],
            [
                'nama' => 'Siti Rahmawati, S.Tr.Kom',
                'nip' => '199607242022032004',
                'email' => 'siti.rahmawati@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Petugas,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin?->id,
            ],

            // 3. Pimpinan (Kepala Pusat Data)
            [
                'nama' => 'Dr. Ir. Hendra Kusuma, M.Eng',
                'nip' => '197508201998031001',
                'email' => 'pimpinan@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Pimpinan,
                'is_active' => true,
                'unit_kerja_id' => $pusdatin?->id,
            ],

            // 4. Pemohon (Pegawai BAPETEN)
            [
                'nama' => 'Ahmad Fauzi, S.T.',
                'nip' => '199411052020121003',
                'email' => 'pemohon@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Pemohon,
                'is_active' => true,
                'unit_kerja_id' => $difrzr?->id,
            ],
            [
                'nama' => 'Dewi Lestari, M.Si',
                'nip' => '199308182019022002',
                'email' => 'dewi.lestari@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Pemohon,
                'is_active' => true,
                'unit_kerja_id' => $dpfrzr?->id,
            ],
            [
                'nama' => 'Rian Hidayat, S.E.',
                'nip' => '199104102017121001',
                'email' => 'rian.hidayat@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Pemohon,
                'is_active' => true,
                'unit_kerja_id' => $bpik?->id,
            ],
            [
                'nama' => 'Nurul Hidayati, S.H.',
                'nip' => '199512012021042001',
                'email' => 'nurul.hidayati@bapeten.go.id',
                'password' => $defaultPassword,
                'role' => UserRole::Pemohon,
                'is_active' => true,
                'unit_kerja_id' => $dp2frzr?->id,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
