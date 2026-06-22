<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanSkt extends Model
{
    use HasUuids;

    protected $table = 'permohonan_skt';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tiket_id',
        'nama_organisasi',
        'bidang_kegiatan',
        'alamat_sekretariat',
        'nama_ketua',
        'no_kontak',
        'nama_notaris',
        'tanggal_akte',
        'akta_pendirian_path',
        'nomor_sk_kemenkumham',
        'tanggal_sk_kemenkumham',
        'sk_kemenkumham_path',
        'surat_domisili_path',
        'file_ad_art_path',
        'file_program_kerja_path',
        'nomor_sk_terlapor',
        'tanggal_berlaku_sk_terlapor',
        'file_sk_terlapor_path',
        'file_npwp_path',
        'file_foto_kantor_path',
        'file_surat_mandat_path',
        'periode_sk_kepengurusan',
        'file_sk_kepengurusan_path'
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }
}