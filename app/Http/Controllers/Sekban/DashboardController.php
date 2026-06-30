<?php

namespace App\Http\Controllers\Sekban;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;
use App\Models\RiwayatStatusTiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->where('status', 'menunggu_penandatanganan')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->whereIn('status', ['skt_disetujui', 'skt_ditolak'])
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        $totalMenunggu = Tiket::where('status', 'menunggu_penandatanganan')->count();
        $totalDisetujui = Tiket::where('status', 'skt_disetujui')->count();
        $totalDitolak = Tiket::where('status', 'skt_ditolak')->count();

        return view('pages.sekban.dashboard', compact(
            'tiketMenunggu',
            'tiketHistory',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
    }
}