<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PetugasVerifikasiLapangan extends Model {
    use HasUuids;
    protected $table = 'petugas_verifikasi_lapangan';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['users_id', 'nip'];
    public function user() { return $this->belongsTo(User::class, 'users_id', 'uuid'); }
}