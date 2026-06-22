<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiodataPengurusOrmas extends Model
{
    use HasUuids;

    protected $table = 'biodata_pengurus_ormas';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'formulir_id', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 
        'jenis_kelamin', 'status_perkawinan', 'agama', 'utusan_organisasi', 
        'jabatan', 'alamat_organisasi', 'telepon_organisasi', 'alamat_rumah', 
        'telepon_rumah_hp', 'pendidikan_terakhir', 'riwayat_organisasi', 'hobi', 
        'foto_resmi', 'file_ktp_path', 'file_tanda_tangan', 'tanggal_pengisian'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_pengisian' => 'date',
            'riwayat_organisasi' => 'array',
        ];
    }

    public function formulirPermohonan(): BelongsTo
    {
        return $this->belongsTo(FormulirPermohonanBaruPencatatanOrmas::class, 'formulir_id', 'uuid');
    }
}