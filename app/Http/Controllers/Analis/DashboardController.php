<?php

namespace App\Http\Controllers\Analis;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\DraftSkt;
use App\Models\SuratRegistrasiOrmas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RiwayatStatusTiket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan tiket yang butuh draft/paraf dari analis
        $tiketMenunggu = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->where('status', 'pembuatan_draft_skt')
            ->latest()
            ->paginate(10, ['*'], 'antrean_page');

        // Menambahkan status intermediate (menunggu_paraf_kabid, menunggu_paraf_sekban) ke history
        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->whereIn('status', [
                'menunggu_paraf_kabid',
                'menunggu_paraf_sekban',
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
                'menunggu_paraf_kabid',
                'menunggu_paraf_sekban',
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

   public function unduhSuratRegistrasi(Tiket $tiket)
    {
        try {
            $suratRegistrasi = SuratRegistrasiOrmas::where('tiket_id', $tiket->uuid)->first();

            if (!$suratRegistrasi) {
                return back()->with('error', 'Data Surat Registrasi Ormas belum tersedia untuk tiket ini.');
            }

            // Mengambil data Kaban
            $kaban = \App\Models\User::select('users.nama', 'kaban.nip')
                ->join('kaban', 'users.uuid', '=', 'kaban.users_id')
                ->where('users.role', 'kaban')
                ->first();

            $data = [
                'tiket'            => $tiket,
                'surat_registrasi' => $suratRegistrasi,
                'kaban'            => $kaban, // Melempar data Kaban ke view
                'tanggal_cetak'    => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            ];

            $pdf = Pdf::loadView('pdf.surat-registrasi-ormas', $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

            $cleanNoTiket = str_replace(['/', '\\', ' '], '-', $tiket->no_tiket ?? $tiket->uuid);
            $fileName = 'Draft_SKT_' . $cleanNoTiket . '.pdf';

            return $pdf->download($fileName); // Analis menggunakan download

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Surat Registrasi): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh surat: ' . $e->getMessage());
        }
    }

    /**
     * Fitur Paraf Draft oleh Analis
     */
    public function parafDraft(Request $request)
    {
        $request->validate([
            'tiket_uuid' => 'required|exists:tiket,uuid',
        ]);

        DB::beginTransaction();
        try {
            $tiket = Tiket::where('uuid', $request->tiket_uuid)
                ->where('status', 'pembuatan_draft_skt')
                ->firstOrFail();

            $statusLama = $tiket->status;
            $statusBaru = 'menunggu_paraf_kabid'; // Lanjut ke Kabid Kesbak

            // Update status tiket
            $tiket->update(['status' => $statusBaru]);

            // Set is_paraf_analis = true (pastikan Anda sudah menambahkan kolom ini di migrasi draft_skt sebelumnya)
            DraftSkt::where('tiket_id', $tiket->uuid)->update([
                'is_paraf_analis' => true
            ]);

            // Catat riwayat tiket
            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->uuid,
                'users_id' => Auth::user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
            ]);

            DB::commit();

            return back()->with('success', 'Draft SKT berhasil diparaf dan diteruskan ke Kabid Kesbak.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Paraf Analis: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses paraf: ' . $e->getMessage());
        }
    }
}