<?php

namespace App\Http\Controllers\PetugasVerifikasiLapangan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\BeritaAcaraLapangan;
use App\Models\RiwayatStatusTiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\WordTemplateServiceSieko;

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

        $tiketHistory = Tiket::with(['layanan', 'user', 'permohonanSkt', 'formulirPermohonanBaruOrmas'])
            ->whereIn('status', [
                'review_berita_acara', 
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

        return view('pages.petugas_verifikasi_lapangan.workdesk.workdesk', compact('tiketWorkdesk'));
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
        ->where('petugas_id', $request->user()->uuid)
        ->firstOrFail();

        return view('pages.petugas_verifikasi_lapangan.workdesk.show', compact('ticket'));
    }

    public function simpanBeritaAcaraAjax(Request $request, $uuid)
    {
        try {
            $validated = $request->validate([
                'nomor_berita_acara'     => 'required|string|unique:berita_acara_lapangan,nomor_berita_acara',
                'tanggal_kunjungan'      => 'required|date|before_or_equal:today', 
                'is_sesuai'              => 'required|boolean',
                'keterangan_hasil'       => 'required|string', 
                'nama_anggota'           => 'required|array',
                'nama_anggota.*'         => 'nullable|string',
                'nomor_sk_kemenkumham'   => 'nullable|string',
            ]);

            DB::beginTransaction();
            $tiket = Tiket::findOrFail($uuid);

            if ($request->has('nomor_sk_kemenkumham') && $tiket->permohonanSkt) {
                $tiket->permohonanSkt()->update([
                    'nomor_sk_kemenkumham' => $request->nomor_sk_kemenkumham
                ]);
            }

            $anggotaTim = [];
            $namaAnggota = $request->input('nama_anggota');
            $jabatanAnggota = $request->input('jabatan_anggota');

            foreach ($namaAnggota as $index => $nama) {
                if (!empty($nama)) {
                    $anggotaTim[] = [
                        'nama'    => $nama,
                        'jabatan' => $jabatanAnggota[$index] ?? 'Anggota'
                    ];
                }
            }

            BeritaAcaraLapangan::create([
                'uuid'                   => (string) Str::uuid(),
                'tiket_id'               => $tiket->uuid,
                'ketua_tim_id'           => $request->user()->uuid, 
                'nomor_berita_acara'     => $validated['nomor_berita_acara'],
                'tanggal_kunjungan'      => $validated['tanggal_kunjungan'],
                'keterangan_hasil'       => $validated['keterangan_hasil'],
                'is_sesuai'              => $validated['is_sesuai'],
                'keberadaan_sekretariat'  => $request->keberadaan_sekretariat,
                'papan_nama_terpasang'    => $request->papan_nama_terpasang,
                'sekretariat_aktif'       => $request->sekretariat_aktif,
                'kondisi_sekretariat'     => $request->kondisi_sekretariat,
                'kepengurusan_ditemui'    => $request->kepengurusan_ditemui,
                'dokumen_tersedia'        => $request->dokumen_tersedia,
                'kegiatan_berjalan'       => $request->kegiatan_berjalan,
                'kesimpulan_sekretariat'  => $request->kesimpulan_sekretariat,
                'kesimpulan_kepengurusan' => $request->kesimpulan_kepengurusan,
                'anggota_tim'             => json_encode($anggotaTim),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data Berita Acara berhasil disimpan.',
                'uuid'    => $tiket->uuid,
                'pdf_url' => route('verif_lapangan.ticket.generate_pdf', $tiket->uuid)
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors'  => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }

    public function generatePdfBeritaAcaraLapangan(Request $request, string $uuid, WordTemplateServiceSieko $pdfService)
    {
        try {
            $tiket = Tiket::with([
                'user',
                'layanan',
                'permohonanSkt',
                'formulirPermohonanBaruOrmas',
                'beritaAcaraLapangan.ketuaTim'
            ])->findOrFail($uuid);

            if (!$tiket->beritaAcaraLapangan) {
                return redirect()->back()->with('error', 'Data Berita Acara belum lengkap atau belum dibuat.');
            }

            return $pdfService->generateDokumenBeritaAcara($tiket);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Tiket tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh PDF: ' . $e->getMessage());
        }
    }

    public function simpanBeritaAcara(Request $request, $uuid)
    {
        $validated = $request->validate([
            'nomor_berita_acara'     => 'required|string|unique:berita_acara_lapangan,nomor_berita_acara',
            'tanggal_kunjungan'      => 'required|date|before_or_equal:today', 
            'is_sesuai'              => 'required|boolean',
            'keterangan_hasil'       => 'required|string', 
            'nama_anggota'           => 'required|array',
            'nama_anggota.*'         => 'nullable|string',
            'nomor_sk_kemenkumham'   => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $tiket = Tiket::findOrFail($uuid);

            if ($request->has('nomor_sk_kemenkumham') && $tiket->permohonanSkt) {
                $tiket->permohonanSkt()->update([
                    'nomor_sk_kemenkumham' => $request->nomor_sk_kemenkumham
                ]);
            }

            $anggotaTim = [];
            $namaAnggota = $request->input('nama_anggota');
            $jabatanAnggota = $request->input('jabatan_anggota');

            foreach ($namaAnggota as $index => $nama) {
                if (!empty($nama)) {
                    $anggotaTim[] = [
                        'nama'    => $nama,
                        'jabatan' => $jabatanAnggota[$index] ?? 'Anggota'
                    ];
                }
            }

            BeritaAcaraLapangan::create([
                'uuid'                   => (string) Str::uuid(),
                'tiket_id'               => $tiket->uuid,
                'ketua_tim_id'           => $request->user()->uuid, 
                'nomor_berita_acara'     => $validated['nomor_berita_acara'],
                'tanggal_kunjungan'      => $validated['tanggal_kunjungan'],
                'keterangan_hasil'       => $validated['keterangan_hasil'],
                'is_sesuai'              => $validated['is_sesuai'],
                'keberadaan_sekretariat'  => $request->keberadaan_sekretariat,
                'papan_nama_terpasang'    => $request->papan_nama_terpasang,
                'sekretariat_aktif'       => $request->sekretariat_aktif,
                'kondisi_sekretariat'     => $request->kondisi_sekretariat,
                'kepengurusan_ditemui'    => $request->kepengurusan_ditemui,
                'dokumen_tersedia'        => $request->dokumen_tersedia,
                'kegiatan_berjalan'       => $request->kegiatan_berjalan,
                'kesimpulan_sekretariat'  => $request->kesimpulan_sekretariat,
                'kesimpulan_kepengurusan' => $request->kesimpulan_kepengurusan,
                'anggota_tim'             => json_encode($anggotaTim),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data Berita Acara berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBeritaAcara(Request $request, $uuid)
    {
        $validated = $request->validate([
            'tanggal_kunjungan'      => 'required|date|before_or_equal:today', 
            'is_sesuai'              => 'required|boolean',
            'keterangan_hasil'       => 'required|string', 
            'nomor_sk_kemenkumham'   => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $tiket = Tiket::findOrFail($uuid);
            $beritaAcara = BeritaAcaraLapangan::where('tiket_id', $tiket->uuid)->firstOrFail();

            if ($request->has('nomor_sk_kemenkumham') && $tiket->permohonanSkt) {
                $tiket->permohonanSkt()->update([
                    'nomor_sk_kemenkumham' => $request->nomor_sk_kemenkumham
                ]);
            }

            $anggotaArray = array_map('trim', explode(',', $request->anggota_tim));
            $anggotaJson = [];
            foreach($anggotaArray as $nama) {
                if(!empty($nama)) {
                    $anggotaJson[] = ['nama' => $nama, 'jabatan' => 'Anggota'];
                }
            }

            $beritaAcara->update([
                'tanggal_kunjungan'       => $validated['tanggal_kunjungan'],
                'keterangan_hasil'        => $validated['keterangan_hasil'],
                'is_sesuai'               => $validated['is_sesuai'],
                'keberadaan_sekretariat'  => $request->keberadaan_sekretariat,
                'papan_nama_terpasang'    => $request->papan_nama_terpasang,
                'sekretariat_aktif'       => $request->sekretariat_aktif,
                'kondisi_sekretariat'     => $request->kondisi_sekretariat,
                'kepengurusan_ditemui'    => $request->kepengurusan_ditemui,
                'dokumen_tersedia'        => $request->dokumen_tersedia,
                'kegiatan_berjalan'       => $request->kegiatan_berjalan,
                'kesimpulan_sekretariat'  => $request->kesimpulan_sekretariat,
                'kesimpulan_kepengurusan' => $request->kesimpulan_kepengurusan,
                'anggota_tim'             => json_encode($anggotaJson),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Berita Acara berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function updateBeritaAcaraAjax(Request $request, $uuid)
    {
        try {
            $validated = $request->validate([
                'tanggal_kunjungan'      => 'required|date|before_or_equal:today', 
                'is_sesuai'              => 'required|boolean',
                'keterangan_hasil'       => 'required|string', 
                'nomor_sk_kemenkumham'   => 'nullable|string',
            ]);

            DB::beginTransaction();
            $tiket = Tiket::findOrFail($uuid);
            $beritaAcara = BeritaAcaraLapangan::where('tiket_id', $tiket->uuid)->firstOrFail();

            if ($request->has('nomor_sk_kemenkumham') && $tiket->permohonanSkt) {
                $tiket->permohonanSkt()->update([
                    'nomor_sk_kemenkumham' => $request->nomor_sk_kemenkumham
                ]);
            }

            $beritaAcara->update([
                'tanggal_kunjungan'       => $validated['tanggal_kunjungan'],
                'keterangan_hasil'        => $validated['keterangan_hasil'],
                'is_sesuai'               => $validated['is_sesuai'],
                'keberadaan_sekretariat'  => $request->keberadaan_sekretariat,
                'papan_nama_terpasang'    => $request->papan_nama_terpasang,
                'sekretariat_aktif'       => $request->sekretariat_aktif,
                'kondisi_sekretariat'     => $request->kondisi_sekretariat,
                'kepengurusan_ditemui'    => $request->kepengurusan_ditemui,
                'dokumen_tersedia'        => $request->dokumen_tersedia,
                'kegiatan_berjalan'       => $request->kegiatan_berjalan,
                'kesimpulan_sekretariat'  => $request->kesimpulan_sekretariat,
                'kesimpulan_kepengurusan' => $request->kesimpulan_kepengurusan,
                'anggota_tim'             => json_encode(explode(',', $request->anggota_tim)),
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Berita Acara berhasil diperbarui.', 'uuid' => $tiket->uuid], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadScanBeritaAcara(Request $request, $uuid)
    {
        $request->validate([
            'file_berita_acara_path' => 'required|mimes:pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();
            $tiket = Tiket::with('beritaAcaraLapangan')->findOrFail($uuid);

            if (!$tiket->beritaAcaraLapangan) {
                return redirect()->back()->with('error', 'Data Berita Acara belum dibuat.');
            }

            if ($request->hasFile('file_berita_acara_path')) {
                $file = $request->file('file_berita_acara_path');
                $path = $file->store('berita_acara_scan', 'public');
                
                $tiket->beritaAcaraLapangan->update([
                    'file_berita_acara_path' => $path
                ]);
            }

            $statusLama = $tiket->status;
            $statusBaru = 'review_berita_acara'; 
            
            $tiket->update(['status' => $statusBaru]);

            RiwayatStatusTiket::create([
                'uuid'              => (string) Str::uuid(),
                'tiket_id'          => $tiket->uuid,
                'users_id'          => $request->user()->uuid,
                'status_sebelumnya' => $statusLama,
                'status_baru'       => $statusBaru,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'File scan Berita Acara berhasil diunggah. Status tiket berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }
    }
}