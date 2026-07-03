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
    public function index()
    {
        // Antrean: Mencari tiket yang sudah diparaf Analis dan masuk ke antrean Kabid
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where('status', 'menunggu_paraf_kabid')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        // Riwayat: Tiket yang sudah diparaf Kabid (is_paraf_kabid = true) ATAU ditolak oleh Kabid ini
        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where(function ($query) {
                $query->whereHas('draftSkt', function ($q) {
                    $q->where('is_paraf_kabid', true);
                })->orWhereHas('riwayatStatus', function ($q) {
                    $q->where('status_baru', 'skt_ditolak')
                      ->where('users_id', Auth::user()->uuid);
                });
            })
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        $totalMenunggu = Tiket::where('status', 'menunggu_paraf_kabid')->count();
            
        $totalDiterima = Tiket::whereHas('draftSkt', function ($q) {
                $q->where('is_paraf_kabid', true);
            })->count();
            
        $totalDitolak = Tiket::whereHas('riwayatStatus', function ($q) {
                $q->where('status_baru', 'skt_ditolak')
                  ->where('users_id', Auth::user()->uuid);
            })->count();

        return view('pages.kabid.dashboard', compact(
            'tiketMenunggu', 
            'tiketHistory', 
            'totalMenunggu', 
            'totalDiterima', 
            'totalDitolak'
        ));
    }

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
                // Update paraf kabid menggunakan nama kolom yang baru dari migrasi
                if ($tiket->draftSkt) {
                    $tiket->draftSkt->is_paraf_kabid = true;
                    $tiket->draftSkt->save();
                }
                
                // Ubah status menuju antrean Sekban
                $statusBaru = 'menunggu_paraf_sekban';
                $tiket->update(['status' => $statusBaru]);
                
                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->uuid,
                    'users_id' => Auth::user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => $statusBaru 
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