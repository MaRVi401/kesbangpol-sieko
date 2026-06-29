<div id="modal-update-status" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800 border dark:border-gray-700">
            <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white text-left">
                    Update Status: {{ $ticket->no_tiket }}
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer" data-modal-hide="modal-update-status">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="{{ route('verif_data.ticket.update', $ticket->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-semibold">Pengaju</label>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $ticket->user->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 uppercase font-semibold">Layanan</label>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $ticket->layanan->nama ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 uppercase font-semibold">Deskripsi Masalah</label>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border dark:border-gray-600 italic">
                            "{{ $ticket->deskripsi ?? 'Permohonan Pencatatan Ormas' }}"
                        </p>
                    </div>

                    <hr class="dark:border-gray-600">

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Pilih Status Akhir</label>
                        <select name="status" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                            <option value="">-- Pilih Status --</option>
                            <option value="persyaratan_lengkap">Verifikasi Lengkap (Berkas Sesuai)</option>
                            <option value="data_tidak_sesuai">Verifikasi Gagal (Berkas Tidak Sesuai)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Balasan ke Pengguna</label>
                        <textarea name="komentar" rows="4" required class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Tuliskan instruksi langkah selanjutnya atau alasan penolakan..."></textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Catatan Internal (Untuk Petugas Lapangan)</label>
                        <textarea name="catatan_lapangan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-yellow-50 rounded-lg border border-yellow-300 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Tuliskan instruksi khusus untuk tim verifikasi lapangan..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-start p-6 border-t border-gray-200 rounded-b dark:border-gray-600 gap-3">
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-all cursor-pointer">
                        Simpan Perubahan
                    </button>
                    <button data-modal-hide="modal-update-status" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 cursor-pointer transition-all dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>