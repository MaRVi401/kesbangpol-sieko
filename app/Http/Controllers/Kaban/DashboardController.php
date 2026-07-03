<?php

namespace App\Http\Controllers\Kaban;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\DraftSkt;
use App\Models\RiwayatStatusTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        return view('pages.Kaban.dashboard', compact(
            'tiketMenunggu',
            'tiketHistory',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * Fitur Tanda Tangan Langsung oleh Kaban (Tanpa Unggah File)
     */
    public function tandaTangan(Request $request)
    {
        $request->validate([
            'tiket_uuid' => 'required|exists:tiket,uuid',
        ]);

        DB::beginTransaction();
        try {
            // 1. Ambil data tiket dengan status menunggu penandatanganan
            $tiket = Tiket::where('uuid', $request->tiket_uuid)
                ->where('status', 'menunggu_penandatanganan')
                ->firstOrFail();

            $statusLama = $tiket->status;
            $statusBaru = 'skt_disetujui'; // Menjadi selesai / disetujui pimpinan

            // 2. Update status tiket
            $tiket->update(['status' => $statusBaru]);

            // 3. Update flag tanda tangan di tabel draft_skt
            DraftSkt::where('tiket_id', $tiket->uuid)->update([
                'is_ttd_kaban' => true
            ]);

            // 4. Catat ke riwayat status tiket
            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->uuid,
                'users_id' => Auth::user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
            ]);

            DB::commit();

            return back()->with('success', 'SKT berhasil ditandatangani dan status tiket telah diselesaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}