<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemohon;
use App\Models\Tiket;

class FileController extends Controller
{
    public function show(string $path)
    {
        $path = ltrim($path, '/');

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Dokumen tidak ditemukan di server.');
        }

        $user = Auth::user();
        $userRole = trim(strtolower($user->role));

        $allowedRoles = [
            'super_admin',
            'super-admin',
            'petugas_verifikasi_data',
            'petugas_verifikasi_lapangan',
            'analis_kebijakan_ahli_muda',
            'kabid_kesbak',
            'sekban',
            'kaban',
            'operator'
        ];

        if (in_array($userRole, $allowedRoles)) {
            return Storage::disk('local')->response($path);
        }

        if ($userRole === 'pemohon') {
            $isProfileOwner = Pemohon::where('users_id', $user->uuid)
                ->where(function ($query) use ($path) {
                    $query->where('kta_path', $path)
                        ->orWhere('surat_rekomendasi_path', $path);
                })
                ->exists();

            if ($isProfileOwner) {
                return Storage::disk('local')->response($path);
            }

            $filename = basename($path);

            $isTicketOwner = Tiket::where('users_id', $user->uuid)
                ->where(function($queryWrapper) use ($filename) {
                    
                    
                    $queryWrapper->whereHas('formulirPermohonanBaruOrmas', function ($query) use ($filename) {
                        $query->where('file_kop_surat', $filename)
                            ->orWhere('file_tanda_tangan_pemohon', $filename)
                            ->orWhereHas('biodataPengurus', function ($q) use ($filename) {
                                $q->where('foto_resmi', $filename)
                                    ->orWhere('file_tanda_tangan', $filename)
                                    ->orWhere('file_ktp_path', $filename); 
                            })
                            ->orWhereHas('suratPernyataan', function ($q) use ($filename) {
                                $q->where('file_ttd_ketua_materai', $filename)
                                    ->orWhere('file_ttd_sekretaris', $filename);
                            })
                            ->orWhereHas('formulirIsian', function ($q) use ($filename) {
                                $q->where('file_logo_organisasi', $filename)
                                    ->orWhere('file_bendera_organisasi', $filename);
                            });
                    })
                    
                    
                    ->orWhereHas('permohonanSkt', function ($query) use ($filename) {
                        $query->where('akta_pendirian_path', $filename)
                            ->orWhere('sk_kemenkumham_path', $filename)
                            ->orWhere('file_ad_art_path', $filename)
                            ->orWhere('file_program_kerja_path', $filename)
                            ->orWhere('file_sk_kepengurusan_path', $filename)
                            ->orWhere('file_surat_mandat_path', $filename)
                            ->orWhere('surat_domisili_path', $filename)
                            ->orWhere('file_foto_kantor_path', $filename)
                            ->orWhere('file_npwp_path', $filename)
                            ->orWhere('file_sk_terlapor_path', $filename);
                    });
                    
                })
                ->exists();

            if ($isTicketOwner) {
                return Storage::disk('local')->response($path);
            }
        }

        abort(403, 'Akses Dibatasi: Anda tidak memiliki izin untuk melihat dokumen ini.');
    }
}