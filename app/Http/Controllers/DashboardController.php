<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'super_admin'                 => app(Admin\DashboardController::class)->index(request()),
            'pemohon'                     => app(Pemohon\DashboardController::class)->index(request()),
            'petugas_verifikasi_data'     => app(PetugasVerifikasiData\DashboardController::class)->index(request()),
            'petugas_verifikasi_lapangan' => app(PetugasVerifikasiLapangan\DashboardController::class)->index(request()),
            'analis_kebijakan_ahli_muda'  => app(Analis\DashboardController::class)->index(request()),
            'kabid_kesbak'                => app(Kabid\DashboardController::class)->index(request()),
            'sekban'                      => app(Sekban\DashboardController::class)->index(request()),
            'kaban'                       => app(Kaban\DashboardController::class)->index(request()),
            
            default => abort(403, 'Role tidak dikenali.'),
        };
    }
}