<?php

namespace App\Http\Controllers\PetugasVerifikasiLapangan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userUuid = $request->user()->uuid;

        $tiketVerifikasi = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->where('status', 'verifikasi_lapangan')
            ->where(function($query) use ($userUuid) {
                $query->where('petugas_id', $userUuid)
                      ->orWhereNull('petugas_id');
            })
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->whereIn('status', [
                'pembuatan_berita_acara',
                'pembuatan_draft_skt',
                'menunggu_penandatanganan',
                'skt_disetujui',
                'skt_diterbitkan'
            ])
            ->where('petugas_id', $userUuid)
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        $totalTugas = Tiket::where('status', 'verifikasi_lapangan')
            ->where(function($query) use ($userUuid) {
                $query->where('petugas_id', $userUuid)
                      ->orWhereNull('petugas_id');
            })->count();

        $totalSelesai = Tiket::where('petugas_id', $userUuid)
            ->whereNotIn('status', ['draft', 'diajukan', 'pemeriksaan_kelengkapan', 'verifikasi_lapangan'])
            ->count();

        return view('pages.petugas_verifikasi_lapangan.dashboard', compact(
            'tiketVerifikasi',
            'tiketHistory',
            'totalTugas',
            'totalSelesai'
        ));
    }
}