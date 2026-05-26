<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeritaAcaraLapangan extends Model
{
    use HasUuids;

    protected $table = 'berita_acara_lapangan';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    
    protected $fillable = [
        'tiket_id',
        'petugas_lapangan_id',
        'tanggal_verifikasi',
        'catatan_lapangan',
        'is_sesuai',
        'foto_dokumentasi', 
        'file_berita_acara_path'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_verifikasi' => 'date',
            'is_sesuai' => 'boolean',
            'foto_dokumentasi' => 'array', 
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }

    public function petugasLapangan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_lapangan_id', 'uuid');
    }
}