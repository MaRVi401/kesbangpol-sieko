<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', [
                'super_admin', 
                'pemohon', 
                'petugas_verifikasi_data', 
                'petugas_verifikasi_lapangan', 
                'analis_kebijakan_ahli_muda', 
                'kabid_kesbak', 
                'sekban', 
                'kaban'
            ]);
            $table->string('alamat')->nullable();
            $table->string('email')->unique();
            $table->string('no_wa')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });

        Schema::create('super_admin', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->nullable();
            $table->timestamps();
        });

        Schema::create('pemohon', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nik_ketua')->nullable();
            $table->string('nama_organisasi')->nullable();
            $table->enum('status_akun', ['pending', 'aktif', 'ditolak'])->default('pending');
            $table->timestamps();
        });

        Schema::create('petugas_verifikasi_data', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('petugas_verifikasi_lapangan', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('analis_kebijakan_ahli_muda', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('kabid_kesbak', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('sekban', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('kaban', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('nip')->unique();
            $table->timestamps();
        });

        Schema::create('layanan', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('nama');
            $table->boolean('status_arsip')->default(false);
            $table->enum('status_prioritas', ['rendah', 'sedang', 'tinggi']);
            $table->timestamps();
        });

        Schema::create('tiket', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('layanan_id')->constrained('layanan', 'uuid');
            $table->foreignUuid('petugas_id')->nullable()->constrained('users', 'uuid');
            $table->string('no_tiket')->unique();
            $table->string('lampiran')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('payload_draft')->nullable();
            $table->enum('status', [
                'draft', 
                'diajukan', 
                'pemeriksaan_kelengkapan', 
                'data_tidak_sesuai', 
                'persyaratan_lengkap',
                'verifikasi_lapangan',
                'pembuatan_berita_acara',
                'pembuatan_draft_skt',
                'menunggu_penandatanganan',
                'skt_disetujui',
                'penomoran_skt',
                'skt_diterbitkan',
                'skt_ditolak'
            ]);
            $table->timestamps();
        });

        Schema::create('berita_acara_lapangan', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('petugas_lapangan_id')->constrained('users', 'uuid');
            $table->date('tanggal_verifikasi');
            $table->text('catatan_lapangan');
            $table->boolean('is_sesuai')->default(false);
            $table->string('file_berita_acara_path')->nullable();
            $table->timestamps();
        });

        Schema::create('draft_skt', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('analis_id')->constrained('users', 'uuid');
            $table->string('no_skt_sementara')->nullable();
            $table->string('file_draft_path')->nullable();
            $table->boolean('is_ttd_kabid')->default(false);
            $table->boolean('is_ttd_sekban')->default(false);
            $table->boolean('is_ttd_kaban')->default(false);
            $table->timestamps();
        });

        Schema::create('permohonan_skt', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->string('nama_organisasi');
            $table->string('bidang_kegiatan');
            $table->text('alamat_sekretariat');
            $table->string('nama_ketua');
            $table->string('no_kontak');
            $table->string('akta_pendirian_path')->nullable();
            $table->string('sk_kemenkumham_path')->nullable();
            $table->string('surat_domisili_path')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_status_tiket', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->timestamps();
        });

        Schema::create('komentar_tiket', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('users_id')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->string('komentar');
            $table->timestamps();
        });

        Schema::create('log_keamanan', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->string('username_attempt');
            $table->enum('tipe_event', ['login_sukses', 'login_gagal', 'logout', 'lockout']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();
        });

        Schema::create('jejak_audit', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('users_id')->nullable()->constrained('users', 'uuid')->nullOnDelete();
            $table->enum('aksi', ['create', 'update', 'delete']);
            $table->string('nama_tabel');
            $table->uuid('record_id');
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jejak_audit');
        Schema::dropIfExists('log_keamanan');
        Schema::dropIfExists('riwayat_status_tiket');
        Schema::dropIfExists('komentar_tiket');
        Schema::dropIfExists('permohonan_skt');
        Schema::dropIfExists('draft_skt');
        Schema::dropIfExists('berita_acara_lapangan');
        Schema::dropIfExists('tiket');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('kaban');
        Schema::dropIfExists('sekban');
        Schema::dropIfExists('kabid_kesbak');
        Schema::dropIfExists('analis_kebijakan_ahli_muda');
        Schema::dropIfExists('petugas_verifikasi_lapangan');
        Schema::dropIfExists('petugas_verifikasi_data');
        Schema::dropIfExists('pemohon');
        Schema::dropIfExists('super_admin');
        Schema::dropIfExists('users');
    }
};