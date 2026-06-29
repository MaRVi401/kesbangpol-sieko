@extends('layouts.main')

@section('title', 'Proses Verifikasi Tiket')

@section('content')
<div class="p-4 mt-14">
    <div class="max-w-5xl mx-auto">

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Gagal!</span> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Ada kesalahan pengisian data:</span>
                <ul class="mt-1.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Verifikasi Tiket #{{ $ticket->no_tiket }}</h2>
                <p class="text-sm text-gray-500">Pemohon: {{ $ticket->user->nama ?? '-' }}</p>
            </div>
            
            @if(empty($ticket->beritaAcaraLapangan))
                <button type="button" 
                        data-modal-target="update-modal-{{ $ticket->uuid }}" 
                        data-modal-toggle="update-modal-{{ $ticket->uuid }}" 
                        class="bg-green-400 hover:bg-green-500 text-gray-900 font-bold rounded-lg text-sm px-5 py-2.5 transition-all duration-200 shadow-[0_0_15px_rgba(163,230,53,0.6)] hover:shadow-[0_0_25px_rgba(163,230,53,0.9)] cursor-pointer">
                    Buat Berita Acara
                </button>
            @else
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" 
                            data-modal-target="edit-modal-{{ $ticket->uuid }}" 
                            data-modal-toggle="edit-modal-{{ $ticket->uuid }}" 
                            class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold rounded-lg text-sm px-4 py-2.5 transition-all duration-200 shadow-md cursor-pointer flex items-center gap-1">
                        <i class="ti ti-edit text-base"></i> Edit Berita Acara
                    </button>
                    
                    <a href="{{ route('verif_lapangan.ticket.generate_pdf', $ticket->uuid) }}" target="_blank"
                       class="bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-bold rounded-lg text-sm px-4 py-2.5 transition-all duration-200 shadow-md flex items-center gap-1">
                        <i class="ti ti-download text-base"></i> Download PDF
                    </a>
                </div>
            @endif
        </div>

        @if(!empty($ticket->beritaAcaraLapangan))
        <div class="bg-blue-50 dark:bg-gray-800/50 rounded-xl shadow-sm border border-blue-200 dark:border-gray-700 p-6 mb-8 animate-fade-in">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Upload Hasil Scan Berita Acara</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Silakan unduh PDF di atas, lakukan tanda tangan basah bersama tim dan ormas, kemudian pindai (scan) menjadi format PDF lalu unggah di bawah ini.</p>
            
            <form action="{{ route('verif_lapangan.ticket.upload_scan', $ticket->uuid) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block mb-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">File Berita Acara Resmi (Scan PDF)</label>
                    <input type="file" name="file_berita_acara_path" accept=".pdf" required
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm px-5 py-2.5 shadow-md transition-all cursor-pointer">
                    Upload Berkas Scan
                </button>
            </form>
            
            @if($ticket->beritaAcaraLapangan->file_berita_acara_path)
                <div class="mt-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400 font-semibold flex items-center gap-2">
                    <i class="ti ti-circle-check text-lg"></i> Berkas fisik hasil scan telah terarsip dengan aman di dalam sistem.
                </div>
            @endif
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 p-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Dokumen & Data Pemohon</h3>
            </div>
            
            <div class="p-6">
                @include('pages.pemohon.DetailSuratPermohonan.partials._detail_ormas')
            </div>
        </div>

    </div>
</div>

@include('pages.petugas_verifikasi_lapangan.workdesk.Partials.modal-berita-acara') 

@if(!empty($ticket->beritaAcaraLapangan))
    @include('pages.petugas_verifikasi_lapangan.workdesk.Partials.modal-edit-berita-acara') 
@endif

@endsection