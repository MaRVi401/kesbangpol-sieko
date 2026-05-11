<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        $tables = [
            'users', 'super_admin', 'pemohon', 'petugas_verifikasi_data', 
            'petugas_verifikasi_lapangan', 'analis_kebijakan_ahli_muda', 
            'kabid_kesbak', 'sekban', 'kaban'
        ];
        foreach ($tables as $table) { DB::table($table)->truncate(); }
        
        Schema::enableForeignKeyConstraints();

        // Data array untuk looping pembuatan user
        $users = [
            ['nama' => 'Jack Super', 'username' => 'superadmin', 'role' => 'super_admin', 'tabel_relasi' => 'super_admin', 'kolom' => ['nip' => '199001012024011001']],
            ['nama' => 'Budi Pemohon', 'username' => 'pemohon', 'role' => 'pemohon', 'tabel_relasi' => 'pemohon', 'kolom' => ['nik_ketua' => '3212000000000001', 'nama_organisasi' => 'Ormas Bakti Negeri', 'status_akun' => 'aktif']],
            ['nama' => 'Siti Verifikator Data', 'username' => 'petugas_data', 'role' => 'petugas_verifikasi_data', 'tabel_relasi' => 'petugas_verifikasi_data', 'kolom' => ['nip' => '198501012010011001']],
            ['nama' => 'Agus Lapangan', 'username' => 'petugas_lapangan', 'role' => 'petugas_verifikasi_lapangan', 'tabel_relasi' => 'petugas_verifikasi_lapangan', 'kolom' => ['nip' => '198602022011011002']],
            ['nama' => 'Dewi Analis', 'username' => 'analis', 'role' => 'analis_kebijakan_ahli_muda', 'tabel_relasi' => 'analis_kebijakan_ahli_muda', 'kolom' => ['nip' => '198003032005011003']],
            ['nama' => 'Bapak Kabid', 'username' => 'kabid', 'role' => 'kabid_kesbak', 'tabel_relasi' => 'kabid_kesbak', 'kolom' => ['nip' => '197504042000011004']],
            ['nama' => 'Bapak Sekban', 'username' => 'sekban', 'role' => 'sekban', 'tabel_relasi' => 'sekban', 'kolom' => ['nip' => '197005051998011005']],
            ['nama' => 'Bapak Kaban', 'username' => 'kaban', 'role' => 'kaban', 'tabel_relasi' => 'kaban', 'kolom' => ['nip' => '196506061990011006']],
        ];

        foreach ($users as $u) {
            $uuid = (string) Str::uuid();
            
            DB::table('users')->insert([
                'uuid' => $uuid,
                'nama' => $u['nama'],
                'username' => $u['username'],
                'password' => Hash::make('password'), // password default
                'role' => $u['role'],
                'email' => $u['username'] . '@mail.com',
                'no_wa' => '0812' . rand(10000000, 99999999),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert ke tabel relasi rolenya
            $relasiData = array_merge([
                'uuid' => (string) Str::uuid(),
                'users_id' => $uuid,
                'created_at' => now(),
                'updated_at' => now(),
            ], $u['kolom']);

            DB::table($u['tabel_relasi'])->insert($relasiData);
        }

        $this->command->info('Berhasil: 8 User dengan Role berbeda telah dibuat.');
    }
}