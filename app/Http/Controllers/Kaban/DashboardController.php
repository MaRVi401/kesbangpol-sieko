<?php

namespace App\Http\Controllers\Kaban;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\DraftSkt;
use App\Models\RiwayatStatusTiket;
use App\Models\SuratRegistrasiOrmas; // Tambahan
use Barryvdh\DomPDF\Facade\Pdf;      // Tambahan
use Illuminate\Support\Facades\Log;  // Tambahan
use Carbon\Carbon;                   // Tambahan
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
            $fileName = 'SKT_' . $cleanNoTiket . '.pdf';

            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Surat Registrasi): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat surat: ' . $e->getMessage());
        }
    }

    
    public function tandaTangan(Request $request)
    {
        $request->validate([
            'tiket_uuid' => 'required|exists:tiket,uuid',
        ]);

        DB::beginTransaction();
        try {
            $tiket = Tiket::where('uuid', $request->tiket_uuid)
                ->where('status', 'menunggu_penandatanganan')
                ->firstOrFail();

            $statusLama = $tiket->status;
            $statusBaru = 'skt_disetujui'; 

            $tiket->update(['status' => $statusBaru]);

            DraftSkt::where('tiket_id', $tiket->uuid)->update([
                'is_ttd_kaban' => true
            ]);

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