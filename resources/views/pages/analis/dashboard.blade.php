@extends('layouts.master')

@section('title', 'Dashboard Analis Kebijakan Ahli Muda')

@section('content')
    <div class="p-4 mt-14">
        <div class="mb-8 border-b border-gray-200 pb-6 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-center md:text-left">
                    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        @php
                            $hour = date('H');
                            $sapaan = $hour < 12 ? 'Pagi' : ($hour < 15 ? 'Siang' : ($hour < 18 ? 'Sore' : 'Malam'));
                        @endphp
                        Selamat <span id="sapaan-teks">{{ $sapaan }}</span>,
                        <span class="block md:inline text-transparent bg-clip-text bg-linear-to-r from-blue-600 to-cyan-500 uppercase font-black">
                            {{ auth()->user()->nama }}
                        </span>
                    </h2>
                    <p class="mt-1 text-xs md:text-sm text-gray-500 dark:text-gray-400 tracking-wider">
                        Meja Kerja Analis Kebijakan Ahli Muda
                    </p>
                </div>
                
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <div class="flex items-center justify-center md:justify-end space-x-3 md:space-x-4 bg-white dark:bg-gray-800 px-4 py-2 md:px-5 md:py-2.5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all hover:shadow-md">
                        <div class="flex flex-col items-center md:items-end border-r border-gray-200 dark:border-gray-600 pr-3 md:pr-4">
                            <span id="realtime-clock" class="text-lg md:text-xl font-black font-mono text-blue-600 dark:text-blue-400 leading-none">00:00:00</span>
                            <span class="text-[9px] md:text-[10px] uppercase tracking-widest font-bold text-gray-400 mt-1">Waktu Server</span>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-200 leading-none">
                                {{ \Carbon\Carbon::now()->translatedFormat('l') }}
                            </span>
                            <span class="text-[10px] md:text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 flex items-center gap-2" role="alert">
                <i class="ti ti-check text-lg"></i>
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 flex items-center gap-2" role="alert">
                <i class="ti ti-x text-lg"></i>
                <span class="font-medium">Gagal!</span> {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-xl shadow-sm dark:bg-blue-900/10 dark:border-blue-900/20">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase text-blue-600 dark:text-blue-400 tracking-wider">Antrean Pembuatan Draft</p>
                    <i class="ti ti-file-pencil text-blue-500 text-xl"></i>
                </div>
                <h5 class="text-3xl font-black text-blue-700 dark:text-blue-100">{{ $totalMenunggu }}</h5>
            </div>

            <div class="p-5 bg-green-50/50 border border-green-100 rounded-xl shadow-sm dark:bg-green-900/10 dark:border-green-900/20">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase text-green-600 dark:text-green-400 tracking-wider">Draft Selesai Diajukan</p>
                    <i class="ti ti-file-check text-green-500 text-xl"></i>
                </div>
                <h5 class="text-3xl font-black text-green-700 dark:text-green-100">{{ $totalDraftSelesai }}</h5>
            </div>

            <div class="p-5 bg-red-50/50 border border-red-100 rounded-xl shadow-sm dark:bg-red-900/10 dark:border-red-900/20">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase text-red-600 dark:text-red-400 tracking-wider">SKT Ditolak Pimpinan</p>
                    <i class="ti ti-file-x text-red-500 text-xl"></i>
                </div>
                <h5 class="text-3xl font-black text-red-700 dark:text-red-100">{{ $totalDitolak }}</h5>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden flex flex-col mb-8">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center text-heading font-bold italic">
                <h3 class="flex items-center text-gray-900 dark:text-white font-black">
                    <i class="ti ti-inbox text-blue-600 me-2 text-xl"></i> Tiket Membutuhkan Draft SKT
                </h3>
            </div>

            <div class="relative overflow-x-auto bg-white dark:bg-[#1e293b]">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-white dark:bg-[#1e293b] border-b border-gray-100 dark:border-gray-700/50">
                        <tr>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">No. Tiket</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">Organisasi</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">Layanan</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">Status</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/30">
                        @forelse($tiketMenunggu as $tiket)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-700/40 transition-all duration-200">
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 text-xs font-black rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 tracking-wider">
                                        {{ $tiket->no_tiket }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $tiket->permohonanSkt->nama_organisasi ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $tiket->layanan->nama ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                        {{ str_replace('_', ' ', $tiket->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="#" 
                                            class="inline-flex items-center justify-center px-3 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-bold text-xs transition-all shadow-sm">
                                                <i class="ti ti-edit mr-1"></i> Buat Draft SKT
                                        </a>

                                        <a href="{{ route('analis.unduh.surat', $tiket->uuid) }}" 
                                            class="inline-flex items-center justify-center px-3 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 font-bold text-xs transition-all shadow-sm">
                                            <i class="ti ti-download mr-1"></i> Unduh Surat
                                        </a>
                                        
                                        <button type="button" onclick="openUploadModal('{{ $tiket->uuid }}', '{{ $tiket->no_tiket }}')"
                                            class="inline-flex items-center justify-center px-3 py-2 text-white bg-purple-600 rounded-lg hover:bg-purple-700 font-bold text-xs transition-all shadow-sm">
                                            <i class="ti ti-upload mr-1"></i> Unggah TTD Basah
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center italic text-gray-400 dark:text-gray-500">
                                    Belum ada tiket yang memerlukan pembuatan draft SKT.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-[#1e293b] border-t border-gray-100 dark:border-gray-700">
                {{ $tiketMenunggu->links() }}
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="flex items-center text-gray-900 dark:text-white font-black italic">
                    <i class="ti ti-history text-blue-600 me-2 text-xl"></i> Riwayat Tiket Diproses
                </h3>
            </div>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-white dark:bg-[#1e293b] border-b border-gray-100 dark:border-gray-700/50">
                        <tr>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">No. Tiket</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">Organisasi</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400">Status Saat Ini</th>
                            <th class="px-6 py-5 font-black tracking-widest text-blue-900 dark:text-blue-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/30">
                        @forelse($tiketHistory as $history)
                            <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-700/40">
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 text-xs font-black rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $history->no_tiket }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $history->permohonanSkt->nama_organisasi ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if(in_array($history->status, ['skt_disetujui', 'penomoran_skt', 'skt_diterbitkan']))
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase text-green-700 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-400">
                                            {{ str_replace('_', ' ', $history->status) }}
                                        </span>
                                    @elseif($history->status == 'skt_ditolak')
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase text-red-700 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-400">
                                            SKT DITOLAK
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ str_replace('_', ' ', $history->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="#" class="text-blue-600 hover:underline font-bold text-xs">Lihat Detail</a>
                                        
                                        <a href="{{ route('analis.unduh.surat', $history->uuid) }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 text-white bg-green-600 rounded-lg hover:bg-green-700 font-bold text-xs transition-all shadow-sm">
                                           <i class="ti ti-download mr-1"></i> Unduh SKT
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center italic text-gray-400">Belum ada riwayat draft SKT yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 bg-gray-50 dark:bg-[#1e293b] border-t border-gray-100 dark:border-gray-700">
                {{ $tiketHistory->links() }}
            </div>
        </div>
    </div>

    <div id="uploadModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-full flex items-center justify-center bg-gray-900/60 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="relative w-full max-w-lg transform scale-95 transition-transform duration-300" id="modalContent">
            <div class="relative bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between p-5 border-b border-gray-100 dark:border-gray-700 rounded-t-2xl bg-gray-50/80 dark:bg-gray-700/50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center">
                            <i class="ti ti-file-upload text-purple-600 me-2 text-xl"></i> Unggah Dokumen TTD Basah
                        </h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No. Tiket: <span id="modal_display_tiket" class="font-bold text-purple-600 dark:text-purple-400"></span></p>
                    </div>
                    <button type="button" onclick="closeUploadModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors">
                        <i class="ti ti-x text-lg"></i>
                        <span class="sr-only">Tutup modal</span>
                    </button>
                </div>
                <div class="p-6">
                    <form id="uploadForm" action="{{ route('analis.unggah.ttd_basah') ?? '#' }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tiket_uuid" id="modal_input_uuid" value="">
                        
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white" for="file_dokumen">
                                File Surat Organisasi <span class="text-red-500">*</span>
                            </label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 p-2.5 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-all" 
                                   id="file_dokumen" name="file_dokumen" type="file" required accept=".pdf,.jpg,.jpeg,.png">
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Format yang didukung: PDF, JPG, PNG (Maks 2MB). Pastikan tanda tangan dan stempel terlihat jelas.</p>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" onclick="closeUploadModal()" class="text-gray-700 bg-white border border-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-100 font-bold rounded-lg text-sm px-5 py-2.5 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 transition-colors shadow-sm">
                                Batal
                            </button>
                            <button type="submit" class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-bold rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-800 transition-all shadow-sm flex items-center">
                                <i class="ti ti-cloud-upload mr-2"></i> Simpan Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  
@endsection

@push('scripts')
    @vite('resources/js/dashboard-analis.js')
@endpush