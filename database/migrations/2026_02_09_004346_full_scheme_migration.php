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
            $table->string('kta_path')->nullable();
            $table->string('surat_rekomendasi_path')->nullable();
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
                'menunggu_lampiran',
                'pemeriksaan_kelengkapan',
                'data_tidak_sesuai',
                'persyaratan_lengkap',
                'verifikasi_lapangan',
                'pembuatan_berita_acara',
                'review_berita_acara',
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
            $table->json('foto_dokumentasi')->nullable();
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
            $table->string('nama_notaris')->nullable();
            $table->date('tanggal_akte')->nullable();
            $table->string('akta_pendirian_path')->nullable();
            $table->string('nomor_sk_kemenkumham')->nullable();
            $table->date('tanggal_sk_kemenkumham')->nullable();
            $table->string('sk_kemenkumham_path')->nullable();
            $table->string('surat_domisili_path')->nullable();
            $table->string('file_ad_art_path')->nullable();
            $table->string('file_program_kerja_path')->nullable();
            $table->string('nomor_sk_terlapor')->nullable();
            $table->date('tanggal_berlaku_sk_terlapor')->nullable();
            $table->string('file_sk_terlapor_path')->nullable();
            $table->string('file_npwp_path')->nullable();
            $table->string('file_foto_kantor_path')->nullable();
            $table->string('file_surat_mandat_path')->nullable();
            $table->string('periode_sk_kepengurusan')->nullable();
            $table->string('file_sk_kepengurusan_path')->nullable();
            $table->timestamps();
        });

        Schema::create('formulir_permohonan_baru_pencatatan_ormas', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('tiket_id')->constrained('tiket', 'uuid')->cascadeOnDelete();
            $table->string('nomor')->nullable();
            $table->string('perihal')->nullable();
            $table->string('nama_pemohon');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jabatan_pemohon');
            $table->text('alamat_rumah');
            $table->string('nomor_ktp', 16);
            $table->string('nama_organisasi');
            $table->string('nomor_npwp_organisasi')->nullable();
            $table->string('sifat_kekhususan');
            $table->string('nomor_akte_pendirian');
            $table->text('alamat_organisasi');
            $table->text('alamat_sekretariat');
            $table->string('nama_ketua');
            $table->string('nama_sekretaris');
            $table->string('nama_bendahara');
            $table->integer('jumlah_anggota')->default(0);
            $table->integer('jumlah_cabang')->default(0);
            $table->date('tanggal_permohonan')->nullable();
            $table->string('file_kop_surat')->nullable();
            $table->string('file_tanda_tangan_pemohon')->nullable();
            $table->timestamps();
        });

        Schema::create('biodata_pengurus_ormas', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('formulir_id')->constrained('formulir_permohonan_baru_pencatatan_ormas', 'uuid')->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Pria', 'Wanita']);
            $table->enum('status_perkawinan', ['Kawin', 'Belum Kawin', 'Janda', 'Duda']);
            $table->string('agama');
            $table->string('utusan_organisasi')->nullable();
            $table->string('jabatan');
            $table->text('alamat_organisasi')->nullable();
            $table->string('telepon_organisasi')->nullable();
            $table->text('alamat_rumah');
            $table->string('telepon_rumah_hp');
            $table->string('pendidikan_terakhir');
            $table->json('riwayat_organisasi')->nullable();
            $table->string('hobi')->nullable();
            $table->string('foto_resmi');
            $table->string('file_ktp_path')->nullable();
            $table->string('file_tanda_tangan')->nullable();
            $table->date('tanggal_pengisian')->nullable();
            $table->timestamps();
        });

        Schema::create('surat_pernyataan_ormas', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('formulir_id')->constrained('formulir_permohonan_baru_pencatatan_ormas', 'uuid')->cascadeOnDelete();
            $table->string('nama_ketua');
            $table->string('nomor_ktp_ketua', 16);
            $table->string('nama_sekretaris');
            $table->string('nomor_ktp_sekretaris', 16);
            $table->date('tanggal_surat_pernyataan')->nullable();
            $table->string('file_ttd_ketua_materai')->nullable();
            $table->string('file_ttd_sekretaris')->nullable();
            $table->timestamps();
        });

        Schema::create('formulir_isian_ormas', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('formulir_id')->constrained('formulir_permohonan_baru_pencatatan_ormas', 'uuid')->cascadeOnDelete();
            $table->string('nama_organisasi');
            $table->string('bidang_kegiatan');
            $table->string('ruang_lingkup');
            $table->text('alamat_sekretariat');
            $table->string('tempat_pendirian');
            $table->date('tanggal_pendirian');
            $table->string('asas_ciri_organisasi');
            $table->text('tujuan_organisasi');
            $table->text('nama_pendiri');
            $table->string('nama_pembina')->nullable();
            $table->string('nama_penasehat')->nullable();
            $table->string('nama_ketua');
            $table->string('nama_sekretaris');
            $table->string('nama_bendahara');
            $table->string('masa_bhakti_kepengurusan');
            $table->string('keputusan_tertinggi_organisasi');
            $table->string('unit_sayap_otonom')->nullable();
            $table->string('usaha_organisasi')->nullable();
            $table->string('sumber_keuangan');
            $table->string('file_logo_organisasi')->nullable();
            $table->string('file_bendera_organisasi')->nullable();
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
        Schema::dropIfExists('komentar_tiket');
        Schema::dropIfExists('riwayat_status_tiket');
        Schema::dropIfExists('formulir_isian_ormas');
        Schema::dropIfExists('surat_pernyataan_ormas');
        Schema::dropIfExists('biodata_pengurus_ormas');
        Schema::dropIfExists('formulir_permohonan_baru_pencatatan_ormas');
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