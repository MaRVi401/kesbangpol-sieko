<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemohon;

class FileController extends Controller
{
    /**
     * Menampilkan file dari storage private
     * * @param string
     */
    public function show(string $path)
    {
        // 1. Bersihkan path
        $path = ltrim($path, '/');

        // 2. Cek eksistensi file
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Dokumen tidak ditemukan di server.');
        }

        // 3. Ambil data user yang sedang login menggunakan Facade Auth
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 4. Daftar Role Admin & Petugas
        $allowedRoles = [
            'super_admin',
            'petugas_verifikasi_data',
            'petugas_verifikasi_lapangan',
            'analis_kebijakan_ahli_muda',
            'kabid_kesbak',
            'sekban',
            'kaban'
        ];

        // 5. Izinkan jika role termasuk admin/petugas
        if (in_array($user->role, $allowedRoles)) {
            return Storage::disk('local')->response($path);
        }

        // 6. Validasi jika login sebagai pemohon
        if ($user->role === 'pemohon') {
            $isOwner = Pemohon::where('users_id', $user->uuid)
                ->where(function ($query) use ($path) {
                    $query->where('kta_path', $path)
                          ->orWhere('surat_rekomendasi_path', $path);
                })
                ->exists();

            if ($isOwner) {
                return Storage::disk('local')->response($path);
            }
        }

        // 7. Jika tidak lolos semua validasi di atas
        abort(403, 'Akses Dibatasi: Anda tidak memiliki izin untuk melihat dokumen ini.');
    }
}
