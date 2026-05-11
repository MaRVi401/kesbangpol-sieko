<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

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
        $totalDiterima = Tiket::where('status', 'skt_disetujui')->count();
        $totalDitolak = Tiket::where('status', 'skt_ditolak')->count();

        return view('kabid.dashboard', compact(
            'tiketMenunggu',
            'tiketHistory',
            'totalMenunggu',
            'totalDiterima',
            'totalDitolak'
        ));
    }
}