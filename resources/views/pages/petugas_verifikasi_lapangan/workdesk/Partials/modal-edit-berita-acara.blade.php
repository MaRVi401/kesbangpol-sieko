<div id="edit-modal-{{ $ticket->uuid }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 border dark:border-gray-700">
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-xl font-black text-gray-900 dark:text-white text-left flex items-center">
                    <i class="ti ti-edit text-yellow-500 mr-2 text-2xl"></i> Edit Berita Acara Lapangan
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white cursor-pointer" data-modal-hide="edit-modal-{{ $ticket->uuid }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('verif_lapangan.ticket.update_berita', $ticket->uuid) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-5 text-left max-h-[70vh] overflow-y-auto">
                    @php $ba = $ticket->beritaAcaraLapangan; @endphp

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-100 dark:border-yellow-800/30 mb-2">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300 font-bold">Mode Edit Berita Acara</p>
                        <p class="text-xs text-gray-500">Mengubah isi berkas tidak akan menghapus berkas scan fisik jika sudah terunggah sebelumnya.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal_kunjungan" required max="{{ date('Y-m-d') }}" value="{{ $ba->tanggal_kunjungan instanceof \Carbon\Carbon ? $ba->tanggal_kunjungan->format('Y-m-d') : ($ba->tanggal_kunjungan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Anggota Tim</label>
                            @php
                                $anggotaArray = is_string($ba->anggota_tim) ? json_decode($ba->anggota_tim, true) : $ba->anggota_tim;
                                $namaAnggota = collect($anggotaArray)->map(function($item) {
                                    return is_array($item) ? ($item['nama'] ?? '') : $item;
                                })->filter()->implode(', ');
                            @endphp
                            <input type="text" name="anggota_tim" value="{{ $namaAnggota }}" placeholder="Pisahkan dengan koma" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 mt-4 mb-2">
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Nomor SK Kemenkumham (Jika Ada / Opsional)</label>
                            <input type="text" name="nomor_sk_kemenkumham" value="{{ $ticket->permohonanSkt->nomor_sk_kemenkumham ?? '' }}" placeholder="Masukkan Nomor SK jika ingin diperbarui..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mt-4 border-b border-gray-200 dark:border-gray-600 pb-2">Kondisi Sekretariat & Kepengurusan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Keberadaan Sekretariat</label>
                            <select name="keberadaan_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="ada" {{ ($ba->keberadaan_sekretariat ?? '') == 'ada' ? 'selected' : '' }}>Ada</option>
                                <option value="tidak_ada" {{ ($ba->keberadaan_sekretariat ?? '') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Papan Nama Terpasang</label>
                            <select name="papan_nama_terpasang" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="ada" {{ ($ba->papan_nama_terpasang ?? '') == 'ada' ? 'selected' : '' }}>Ada</option>
                                <option value="tidak_ada" {{ ($ba->papan_nama_terpasang ?? '') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Sekretariat Aktif</label>
                            <select name="sekretariat_aktif" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="1" {{ ($ba->sekretariat_aktif ?? '') == '1' ? 'selected' : '' }}>Ya (Aktif)</option>
                                <option value="0" {{ ($ba->sekretariat_aktif ?? '') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kondisi Sekretariat</label>
                            <select name="kondisi_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="layak" {{ ($ba->kondisi_sekretariat ?? '') == 'layak' ? 'selected' : '' }}>Layak</option>
                                <option value="kurang_layak" {{ ($ba->kondisi_sekretariat ?? '') == 'kurang_layak' ? 'selected' : '' }}>Kurang Layak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kepengurusan Ditemui</label>
                            <select name="kepengurusan_ditemui" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="1" {{ ($ba->kepengurusan_ditemui ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ ($ba->kepengurusan_ditemui ?? '') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Dokumen Tersedia</label>
                            <select name="dokumen_tersedia" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="1" {{ ($ba->dokumen_tersedia ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ ($ba->dokumen_tersedia ?? '') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kegiatan Berjalan</label>
                            <select name="kegiatan_berjalan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="1" {{ ($ba->kegiatan_berjalan ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ ($ba->kegiatan_berjalan ?? '') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 dark:text-gray-200 mt-4 border-b border-gray-200 dark:border-gray-600 pb-2">Kesimpulan & Hasil</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesimpulan Sekretariat</label>
                            <select name="kesimpulan_sekretariat" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="ditemukan_dan_aktif" {{ ($ba->kesimpulan_sekretariat ?? '') == 'ditemukan_dan_aktif' ? 'selected' : '' }}>Ditemukan dan Aktif</option>
                                <option value="ditemukan_tidak_aktif" {{ ($ba->kesimpulan_sekretariat ?? '') == 'ditemukan_tidak_aktif' ? 'selected' : '' }}>Ditemukan Tidak Aktif</option>
                                <option value="tidak_ditemukan" {{ ($ba->kesimpulan_sekretariat ?? '') == 'tidak_ditemukan' ? 'selected' : '' }}>Tidak Ditemukan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesimpulan Kepengurusan</label>
                            <select name="kesimpulan_kepengurusan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="aktif_berkegiatan" {{ ($ba->kesimpulan_kepengurusan ?? '') == 'aktif_berkegiatan' ? 'selected' : '' }}>Aktif Berkegiatan</option>
                                <option value="tidak_aktif" {{ ($ba->kesimpulan_kepengurusan ?? '') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Kesesuaian Lokasi (Final)</label>
                            <select name="is_sesuai" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white cursor-pointer">
                                <option value="1" {{ ($ba->is_sesuai ?? '') == '1' ? 'selected' : '' }}>Sesuai (Valid)</option>
                                <option value="0" {{ ($ba->is_sesuai ?? '') == '0' ? 'selected' : '' }}>Tidak Sesuai (Fiktif / Pindah)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-900 dark:text-white">Keterangan Hasil (Catatan Lapangan)</label>
                        <textarea name="keterangan_hasil" rows="4" required class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-yellow-500 focus:border-yellow-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ $ba->keterangan_hasil ?? '' }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end p-5 border-t border-gray-200 rounded-b dark:border-gray-600 gap-3 bg-gray-50/50 dark:bg-gray-700/30">
                    <button data-modal-hide="edit-modal-{{ $ticket->uuid }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-bold px-5 py-2.5 cursor-pointer dark:bg-gray-800 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit" class="text-gray-900 bg-yellow-400 hover:bg-yellow-500 font-bold rounded-lg text-sm px-5 py-2.5 text-center transition-all cursor-pointer shadow-md flex items-center">
                        <i class="ti ti-device-floppy mr-2 text-lg"></i> Perbarui Berita Acara
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>