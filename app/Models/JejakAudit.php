<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JejakAudit extends Model 
{
    use HasUuids;

    protected $table = 'jejak_audit';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'users_id', 
        'aksi', 
        'nama_tabel', 
        'record_id', 
        'data_lama', 
        'data_baru', 
        'ip_address'
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
        ];
    }

    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'users_id', 'uuid'); 
    }
}