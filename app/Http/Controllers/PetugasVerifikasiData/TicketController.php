<?php

namespace App\Http\Controllers\PetugasVerifikasiData;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\JejakAudit;
use App\Services\WordTemplateServiceSieko;
use App\Models\User;
use App\Models\DraftSkt;
use App\Models\SuratRegistrasiOrmas;


class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Tiket::with(['user', 'layanan'])
            ->where('status', 'diajukan')
            ->whereNull('verifikator_data_id');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('no_tiket', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function (Builder $qu) use ($search) {
                        $qu->where('nama', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('layanan', function (Builder $ql) use ($search) {
                        $ql->where('nama', 'ilike', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->paginate(10);

        return view('pages.PetugasVerifikasiData.ticket.index', compact('tickets'));
    }

    public function handle(Request $request, string $uuid): RedirectResponse
    {
        $ticket = Tiket::where('uuid', $uuid)
            ->whereNull('verifikator_data_id')
            ->firstOrFail();

        DB::transaction(function () use ($ticket, $request) {
            $statusSebelumnya = $ticket->status;
            $statusBaru = ($statusSebelumnya === 'diajukan') ? 'pemeriksaan_kelengkapan' : 'review_berita_acara';

            $ticket->update([
                'petugas_id'          => $request->user()->uuid,
                'verifikator_data_id' => $request->user()->uuid,
                'status'              => $statusBaru,
            ]);

            DB::table('riwayat_status_tiket')->insert([
                'uuid'              => (string) Str::uuid(),
                'tiket_id'          => $ticket->uuid,
                'users_id'          => $request->user()->uuid,
                'status_sebelumnya' => 'diajukan',
                'status_baru'       => 'pemeriksaan_kelengkapan',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            JejakAudit::create([
                'users_id'   => $request->user()->uuid,
                'aksi'       => 'update',
                'nama_tabel' => 'tiket',
                'record_id'  => $ticket->uuid,
                'data_lama'  => ['status' => 'diajukan', 'petugas_id' => null, 'verifikator_data_id' => null],
                'data_baru'  => ['status' => 'pemeriksaan_kelengkapan', 'petugas_id' => $request->user()->uuid, 'verifikator_data_id' => $request->user()->uuid],
                'ip_address' => $request->ip()
            ]);
        });

        return redirect()
            ->route('verif_data.ticket.workdesk')
            ->with('success', 'Tiket berhasil dipindahkan ke meja kerja Anda.');
    }

    public function workDesk(Request $request): View
    {
        $search = $request->input('search');

        $query = Tiket::with(['user', 'layanan', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->where('verifikator_data_id', $request->user()->uuid)
            ->whereIn('status', ['pemeriksaan_kelengkapan', 'review_berita_acara']);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('no_tiket', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function (Builder $qu) use ($search) {
                        $qu->where('nama', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('layanan', function (Builder $ql) use ($search) {
                        $ql->where('nama', 'ilike', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->paginate(10);

        return view('pages.PetugasVerifikasiData.ticket.workdesk', compact('tickets'));
    }

    public function update(Request $request, string $uuid): RedirectResponse
    {
        $request->validate([
            'status'           => 'required|in:persyaratan_lengkap,data_tidak_sesuai,pembuatan_draft_skt', 
            'komentar'         => 'required|string|min:1',
            'catatan_lapangan' => 'nullable|string', 
        ]);

        $ticket = Tiket::where('uuid', $uuid)
            ->where('verifikator_data_id', $request->user()->uuid)
            ->firstOrFail();

        $statusLama = $ticket->status;

        DB::transaction(function () use ($request, $ticket, $statusLama) {
            
            $ticket->update([
                'status'           => $request->status,
                'petugas_id'       => null, 
                'catatan_lapangan' => $request->catatan_lapangan, 
            ]);

            DB::table('komentar_tiket')->insert([
                'uuid'       => (string) Str::uuid(),
                'tiket_id'   => $ticket->uuid,
                'users_id'   => $request->user()->uuid,
                'komentar'   => $request->komentar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($statusLama === 'review_berita_acara' && $request->status === 'pembuatan_draft_skt') {
                DB::table('berita_acara_lapangan')
                    ->where('tiket_id', $ticket->uuid)
                    ->update(['is_sesuai' => true]);
            }

            DB::table('riwayat_status_tiket')->insert([
                'uuid'              => (string) Str::uuid(),
                'tiket_id'          => $ticket->uuid,
                'users_id'          => $request->user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru'       => $request->status,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        });

        return redirect()
            ->route('verif_data.ticket.workdesk')
            ->with('success', 'Tiket berhasil diverifikasi dan dilepas ke antrean selanjutnya.');
    }

    public function history(Request $request): View
    {
        $search = $request->input('search');
        $filterTime = $request->input('filter_time');
        $userUuid = $request->user()->uuid;

        $query = Tiket::with(['user', 'layanan'])
            ->whereIn('uuid', function ($q) use ($userUuid) {
                $q->select('tiket_id')
                  ->from('riwayat_status_tiket')
                  ->where('users_id', $userUuid)
                  ->whereIn('status_baru', ['persyaratan_lengkap', 'data_tidak_sesuai', 'pembuatan_draft_skt']);
            });

        if ($filterTime) {
            $now = now();
            if ($filterTime === 'hari') {
                $query->whereDate('updated_at', $now->toDateString());
            } elseif ($filterTime === 'minggu') {
                $query->whereBetween('updated_at', [$now->startOfWeek(), $now->endOfWeek()]);
            } elseif ($filterTime === 'bulan') {
                $query->whereMonth('updated_at', $now->month)
                      ->whereYear('updated_at', $now->year);
            }
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('no_tiket', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function (Builder $qu) use ($search) {
                        $qu->where('nama', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('layanan', function (Builder $ql) use ($search) {
                        $ql->where('nama', 'ilike', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest('updated_at')->paginate(10);

        return view('pages.PetugasVerifikasiData.ticket.history', compact('tickets'));
    }

    public function show(Request $request, string $uuid): View
    {
        $ticket = Tiket::with([
            'user', 
            'layanan',
            'permohonanSkt', 
            'formulirPermohonanBaruOrmas.biodataPengurus',
            'formulirPermohonanBaruOrmas.suratPernyataan',
            'formulirPermohonanBaruOrmas.formulirIsian'
        ])
        ->where('uuid', $uuid)
        ->where('verifikator_data_id', $request->user()->uuid)
        ->firstOrFail();

        $analisList = User::where('role', 'analis_kebijakan_ahli_muda')->get();

        return view('pages.PetugasVerifikasiData.ticket.show', compact('ticket', 'analisList'));
    }

    public function previewPdf(Request $request, string $uuid, WordTemplateServiceSieko $pdfService)
    {
        $ticket = Tiket::with('beritaAcaraLapangan')
            ->where('uuid', $uuid)
            ->where('verifikator_data_id', $request->user()->uuid)
            ->firstOrFail();

        return $pdfService->generateDokumenBeritaAcara($ticket);
    }

    public function kirimKeAnalis(Request $request, string $uuid): RedirectResponse
    {
        // 1. Tambahkan validasi nomor_surat
        $request->validate([
            'status'      => 'required|in:pembuatan_draft_skt,data_tidak_sesuai', 
            'komentar'    => 'required_if:status,data_tidak_sesuai|nullable|string',
            'analis_id'   => 'required_if:status,pembuatan_draft_skt|nullable|exists:users,uuid',
            'nomor_surat' => 'required_if:status,pembuatan_draft_skt|nullable|string|max:255', // Tangkap nomor surat
        ]);

        $ticket = Tiket::with([
            'permohonanSkt', 
            'formulirPermohonanBaruOrmas.formulirIsian'
        ])
        ->where('uuid', $uuid)
        ->where('verifikator_data_id', $request->user()->uuid)
        ->firstOrFail();

        $statusLama = $ticket->status;

        DB::transaction(function () use ($request, $ticket, $statusLama) {
            
            $ticket->update([
                'status'     => $request->status,
                'petugas_id' => null, 
            ]);

            if ($request->filled('komentar')) {
                DB::table('komentar_tiket')->insert([
                    'uuid'       => (string) Str::uuid(),
                    'tiket_id'   => $ticket->uuid,
                    'users_id'   => $request->user()->uuid,
                    'komentar'   => $request->komentar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($request->status === 'pembuatan_draft_skt') {
                
                DB::table('berita_acara_lapangan')
                    ->where('tiket_id', $ticket->uuid)
                    ->update(['is_sesuai' => true]);

                // 2. Lempar nomor_surat ke fungsi pembuatan surat
                $this->generateSuratRegistrasi($ticket, $request->analis_id, $request->nomor_surat);

                // 3. Simpan juga ke Draft SKT
                DraftSkt::create([
                    'tiket_id'         => $ticket->uuid,
                    'analis_id'        => $request->analis_id,
                    'no_skt_sementara' => $request->nomor_surat, 
                ]);
            }

            DB::table('riwayat_status_tiket')->insert([
                'uuid'              => (string) Str::uuid(),
                'tiket_id'          => $ticket->uuid,
                'users_id'          => $request->user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru'       => $request->status,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        });

        $pesanSukses = $request->status === 'pembuatan_draft_skt' 
            ? 'Tiket berhasil diteruskan ke Analis Muda.' 
            : 'Tiket ditolak dan dikembalikan ke pemohon.';

        return redirect()
            ->route('verif_data.ticket.workdesk')
            ->with('success', $pesanSukses);
    }

    // 4. Tambahkan parameter $nomorSurat di sini
    private function generateSuratRegistrasi(Tiket $ticket, string $analisId, string $nomorSurat): void
    {
        $permohonan = $ticket->permohonanSkt;
        $formulir   = $ticket->formulirPermohonanBaruOrmas;
        $isian      = $formulir ? $formulir->formulirIsian : null;

        SuratRegistrasiOrmas::create([
            'tiket_id'                 => $ticket->uuid,
            'analis_id'                => $analisId,
            
            // 5. Simpan nomor surat ke tabel surat_registrasi_ormas
            'nomor_surat_registrasi'   => $nomorSurat, 
            
            'nama_organisasi_pemohon'  => $formulir->nama_pemohon ?? '-',
            'nomor_surat_pemohon'      => $formulir->nomor ?? '-',
            'tanggal_surat_pemohon'    => $formulir->tanggal_permohonan ?? now(),
            'perihal_surat_pemohon'    => $formulir->perihal ?? '-',
            'nama_ormas'               => $formulir->nama_organisasi ?? '-',
            'tanggal_berdiri'          => $isian->tanggal_pendirian ?? now(),
            'bidang_kegiatan'          => $permohonan->bidang_kegiatan ?? '-',
            'npwp'                     => $formulir->nomor_npwp_organisasi ?? null,
            'sk_kepengurusan_penerbit' => '-', 
            'sk_kepengurusan_nomor'    => '-', 
            'sk_kepengurusan_periode'  => $permohonan->periode_sk_kepengurusan ?? '-',
            'nama_ketua'               => $formulir->nama_ketua ?? '-',
            'nama_sekretaris'          => $formulir->nama_sekretaris ?? '-',
            'nama_bendahara'           => $formulir->nama_bendahara ?? '-',
            'akta_notaris_keterangan'  => 'Akta Pendirian',
            'akta_notaris_nama'        => $permohonan->nama_notaris ?? '-',
            'akta_notaris_nomor'       => '-',
            'akta_notaris_tanggal'     => $permohonan->tanggal_akte,
            'sk_kemenkumham_keterangan'=> 'Pengesahan Badan Hukum',
            'sk_kemenkumham_nomor'     => $permohonan->nomor_sk_kemenkumham ?? '-',
            'sk_kemenkumham_tanggal'   => $permohonan->tanggal_sk_kemenkumham,
            'alamat_sekretariat'       => $formulir->alamat_sekretariat ?? '-',
            'jenis_pencatatan'         => ($permohonan && $permohonan->jenis_permohonan == 'perubahan') ? 'Perubahan' : 'Baru',
        ]);
    }


    public function unduhSurat(Request $request, string $uuid, WordTemplateServiceSieko $pdfService)
    {
        $ticket = Tiket::where('uuid', $uuid)
            ->where('verifikator_data_id', $request->user()->uuid)
            ->firstOrFail();

        return $pdfService->generateSuratRegistrasiOrmas($ticket);
    }
}