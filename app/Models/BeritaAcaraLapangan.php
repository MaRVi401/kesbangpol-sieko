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
        'nomor_berita_acara',
        'tanggal_kunjungan',
        'keberadaan_sekretariat',
        'papan_nama_terpasang',
        'sekretariat_aktif',
        'kepengurusan_ditemui',
        'dokumen_tersedia',
        'kegiatan_berjalan',
        'kondisi_sekretariat',
        'keterangan_hasil',
        'kesimpulan_sekretariat',
        'kesimpulan_kepengurusan',
        'ketua_tim_id',
        'anggota_tim',
        'foto_dokumentasi',
        'is_sesuai',
        'file_berita_acara_path'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
            'sekretariat_aktif' => 'boolean',
            'kepengurusan_ditemui' => 'boolean',
            'dokumen_tersedia' => 'boolean',
            'kegiatan_berjalan' => 'boolean',
            'is_sesuai' => 'boolean',
            'anggota_tim' => 'array',
            'foto_dokumentasi' => 'array',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id', 'uuid');
    }

    public function ketuaTim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_tim_id', 'uuid');
    }
}