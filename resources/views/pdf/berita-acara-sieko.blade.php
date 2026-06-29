<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Verifikasi Sekretariat Ormas</title>
    <style>
        @page {
            margin: 1cm 1.5cm 1cm 1.5cm; 
        }
        body { 
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt; 
            line-height: 1.15;
            margin: 0; 
            padding: 0; 
            text-rendering: optimizeLegibility;
        }
        
        /* Kop Surat Menyesuaikan Asli */
        .kop-surat { 
            width: 100%; 
            border-bottom: 2px solid black; /* Diperhalus ketebalannya */
            padding-bottom: 6px;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .kop-surat td { vertical-align: middle; }
        .logo-container { width: 18%; text-align: left; padding-left: 10px; }
        .logo { width: 85px; height: auto; }
        
        .text-kop { 
            width: 82%; 
            text-align: center; 
            line-height: 1.25;
            padding-right: 30px; /* Agar teks benar-benar ke tengah */
        }
        .text-kop h3 { margin: 0; font-size: 13pt; font-weight: normal; }
        .text-kop h2 { margin: 0; font-size: 17pt; font-weight: bold; letter-spacing: 0.5px; }
        .text-kop p { margin: 0; font-size: 10.5pt; }
        .text-kop .email-web { font-style: italic; }

        /* Judul Dokumen Menyesuaikan Asli */
        .judul-dokumen {
            text-align: center;
            margin-bottom: 15px;
            margin-top: 20px;
        }
        .judul-dokumen h4 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.3;
            /* text-decoration: underline; dihapus agar sama dengan gambar */
        }
        .judul-dokumen p {
            margin: 0;
            margin-top: 3px;
            font-size: 11pt;
        }

        /* Utilitas Teks & Layout */
        .text-justify { text-align: justify; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
        
        /* Tabel Data & Identitas */
        table.layout-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.layout-table td { vertical-align: top; padding: 2px 0; }
        
        /* Tabel Verifikasi (Border) */
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-top: 5px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid black;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .table-bordered th {
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }

        /* Checkbox styling untuk PDF */
        .checkbox-container {
            margin-bottom: 5px;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid black;
            margin-right: 8px;
            text-align: center;
            line-height: 12px;
            font-size: 9pt;
            vertical-align: middle;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: bottom;
            height: 90px;
        }
        .materai {
            font-size: 8pt;
            color: #666;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    @php
        // Ambil logo fisik Subang
        $logoPathFisik = public_path('images/logo-subang.png'); 
        $logoBase64 = null;
        
        if (file_exists($logoPathFisik)) {
            $fileContent = file_get_contents($logoPathFisik);
            $mimeType = mime_content_type($logoPathFisik);
            $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        } else {
            // Fallback placeholder jika logo tidak ada
            $logoBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        }
    @endphp

    <table class="kop-surat">
        <tr>
            <td class="logo-container">
                <img src="{{ $logoBase64 }}" class="logo" alt="Logo Subang">
            </td>
            <td class="text-kop">
                <h3>PEMERINTAH DAERAH KABUPATEN SUBANG</h3>
                <h2>BADAN KESATUAN BANGSA DAN POLITIK</h2>
                <p class="email-web">E-mail : bakesbangpol@subang.go.id Website : bakesbangpol.subang.go.id</p>
                <p>Jl. Ade Irma Suryani No. 4 Telp. (0260) 411109 Subang 41214</p>
            </td>
        </tr>
    </table>

    <div class="judul-dokumen">
        <h4>BERITA ACARA HASIL VERIFIKASI SEKRETARIAT ORGANISASI<br>KEMASYARAKATAN</h4>
        <p>Nomor: {{ $berita_acara->nomor_berita_acara ?? '.......................................' }}</p>
    </div>

    @php
        // Helper untuk parse tanggal
        $tgl = \Carbon\Carbon::parse($berita_acara->tanggal_kunjungan)->locale('id');
        $hari = $tgl->translatedFormat('l');
        $tanggal = $tgl->translatedFormat('d');
        $bulan = $tgl->translatedFormat('F');
        $tahun = $tgl->translatedFormat('Y');
        
        // Ambil data SKT (Untuk Nomor SK, Alamat, dll)
        $permohonan = $tiket->permohonanSkt; 
        
        // Ambil data Formulir Baru (Untuk mendapatkan Nama Sekretaris yang tidak ada di permohonan_skt)
        $formulir = $tiket->formulirPermohonanBaruOrmas;
    @endphp

    <p class="text-justify" style="margin-bottom: 10px;">
        Pada hari ini <strong>{{ $hari }}</strong> tanggal <strong>{{ $tanggal }}</strong> bulan <strong>{{ $bulan }}</strong> tahun <strong>{{ $tahun }}</strong>, kami Tim Verifikasi dari Badan Kesatuan Bangsa dan Politik Kabupaten Subang berdasarkan tugas dan kewenangan dalam rangka pembinaan dan pengawasan Organisasi Kemasyarakatan di wilayah Kabupaten Subang, telah melaksanakan kegiatan verifikasi keberadaan sekretariat Organisasi Kemasyarakatan sebagai berikut:
    </p>

    <div class="section-title">I. IDENTITAS ORGANISASI KEMASYARAKATAN</div>
    <table class="layout-table">
        <tr>
            <td width="35%">Nama Organisasi</td>
            <td width="3%">:</td>
            <td width="62%">{{ $permohonan->nama_organisasi ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor SK Kemenkumham/Terdaftar</td>
            <td>:</td>
            <td>{{ $permohonan->nomor_sk_kemenkumham ?? '-' }}</td>
        </tr>
        <tr>
            <td>Alamat Sekretariat</td>
            <td>:</td>
            <td class="text-justify">{{ $permohonan->alamat_sekretariat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Ketua</td>
            <td>:</td>
            <td>{{ $permohonan->nama_ketua ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor HP Ketua</td>
            <td>:</td>
            <td>{{ $permohonan->no_kontak ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">II. PELAKSANAAN VERIFIKASI</div>
    <p class="text-justify" style="margin-bottom: 5px;">Berdasarkan hasil kunjungan lapangan yang dilaksanakan pada tanggal {{ $tgl->translatedFormat('d F Y') }}, Tim Verifikasi Bakesbangpol Kabupaten Subang memperoleh hasil sebagai berikut:</p>
    
    <table class="table-bordered">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 65%;">Uraian Verifikasi</th>
                <th style="width: 30%;">Hasil</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>Keberadaan sekretariat sesuai alamat yang dilaporkan</td>
                <td class="text-center">
                    {{ $berita_acara->keberadaan_sekretariat === 'ada' ? 'Ada' : 'Tidak Ada' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Papan nama organisasi terpasang</td>
                <td class="text-center">
                    {{ $berita_acara->papan_nama_terpasang === 'ada' ? 'Ada' : 'Tidak Ada' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Sekretariat aktif digunakan untuk kegiatan organisasi</td>
                <td class="text-center">
                    {{ $berita_acara->sekretariat_aktif ? 'Ya' : 'Tidak' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Kepengurusan organisasi dapat ditemui</td>
                <td class="text-center">
                    {{ $berita_acara->kepengurusan_ditemui ? 'Ya' : 'Tidak' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Dokumen kepengurusan tersedia</td>
                <td class="text-center">
                    {{ $berita_acara->dokumen_tersedia ? 'Ya' : 'Tidak' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Kegiatan organisasi berjalan</td>
                <td class="text-center">
                    {{ $berita_acara->kegiatan_berjalan ? 'Ya' : 'Tidak' }}
                </td>
            </tr>
            <tr>
                <td class="text-center">7</td>
                <td>Kondisi sekretariat</td>
                <td class="text-center">
                    {{ $berita_acara->kondisi_sekretariat === 'layak' ? 'Layak' : 'Kurang Layak' }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">III. KETERANGAN HASIL VERIFIKASI</div>
    <p class="text-justify" style="margin-bottom: 10px;">
        Berdasarkan hasil pemeriksaan dan konfirmasi kepada pengurus organisasi, diperoleh keterangan sebagai berikut:<br>
        {{ $berita_acara->keterangan_hasil ?? '................................................................................................................................' }}
    </p>

    <div class="section-title">IV. KESIMPULAN</div>
    <div style="margin-bottom: 10px;">
        <p style="margin-bottom: 8px;">Berdasarkan hasil verifikasi lapangan yang telah dilakukan, dapat disimpulkan bahwa:</p>
        
        <div class="checkbox-container">
            <span class="checkbox">{{ $berita_acara->kesimpulan_sekretariat === 'ditemukan_dan_aktif' ? 'V' : '' }}</span> Sekretariat Organisasi Kemasyarakatan ditemukan dan aktif sesuai alamat yang dilaporkan.
        </div>
        <div class="checkbox-container">
            <span class="checkbox">{{ $berita_acara->kesimpulan_sekretariat === 'ditemukan_tidak_aktif' ? 'V' : '' }}</span> Sekretariat Organisasi Kemasyarakatan ditemukan namun tidak aktif.
        </div>
        <div class="checkbox-container">
            <span class="checkbox">{{ $berita_acara->kesimpulan_sekretariat === 'tidak_ditemukan' ? 'V' : '' }}</span> Sekretariat Organisasi Kemasyarakatan tidak ditemukan pada alamat yang dilaporkan.
        </div>
        <div class="checkbox-container">
            <span class="checkbox">{{ $berita_acara->kesimpulan_kepengurusan === 'aktif_berkegiatan' ? 'V' : '' }}</span> Organisasi Kemasyarakatan masih melakukan kegiatan dan kepengurusan masih aktif.
        </div>
        <div class="checkbox-container">
            <span class="checkbox">{{ $berita_acara->kesimpulan_kepengurusan === 'tidak_aktif' ? 'V' : '' }}</span> Organisasi Kemasyarakatan tidak dapat menunjukkan aktivitas dan kepengurusan yang aktif.
        </div>
    </div>

    <div class="section-title">V. PENUTUP</div>
    <p class="text-justify">Demikian Berita Acara Hasil Verifikasi ini dibuat dengan sebenarnya untuk digunakan sebagaimana mestinya.</p>

    <div style="text-align: right; margin-top: 15px;">
        Subang, {{ $tanggal_cetak }}
    </div>
    
    <div style="text-align: center; font-weight: bold; margin-top: 10px;">
        TIM VERIFIKASI<br>
        BADAN KESATUAN BANGSA DAN POLITIK KABUPATEN SUBANG
    </div>

    @php
        $anggotaTim = json_decode($berita_acara->anggota_tim, true) ?? [];
    @endphp

    <table class="table-bordered" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Nama</th>
                <th style="width: 25%;">Jabatan</th>
                <th style="width: 25%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $berita_acara->ketuaTim->nama ?? '.........................' }}</td>
                <td class="text-center">Ketua Tim</td>
                <td>1.</td>
            </tr>
            @foreach($anggotaTim as $index => $anggota)
            <tr>
                <td class="text-center">{{ $index + 2 }}</td>
                <td>{{ $anggota['nama'] ?? '.........................' }}</td>
                <td class="text-center">Anggota</td>
                <td>{{ $index + 2 }}.</td>
            </tr>
            @endforeach
            @if(count($anggotaTim) == 0)
            <tr>
                <td class="text-center">2</td>
                <td>.........................................</td>
                <td class="text-center">Anggota</td>
                <td>2.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="ttd-container">
        <div style="text-align: center; margin-bottom: 10px;">Mengetahui,<br>Pengurus Organisasi Kemasyarakatan</div>
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    Ketua,<br>
                    <div style="height: 60px;"></div>
                    <div class="materai">Materai Rp10.000</div>
                    <strong><u>{{ $formulir->nama_ketua ?? $permohonan->nama_ketua ?? '................................' }}</u></strong>
                </td>
                <td style="width: 50%;">
                    Sekretaris,<br>
                    <div style="height: 70px;"></div>
                    <strong><u>{{ $formulir->nama_sekretaris ?? '................................' }}</u></strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>