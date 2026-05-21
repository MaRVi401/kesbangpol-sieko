<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\RiwayatStatusTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceHistoryTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Tiket::with('layanan')
            ->where('users_id', Auth::id());

        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->where('no_tiket', 'like', '%' . $search . '%')
                  ->orWhereHas('layanan', function($qLayanan) use ($search) {
                      $qLayanan->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        $tickets = $query->latest('updated_at')->paginate(10);

        return view('pages.pemohon.history_ticket.index', compact('tickets'));
    }

    public function revisi($uuid)
    {
        // 1. Ambil tiket beserta relasi formulir yang berpotensi dimiliki
        $tiket = Tiket::with([
                'permohonanSkt', 
                'formulirPermohonanBaruOrmas.biodataPengurus', 
                'formulirPermohonanBaruOrmas.suratPernyataan', 
                'formulirPermohonanBaruOrmas.formulirIsian'
            ])
            ->where('uuid', $uuid)
            ->where('users_id', Auth::user()->uuid)
            ->firstOrFail();

        // 2. Validasi status berdasarkan Enum skema terbaru
        // Memakai 'data_tidak_sesuai' atau 'skt_ditolak' sebagai syarat revisi
        if (!in_array(strtolower($tiket->status), ['data_tidak_sesuai', 'skt_ditolak'])) {
            return redirect()->back()->with('error', 'Hanya tiket dengan status Data Tidak Sesuai atau SKT Ditolak yang dapat direvisi.');
        }

        // 3. Ambil data lama ke dalam payload_draft
        $payload = [];

        // Jika layanan merupakan Permohonan SKT biasa
        if ($tiket->permohonanSkt) {
            $payload['permohonan_skt'] = $tiket->permohonanSkt->toArray();
        }

        // Jika layanan merupakan Pencatatan Ormas Baru (sertakan relasi nested-nya)
        if ($tiket->formulirPermohonanBaruOrmas) {
            $payload['formulir_ormas'] = $tiket->formulirPermohonanBaruOrmas->toArray();
        }

        // Simpan status lama sebelum diupdate
        $statusSebelumnya = $tiket->status;

        // 4. Update tiket ke draft
        $tiket->update([
            'status' => 'draft',
            'payload_draft' => $payload 
        ]);

        // 5. Catat ke riwayat berdasarkan kolom skema terbaru (status_sebelumnya & status_baru)
        RiwayatStatusTiket::create([
            'uuid'              => (string) Str::uuid(), 
            'tiket_id'          => $tiket->uuid,
            'users_id'          => Auth::user()->uuid, 
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru'       => 'draft'
        ]);

        return redirect()->route('pemohon.services.index')->with('success', 'Silakan perbarui data pengajuan Anda melalui form draf.');
    }


    public function show($uuid)
    {
        
        $ticket = Tiket::with([
            'layanan',
            'komentar',
            'formulirPermohonanBaruOrmas.biodataPengurus', 
            'formulirPermohonanBaruOrmas.suratPernyataan', 
            'formulirPermohonanBaruOrmas.formulirIsian'
        ])->where('uuid', $uuid)->firstOrFail();
        
        
        $jumlahRevisi = RiwayatStatusTiket::where('tiket_id', $ticket->uuid)
                            ->where('status_baru', 'draft')
                            ->count();
        
        return view('pages.pemohon.DetailSuratPermohonan.show', compact('ticket', 'jumlahRevisi'));
    }

    public function destroy($uuid)
    {
        // 1. Tarik tiket beserta SEMUA relasinya (agar kita punya nama-nama filenya)
        $ticket = Tiket::with([
            'formulirPermohonanBaruOrmas.biodataPengurus',
            'formulirPermohonanBaruOrmas.suratPernyataan',
            'formulirPermohonanBaruOrmas.formulirIsian'
        ])->where('uuid', $uuid)
          ->where('users_id', Auth::user()->uuid)
          ->firstOrFail();
                       
        // 2. Validasi status 
        if (!in_array(strtolower($ticket->status), ['draft', 'diajukan', 'skt_ditolak', 'data_tidak_sesuai', 'belum diajukan'])) {
            return redirect()->back()->with('error', 'Tiket ini sedang atau sudah diproses sehingga tidak dapat dihapus.');
        }

        
        if ($formulir = $ticket->formulirPermohonanBaruOrmas) {
            
            // Hapus File Formulir Utama
            if ($formulir->file_kop_surat) {
                Storage::delete('private/ormas/kop_surat/' . $formulir->file_kop_surat);
            }
            if ($formulir->file_tanda_tangan_pemohon) {
                Storage::delete('private/ormas/ttd/' . $formulir->file_tanda_tangan_pemohon);
            }

            // Hapus File Biodata Pengurus (Di-loop karena ada Ketua, Sekretaris, Bendahara)
            if ($formulir->biodataPengurus) {
                foreach ($formulir->biodataPengurus as $pengurus) {
                    if ($pengurus->foto_resmi) {
                        Storage::delete('private/ormas/foto_pengurus/' . $pengurus->foto_resmi);
                    }
                    if ($pengurus->file_tanda_tangan) {
                        Storage::delete('private/ormas/ttd_pengurus/' . $pengurus->file_tanda_tangan);
                    }
                }
            }

            // Hapus File Surat Pernyataan
            if ($formulir->suratPernyataan) {
                if ($formulir->suratPernyataan->file_ttd_ketua_materai) {
                    Storage::delete('private/ormas/pernyataan/' . $formulir->suratPernyataan->file_ttd_ketua_materai);
                }
                if ($formulir->suratPernyataan->file_ttd_sekretaris) {
                    Storage::delete('private/ormas/pernyataan/' . $formulir->suratPernyataan->file_ttd_sekretaris);
                }
            }

            // Hapus File Formulir Isian
            if ($formulir->formulirIsian) {
                if ($formulir->formulirIsian->file_logo_organisasi) {
                    Storage::delete('private/ormas/logo/' . $formulir->formulirIsian->file_logo_organisasi);
                }
                if ($formulir->formulirIsian->file_bendera_organisasi) {
                    Storage::delete('private/ormas/bendera/' . $formulir->formulirIsian->file_bendera_organisasi);
                }
            }
        }

        // 4. Baru Hapus Data dari Database
        $ticket->delete();

        return redirect()->route('pemohon.history.index')->with('success', 'Tiket beserta seluruh file lampirannya berhasil dihapus.');
    }
}