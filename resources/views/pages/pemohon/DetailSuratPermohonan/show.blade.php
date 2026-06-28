@extends('layouts.main')

@section('title', 'Detail Pengajuan Tiket')

@section('content')
<div class="p-4 mt-14">
    <hr class="mb-6 border-gray-200 dark:border-gray-700">

    <div class="max-w-4xl mx-auto">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tiket #{{ $ticket->no_tiket }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Layanan: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $ticket->layanan->nama ?? 'Layanan SKT/Ormas' }}</span></p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $statusColor = match(strtolower($ticket->status)) {
                        'draft', 'diajukan' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'pemeriksaan_kelengkapan', 'verifikasi_lapangan', 'pembuatan_berita_acara', 'pembuatan_draft_skt', 'menunggu_penandatanganan', 'penomoran_skt' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'skt_disetujui', 'skt_diterbitkan', 'persyaratan_lengkap' => 'bg-green-100 text-green-800 border-green-200',
                        'data_tidak_sesuai', 'skt_ditolak' => 'bg-red-100 text-red-800 border-red-200',
                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border {{ $statusColor }}">
                    Status: {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                </span>

                @if(($jumlahRevisi ?? 0) > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border bg-purple-100 text-purple-800 border-purple-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Revisi ke-{{ $jumlahRevisi }}
                    </span>
                @endif
            </div>
        </div>

        @if(strtolower($ticket->status) === 'menunggu_lampiran')
            <div class="mb-6 bg-white dark:bg-gray-800 p-8 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Tindakan Diperlukan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-lg">
                    Permohonan Anda tertunda. Silakan selesaikan proses pengajuan dengan mengunggah seluruh dokumen persyaratan administratif yang diminta.
                </p>
                
                <a href="{{ route('pemohon.services.lampiran', $ticket->uuid) }}" 
                   class="inline-flex items-center justify-center px-8 py-3 text-sm font-extrabold text-gray-900 bg-[#39ff14] rounded-lg border border-[#32e612] hover:bg-[#32e612] focus:ring-4 focus:outline-none focus:ring-[#39ff14]/50 transition-all shadow-[0_0_15px_rgba(57,255,20,0.4)] hover:shadow-[0_0_25px_rgba(57,255,20,0.7)]">
                    <svg class="w-5 h-5 mr-2 -ml-1 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    LANJUTKAN UNGGAH LAMPIRAN
                </a>
            </div>
        @endif

        @if(in_array(strtolower($ticket->status), ['data_tidak_sesuai', 'skt_ditolak']) && isset($ticket->komentar) && $ticket->komentar->isNotEmpty())
            <div class="mb-6 p-5 rounded-xl border shadow-sm bg-red-50 border-red-200 dark:bg-red-900/10 dark:border-red-800">
                <div class="flex items-center mb-3">
                    <div class="p-2 rounded-lg bg-red-100 dark:bg-red-800 mr-3">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-400">
                        Catatan Perbaikan dari Verifikator
                    </h3>
                </div>
                <div class="space-y-4">
                    @foreach($ticket->komentar as $item)
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-red-100 dark:border-red-900/50 shadow-sm">
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                "{{ $item->komentar }}"
                            </p>
                            <span class="text-xs text-gray-500 mt-2 block">
                                Ditambahkan pada: {{ $item->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 p-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rincian Data Permohonan</h3>
            </div>
            
            <div class="p-6">
                @include('pages.pemohon.DetailSuratPermohonan.partials._detail_ormas')
            </div>
        </div>

    </div>
</div>
@endsectiony