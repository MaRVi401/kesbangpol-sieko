<?php
namespace App\Http\Controllers\Pemohon;

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

        $totalDiajukan = Tiket::where('users_id', $userUuid)
            ->whereIn('status', ['draft', 'diajukan'])
            ->count();

        $totalDiproses = Tiket::where('users_id', $userUuid)
            ->whereNotIn('status', ['draft', 'diajukan', 'skt_diterbitkan', 'skt_ditolak'])
            ->count();

        $totalSelesai = Tiket::where('users_id', $userUuid)
            ->where('status', 'skt_diterbitkan')
            ->count();

        $totalDitolak = Tiket::where('users_id', $userUuid)
            ->where('status', 'skt_ditolak')
            ->count();

        $trenData = DB::table('tiket')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('users_id', $userUuid)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $recentTickets = Tiket::with(['layanan'])
            ->where('users_id', $userUuid)
            ->latest()
            ->take(5)
            ->get();

        return view('pemohon.dashboard', compact(
            'totalDiajukan', 
            'totalDiproses', 
            'totalSelesai', 
            'totalDitolak', 
            'trenData',
            'recentTickets'
        ));
    }
}