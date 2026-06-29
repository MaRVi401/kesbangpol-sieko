<div id="update-modal-{{ $ticket->uuid }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700">
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-xl font-black text-gray-900 dark:text-white text-left flex items-center">
                    <i class="ti ti-file-description text-orange-500 mr-2 text-2xl"></i> Form Berita Acara Lapangan
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer" data-modal-hide="update-modal-{{ $ticket->uuid }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('verif_lapangan.ticket.simpan_berita', $ticket->uuid) }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 text-left max-h-[70vh] overflow-y-auto">
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800/30 mb-2">
                        <p class="text-sm text-blue-800 dark:text-blue-300 font-bold">Organisasi: {{ $ticket->formulir->nama_organisasi ?? '-' }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">No Tiket: {{ $ticket->no_tiket }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Nomor Berita Acara</label>
                            <input type="text" name="nomor_berita_acara" placeholder="Contoh: 220/BA-Kesbangpol/2026" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal_kunjungan" required max="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-4 mb-2">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Nomor SK Kemenkumham (Jika Ada / Opsional)</label>
                            <input type="text" name="nomor_sk_kemenkumham" value="{{ $ticket->permohonanSkt->nomor_sk_kemenkumham ?? '' }}" placeholder="Masukkan Nomor SK jika ingin diperbarui..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-4">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white border-b pb-2">Daftar Anggota Tim Verifikasi</label>
                            <p class="text-xs text-gray-500 mb-2">Ketua tim otomatis akan diisi oleh akun Anda. Silakan isi anggota lainnya.</p>
                            
                            <div id="anggota-container-{{ $ticket->uuid }}" class="space-y-3">
                                <div class="flex gap-3">
                                    <input type="text" name="nama_anggota[]" placeholder="Nama Anggota" required class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <input type="text" name="jabatan_anggota[]" value="Anggota" readonly class="w-1/3 bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">
                                </div>
                                <div class="flex gap-3">
                                    <input type="text" name="nama_anggota[]" placeholder="Nama Anggota (Opsional)" class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <input type="text" name="jabatan_anggota[]" value="Anggota" readonly class="w-1/3 bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg block p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mt-4 border-b border-gray-200 dark:border-gray-600 pb-2">Kondisi Sekretariat & Kepengurusan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Keberadaan Sekretariat</label>
                            <select name="keberadaan_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="ada">Ada</option>
                                <option value="tidak_ada">Tidak Ada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Papan Nama Terpasang</label>
                            <select name="papan_nama_terpasang" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="ada">Ada</option>
                                <option value="tidak_ada">Tidak Ada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Sekretariat Aktif</label>
                            <select name="sekretariat_aktif" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya (Aktif)</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kondisi Sekretariat</label>
                            <select name="kondisi_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="layak">Layak</option>
                                <option value="kurang_layak">Kurang Layak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kepengurusan Ditemui</label>
                            <select name="kepengurusan_ditemui" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Dokumen Tersedia</label>
                            <select name="dokumen_tersedia" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kegiatan Berjalan</label>
                            <select name="kegiatan_berjalan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mt-4 border-b border-gray-200 dark:border-gray-600 pb-2">Kesimpulan & Hasil</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesimpulan Sekretariat</label>
                            <select name="kesimpulan_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="ditemukan_dan_aktif">Ditemukan dan Aktif</option>
                                <option value="ditemukan_tidak_aktif">Ditemukan Tidak Aktif</option>
                                <option value="tidak_ditemukan">Tidak Ditemukan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesimpulan Kepengurusan</label>
                            <select name="kesimpulan_kepengurusan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="aktif_berkegiatan">Aktif Berkegiatan</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesesuaian Lokasi (Final)</label>
                            <select name="is_sesuai" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="">-- Pilih Hasil --</option>
                                <option value="1">Sesuai (Valid)</option>
                                <option value="0">Tidak Sesuai (Fiktif / Pindah)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Keterangan Hasil (Catatan Lapangan)</label>
                        <textarea name="keterangan_hasil" rows="4" required class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Deskripsikan penjelasan detail mengenai temuan kunjungan lapangan..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b dark:border-gray-600 gap-3 bg-gray-50/50 dark:bg-gray-700/30">
                    <button data-modal-hide="update-modal-{{ $ticket->uuid }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-bold px-5 py-2.5 cursor-pointer transition-all dark:bg-gray-800 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-orange-600 hover:bg-orange-700 focus:ring-4 focus:outline-none focus:ring-orange-300 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition-all cursor-pointer shadow-md flex items-center">
                        <i class="ti ti-device-floppy mr-2 text-lg"></i> Simpan Berita Acara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>