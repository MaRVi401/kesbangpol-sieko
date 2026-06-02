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

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Tiket::with(['user', 'layanan'])
            ->whereIn('status', ['diajukan', 'review_berita_acara']) 
            ->whereNull('petugas_id');

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
            ->whereNull('petugas_id')
            ->firstOrFail();

        DB::transaction(function () use ($ticket, $request) {
            $statusSebelumnya = $ticket->status;
            $statusBaru = ($statusSebelumnya === 'diajukan') ? 'pemeriksaan_kelengkapan' : 'review_berita_acara';

            $ticket->update([
                'petugas_id' => $request->user()->uuid,
                'status'     => $statusBaru,
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
                'data_lama'  => ['status' => 'diajukan', 'petugas_id' => null],
                'data_baru'  => ['status' => 'pemeriksaan_kelengkapan', 'petugas_id' => $request->user()->uuid],
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

        // Relasi sudah sesuai dengan tabel di migration
        $query = Tiket::with(['user', 'layanan', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->where('petugas_id', $request->user()->uuid)
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

        // PERBAIKAN: Sesuaikan path direktori view dengan struktur yang benar
        return view('pages.PetugasVerifikasiData.ticket.workdesk', compact('tickets'));
    }

    public function update(Request $request, string $uuid): RedirectResponse
    {
        $request->validate([
            'status'   => 'required|in:persyaratan_lengkap,data_tidak_sesuai,pembuatan_draft_skt', 
            'komentar' => 'required|string|min:1',
        ]);

        $ticket = Tiket::where('uuid', $uuid)
            ->where('petugas_id', $request->user()->uuid)
            ->firstOrFail();

        $statusLama = $ticket->status;

        DB::transaction(function () use ($request, $ticket, $statusLama) {
            // PERBAIKAN: Kosongkan petugas_id agar tiket masuk ke antrean berikutnya
            $ticket->update([
                'status' => $request->status,
                'petugas_id' => null, 
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
                  ->whereIn('status_baru', ['persyaratan_lengkap', 'data_tidak_sesuai']);
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
            'formulirPermohonanBaruOrmas.biodataPengurus',
            'formulirPermohonanBaruOrmas.suratPernyataan',
            'formulirPermohonanBaruOrmas.formulirIsian'
        ])
        ->where('uuid', $uuid)
        ->where('petugas_id', $request->user()->uuid)
        ->firstOrFail();

        return view('pages.PetugasVerifikasiData.ticket.show', compact('ticket'));
    }
}