<?php
namespace App\Http\Controllers\PetugasVerifikasiData;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userUuid = $request->user()->uuid; 

        $totalMasuk = Tiket::where('status', 'diajukan')
            ->whereNull('petugas_id')
            ->count();

        $sedangDitangani = Tiket::where('status', 'pemeriksaan_kelengkapan')
            ->where('petugas_id', $userUuid)
            ->count();

        $totalSelesai = Tiket::where('status', 'persyaratan_lengkap')
            ->where('petugas_id', $userUuid)
            ->count();

        $totalDitolak = Tiket::where('status', 'data_tidak_sesuai')
            ->where('petugas_id', $userUuid)
            ->count();

        $trenData = DB::table('riwayat_status_tiket')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('users_id', $userUuid)
            ->whereIn('status_baru', ['persyaratan_lengkap', 'data_tidak_sesuai'])
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $recentTickets = Tiket::with(['user', 'layanan'])
            ->where('petugas_id', $userUuid)
            ->where('status', 'pemeriksaan_kelengkapan')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.PetugasVerifikasiData.dashboard', compact(
            'totalMasuk', 
            'sedangDitangani', 
            'totalSelesai', 
            'totalDitolak', 
            'trenData',
            'recentTickets'
        ));
    }
}