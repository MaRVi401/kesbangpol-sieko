<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TiketSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('riwayat_status_tiket')->truncate();
        DB::table('permohonan_skt')->truncate();
        DB::table('tiket')->truncate();

        $layanan = DB::table('layanan')->first();
        $pemohon = DB::table('users')->where('role', 'pemohon')->first();

        if (!$layanan || !$pemohon) {
            $this->command->error("Gagal: Pastikan ServiceSeeder & UserSeeder sudah berjalan!");
            return;
        }

        $tiketUuid = (string) Str::uuid();

        // 1. Insert Tiket Utama
        DB::table('tiket')->insert([
            'uuid'       => $tiketUuid,
            'users_id'   => $pemohon->uuid,
            'layanan_id' => $layanan->uuid,
            'no_tiket'   => 'SKT-' . date('Ymd') . '-0001',
            'status'     => 'diajukan', // Status Enum Baru
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert Detail Permohonan SKT
        DB::table('permohonan_skt')->insert([
            'uuid'               => (string) Str::uuid(),
            'tiket_id'           => $tiketUuid,
            'nama_organisasi'    => 'Ormas Bakti Negeri',
            'bidang_kegiatan'    => 'Sosial dan Kemanusiaan',
            'alamat_sekretariat' => 'Jl. Perintis Kemerdekaan No. 10',
            'nama_ketua'         => $pemohon->nama,
            'no_kontak'          => '081234567890',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // 3. Insert Riwayat Status Tiket
        DB::table('riwayat_status_tiket')->insert([
            'uuid'              => (string) Str::uuid(),
            'tiket_id'          => $tiketUuid,
            'users_id'          => $pemohon->uuid,
            'status_sebelumnya' => null,
            'status_baru'       => 'diajukan',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        Schema::enableForeignKeyConstraints();
        $this->command->info("Selesai! 1 Tiket Dummy Pengajuan SKT berhasil dibuat.");
    }
}