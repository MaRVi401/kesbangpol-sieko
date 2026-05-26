@extends('layouts.main')

@section('title', 'Antrean Verifikasi Lapangan')

@section('content')
    <div class="p-4 mt-14">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <svg class="w-3 h-3 me-2.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-500">
                            Antrean Verifikasi Lapangan
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header & Title --}}
        <div class="flex flex-col md:flex-row items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center">
                <i class="ti ti-map-pin text-blue-600 mr-2"></i> Antrean Verifikasi Lapangan
            </h2>
        </div>

        <hr class="mb-6 border-gray-200 dark:border-gray-700">

        {{-- Notifikasi Sukses --}}
        @if (session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-6">
            <div class="w-full flex flex-col md:flex-row gap-3">
                <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-10 p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white transition-all"
                            placeholder="Cari No. Tiket, Organisasi, atau Lokasi...">

                        @if (request('search'))
                            <a href="{{ url()->current() }}"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition-colors"
                                title="Bersihkan Pencarian">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400 w-16">No</th>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400">No Tiket</th>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400">Organisasi</th>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400">Lokasi / Alamat</th>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400 text-center">Tgl Masuk</th>
                            <th scope="col" class="px-6 py-4 text-center font-black tracking-widest text-blue-900 dark:text-blue-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tiketVerifikasi as $index => $ticket)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-blue-50/30 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $tiketVerifikasi->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <span class="inline-block px-2.5 py-1 text-xs font-black rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 tracking-wider border border-gray-200 dark:border-gray-600">
                                        {{ $ticket->no_tiket ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-base">
                                            {{ $ticket->permohonanSkt->nama_organisasi ?? 'N/A' }}
                                        </span>
                                        <span class="text-xs text-gray-500 flex items-center mt-1">
                                            <i class="ti ti-user mr-1"></i> {{ $ticket->permohonanSkt->nama_ketua ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 line-clamp-2" title="{{ $ticket->permohonanSkt->alamat_sekretariat ?? '-' }}">
                                            {{ $ticket->permohonanSkt->alamat_sekretariat ?? 'Alamat tidak ditemukan' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">
                                        {{ $ticket->updated_at->format('d/m/Y') }}<br>
                                        {{ $ticket->updated_at->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    {{-- Modal Trigger Button --}}
                                    <button data-modal-target="modal-{{ $ticket->uuid }}" data-modal-toggle="modal-{{ $ticket->uuid }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-bold text-xs transition-all shadow-sm cursor-pointer">
                                        <i class="ti ti-hand-click mr-1.5 text-lg"></i> Ambil Tiket
                                    </button>

                                    {{-- Modal Dialog --}}
                                    <div id="modal-{{ $ticket->uuid }}" tabindex="-1" aria-hidden="true"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                                            <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                                    <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center">
                                                        <i class="ti ti-map-search text-blue-600 mr-2 text-2xl"></i> Detail Penugasan Lapangan
                                                    </h3>
                                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-{{ $ticket->uuid }}">
                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                        <span class="sr-only">Close modal</span>
                                                    </button>
                                                </div>

                                                <div class="p-6 text-left space-y-5">
                                                    <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                                        <div>
                                                            <p class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400 mb-1">No Tiket</p>
                                                            <p class="font-black text-gray-900 dark:text-white">{{ $ticket->no_tiket }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400 mb-1">Kontak Ketua</p>
                                                            <p class="font-bold text-gray-900 dark:text-white">{{ $ticket->permohonanSkt->no_kontak ?? '-' }}</p>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400 mb-1">Organisasi yang diverifikasi</p>
                                                        <p class="text-lg font-bold text-gray-900 dark:text-white border-l-4 border-blue-500 pl-3">
                                                            {{ $ticket->permohonanSkt->nama_organisasi ?? '-' }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400 mb-1">Alamat Tujuan Verifikasi</p>
                                                        <div class="flex items-start bg-yellow-50 dark:bg-yellow-900/10 p-3 rounded-lg border border-yellow-100 dark:border-yellow-900/30">
                                                            <i class="ti ti-map-pin text-yellow-600 dark:text-yellow-500 text-xl mt-0.5 mr-2"></i>
                                                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-400 leading-relaxed">
                                                                {{ $ticket->permohonanSkt->alamat_sekretariat ?? 'Alamat tidak ditemukan' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b dark:border-gray-700 gap-3">
                                                    <button data-modal-hide="modal-{{ $ticket->uuid }}" type="button"
                                                        class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-bold px-5 py-2.5 cursor-pointer transition-all dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                                                        Batal
                                                    </button>
                                                    
                                                    {{-- Form untuk action ambil tiket --}}
                                                    <form action="{{ route('verif_lapangan.ticket.mulai', $ticket->uuid) }}" method="GET">
                                                        <button type="submit"
                                                            class="text-white bg-blue-600 hover:bg-blue-700 font-bold rounded-lg text-sm px-5 py-2.5 cursor-pointer transition-all flex items-center shadow-md">
                                                            <i class="ti ti-check mr-2"></i> Ambil & Tangani Tiket
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Modal --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-700">
                                            <i class="ti ti-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-gray-900 dark:text-white font-bold text-lg mb-1">
                                            Tidak ada antrean tugas verifikasi lapangan.
                                        </p>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                                            Anda bisa bersantai sejenak, antrean saat ini kosong.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($tiketVerifikasi->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800">
                    {{ $tiketVerifikasi->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection