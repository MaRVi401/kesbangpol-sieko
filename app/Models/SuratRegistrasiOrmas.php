<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratRegistrasiOrmas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'surat_registrasi_ormas';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tiket_id',
        'analis_id',
        'nomor_surat_registrasi',
        'nama_organisasi_pemohon',
        'nomor_surat_pemohon',
        'tanggal_surat_pemohon',
        'perihal_surat_pemohon',
        'nama_ormas',
        'tanggal_berdiri',
        'bidang_kegiatan',
        'npwp',
        'sk_kepengurusan_penerbit',
        'sk_kepengurusan_nomor',
        'sk_kepengurusan_periode',
        'nama_ketua',
        'nama_sekretaris',
        'nama_bendahara',
        'akta_notaris_keterangan',
        'akta_notaris_nama',
        'akta_notaris_nomor',
        'akta_notaris_tanggal',
        'sk_kemenkumham_keterangan',
        'sk_kemenkumham_nomor',
        'sk_kemenkumham_tanggal',
        'alamat_sekretariat',
        'masa_berlaku_sampai',
        'tanggal_ditetapkan',
        'penandatangan_nama',
        'penandatangan_jabatan',
        'penandatangan_pangkat',
        'penandatangan_nip',
        'jenis_pencatatan',
        'file_surat_ttd_basah_path',
    ];

    protected $casts = [
        'tanggal_surat_pemohon' => 'date',
        'tanggal_berdiri' => 'date',
        'akta_notaris_tanggal' => 'date',
        'sk_kemenkumham_tanggal' => 'date',
        'masa_berlaku_sampai' => 'date',
        'tanggal_ditetapkan' => 'date',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }

    public function analis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analis_id', 'uuid');
    }
}