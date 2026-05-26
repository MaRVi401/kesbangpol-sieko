@extends('layouts.main')

@section('title', 'Riwayat Verifikasi Lapangan')

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
                            Riwayat Verifikasi
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center">
                <i class="ti ti-history text-green-600 mr-2 text-3xl"></i> Riwayat Verifikasi Selesai
            </h2>
        </div>

        <hr class="mb-6 border-gray-200 dark:border-gray-700">

        {{-- Search & Filter Bar --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mb-6">
            <div class="w-full md:w-3/4">
                <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row gap-3 w-full">
                    
                    <div class="w-full md:w-48 shrink-0">
                        <select name="filter_time" onchange="this.form.submit()" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white transition-colors cursor-pointer">
                            <option value="">Semua Waktu</option>
                            <option value="hari" {{ request('filter_time') == 'hari' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu" {{ request('filter_time') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="bulan" {{ request('filter_time') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                        </select>
                    </div>

                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <input type="text" name="search" id="simple-search" value="{{ request('search') }}"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-10 p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Cari No Tiket atau Nama Organisasi...">

                        @if (request('search') || request('filter_time'))
                            <a href="{{ url()->current() }}"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition-colors"
                                title="Bersihkan Pencarian dan Filter">
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
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400 text-center">Tgl Selesai Verifikasi</th>
                            <th scope="col" class="px-6 py-4 font-black tracking-widest text-blue-900 dark:text-blue-400 text-center">Status Terkini</th>
                            <th scope="col" class="px-6 py-4 text-right font-black tracking-widest text-blue-900 dark:text-blue-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($tiketHistory as $index => $ticket)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $tiketHistory->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-md border border-gray-200 dark:border-gray-600 font-bold tracking-wider text-gray-700 dark:text-gray-300">
                                        {{ $ticket->no_tiket ?? '-' }}
                                    </span>
                                </td>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800/30">
                                            <i class="ti ti-building-community text-xl"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $ticket->permohonanSkt->nama_organisasi ?? 'N/A' }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-normal">
                                                Ketua: {{ $ticket->permohonanSkt->nama_ketua ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </th>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">
                                        {{ $ticket->updated_at->format('d M Y, H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800 shadow-sm uppercase tracking-wider">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Lihat Berita Acara yang akan mengarah ke method lihatBeritaAcara di Controller --}}
                                        <a href="{{ route('verif_lapangan.ticket.berita-acara', $ticket->uuid) }}"
                                            class="inline-flex items-center px-3 py-2 text-sm font-bold text-center text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:text-green-800 focus:ring-4 focus:outline-none focus:ring-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800 dark:hover:bg-green-900/40 transition-all shadow-sm">
                                            <i class="ti ti-file-text mr-1.5 text-lg"></i>
                                            Berita Acara
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-700">
                                            <i class="ti ti-history text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium text-lg mb-1">
                                            @if(request('search'))
                                                Riwayat dengan kata kunci tersebut tidak ditemukan.
                                            @else
                                                Belum ada riwayat verifikasi yang Anda selesaikan.
                                            @endif
                                        </p>

                                        @if(request('search') || request('filter_time'))
                                            <a href="{{ url()->current() }}"
                                                class="mt-4 text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-2 font-bold">
                                                <i class="ti ti-refresh"></i> Tampilkan semua riwayat
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($tiketHistory->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800">
                    {{ $tiketHistory->appends([
                        'search' => request('search'), 
                        'filter_time' => request('filter_time')
                    ])->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection