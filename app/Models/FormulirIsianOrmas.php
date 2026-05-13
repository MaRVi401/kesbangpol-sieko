<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulirIsianOrmas extends Model
{
    use HasUuids;

    protected $table = 'formulir_isian_ormas';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'formulir_id', 'nama_organisasi', 'bidang_kegiatan', 'ruang_lingkup', 
        'alamat_sekretariat', 'tempat_pendirian', 'tanggal_pendirian', 'asas_ciri_organisasi', 
        'tujuan_organisasi', 'nama_pendiri', 'nama_pembina', 'nama_penasehat', 
        'nama_ketua', 'nama_sekretaris', 'nama_bendahara', 'masa_bhakti_kepengurusan', 
        'keputusan_tertinggi_organisasi', 'unit_sayap_otonom', 'usaha_organisasi', 
        'sumber_keuangan', 'file_logo_organisasi', 'file_bendera_organisasi'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pendirian' => 'date',
        ];
    }

    public function formulirPermohonan(): BelongsTo
    {
        return $this->belongsTo(FormulirPermohonanBaruPencatatanOrmas::class, 'formulir_id', 'uuid');
    }
}