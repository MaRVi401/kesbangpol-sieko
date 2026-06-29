<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Tiket;

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
}