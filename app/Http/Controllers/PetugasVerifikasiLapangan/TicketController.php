<?php

namespace App\Http\Controllers\PetugasVerifikasiLapangan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\BeritaAcaraLapangan;
use App\Models\RiwayatStatusTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tiketVerifikasi = Tiket::with(['layanan', 'user', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->where('status', 'persyaratan_lengkap')
            ->whereNull('petugas_id') 
            ->latest()
            ->paginate(10);

        return view('pages.petugas_verifikasi_lapangan.antrean', compact('tiketVerifikasi'));
    }

    public function mulaiVerifikasi(Request $request, $uuid)
    {
        $tiket = Tiket::findOrFail($uuid);
        
        if (is_null($tiket->petugas_id)) {
            $statusLama = $tiket->status;

            DB::transaction(function () use ($tiket, $request, $statusLama) {
                $tiket->update([
                    'petugas_id' => $request->user()->uuid,
                    'status'     => 'verifikasi_lapangan'
                ]);

                RiwayatStatusTiket::create([
                    'uuid'              => (string) Str::uuid(),
                    'tiket_id'          => $tiket->uuid,
                    'users_id'          => $request->user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru'       => 'verifikasi_lapangan',
                ]);
            });
        }

        return redirect()->route('verif_lapangan.ticket.workdesk')->with('success', 'Tiket berhasil diambil. Silakan laksanakan verifikasi lapangan.');
    }

    public function history(Request $request)
    {
        $userUuid = $request->user()->uuid;

        // Jangan lupa tambahkan 'formulirPermohonanBaruOrmas' di sini agar tidak kena bug "Alamat Tidak Ditemukan" lagi
        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->whereIn('status', [
                'review_berita_acara', // <--- INI STATUS YANG SEBELUMNYA HILANG
                'pembuatan_berita_acara',
                'pembuatan_draft_skt',
                'menunggu_penandatanganan',
                'skt_disetujui',
                'penomoran_skt', 
                'skt_diterbitkan',
                'skt_ditolak' 
            ])
            ->where('petugas_id', $userUuid)
            ->latest()
            ->paginate(10);

        return view('pages.petugas_verifikasi_lapangan.history', compact('tiketHistory'));
    }

    public function lihatBeritaAcara($uuid)
    {
        $tiket = Tiket::with(['permohonanSkt', 'formulirPermohonanBaruOrmas', 'beritaAcaraLapangan'])->findOrFail($uuid);
        
        return view('pages.petugas_verifikasi_lapangan.berita_acara', compact('tiket'));
    }

    public function workdesk(Request $request)
    {
        $userUuid = $request->user()->uuid;

        $tiketWorkdesk = Tiket::with(['layanan', 'user', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->where('status', 'verifikasi_lapangan')
            ->where('petugas_id', $userUuid)
            ->latest()
            ->paginate(10);

        return view('pages.petugas_verifikasi_lapangan.workdesk', compact('tiketWorkdesk'));
    }

    public function simpanBeritaAcara(Request $request, $uuid)
    {
        $validated = $request->validate([
            'tanggal_verifikasi'     => 'required|date|before_or_equal:today',
            'is_sesuai'              => 'required|boolean',
            'catatan_lapangan'       => 'required|string',
            'foto_dokumentasi'       => 'required|array',
            'foto_dokumentasi.*'     => 'image|mimes:jpeg,png,jpg|max:2048',
            'file_berita_acara_path' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $tiket = Tiket::findOrFail($uuid);

            $fotoPaths = [];
            if ($request->hasFile('foto_dokumentasi')) {
                foreach ($request->file('foto_dokumentasi') as $file) {
                    $path = $file->store('berita_acara/foto'); 
                    $fotoPaths[] = $path;
                }
            }

            $filePath = null;
            if ($request->hasFile('file_berita_acara_path')) {
                $filePath = $request->file('file_berita_acara_path')->store('berita_acara/dokumen');
            }

            BeritaAcaraLapangan::create([
                'uuid'                   => (string) Str::uuid(),
                'tiket_id'               => $tiket->uuid,
                'petugas_lapangan_id'    => $request->user()->uuid,
                'tanggal_verifikasi'     => $validated['tanggal_verifikasi'],
                'catatan_lapangan'       => $validated['catatan_lapangan'],
                'foto_dokumentasi'       => json_encode($fotoPaths),
                'is_sesuai'              => $validated['is_sesuai'],
                'file_berita_acara_path' => $filePath,
            ]);

            $statusLama = $tiket->status;
            $statusBaru = 'review_berita_acara'; 
            
            $tiket->update([
                'status' => $statusBaru
            ]);

            RiwayatStatusTiket::create([
                'uuid'              => (string) Str::uuid(),
                'tiket_id'          => $tiket->uuid,
                'users_id'          => $request->user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru'       => $statusBaru,
            ]);

            DB::commit();

            return redirect()->route('verif_lapangan.ticket.workdesk')
                ->with('success', 'Berita Acara Lapangan berhasil disimpan. Tiket diteruskan ke tahap Review.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('verif_lapangan.ticket.workdesk')
                ->with('error', 'Gagal memproses! Tiket tidak ditemukan atau sudah dihapus dari sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan Berita Acara: ' . $e->getMessage())
                ->withInput();
        }
    }
}