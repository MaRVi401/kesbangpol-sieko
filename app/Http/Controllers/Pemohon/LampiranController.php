<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Tiket;
use App\Models\PermohonanSkt;
use App\Models\BiodataPengurusOrmas;
use App\Models\FormulirPermohonanBaruPencatatanOrmas;
use App\Models\RiwayatStatusTiket;
use App\Models\JejakAudit;

class LampiranController extends Controller
{
    public function create($tiketUuid)
    {
        $userId = Auth::user()->uuid;

        $tiket = Tiket::where('uuid', $tiketUuid)
                      ->where('users_id', $userId)
                      ->where('status', 'menunggu_lampiran')
                      ->firstOrFail();

        $permohonan = PermohonanSkt::where('tiket_id', $tiket->uuid)->first();
        $formulir = FormulirPermohonanBaruPencatatanOrmas::with('biodataPengurus')->where('tiket_id', $tiket->uuid)->first();

        // Cek kelengkapan setiap bagian
        $isLegalitasDone = $permohonan && $permohonan->akta_pendirian_path && $permohonan->sk_kemenkumham_path && $permohonan->file_ad_art_path && $permohonan->file_program_kerja_path;
        
        $isDomisiliDone = $permohonan && $permohonan->file_sk_kepengurusan_path && $permohonan->file_surat_mandat_path && $permohonan->surat_domisili_path && $permohonan->file_foto_kantor_path && $permohonan->file_npwp_path;
        
        $isKtpDone = false;
        if ($formulir && $formulir->biodataPengurus) {
            $ktpCount = $formulir->biodataPengurus->whereNotNull('file_ktp_path')->count();
            $isKtpDone = ($ktpCount >= 3); // Harus ada KTP Ketua, Sekretaris, Bendahara
        }

        return view('pages.pemohon.formulir-pemohon.lampiran', compact(
            'tiketUuid', 'tiket', 'isLegalitasDone', 'isDomisiliDone', 'isKtpDone'
        ));
    }

    public function storeLegalitas(Request $request, $tiketUuid)
    {
        $request->validate([
            'akta_pendirian'     => 'required|mimes:pdf|max:10240',
            'sk_kemenkumham'     => 'required|mimes:pdf|max:10240',
            'file_ad_art'        => 'required|mimes:pdf|max:10240',
            'file_program_kerja' => 'required|mimes:pdf|max:10240',
        ]);

        return $this->processUpload($request, $tiketUuid, function($request, $userId, $permohonan, $formulir) {
            $permohonan->update([
                'akta_pendirian_path'     => $this->handleFileUpload($request->file('akta_pendirian'), $userId, 'private/ormas/lampiran'),
                'sk_kemenkumham_path'     => $this->handleFileUpload($request->file('sk_kemenkumham'), $userId, 'private/ormas/lampiran'),
                'file_ad_art_path'        => $this->handleFileUpload($request->file('file_ad_art'), $userId, 'private/ormas/lampiran'),
                'file_program_kerja_path' => $this->handleFileUpload($request->file('file_program_kerja'), $userId, 'private/ormas/lampiran'),
            ]);
        }, 'Dokumen Legalitas Utama berhasil diunggah.');
    }

    public function storeDomisili(Request $request, $tiketUuid)
    {
        $request->validate([
            'file_sk_kepengurusan' => 'required|mimes:pdf|max:10240',
            'file_surat_mandat'    => 'required|mimes:pdf|max:10240',
            'surat_domisili'       => 'required|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'file_foto_kantor'     => 'required|mimes:jpeg,png,jpg,webp|max:5120',
            'file_npwp'            => 'required|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'file_sk_terlapor'     => 'nullable|mimes:pdf|max:10240',
        ]);

        return $this->processUpload($request, $tiketUuid, function($request, $userId, $permohonan, $formulir) {
            $permohonan->update([
                'file_sk_kepengurusan_path' => $this->handleFileUpload($request->file('file_sk_kepengurusan'), $userId, 'private/ormas/lampiran'),
                'file_surat_mandat_path'    => $this->handleFileUpload($request->file('file_surat_mandat'), $userId, 'private/ormas/lampiran'),
                'surat_domisili_path'       => $this->handleFileUpload($request->file('surat_domisili'), $userId, 'private/ormas/lampiran'),
                'file_foto_kantor_path'     => $this->handleFileUpload($request->file('file_foto_kantor'), $userId, 'private/ormas/lampiran'),
                'file_npwp_path'            => $this->handleFileUpload($request->file('file_npwp'), $userId, 'private/ormas/lampiran'),
                'file_sk_terlapor_path'     => $request->hasFile('file_sk_terlapor') ? $this->handleFileUpload($request->file('file_sk_terlapor'), $userId, 'private/ormas/lampiran') : null,
            ]);
        }, 'Dokumen Identitas & Domisili berhasil diunggah.');
    }

