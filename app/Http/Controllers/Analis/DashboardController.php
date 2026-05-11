<?php

namespace App\Http\Controllers\Analis;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->where('status', 'pembuatan_draft_skt')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->whereIn('status', [
                'menunggu_penandatanganan', 
                'skt_disetujui', 
                'penomoran_skt', 
                'skt_diterbitkan', 
                'skt_ditolak'
            ])
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        $totalMenunggu = Tiket::where('status', 'pembuatan_draft_skt')->count();
        
        $totalDraftSelesai = Tiket::whereIn('status', [
                'menunggu_penandatanganan', 
                'skt_disetujui', 
                'penomoran_skt', 
                'skt_diterbitkan'
            ])->count();

        $totalDitolak = Tiket::where('status', 'skt_ditolak')->count();

        return view('pages.analis.dashboard', compact(
            'tiketMenunggu',
            'tiketHistory',
            'totalMenunggu',
            'totalDraftSelesai',
            'totalDitolak'
        ));
    }
}