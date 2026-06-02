@extends('layouts.app')

@section('title', 'SIEKOP KESBANGPOL Kabupaten Subang')

@section('content')
    {{-- Menggunakan @include yang benar agar struktur halaman tidak bertumpuk --}}
    @include('partials.landingPage.navbar')

    <!-- Hero Section (Jumbotron) -->
    <header id="beranda"
        class="relative pt-32 pb-16 md:pt-48 md:pb-32 overflow-hidden bg-gradient-to-br from-white to-red-50/40 dark:from-gray-900 dark:to-slate-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center gap-10 md:gap-12">
            
            <!-- Teks Kiri -->
            <div class="w-full lg:w-1/2 text-center lg:text-left order-2 lg:order-1">
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight mb-4 md:mb-6">
                    Layanan Publik Kesbangpol <br class="hidden sm:block">
                    <span class="text-red-600 dark:text-red-500 italic font-serif">Lebih Transparan & Cepat</span>
                </h1>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-300 mb-8 md:mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Sistem Informasi dan Kesiapan Organisasi Kemasyarakatan Terintegrasi Kabupaten Subang. Memudahkan pendaftaran Ormas serta permohonan Surat Rekomendasi Penelitian Online secara aman.
                </p>
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4">
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 md:py-4 bg-red-600 text-white rounded-2xl font-bold text-lg hover:bg-red-700 transition-all transform hover:scale-105 shadow-xl shadow-red-600/20 dark:shadow-none text-center">
                        Mulai Pengajuan
                    </a>
                    <a href="#layanan"
                        class="px-8 py-3 md:py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-2xl font-bold text-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm text-center">
                        Lihat Layanan
                    </a>
                </div>
            </div>

            <!-- Ilustrasi Kanan -->
            <div class="w-full lg:w-1/2 order-1 lg:order-2 flex justify-center">
                <img src="{{ asset('assets/images/landingPages/logo-jumbotron.svg') }}"
                    class="w-4/5 sm:w-full max-w-sm md:max-w-lg mx-auto drop-shadow-2xl animate-pulse"
                    style="animation-duration: 6s;" alt="Hero Illustration">
            </div>
        </div>

        <!-- Dekorasi Background Ambient (Aman dari Linter) -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 md:w-96 md:h-96 bg-red-600 bg-opacity-10 dark:bg-red-500 dark:bg-opacity-10 rounded-full blur-3xl opacity-40 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 md:w-96 md:h-96 bg-blue-600 bg-opacity-5 dark:bg-blue-500 dark:bg-opacity-5 rounded-full blur-3xl opacity-30 pointer-events-none"></div>
    </header>

    <!-- Section Layanan Utama -->
    <section id="layanan" class="py-16 bg-gray-50 dark:bg-gray-900/40 transition-colors duration-300 border-t dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-tight">
                    Layanan Pengajuan Online
                </h2>
                <div class="h-1.5 w-20 bg-red-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Kartu Layanan Ormas -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center mb-6 font-bold text-xl">
                        A
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pendaftaran & Keberadaan Ormas (SKT)</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                        Pencatatan administrasi keberadaan Organisasi Kemasyarakatan, LSM, maupun Yayasan di lingkup Kabupaten Subang.
                    </p>
                    <a href="{{ route('login') }}" class="text-red-600 dark:text-red-400 font-bold text-sm inline-flex items-center gap-2 group-hover:underline">
                        Ajukan Ormas <span class="transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Kartu Layanan Penelitian -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition group">
                    <div class="w-12 h-12 bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center mb-6 font-bold text-xl">
                        B
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Rekomendasi Surat Izin Penelitian</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                        Pelayanan pembuatan Surat Rekomendasi Penelitian bagi Mahasiswa, Dosen, Akademisi, maupun Lembaga Independen.
                    </p>
                    <a href="{{ route('login') }}" class="text-red-600 dark:text-red-400 font-bold text-sm inline-flex items-center gap-2 group-hover:underline">
                        Ajukan Izin <span class="transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Alur Kerja -->
    <section id="alur"
        class="py-16 md:py-24 bg-white dark:bg-gray-900 transition-colors duration-300 border-t dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-tight">
                    Langkah Mudah Pengajuan
                </h2>
                <div class="h-1.5 w-20 bg-red-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                @php
                    $steps = [
                        [
                            'no' => 1,
                            'title' => 'Registrasi Akun',
                            'desc' => 'Buat akun pemohon menggunakan email aktif untuk melacak berkas secara real-time.',
                        ],
                        [
                            'no' => 2,
                            'title' => 'Isi Formulir',
                            'desc' => 'Lengkapi dokumen profil dan unggah berkas persyaratan sesuai modul layanan Anda.',
                        ],
                        [
                            'no' => 3,
                            'title' => 'Verifikasi Berkas',
                            'desc' => 'Tim penilai internal Kesbangpol memproses keabsahan berkas & melakukan cek lapangan.',
                        ],
                        [
                            'no' => 4,
                            'title' => 'Unduh Dokumen',
                            'desc' => 'Jika disetujui, Surat Rekomendasi/Tanda Lapor resmi langsung diterbitkan secara digital.',
                        ],
                    ];
                @endphp

                @foreach ($steps as $step)
                    <div
                        class="group p-6 md:p-8 rounded-3xl bg-gray-50 dark:bg-gray-800 hover:bg-red-600 dark:hover:bg-red-600 transition-all duration-300 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div
                            class="w-12 h-12 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center text-xl font-extrabold text-red-600 dark:text-red-400 mb-6 shadow-sm group-hover:rotate-12 transition-transform">
                            {{ $step['no'] }}
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-white transition">
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-red-50 transition leading-relaxed">
                            {{ $step['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Section Berita -->
    <section id="berita" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900/50 transition-colors duration-300 border-t dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div class="text-left">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2 uppercase tracking-tight">
                        Berita & Informasi
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">Update terkini kebijakan regulasi dan agenda Kesbangpol.</p>
                </div>
                <a href="#"
                    class="text-red-600 dark:text-red-400 font-bold hover:underline flex items-center gap-2 transition">
                    Lihat Semua Berita
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $news = [
                        [
                            'title' => 'Sosialisasi Kebijakan Administrasi Pendaftaran Ormas Berbasis PWA 2026',
                            'date' => '02 Mei 2026',
                            'category' => 'Pengumuman',
                            'img' => asset('assets/images/landingPages/logo-kabSubang.webp'),
                        ],
                        [
                            'title' => 'Kunjungan Kerja Lapangan Tim Verifikasi Faktual Kesbangpol Subang',
                            'date' => '28 April 2026',
                            'category' => 'Kegiatan',
                            'img' => asset('assets/images/landingPages/logo-kabSubang.webp'),
                        ],
                        [
                            'title' => 'Peningkatan Keamanan Berkas dan Data Rekomendasi Lewat Sistem SIEKO',
                            'date' => '15 April 2026',
                            'category' => 'Inovasi',
                            'img' => asset('assets/images/landingPages/logo-kabSubang.webp'),
                        ],
                    ];
                @endphp

                @foreach ($news as $item)
                    <div
                        class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition duration-300 flex flex-col">
                        <img src="{{ $item['img'] }}" alt="Berita" class="w-full h-48 object-cover">
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-3 mb-3 text-xs font-semibold uppercase tracking-wider">
                                <span class="text-red-600 dark:text-red-400">{{ $item['category'] }}</span>
                                <span class="text-gray-400">|</span>
                                <span class="text-gray-500 dark:text-gray-500">{{ $item['date'] }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 line-clamp-2 leading-snug">
                                {{ $item['title'] }}
                            </h3>
                            <div class="mt-auto">
                                <a href="#"
                                    class="text-sm font-bold text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition inline-flex items-center gap-1">
                                    Baca Selengkapnya
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Tombol Back to Top -->
    <button id="back-to-top" type="button"
        class="fixed bottom-8 right-8 z-50 inline-flex items-center p-3 text-white bg-red-600 rounded-lg shadow-lg opacity-0 invisible transition-all duration-300 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 active:scale-90">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
            class="w-6 h-6">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M10.586 3l-6.586 6.586a2 2 0 0 0 -.434 2.18l.068 .145a2 2 0 0 0 1.78 1.089h2.586v5a1 1 0 0 0 1 1h6l.117 -.007a1 1 0 0 0 .883 -.993l-.001 -5h2.587a2 2 0 0 0 1.414 -3.414l-6.586 -6.586a2 2 0 0 0 -2.828 0z" />
            <path d="M15 20a1 1 0 0 1 .117 1.993l-.117 .007h-6a1 1 0 0 1 -.117 -1.993l.117 -.007h6z" />
        </svg>
    </button>

    @include('partials.landingPage.footer')
@endsection