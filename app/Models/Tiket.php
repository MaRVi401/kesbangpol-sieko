<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tiket extends Model
{
    use HasUuids;

    protected $table = 'tiket';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'users_id', 
        'layanan_id', 
        'petugas_id', 
        'no_tiket', 
        'lampiran',
        'deskripsi',
        'payload_draft',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'payload_draft' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id', 'uuid');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id', 'uuid');
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'uuid');
    }

    public function permohonanSkt(): HasOne
    {
        return $this->hasOne(PermohonanSkt::class, 'tiket_id', 'uuid');
    }

    public function beritaAcaraLapangan(): HasOne
    {
        return $this->hasOne(BeritaAcaraLapangan::class, 'tiket_id', 'uuid');
    }

    public function draftSkt(): HasOne
    {
        return $this->hasOne(DraftSkt::class, 'tiket_id', 'uuid');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusTiket::class, 'tiket_id', 'uuid');
    }

    public function komentar(): HasMany
    {
        return $this->hasMany(KomentarTiket::class, 'tiket_id', 'uuid');
    }

    public function formulirPermohonanBaruOrmas(): HasOne
    {
        return $this->hasOne(FormulirPermohonanBaruPencatatanOrmas::class, 'tiket_id', 'uuid');
    }
}