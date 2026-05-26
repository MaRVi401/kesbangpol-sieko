@extends('layouts.main')

@section('title', 'Meja Kerja Verifikasi Lapangan')

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
                            Meja Kerja Lapangan
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white flex items-center">
                <i class="ti ti-briefcase text-orange-600 mr-2"></i> Tiket Sedang Ditangani (Survey Lapangan)
            </h2>
        </div>

        <hr class="mb-6 border-gray-200 dark:border-gray-700">

        {{-- Notifikasi --}}
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
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full pl-10 pr-10 p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Cari No. Tiket atau Organisasi...">
                        @if (request('search'))
                            <a href="{{ url()->current() }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-500 transition-colors" title="Bersihkan Pencarian">
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
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold text-gray-900 dark:text-white w-16">No</th>
                            <th scope="col" class="px-6 py-4 font-bold">Tiket</th>
                            <th scope="col" class="px-6 py-4 font-bold">Organisasi</th>
                            <th scope="col" class="px-6 py-4 font-bold">Alamat Verifikasi</th>
                            <th scope="col" class="px-6 py-4 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($tiketWorkdesk as $index => $ticket)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $tiketWorkdesk->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <span class="font-mono text-xs bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 px-2 py-1 rounded border border-orange-100 dark:border-orange-800">
                                        {{ $ticket->no_tiket ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 dark:text-white text-base">
                                            {{ $ticket->permohonanSkt->nama_organisasi ?? 'N/A' }}
                                        </span>
                                        <span class="text-xs text-gray-500 mt-1 flex items-center">
                                            <i class="ti ti-user mr-1"></i> {{ $ticket->permohonanSkt->nama_ketua ?? 'N/A' }} ({{ $ticket->permohonanSkt->no_kontak ?? '-' }})
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white">
                                    <span class="line-clamp-2 text-sm">{{ $ticket->permohonanSkt->alamat_sekretariat ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button data-modal-target="update-modal-{{ $ticket->uuid }}"
                                        data-modal-toggle="update-modal-{{ $ticket->uuid }}"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm cursor-pointer transition-all shadow-sm font-bold flex items-center justify-end ml-auto">
                                        <i class="ti ti-edit mr-1"></i> Buat Berita Acara
                                    </button>

                                    {{-- Modal Pengisian Berita Acara --}}
                                    <div id="update-modal-{{ $ticket->uuid }}" tabindex="-1" aria-hidden="true"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <div class="relative p-4 w-full max-w-3xl max-h-full">
                                            <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700">
                                                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30">
                                                    <h3 class="text-xl font-black text-gray-900 dark:text-white text-left flex items-center">
                                                        <i class="ti ti-file-description text-orange-500 mr-2 text-2xl"></i> Form Berita Acara Lapangan
                                                    </h3>
                                                    <button type="button"
                                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer"
                                                        data-modal-hide="update-modal-{{ $ticket->uuid }}">
                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                {{-- Form Input Berita Acara --}}
                                                {{-- Catatan: Route ini perlu kita buat di web.php setelah ini --}}
                                                <form action="{{ route('verif_lapangan.ticket.simpan_berita', $ticket->uuid) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="p-6 space-y-5 text-left max-h-[60vh] overflow-y-auto">
                                                        
                                                        {{-- Info Singkat --}}
                                                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800/30 mb-2">
                                                            <p class="text-sm text-blue-800 dark:text-blue-300 font-bold">Organisasi: {{ $ticket->permohonanSkt->nama_organisasi }}</p>
                                                            <p class="text-xs text-blue-600 dark:text-blue-400">No Tiket: {{ $ticket->no_tiket }}</p>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Tanggal Verifikasi</label>
                                                                <input type="date" name="tanggal_verifikasi" required max="{{ date('Y-m-d') }}"
                                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Hasil Kesesuaian Lokasi</label>
                                                                <select name="is_sesuai" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                                                    <option value="">-- Pilih Hasil --</option>
                                                                    <option value="1">Sesuai (Keberadaan Organisasi Valid)</option>
                                                                    <option value="0">Tidak Sesuai (Fiktif / Pindah Lokasi)</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Catatan Lapangan (Temuan)</label>
                                                            <textarea name="catatan_lapangan" rows="4" required
                                                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                                                placeholder="Deskripsikan kondisi sekretariat, plang nama, fasilitas, pengurus yang ditemui, dll..."></textarea>
                                                        </div>

                                                        <hr class="dark:border-gray-700">

                                                        <div>
                                                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">
                                                                Upload Foto Dokumentasi <span class="text-red-500 text-xs font-normal">(Bisa pilih lebih dari 1 foto)</span>
                                                            </label>
                                                            <input type="file" name="foto_dokumentasi[]" multiple accept="image/*" required
                                                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG, JPEG. Max: 2MB/foto. (Foto Plang, Foto Ruangan, Foto Bersama)</p>
                                                        </div>

                                                        <div>
                                                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Upload File Berita Acara (Scan PDF)</label>
                                                            <input type="file" name="file_berita_acara_path" accept=".pdf,.doc,.docx"
                                                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Opsional jika sistem melakukan auto-generate. Jika Anda memiliki hasil scan manual bertanda tangan, silakan unggah di sini.</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b dark:border-gray-600 gap-3 bg-gray-50/50 dark:bg-gray-700/30">
                                                        <button data-modal-hide="update-modal-{{ $ticket->uuid }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-bold px-5 py-2.5 cursor-pointer transition-all dark:bg-gray-800 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-700">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition-all cursor-pointer shadow-md flex items-center">
                                                            <i class="ti ti-device-floppy mr-2"></i> Simpan Berita Acara & Selesaikan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- End Modal --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-700">
                                            <i class="ti ti-briefcase-off text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-gray-900 dark:text-white font-bold text-lg mb-1">
                                            Meja kerja kosong.
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Silakan ambil tiket baru di menu <a href="{{ route('verif_lapangan.ticket.index') }}" class="text-blue-600 hover:underline">Antrean Verifikasi</a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tiketWorkdesk->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-800">
                    {{ $tiketWorkdesk->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection