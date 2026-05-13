<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPernyataanOrmas extends Model
{
    use HasUuids;

    protected $table = 'surat_pernyataan_ormas';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'formulir_id', 'nama_ketua', 'nomor_ktp_ketua', 'nama_sekretaris', 
        'nomor_ktp_sekretaris', 'tanggal_surat_pernyataan', 'file_ttd_ketua_materai', 
        'file_ttd_sekretaris'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat_pernyataan' => 'date',
        ];
    }

    public function formulirPermohonan(): BelongsTo
    {
        return $this->belongsTo(FormulirPermohonanBaruPencatatanOrmas::class, 'formulir_id', 'uuid');
    }
}