<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\Tiket;
use App\Models\Layanan;
use App\Models\RiwayatStatusTiket;
use App\Models\JejakAudit;

use App\Models\FormulirPermohonanBaruPencatatanOrmas; 
use App\Models\BiodataPengurusOrmas;
use App\Models\SuratPernyataanOrmas;
use App\Models\FormulirIsianOrmas;

class ServiceController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->uuid;
        
        $draft = Tiket::where('users_id', $userId)
                      ->where('status', 'draft')
                      ->whereHas('layanan', function($q) {
                          $q->where('nama', 'LIKE', '%(SKT) Ormas%');
                      })
                      ->latest()
                      ->first();

        $payloadDraft = [];
        if ($draft && $draft->payload_draft) {
            if (is_string($draft->payload_draft)) {
                 $payloadDraft = json_decode($draft->payload_draft, true) ?? [];
            } else {
                 $payloadDraft = (array) $draft->payload_draft;
            }
        }
        
        $tiketUuid = $draft ? $draft->uuid : null;

        return view('pages.pemohon.formulir-pemohon.index', compact('payloadDraft', 'tiketUuid'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemohon'                            => 'required|string|max:255',
            'nomor_ktp'                               => 'required|digits:16',
            'nama_organisasi'                         => 'required|string|max:255',
            'nomor_akte_pendirian'                    => 'required|string|max:255',
            'nomor_npwp_organisasi'                   => 'nullable|digits_between:15,16',
            'pengurus'                                => 'required|array',
            'surat_pernyataan'                        => 'required|array',
            'formulir_isian'                          => 'required|array',
            'file_kop_surat'                          => 'nullable|mimes:pdf,jpeg,png,jpg,webp|max:2048',
            'file_tanda_tangan_pemohon'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pengurus.*.foto_resmi'                   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pengurus.*.file_tanda_tangan'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'surat_pernyataan.file_ttd_ketua_materai' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'surat_pernyataan.file_ttd_sekretaris'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'formulir_isian.file_logo_organisasi'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'formulir_isian.file_bendera_organisasi'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::user()->uuid;
            
            $layanan = Layanan::where('nama', 'LIKE', '%(SKT) Ormas%')->firstOrFail();
            $noTiket = 'ORMAS-' . Carbon::now()->format('dmY') . '-' . Str::upper(Str::random(4));
            $aksiAudit = 'create';
            
            $tiketUuid = $request->input('tiket_uuid');
            $tiket = null;

            if ($tiketUuid) {
                $tiket = Tiket::where('uuid', $tiketUuid)
                              ->where('users_id', $userId)
                              ->where('status', 'draft')
                              ->first();
            }

            if ($tiket) {
                $tiket->update([
                    'no_tiket' => $noTiket,
                    'status' => 'diajukan',
                    'deskripsi' => 'Permohonan Pencatatan Ormas: ' . $request->nama_organisasi,
                    'payload_draft' => null
                ]);
                $aksiAudit = 'update';
            } else {
                $tiket = Tiket::create([
                    'uuid'       => (string) Str::uuid(),
                    'users_id'   => $userId,
                    'layanan_id' => $layanan->uuid,
                    'no_tiket'   => $noTiket,
                    'status'     => 'diajukan',
                    'deskripsi'  => 'Permohonan Pencatatan Ormas: ' . $request->nama_organisasi,
                    'payload_draft' => null
                ]);
            }

            $formulir = FormulirPermohonanBaruPencatatanOrmas::create([
                'tiket_id'                  => $tiket->uuid,
                'nomor'                     => $request->nomor,
                'perihal'                   => $request->perihal,
                'nama_pemohon'              => $request->nama_pemohon,
                'tempat_lahir'              => $request->tempat_lahir,
                'tanggal_lahir'             => $request->tanggal_lahir,
                'jabatan_pemohon'           => $request->jabatan_pemohon,
                'alamat_rumah'              => $request->alamat_rumah,
                'nomor_ktp'                 => $request->nomor_ktp,
                'nama_organisasi'           => $request->nama_organisasi,
                'nomor_npwp_organisasi'     => $request->nomor_npwp_organisasi,
                'sifat_kekhususan'          => $request->sifat_kekhususan,
                'nomor_akte_pendirian'      => $request->nomor_akte_pendirian,
                'alamat_organisasi'         => $request->alamat_organisasi,
                'alamat_sekretariat'        => $request->alamat_sekretariat,
                'nama_ketua'                => $request->nama_ketua,
                'nama_sekretaris'           => $request->nama_sekretaris,
                'nama_bendahara'            => $request->nama_bendahara,
                'jumlah_anggota'            => $request->jumlah_anggota ?? 0,
                'jumlah_cabang'             => $request->jumlah_cabang ?? 0,
                'tanggal_permohonan'        => $request->tanggal_permohonan,
                'file_kop_surat'            => $this->handleFileUpload($request->file('file_kop_surat'), $userId, 'private/ormas/kop_surat'),
                'file_tanda_tangan_pemohon' => $this->handleFileUpload($request->file('file_tanda_tangan_pemohon'), $userId, 'private/ormas/ttd'),
            ]);

            $formulirUuid = $formulir->uuid;

            if ($request->has('pengurus')) {
                foreach ($request->pengurus as $key => $pengurusData) {
                    BiodataPengurusOrmas::create([
                        'uuid'                  => (string) Str::uuid(),
                        'formulir_id'           => $formulirUuid,
                        'nama_lengkap'          => $pengurusData['nama_lengkap'],
                        'tempat_lahir'          => $pengurusData['tempat_lahir'],
                        'tanggal_lahir'         => $pengurusData['tanggal_lahir'],
                        'jenis_kelamin'         => $pengurusData['jenis_kelamin'],
                        'status_perkawinan'     => $pengurusData['status_perkawinan'],
                        'agama'                 => $pengurusData['agama'],
                        'utusan_organisasi'     => $pengurusData['utusan_organisasi'] ?? null,
                        'jabatan'               => $pengurusData['jabatan'],
                        'alamat_organisasi'     => $pengurusData['alamat_organisasi'] ?? null,
                        'telepon_organisasi'    => $pengurusData['telepon_organisasi'] ?? null,
                        'alamat_rumah'          => $pengurusData['alamat_rumah'],
                        'telepon_rumah_hp'      => $pengurusData['telepon_rumah_hp'],
                        'pendidikan_terakhir'   => $pengurusData['pendidikan_terakhir'],
                        'riwayat_organisasi'    => json_encode($pengurusData['riwayat_organisasi'] ?? []),
                        'hobi'                  => $pengurusData['hobi'] ?? null,
                        'tanggal_pengisian'     => $pengurusData['tanggal_pengisian'] ?? null,
                        'foto_resmi'            => $this->handleFileUpload($request->file("pengurus.{$key}.foto_resmi"), $userId, 'private/ormas/foto_pengurus'),
                        'file_tanda_tangan'     => $this->handleFileUpload($request->file("pengurus.{$key}.file_tanda_tangan"), $userId, 'private/ormas/ttd_pengurus'),
                    ]);
                }
            }

            $pernyataan = $request->surat_pernyataan;
            SuratPernyataanOrmas::create([
                'uuid'                       => (string) Str::uuid(),
                'formulir_id'                => $formulirUuid,
                'nama_ketua'                 => $pernyataan['nama_ketua'],
                'nomor_ktp_ketua'            => $pernyataan['nomor_ktp_ketua'],
                'nama_sekretaris'            => $pernyataan['nama_sekretaris'],
                'nomor_ktp_sekretaris'       => $pernyataan['nomor_ktp_sekretaris'],
                'tanggal_surat_pernyataan'   => $pernyataan['tanggal_surat_pernyataan'] ?? null,
                'file_ttd_ketua_materai'     => $this->handleFileUpload($request->file('surat_pernyataan.file_ttd_ketua_materai'), $userId, 'private/ormas/pernyataan'),
                'file_ttd_sekretaris'        => $this->handleFileUpload($request->file('surat_pernyataan.file_ttd_sekretaris'), $userId, 'private/ormas/pernyataan'),
            ]);

            $isian = $request->formulir_isian;
            FormulirIsianOrmas::create([
                'uuid'                           => (string) Str::uuid(),
                'formulir_id'                    => $formulirUuid,
                'nama_organisasi'                => $isian['nama_organisasi'],
                'bidang_kegiatan'                => $isian['bidang_kegiatan'],
                'ruang_lingkup'                  => $isian['ruang_lingkup'],
                'alamat_sekretariat'             => $isian['alamat_sekretariat'],
                'tempat_pendirian'               => $isian['tempat_pendirian'],
                'tanggal_pendirian'              => $isian['tanggal_pendirian'],
                'asas_ciri_organisasi'           => $isian['asas_ciri_organisasi'],
                'tujuan_organisasi'              => $isian['tujuan_organisasi'],
                'nama_pendiri'                   => $isian['nama_pendiri'],
                'nama_pembina'                   => $isian['nama_pembina'] ?? null,
                'nama_penasehat'                 => $isian['nama_penasehat'] ?? null,
                'nama_ketua'                     => $isian['nama_ketua'],
                'nama_sekretaris'                => $isian['nama_sekretaris'],
                'nama_bendahara'                 => $isian['nama_bendahara'],
                'masa_bhakti_kepengurusan'       => $isian['masa_bhakti_kepengurusan'],
                'keputusan_tertinggi_organisasi' => $isian['keputusan_tertinggi_organisasi'],
                'unit_sayap_otonom'              => $isian['unit_sayap_otonom'] ?? null,
                'usaha_organisasi'               => $isian['usaha_organisasi'] ?? null,
                'sumber_keuangan'                => $isian['sumber_keuangan'],
                'file_logo_organisasi'           => $this->handleFileUpload($request->file('formulir_isian.file_logo_organisasi'), $userId, 'private/ormas/logo'),
                'file_bendera_organisasi'        => $this->handleFileUpload($request->file('formulir_isian.file_bendera_organisasi'), $userId, 'private/ormas/bendera'),
            ]);

            RiwayatStatusTiket::create([
                'tiket_id'          => $tiket->uuid,
                'users_id'          => $userId, 
                'status_sebelumnya' => $request->filled('tiket_uuid') ? 'draft' : null,
                'status_baru'       => 'diajukan'
            ]);
            
            JejakAudit::create([
                'uuid'       => (string) Str::uuid(),
                'users_id'   => $userId,
                'aksi'       => $aksiAudit,
                'nama_tabel' => 'tiket',
                'record_id'  => $tiket->uuid,
                'data_baru'  => $tiket->toArray(),
                'ip_address' => request()->ip()
            ]);

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'uuid'     => $tiket->uuid,
                'no_tiket' => $tiket->no_tiket,
                'message'  => 'Permohonan berhasil diajukan.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function autosave(Request $request)
    {
        $request->validate([
            'tiket_uuid' => 'nullable|uuid',
        ]);

        DB::beginTransaction();
        try {
            $userId = Auth::user()->uuid;
            $layanan = Layanan::where('nama', 'LIKE', '%(SKT) Ormas%')->firstOrFail();
            
            $payload = $request->except(['_token', 'tiket_uuid']);
            
            foreach ($payload as $key => $value) {
                if ($request->hasFile($key)) {
                    unset($payload[$key]);
                }
            }

            $tiket = null;
            if ($request->filled('tiket_uuid')) {
                $tiket = Tiket::where('uuid', $request->tiket_uuid)
                            ->where('users_id', $userId)
                            ->where('status', 'draft')
                            ->first();
            }

            if (!$tiket) {
                $noTiket = 'DRAFT-' . Carbon::now()->format('dmY') . '-' . Str::upper(Str::random(4));
                
                $tiket = Tiket::create([
                    'uuid'          => (string) Str::uuid(),
                    'users_id'      => $userId,
                    'layanan_id'    => $layanan->uuid,
                    'no_tiket'      => $noTiket,
                    'status'        => 'draft',
                    'deskripsi'     => 'Draft Pencatatan Ormas',
                    'payload_draft' => json_encode($payload)
                ]);

                RiwayatStatusTiket::create([
                    'tiket_id'          => $tiket->uuid,
                    'users_id'          => $userId, 
                    'status_sebelumnya' => null,
                    'status_baru'       => 'draft'
                ]);
            } else {
                $tiket->update([
                    'payload_draft' => json_encode($payload)
                ]);
            }

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'tiket_uuid' => $tiket->uuid,
                'message'    => 'Draft tersimpan jam ' . now()->format('H:i:s')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleFileUpload($file, $userId, $path)
    {
        if (!$file) return null;

        $extension = strtolower($file->getClientOriginalExtension());
        $hash = hash_file('sha256', $file->path());
        $unique = Str::random(6); 
        
        $fileName = $userId . '_' . $unique . '_' . $hash . '.' . $extension;
        Storage::putFileAs($path, $file, $fileName);
        
        return $fileName;
    }
}