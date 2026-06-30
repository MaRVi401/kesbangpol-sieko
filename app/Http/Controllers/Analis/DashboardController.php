<?php

namespace App\Http\Controllers\Analis;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
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
            $fileName = 'Surat_Registrasi_Ormas_' . $cleanNoTiket . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Surat Registrasi): ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh surat: ' . $e->getMessage());
        }
    }

    public function unggahTtdBasah(Request $request)
    {
        // 1. Validasi input dari modal form
        $request->validate([
            'tiket_uuid' => 'required|exists:tiket,uuid',
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'file_dokumen.required' => 'File dokumen wajib diunggah.',
            'file_dokumen.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'file_dokumen.max' => 'Ukuran file maksimal 2MB.',
        ]);

        DB::beginTransaction();
        try {
            // 2. Ambil data tiket dan surat registrasi terkait
            $tiket = Tiket::where('uuid', $request->tiket_uuid)->firstOrFail();
            $suratRegistrasi = SuratRegistrasiOrmas::where('tiket_id', $tiket->uuid)->first();

            if (!$suratRegistrasi) {
                return back()->with('error', 'Data Surat Registrasi belum dibuat. Silakan buat draft terlebih dahulu.');
            }

            // 3. Proses upload file ke storage (misal: storage/app/private/ttd_basah)
            $file = $request->file('file_dokumen');
            $cleanNoTiket = str_replace(['/', '\\', ' '], '-', $tiket->no_tiket);
            $fileName = 'TTD_BASAH_' . $cleanNoTiket . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke direktori yang aman
            $path = $file->storeAs('private/ttd_basah', $fileName);

            // 4. Update path di tabel surat_registrasi_ormas
            $suratRegistrasi->update([
                'file_surat_ttd_basah_path' => $path
            ]);

            // 5. Update status tiket dan catat riwayat
            $statusLama = $tiket->status;
            $statusBaru = 'menunggu_penandatanganan'; // Ubah sesuai status setelah draf diunggah

            $tiket->update(['status' => $statusBaru]);

            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->uuid,
                'users_id' => Auth::user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru' => $statusBaru,
            ]);

            DB::commit();

            return back()->with('success', 'Dokumen TTD Basah berhasil diunggah dan tiket diteruskan ke tahap selanjutnya.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error unggah TTD Basah Analis: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengunggah dokumen.');
        }
    }
}