<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormulirPermohonanBaruPencatatanOrmas extends Model
{
    use HasUuids;

    protected $table = 'formulir_permohonan_baru_pencatatan_ormas';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tiket_id','nomor', 'perihal', 'nama_pemohon', 'tempat_lahir', 'tanggal_lahir', 
        'jabatan_pemohon', 'alamat_rumah', 'nomor_ktp', 'nama_organisasi', 
        'nomor_npwp_organisasi', 'sifat_kekhususan', 'nomor_akte_pendirian', 
        'alamat_organisasi', 'alamat_sekretariat', 'nama_ketua', 'nama_sekretaris', 
        'nama_bendahara', 'jumlah_anggota', 'jumlah_cabang', 'tanggal_permohonan', 
        'file_kop_surat', 'file_tanda_tangan_pemohon',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_permohonan' => 'date',
            'jumlah_anggota' => 'integer',
            'jumlah_cabang' => 'integer',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }

    public function biodataPengurus(): HasMany
    {
        return $this->hasMany(BiodataPengurusOrmas::class, 'formulir_id', 'uuid');
    }

    public function suratPernyataan(): HasOne
    {
        return $this->hasOne(SuratPernyataanOrmas::class, 'formulir_id', 'uuid');
    }

    public function formulirIsian(): HasOne
    {
        return $this->hasOne(FormulirIsianOrmas::class, 'formulir_id', 'uuid');
    }
}