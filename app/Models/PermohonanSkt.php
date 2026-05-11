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
        'akta_pendirian_path',
        'sk_kemenkumham_path',
        'surat_domisili_path'
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }
}