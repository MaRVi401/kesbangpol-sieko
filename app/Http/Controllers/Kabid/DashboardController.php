<?php

namespace App\Http\Controllers\Kabid;

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
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt', 'draftSkt'])
            ->where('status', 'menunggu_paraf_kabid')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

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

            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Surat Registrasi): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat surat: ' . $e->getMessage());
        }
    }

    public function proses(Request $request, $uuid)
    {
        $request->validate([
            'action' => 'required|in:setujui', // Menghapus opsi tolak dari validasi
        ]);

        try {
            DB::beginTransaction();

            $tiket = Tiket::with('draftSkt')->where('uuid', $uuid)->firstOrFail();
            $statusLama = $tiket->status;

            if ($request->action === 'setujui') {
                if ($tiket->draftSkt) {
                    $tiket->draftSkt->is_paraf_kabid = true;
                    $tiket->draftSkt->save();
                }
                
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

            DB::commit();
            return redirect()->back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses tiket: ' . $e->getMessage());
        }
    }
}