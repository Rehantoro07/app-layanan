<?php

namespace Database\Seeders;

use App\Models\PermohonanData;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petugas1 = User::where('email', 'petugas@bapeten.go.id')->first();
        $petugas2 = User::where('email', 'siti.rahmawati@bapeten.go.id')->first();
        $pimpinan = User::where('email', 'pimpinan@bapeten.go.id')->first();
        $pemohon = User::where('email', 'pemohon@bapeten.go.id')->first();

        $pSelesai = PermohonanData::where('nomor_permohonan', 'PD/2026/09/0001')->first();
        $pKonfirmasi = PermohonanData::where('nomor_permohonan', 'PD/2026/09/0002')->first();
        $pMelebihiSla = PermohonanData::where('nomor_permohonan', 'PD/2026/09/0003')->first();
        $pDiajukan = PermohonanData::where('nomor_permohonan', 'PD/2026/09/0005')->first();

        $notifications = [];

        // 1. Notifikasi Peringatan SLA Melebihi Batas Waktu -> Dikirim ke Petugas & Pimpinan
        if ($pMelebihiSla) {
            $peringatanData = json_encode([
                'title' => 'Peringatan SLA Terlewati!',
                'body' => "Permohonan {$pMelebihiSla->nomor_permohonan} ({$pMelebihiSla->nama_data}) telah melewati batas waktu SLA 3 hari.",
                'icon' => 'heroicon-o-exclamation-triangle',
                'iconColor' => 'danger',
                'status' => 'danger',
                'permohonan_id' => $pMelebihiSla->id,
                'nomor_permohonan' => $pMelebihiSla->nomor_permohonan,
            ]);

            // Untuk Petugas pengampu
            if ($petugas2) {
                $notifications[] = [
                    'id' => (string) Str::uuid(),
                    'type' => 'App\Notifications\SlaWarningNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $petugas2->id,
                    'data' => $peringatanData,
                    'read_at' => null,
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now()->subDays(2),
                ];
            }

            // Untuk Pimpinan (Kepala Pusat Data)
            if ($pimpinan) {
                $notifications[] = [
                    'id' => (string) Str::uuid(),
                    'type' => 'App\Notifications\SlaWarningNotification',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $pimpinan->id,
                    'data' => $peringatanData,
                    'read_at' => null,
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now()->subDays(2),
                ];
            }
        }

        // 2. Notifikasi Permohonan Menunggu Konfirmasi -> Dikirim ke Pimpinan
        if ($pKonfirmasi && $pimpinan) {
            $notifications[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\MenungguKonfirmasiNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $pimpinan->id,
                'data' => json_encode([
                    'title' => 'Permohonan Menunggu Konfirmasi',
                    'body' => "Petugas telah mengunggah hasil pengolahan data untuk {$pKonfirmasi->nomor_permohonan}. Menunggu persetujuan Anda.",
                    'icon' => 'heroicon-o-clock',
                    'iconColor' => 'warning',
                    'status' => 'warning',
                    'permohonan_id' => $pKonfirmasi->id,
                    'nomor_permohonan' => $pKonfirmasi->nomor_permohonan,
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ];
        }

        // 3. Notifikasi Permohonan Baru Masuk -> Dikirim ke Petugas
        if ($pDiajukan && $petugas1) {
            $notifications[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\PermohonanBaruNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $petugas1->id,
                'data' => json_encode([
                    'title' => 'Permohonan Data Baru Masuk',
                    'body' => "Permohonan baru {$pDiajukan->nomor_permohonan} telah diajukan oleh pemohon dan siap untuk diproses.",
                    'icon' => 'heroicon-o-inbox-arrow-down',
                    'iconColor' => 'info',
                    'status' => 'info',
                    'permohonan_id' => $pDiajukan->id,
                    'nomor_permohonan' => $pDiajukan->nomor_permohonan,
                ]),
                'read_at' => null,
                'created_at' => Carbon::now()->subHours(8),
                'updated_at' => Carbon::now()->subHours(8),
            ];
        }

        // 4. Notifikasi Permohonan Selesai -> Dikirim ke Pemohon
        if ($pSelesai && $pemohon) {
            $notifications[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\PermohonanSelesaiNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $pemohon->id,
                'data' => json_encode([
                    'title' => 'Permohonan Data Telah Selesai',
                    'body' => "Permohonan data {$pSelesai->nomor_permohonan} telah disetujui pimpinan. Dokumen hasil pengolahan data dapat diunduh sekarang.",
                    'icon' => 'heroicon-o-check-circle',
                    'iconColor' => 'success',
                    'status' => 'success',
                    'permohonan_id' => $pSelesai->id,
                    'nomor_permohonan' => $pSelesai->nomor_permohonan,
                ]),
                'read_at' => Carbon::now()->subDays(1), // Sudah dibaca oleh pemohon
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ];
        }

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}
