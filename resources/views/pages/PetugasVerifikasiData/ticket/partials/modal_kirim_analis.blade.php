<div id="modal-kirim-analis" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tindak Lanjut Tiket
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="modal-kirim-analis">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <form class="p-4 md:p-5" action="{{ route('verif_data.ticket.kirim-analis', $ticket->uuid) }}" method="POST" id="form-tindak-lanjut">
                @csrf
                
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keputusan Tindak Lanjut <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="" disabled selected>-- Pilih Tindakan --</option>
                            <option value="pembuatan_draft_skt">Lanjutkan (Kirim ke Analis)</option>
                            <option value="data_tidak_sesuai">Tolak (Kembalikan ke Pemohon)</option>
                        </select>
                    </div>

                    <div id="wrapper-analis" class="hidden">
                        <label for="analis_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Analis Muda yang Bertugas <span class="text-red-500">*</span></label>
                        <select id="analis_id" name="analis_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                            <option value="" disabled selected>-- Pilih Analis Muda --</option>
                            @foreach($analisList as $analis)
                                <option value="{{ $analis->uuid }}">{{ $analis->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="komentar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan <span id="label-catatan-req" class="text-red-500 hidden">*</span></label>
                        <textarea id="komentar" name="komentar" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Tuliskan catatan untuk analis atau pemohon..."></textarea>
                        <p id="help-text-tolak" class="mt-1 text-xs text-red-500 hidden">Catatan wajib diisi jika tiket ditolak.</p>
                    </div>
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="button" data-modal-toggle="modal-kirim-analis" class="py-2.5 px-5 me-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Simpan Tindakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>