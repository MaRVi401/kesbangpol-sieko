<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($path)
    {
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $user = auth()->user();
        
        $allowedRoles = [
            'super_admin',
            'petugas_verifikasi_data',
            'petugas_verifikasi_lapangan',
            'analis_kebijakan_ahli_muda',
            'kabid_kesbak',
            'sekban',
            'kaban'
        ];

        $isAuthorizedRole = in_array($user->role, $allowedRoles);
        
        $isOwner = str_contains($path, auth()->id());

        if (!$isAuthorizedRole && !$isOwner) {
            abort(403);
        }

        return Storage::disk('local')->response($path);
    }
}