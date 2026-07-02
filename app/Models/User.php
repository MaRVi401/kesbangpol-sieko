<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'alamat',
        'email',
        'no_wa',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Role Relations
    public function superAdmin() { return $this->hasOne(SuperAdmin::class, 'users_id', 'uuid'); }
    public function pemohon() { return $this->hasOne(Pemohon::class, 'users_id', 'uuid'); }
    public function petugasVerifikasiData() { return $this->hasOne(PetugasVerifikasiData::class, 'users_id', 'uuid'); }
    public function petugasVerifikasiLapangan() { return $this->hasOne(PetugasVerifikasiLapangan::class, 'users_id', 'uuid'); }
    public function analisKebijakanAhliMuda() { return $this->hasOne(AnalisKebijakanAhliMuda::class, 'users_id', 'uuid'); }
    public function kabidKesbak() { return $this->hasOne(KabidKesbak::class, 'users_id', 'uuid'); }
    public function sekban() { return $this->hasOne(Sekban::class, 'users_id', 'uuid'); }
    public function kaban() { return $this->hasOne(Kaban::class, 'users_id', 'uuid'); }

    // Ticket Relations
    public function tiketDibuat() { return $this->hasMany(Tiket::class, 'users_id', 'uuid'); }
    public function tiketDitangani() { return $this->hasMany(Tiket::class, 'petugas_id', 'uuid'); }
}