    public function storeKtp(Request $request, $tiketUuid)
    {
        $request->validate([
            'ktp_ketua'      => 'required|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'ktp_sekretaris' => 'required|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'ktp_bendahara'  => 'required|mimes:pdf,jpeg,png,jpg,webp|max:5120',
        ]);

        return $this->processUpload($request, $tiketUuid, function($request, $userId, $permohonan, $formulir) {
            BiodataPengurusOrmas::where('formulir_id', $formulir->uuid)->where('jabatan', 'Ketua')->update([
                'file_ktp_path' => $this->handleFileUpload($request->file('ktp_ketua'), $userId, 'private/ormas/ktp')
            ]);
            BiodataPengurusOrmas::where('formulir_id', $formulir->uuid)->where('jabatan', 'Sekretaris')->update([
                'file_ktp_path' => $this->handleFileUpload($request->file('ktp_sekretaris'), $userId, 'private/ormas/ktp')
            ]);
            BiodataPengurusOrmas::where('formulir_id', $formulir->uuid)->where('jabatan', 'Bendahara')->update([
                'file_ktp_path' => $this->handleFileUpload($request->file('ktp_bendahara'), $userId, 'private/ormas/ktp')
            ]);
        }, 'KTP Pengurus Inti berhasil diunggah.');
    }

    public function finalize(Request $request, $tiketUuid)
    {
        $userId = Auth::user()->uuid;
        $tiket = Tiket::where('uuid', $tiketUuid)->where('users_id', $userId)->firstOrFail();
        
        $permohonan = PermohonanSkt::where('tiket_id', $tiket->uuid)->first();
        $formulir = FormulirPermohonanBaruPencatatanOrmas::with('biodataPengurus')->where('tiket_id', $tiket->uuid)->first();

        // Validasi Akhir
        if (!$permohonan || !$permohonan->akta_pendirian_path || !$permohonan->file_sk_kepengurusan_path || $formulir->biodataPengurus->whereNotNull('file_ktp_path')->count() < 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum menyelesaikan semua form unggahan. Harap unggah seluruh dokumen yang wajib.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $tiket->update(['status' => 'diajukan']);

            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->uuid, 'users_id' => $userId, 
                'status_sebelumnya' => 'menunggu_lampiran', 'status_baru' => 'diajukan'
            ]);

            JejakAudit::create([
                'uuid' => (string) Str::uuid(), 'users_id' => $userId, 'aksi' => 'update',
                'nama_tabel' => 'tiket', 'record_id' => $tiket->uuid,
                'data_baru' => json_encode(['status' => 'diajukan', 'action' => 'finalize_lampiran']),
                'ip_address' => request()->ip()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Luar biasa! Permohonan Anda berhasil dikirim ke verifikator.',
                'redirect_url' => route('pemohon.history.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()], 500);
        }
    }

    private function processUpload(Request $request, $tiketUuid, $callback, $successMsg)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::user()->uuid;
            $tiket = Tiket::where('uuid', $tiketUuid)->where('users_id', $userId)->firstOrFail();
            $permohonan = PermohonanSkt::where('tiket_id', $tiket->uuid)->first();
            $formulir = FormulirPermohonanBaruPencatatanOrmas::where('tiket_id', $tiket->uuid)->first();

            $callback($request, $userId, $permohonan, $formulir);
            
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $successMsg]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Gagal mengunggah: ' . $e->getMessage()], 500);
        }
    }

    private function handleFileUpload($file, $userId, $path)
    {
        if (!$file) return null;
        $extension = strtolower($file->getClientOriginalExtension());
        $hash = hash_file('sha256', $file->path());
        $fileName = $userId . '_' . Str::random(6) . '_' . $hash . '.' . $extension;
        Storage::putFileAs($path, $file, $fileName);
        return $fileName;
    }
}