<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Tiket;
use App\Models\SuratRegistrasiOrmas;

class WordTemplateServiceSieko
{
    public function generateDokumenBeritaAcara(Tiket $tiket)
    {
        try {
            $beritaAcara = $tiket->beritaAcaraLapangan;
            
            if (!$beritaAcara) {
                throw new \Exception("Data Berita Acara belum tersedia untuk tiket ini.");
            }

            $data = [
                'tiket'         => $tiket,
                'berita_acara'  => $beritaAcara,
                'tanggal_cetak' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
            ];

            $pdf = Pdf::loadView('pdf.berita-acara-sieko', $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

            $cleanNoTiket = str_replace(['/', '\\', ' '], '-', $tiket->no_tiket ?? $tiket->uuid);
            $fileName = 'Berita_Acara_Lapangan_' . $cleanNoTiket . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Error HTML to PDF (Sieko): ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat mencetak PDF: ' . $e->getMessage());
        }
    }

    public function generateSuratRegistrasiOrmas(Tiket $tiket)
    {
        try {
            $suratRegistrasi = SuratRegistrasiOrmas::where('tiket_id', $tiket->uuid)->first();

            if (!$suratRegistrasi) {
                // Return back() tidak bisa di dalam service, jadi kita throw exception
                throw new \Exception('Data Surat Registrasi Ormas belum tersedia untuk tiket ini.');
            }

            // 1. Ambil data Kaban dan Join ke tabel kaban untuk mendapatkan NIP
            $kaban = \App\Models\User::select('users.nama', 'kaban.nip')
                ->join('kaban', 'users.uuid', '=', 'kaban.users_id')
                ->where('users.role', 'kaban')
                ->first();

            $data = [
                'tiket'            => $tiket,
                'surat_registrasi' => $suratRegistrasi,
                'kaban'            => $kaban, // 2. Lempar data Kaban ke view blade
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
            abort(500, 'Terjadi kesalahan saat mengunduh surat: ' . $e->getMessage());
        }
    }
}