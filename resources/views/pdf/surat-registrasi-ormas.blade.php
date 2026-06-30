<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Registrasi Ormas</title>
    <style>
        @page {
            /* Margin dokumen resmi */
            margin: 1.5cm 2cm 2cm 2cm; 
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11pt; 
            line-height: 1.3;
            margin: 0; 
            padding: 0; 
            color: #000;
            position: relative;
        }

        /* --- WATERMARK --- */
        #watermark {
            position: fixed;
            top: 25%; /* Sesuaikan posisi vertikal ke tengah */
            left: 0;
            width: 100%;
            text-align: center;
            opacity: 0.15; /* Tingkat transparansi watermark (0.1 - 0.3 ideal) */
            z-index: -100; /* Memastikan berada di belakang teks */
        }
        #watermark img {
            width: 450px; /* Ukuran besaran watermark */
            height: auto;
        }

        /* --- KOP SURAT --- */
        .kop-surat {
            text-align: center;
            margin-bottom: 25px;
        }
        .kop-surat img {
            width: 75px; 
            height: auto;
            margin-bottom: 8px;
        }
        .kop-surat .line1 {
            font-size: 13pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: block;
        }
        .kop-surat .line2 {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: block;
            margin-top: 3px;
        }

        /* --- JUDUL SURAT --- */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 25px;
        }
        .judul-dokumen h4 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .judul-dokumen p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }

        /* --- ISI SURAT --- */
        .tabel-list {
            width: 100%;
            border-collapse: collapse;
        }
        .tabel-list > tbody > tr > td {
            vertical-align: top;
            padding-bottom: 8px;
            text-align: justify;
        }
        .tabel-list .nomor { width: 4%; text-align: left; }
        .tabel-list .konten { width: 96%; }

        /* Tabel Rincian */
        .tabel-sub {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .tabel-sub > tbody > tr > td {
            vertical-align: top;
            padding: 2px 0;
            text-align: left;
        }
        .ts-label { width: 33%; font-weight: normal; }
        .ts-titik { width: 3%; text-align: center; }
        .ts-value { width: 64%; font-weight: normal; }

        .tabel-pengurus {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }
        .tabel-pengurus td {
            vertical-align: top;
            padding: 1px 0;
        }

        /* --- FOOTER & TANDA TANGAN --- */
        .footer-container {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
            border-collapse: collapse;
        }
        .footer-container td {
            vertical-align: top;
        }
        
        /* Kotak Putus-putus */
        .dashed-box {
            border: 2px dashed #000;
            width: 130px;
            padding: 5px 0;
            text-align: center;
            font-weight: bold;
            font-style: italic; /* Menyesuaikan gambar: font agak miring/italic */
            font-size: 11pt;
            min-height: 18px; 
            margin-left: 10px;
        }
    </style>
</head>
<body>

    @php
        // LOGO KOP SURAT & WATERMARK
        $logoPathFisik = public_path('images/logo-subang.png'); 
        $logoBase64 = null;
        
        if (file_exists($logoPathFisik)) {
            $fileContent = file_get_contents($logoPathFisik);
            $mimeType = mime_content_type($logoPathFisik);
            $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        } else {
            $logoBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        }
    @endphp

    <div id="watermark">
        <img src="{{ $logoBase64 }}" alt="Watermark Subang">
    </div>

    <div class="kop-surat">
        <img src="{{ $logoBase64 }}" alt="Logo Subang">
        <span class="line1">PEMERINTAH DAERAH KABUPATEN SUBANG</span>
        <span class="line2">BADAN KESATUAN BANGSA DAN POLITIK</span>
    </div>

    <div class="judul-dokumen">
        <h4>SURAT REGISTRASI PELAPORAN ORMAS</h4>
        <p>Nomor : {{ $surat_registrasi->nomor_surat_registrasi }}</p>
    </div>

    <table class="tabel-list">
        <tr>
            <td class="nomor">1.</td>
            <td class="konten">
                Dasar Surat dari {{ $surat_registrasi->nama_organisasi_pemohon }}, Nomor : {{ $surat_registrasi->nomor_surat_pemohon }} Tanggal {{ $surat_registrasi->tanggal_surat_pemohon ? \Carbon\Carbon::parse($surat_registrasi->tanggal_surat_pemohon)->locale('id')->translatedFormat('d F Y') : '-' }} Hal {{ $surat_registrasi->perihal_surat_pemohon }},
            </td>
        </tr>
        
        <tr>
            <td class="nomor">2.</td>
            <td class="konten">
                Badan Kesatuan Bangsa dan Politik Kabupaten Subang telah menerima laporan bahwa :
                <table class="tabel-sub">
                    <tr>
                        <td class="ts-label">Nama Organisasi</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value" style="font-weight: bold; text-transform: uppercase;">{{ $surat_registrasi->nama_ormas }}</td>
                    </tr>
                    <tr>
                        <td class="ts-label">Tanggal Berdiri</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">{{ $surat_registrasi->tanggal_berdiri ? \Carbon\Carbon::parse($surat_registrasi->tanggal_berdiri)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ts-label">Bidang Kegiatan</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">{{ $surat_registrasi->bidang_kegiatan }}</td>
                    </tr>
                    <tr>
                        <td class="ts-label">NPWP</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">{{ $surat_registrasi->npwp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ts-label">SK Kepengurusan</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">
                            {{ $surat_registrasi->sk_kepengurusan_penerbit }}
                            <table class="tabel-pengurus">
                                <tr><td style="width: 25%;">Ketua</td><td style="width: 5%;">:</td><td>{{ $surat_registrasi->nama_ketua }}</td></tr>
                                <tr><td>Sekretaris</td><td>:</td><td>{{ $surat_registrasi->nama_sekretaris }}</td></tr>
                                <tr><td>Bendahara</td><td>:</td><td>{{ $surat_registrasi->nama_bendahara }}</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="ts-label">Akta Notaris</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">
                            {{ $surat_registrasi->akta_notaris_keterangan ?? 'Pendirian Perkumpulan' }}<br>
                            Notaris : {{ $surat_registrasi->akta_notaris_nama ?? '-' }}<br>
                            Nomor {{ $surat_registrasi->akta_notaris_nomor ?? '-' }} Tanggal {{ $surat_registrasi->akta_notaris_tanggal ? \Carbon\Carbon::parse($surat_registrasi->akta_notaris_tanggal)->locale('id')->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="ts-label">SK Kemenkumham</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">
                            {{ $surat_registrasi->sk_kemenkumham_keterangan ?? 'Pengesahan Pendirian Perkumpulan' }}<br>
                            Nomor : {{ $surat_registrasi->sk_kemenkumham_nomor ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="ts-label">Alamat Sekretariat</td>
                        <td class="ts-titik">:</td>
                        <td class="ts-value">{{ $surat_registrasi->alamat_sekretariat }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td class="nomor">3.</td>
            <td class="konten">
                Data tersebut diatas sesuai dengan berkas yang diserahkan oleh {{ $surat_registrasi->nama_organisasi_pemohon }};
            </td>
        </tr>

        <tr>
            <td class="nomor">4.</td>
            <td class="konten">
                Surat Registrasi Pelaporan Ormas di Kabupaten Subang ini dibuat sebagai keterangan bahwa {{ $surat_registrasi->nama_ormas }} telah teregistrasi di Badan Kesatuan Bangsa dan Politik Kabupaten Subang.
            </td>
        </tr>

        <tr>
            <td class="nomor">5.</td>
            <td class="konten">
                Surat Registrasi Pelaporan Ormas di Kabupaten Subang ini berlaku Sampai dengan tanggal {{ $surat_registrasi->masa_berlaku_sampai ? \Carbon\Carbon::parse($surat_registrasi->masa_berlaku_sampai)->locale('id')->translatedFormat('d F Y') : '-' }}. Dalam hal terjadi Perubahan Domisili dan atau Perubahan/Penggantian nama-nama pengurus di atas, maka secepatnya harus dilaporkan untuk mendapatkan pertimbangan lebih lanjut dari Badan Kesatuan Bangsa dan Politik Kabupaten Subang.
            </td>
        </tr>

        <tr>
            <td class="nomor">6.</td>
            <td class="konten">
                Surat Registrasi Pelaporan Ormas ini hanya bersifat <strong>pencatatan</strong>.
            </td>
        </tr>

        <tr>
            <td class="nomor">7.</td>
            <td class="konten">
                Apabila dikemudian hari terdapat kekeliruan, kesalahan, penyimpangan, penyalahgunaan dan pelanggaran hukum akan dilakukan perbaikan, pembekuan dan atau pencabutan sesuai ketentuan yang berlaku.
            </td>
        </tr>

        <tr>
            <td class="nomor">8.</td>
            <td class="konten">
                Setiap 6 (enam) bulan sekali agar menyampaikan laporan perkembangan kegiatan kepada Badan Kesatuan Bangsa dan Politik Kabupaten Subang.
            </td>
        </tr>
    </table>

    @php
        // LOGIKA KOTAK PUTUS-PUTUS SESUAI PERMINTAAN GAMBAR (BARU, PERUBAHAN, REGISTRASI)
        $jenis_permohonan = strtolower($tiket->permohonanSkt->jenis_permohonan ?? 'baru');
        $teksBox = '';
        
        if ($jenis_permohonan == 'perubahan') {
            $teksBox = 'Perubahan';
        } elseif ($jenis_permohonan == 'registrasi') {
            $teksBox = 'Registrasi';
        }
        // Jika 'baru', $teksBox tetap kosong string (''), sehingga div dashed-box tidak akan dicetak.
    @endphp

    <table class="footer-container">
        <tr>
            <td style="width: 40%; vertical-align: bottom;">
                @if($teksBox != '')
                <div class="dashed-box">
                    {{ $teksBox }}
                </div>
                @endif
            </td>
            
            <td style="width: 60%; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 15%;"></td> 
                        <td style="width: 85%; text-align: left;">
                            Subang, {{ $surat_registrasi->tanggal_ditetapkan ? \Carbon\Carbon::parse($surat_registrasi->tanggal_ditetapkan)->locale('id')->translatedFormat('d F Y') : '-' }}<br>
                            
                            {!! nl2br(e($surat_registrasi->penandatangan_jabatan ?? 'ANALIS KEBIJAKAN AHLI MUDA')) !!}<br>
                            
                            <br><br><br><br>
                            
                            <u><strong>{{ $surat_registrasi->analis->nama ?? $surat_registrasi->penandatangan_nama }}</strong></u><br>
                            {{ $surat_registrasi->penandatangan_pangkat }}<br>
                            NIP. {{ $surat_registrasi->penandatangan_nip }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>