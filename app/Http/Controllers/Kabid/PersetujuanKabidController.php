<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PersetujuanKabidController extends Controller
{
    // 1. Fungsi untuk menampilkan Dashboard Kabid
    public function index()
    {
        // Antrean: Tiket status 'menunggu_penandatanganan' TAPI belum diparaf Kabid
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where('status', 'menunggu_penandatanganan')
            ->whereHas('draftSkt', function ($query) {
                $query->where('is_ttd_kabid', false);
            })
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        // Riwayat: Tiket yang sudah diparaf Kabid ATAU ditolak
        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where(function ($query) {
                $query->whereHas('draftSkt', function ($q) {
                    $q->where('is_ttd_kabid', true);
                })->orWhere('status', 'skt_ditolak');
            })
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        // Statistik Dashboard
        $totalMenunggu = Tiket::where('status', 'menunggu_penandatanganan')
            ->whereHas('draftSkt', function ($q) {
                $q->where('is_ttd_kabid', false);
            })->count();
            
        $totalDiterima = Tiket::whereHas('draftSkt', function ($q) {
                $q->where('is_ttd_kabid', true);
            })->count();
            
        $totalDitolak = Tiket::where('status', 'skt_ditolak')->count();

        return view('pages.kabid.dashboard', compact(
            'tiketMenunggu', 
            'tiketHistory', 
            'totalMenunggu', 
            'totalDiterima', 
            'totalDitolak'
        ));
    }

    // 2. Fungsi untuk memproses (Setujui Paraf / Tolak)
    public function proses(Request $request, $uuid)
    {
        $request->validate([
            'action' => 'required|in:setujui,tolak',
            'komentar' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $tiket = Tiket::with('draftSkt')->where('uuid', $uuid)->firstOrFail();
            $statusLama = $tiket->status;

            if ($request->action === 'setujui') {
                // UPDATE PARAF KABID MENJADI TRUE
                if ($tiket->draftSkt) {
                    $tiket->draftSkt->is_ttd_kabid = true;
                    $tiket->draftSkt->save();
                }
                
                // Status tiket utama TETAP 'menunggu_penandatanganan' agar masuk ke antrean Sekban
                
                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->uuid,
                    'users_id' => Auth::user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => 'paraf_kabid_selesai' // Penanda internal di riwayat
                ]);

                $pesan = 'Draft SKT berhasil diparaf dan diteruskan ke Sekban.';
            } 
            else {
                // JIKA DITOLAK
                $tiket->update(['status' => 'skt_ditolak']);

                if ($request->filled('komentar')) {
                    KomentarTiket::create([
                        'tiket_id' => $tiket->uuid,
                        'users_id' => Auth::user()->uuid,
                        'komentar' => $request->komentar
                    ]);
                }

                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->uuid,
                    'users_id' => Auth::user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => 'skt_ditolak'
                ]);

                $pesan = 'Tiket telah ditolak dan dikembalikan.';
            }

            DB::commit();
            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses tiket: ' . $e->getMessage());
        }
    }
}