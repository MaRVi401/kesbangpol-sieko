<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftSkt extends Model
{
    use HasUuids;

    protected $table = 'draft_skt';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    // SESUAIKAN BAGIAN INI
    protected $fillable = [
        'tiket_id',
        'analis_id',
        'no_skt_sementara',
        'file_draft_path',
        'is_paraf_analis', // Tambahan baru
        'is_paraf_kabid',  // Perubahan nama
        'is_paraf_sekban', // Perubahan nama
        'is_ttd_kaban'     // Tetap
    ];

    // SESUAIKAN BAGIAN INI
    protected function casts(): array
    {
        return [
            'is_paraf_analis' => 'boolean', // Tambahan baru
            'is_paraf_kabid'  => 'boolean', // Perubahan nama
            'is_paraf_sekban' => 'boolean', // Perubahan nama
            'is_ttd_kaban'    => 'boolean', // Tetap
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }

    public function analis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analis_id', 'uuid');
    }
}