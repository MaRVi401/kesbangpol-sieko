<?php

namespace App\Http\Controllers\PetugasVerifikasiLapangan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tiketVerifikasi = Tiket::with(['layanan', 'user', 'permohonanSkt'])
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

            \Illuminate\Support\Facades\DB::transaction(function () use ($tiket, $request, $statusLama) {
                $tiket->update([
                    'petugas_id' => $request->user()->uuid,
                    'status'     => 'verifikasi_lapangan'
                ]);

                \Illuminate\Support\Facades\DB::table('riwayat_status_tiket')->insert([
                    'uuid'              => (string) \Illuminate\Support\Str::uuid(),
                    'tiket_id'          => $tiket->uuid,
                    'users_id'          => $request->user()->uuid,
                    'status_sebelumnya' => $statusLama,
                    'status_baru'       => 'verifikasi_lapangan',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            });
        }

        return redirect()->route('verif_lapangan.ticket.workdesk')->with('success', 'Tiket berhasil diambil. Silakan laksanakan verifikasi lapangan.');
    }

    public function history(Request $request)
    {
        $userUuid = $request->user()->uuid;

        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->whereIn('status', [
                'pembuatan_berita_acara',
                'pembuatan_draft_skt',
                'menunggu_penandatanganan',
                'skt_disetujui',
                'skt_diterbitkan'
            ])
            ->where('petugas_id', $userUuid)
            ->latest()
            ->paginate(10);

        return view('pages.petugas_verifikasi_lapangan.history', compact('tiketHistory'));
    }

    public function lihatBeritaAcara($uuid)
    {
        $tiket = Tiket::with(['permohonanSkt', 'beritaAcaraLapangan'])->findOrFail($uuid);
        
        return view('pages.petugas_verifikasi_lapangan.berita_acara', compact('tiket'));
    }

    public function workdesk(Request $request)
    {
        $userUuid = $request->user()->uuid;

       
        $tiketWorkdesk = Tiket::with(['layanan', 'user', 'permohonanSkt'])
            ->where('status', 'verifikasi_lapangan')
            ->where('petugas_id', $userUuid)
            ->latest()
            ->paginate(10);

        return view('pages.petugas_verifikasi_lapangan.workdesk', compact('tiketWorkdesk'));
    }
}