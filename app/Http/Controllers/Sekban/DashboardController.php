<?php

namespace App\Http\Controllers\Sekban;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;
use App\Models\SuratRegistrasiOrmas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Antrean: Mencari tiket yang sudah diparaf Kabid dan menunggu paraf Sekban
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where('status', 'menunggu_paraf_sekban')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        // Riwayat: Tiket yang sudah diparaf Sekban (is_paraf_sekban = true) ATAU ditolak oleh Sekban
        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where(function ($query) {
                $query->whereHas('draftSkt', function ($q) {
                    $q->where('is_paraf_sekban', true);
                })->orWhereHas('riwayatStatus', function ($q) {
                    $q->where('status_baru', 'skt_ditolak')
                      ->where('users_id', Auth::user()->uuid);
                });
            })
            ->latest()
            ->paginate(10, ['*'], 'history_page');

        $totalMenunggu = Tiket::where('status', 'menunggu_paraf_sekban')->count();
        
        $totalDisetujui = Tiket::whereHas('draftSkt', function ($q) {
                $q->where('is_paraf_sekban', true);
            })->count();
            
        $totalDitolak = Tiket::whereHas('riwayatStatus', function ($q) {
                $q->where('status_baru', 'skt_ditolak')
                  ->where('users_id', Auth::user()->uuid);
            })->count();

        return view('pages.sekban.dashboard', compact(
            'tiketMenunggu',
            'tiketHistory',
            'totalMenunggu',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * Fungsi untuk Pratinjau/Unduh PDF Draft SKT
     */
    public function unduhSuratRegistrasi(Tiket $tiket)
    {
        try {
            $suratRegistrasi = SuratRegistrasiOrmas::where('tiket_id', $tiket->uuid)->first();

            if (!$suratRegistrasi) {
                return back()->with('error', 'Data Surat Registrasi Ormas belum tersedia untuk tiket ini.');
            }

            $data = [
                'tiket'            => $tiket,
                'surat_registrasi' => $suratRegistrasi,
                'tanggal_cetak'    => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            ];

            $pdf = Pdf::loadView('pdf.surat-registrasi-ormas', $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

            $cleanNoTiket = str_replace(['/', '\\', ' '], '-', $tiket->no_tiket ?? $tiket->uuid);
            $fileName = 'Draft_SKT_' . $cleanNoTiket . '.pdf';

            // Menggunakan stream agar bisa langsung tampil (pratinjau) di browser tab baru
            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Surat Registrasi): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat surat: ' . $e->getMessage());
        }
    }

    public function proses(Request $request, $uuid)
    {
       
        $request->validate([
            'action' => 'required|in:setujui', 
        ]);

        try {
            DB::beginTransaction();

            $tiket = Tiket::with('draftSkt')->where('uuid', $uuid)->firstOrFail();
            $statusLama = $tiket->status;

            if ($request->action === 'setujui') {
                if ($tiket->draftSkt) {
                    $tiket->draftSkt->is_paraf_sekban = true;
                    $tiket->draftSkt->save();
                }
                
                $statusBaru = 'menunggu_penandatanganan';
                $tiket->update(['status' => $statusBaru]);
                
                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->uuid,
                    'users_id' => Auth::user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru' => $statusBaru 
                ]);

                $pesan = 'Draft SKT berhasil diparaf dan diteruskan ke Kepala Badan untuk ditandatangani.';
            } 
            
            DB::commit();
            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses tiket: ' . $e->getMessage());
        }
    }
}