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

    protected $fillable = [
        'tiket_id',
        'analis_id',
        'no_skt_sementara',
        'file_draft_path',
        'is_ttd_kabid',
        'is_ttd_sekban',
        'is_ttd_kaban'
    ];

    protected function casts(): array
    {
        return [
            'is_ttd_kabid' => 'boolean',
            'is_ttd_sekban' => 'boolean',
            'is_ttd_kaban' => 'boolean',
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