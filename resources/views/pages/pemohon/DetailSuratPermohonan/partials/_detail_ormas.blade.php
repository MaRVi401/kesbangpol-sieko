@php
    $formulir = $ticket->formulirPermohonanBaruOrmas;
@endphp

@if($formulir)
<div class="space-y-10">

    {{-- 1 & 2. INFORMASI SURAT & DATA PEMOHON --}}
    <div>
        <h4 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-blue-100 text-blue-600">1</span>
            Informasi Surat & Data Pemohon
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50/50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Nomor & Perihal Surat</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->nomor ?? '-' }} <br> <span class="text-sm text-gray-600 dark:text-gray-400">{{ $formulir->perihal ?? '-' }}</span></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Tanggal Permohonan</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->tanggal_permohonan ? \Carbon\Carbon::parse($formulir->tanggal_permohonan)->translatedFormat('d F Y') : '-' }}</p>
            </div>
            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-3 mt-1"></div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Nama Lengkap Pemohon</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->nama_pemohon }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Jabatan dalam Organisasi</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->jabatan_pemohon }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Tempat, Tanggal Lahir</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->tempat_lahir }}, {{ \Carbon\Carbon::parse($formulir->tanggal_lahir)->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Nomor KTP</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->nomor_ktp }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 uppercase font-semibold">Alamat Rumah Pemohon</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->alamat_rumah }}</p>
            </div>

            {{-- LAMPIRAN PEMOHON (KOP SURAT & TTD) --}}
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Kop Surat</p>
                    @if($formulir->file_kop_surat)
                        @php $kopUrl = url('/storage/private/private/ormas/kop_surat/' . $formulir->file_kop_surat); @endphp
                        @if(Str::endsWith(strtolower($formulir->file_kop_surat), '.pdf'))
                            <a href="{{ $kopUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg> 
                                Lihat PDF Kop Surat
                            </a>
                        @else
                            <a href="{{ $kopUrl }}" target="_blank"><img src="{{ $kopUrl }}" class="h-32 object-contain rounded-lg border border-gray-200 shadow-sm hover:opacity-90"></a>
                        @endif
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Tanda Tangan Pemohon</p>
                    @if($formulir->file_tanda_tangan_pemohon)
                        @php $ttdPemohonUrl = url('/storage/private/private/ormas/ttd/' . $formulir->file_tanda_tangan_pemohon); @endphp
                        <a href="{{ $ttdPemohonUrl }}" target="_blank"><img src="{{ $ttdPemohonUrl }}" class="h-20 object-contain rounded-lg border border-gray-200 shadow-sm bg-white p-2 hover:opacity-90"></a>
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3 & 4. PROFIL ORGANISASI & KEANGGOTAAN --}}
    <div>
        <h4 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-blue-100 text-blue-600">2</span>
            Profil Organisasi & Susunan Inti
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50/50 dark:bg-gray-800/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 uppercase font-semibold">Nama Organisasi</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $formulir->nama_organisasi }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Sifat Kekhususan</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->sifat_kekhususan }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">No. Akte Pendirian / NPWP</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->nomor_akte_pendirian }} <br> <span class="text-sm">NPWP: {{ $formulir->nomor_npwp_organisasi ?? '-' }}</span></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 uppercase font-semibold">Alamat Organisasi</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->alamat_organisasi }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 uppercase font-semibold">Alamat Sekretariat</p>
                <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->alamat_sekretariat }}</p>
            </div>
            
            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-3 mt-1"></div>
            
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Ketua / Sekretaris / Bendahara</p>
                <ul class="list-disc list-inside text-sm font-medium text-gray-900 dark:text-white mt-1">
                    <li>Ketua: {{ $formulir->nama_ketua }}</li>
                    <li>Sekretaris: {{ $formulir->nama_sekretaris }}</li>
                    <li>Bendahara: {{ $formulir->nama_bendahara }}</li>
                </ul>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Data Keanggotaan</p>
                <ul class="list-disc list-inside text-sm font-medium text-gray-900 dark:text-white mt-1">
                    <li>Jumlah Anggota: {{ $formulir->jumlah_anggota }} Orang</li>
                    <li>Jumlah Cabang: {{ $formulir->jumlah_cabang }} Cabang</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- 5. BIODATA PENGURUS --}}
    @if($formulir->biodataPengurus && $formulir->biodataPengurus->count() > 0)
    <div>
        <h4 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-blue-100 text-blue-600">3</span>
            Biodata Lengkap Pengurus
        </h4>
        <div class="space-y-4">
            @foreach($formulir->biodataPengurus as $pengurus)
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wider">
                    {{ $pengurus->jabatan }}
                </div>
                
                <h5 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pr-20">{{ $pengurus->nama_lengkap }}</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                    <div><span class="text-gray-500 block text-xs uppercase">Tempat, Tgl Lahir</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->tempat_lahir }}, {{ \Carbon\Carbon::parse($pengurus->tanggal_lahir)->format('d-m-Y') }}</span></div>
                    <div><span class="text-gray-500 block text-xs uppercase">Jenis Kelamin / Status</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->jenis_kelamin }} / {{ $pengurus->status_perkawinan }}</span></div>
                    <div><span class="text-gray-500 block text-xs uppercase">Agama</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->agama }}</span></div>
                    <div><span class="text-gray-500 block text-xs uppercase">Pendidikan Terakhir</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->pendidikan_terakhir }}</span></div>
                    <div><span class="text-gray-500 block text-xs uppercase">No. HP / Telepon</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->telepon_rumah_hp }}</span></div>
                    <div><span class="text-gray-500 block text-xs uppercase">Hobi</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->hobi ?? '-' }}</span></div>
                    
                    <div class="md:col-span-2 mt-2"><span class="text-gray-500 block text-xs uppercase">Alamat Rumah</span> <span class="font-medium dark:text-gray-200">{{ $pengurus->alamat_rumah }}</span></div>
                    
                    @if($pengurus->utusan_organisasi || $pengurus->alamat_organisasi)
                        <div class="md:col-span-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                            <span class="text-gray-500 block text-xs uppercase">Utusan / Alamat Org.</span> 
                            <span class="font-medium dark:text-gray-200">{{ $pengurus->utusan_organisasi ?? '-' }} <br> {{ $pengurus->alamat_organisasi ?? '-' }} (Telp: {{ $pengurus->telepon_organisasi ?? '-' }})</span>
                        </div>
                    @endif

                    @php
                        $riwayat = is_string($pengurus->riwayat_organisasi) ? json_decode($pengurus->riwayat_organisasi, true) : $pengurus->riwayat_organisasi;
                        $riwayat = array_filter($riwayat ?? []); // Hapus yang kosong
                    @endphp
                    @if(!empty($riwayat))
                        <div class="md:col-span-2 pt-2 border-t border-gray-100 dark:border-gray-700 mt-2">
                            <span class="text-gray-500 block text-xs uppercase mb-1">Riwayat Organisasi</span> 
                            <ol class="list-decimal list-inside font-medium dark:text-gray-200 text-sm">
                                @foreach($riwayat as $r)
                                    <li>{{ $r }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    {{-- LAMPIRAN PENGURUS (FOTO & TTD) --}}
                    <div class="md:col-span-2 flex flex-wrap gap-6 mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                        @if($pengurus->foto_resmi)
                            <div>
                                <span class="text-gray-500 block text-xs uppercase mb-2">Foto Resmi</span>
                                <a href="{{ url('/storage/private/private/ormas/foto_pengurus/' . $pengurus->foto_resmi) }}" target="_blank">
                                    <img src="{{ url('/storage/private/private/ormas/foto_pengurus/' . $pengurus->foto_resmi) }}" class="w-24 h-32 object-cover rounded-lg border border-gray-300 shadow-sm hover:opacity-90">
                                </a>
                            </div>
                        @endif
                        @if($pengurus->file_tanda_tangan)
                            <div>
                                <span class="text-gray-500 block text-xs uppercase mb-2">Tanda Tangan</span>
                                <a href="{{ url('/storage/private/private/ormas/ttd_pengurus/' . $pengurus->file_tanda_tangan) }}" target="_blank">
                                    <img src="{{ url('/storage/private/private/ormas/ttd_pengurus/' . $pengurus->file_tanda_tangan) }}" class="h-20 object-contain rounded-lg border border-gray-300 shadow-sm bg-white p-2 hover:opacity-90 mt-2">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 6. SURAT PERNYATAAN ORGANISASI --}}
    @if($formulir->suratPernyataan)
    <div>
        <h4 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-blue-100 text-blue-600">4</span>
            Detail Surat Pernyataan
        </h4>
        <div class="bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Nama Ketua & KTP</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->suratPernyataan->nama_ketua }} <br> <span class="text-sm font-normal text-gray-600 dark:text-gray-400">{{ $formulir->suratPernyataan->nomor_ktp_ketua }}</span></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Nama Sekretaris & KTP</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->suratPernyataan->nama_sekretaris }} <br> <span class="text-sm font-normal text-gray-600 dark:text-gray-400">{{ $formulir->suratPernyataan->nomor_ktp_sekretaris }}</span></p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Tanggal Surat Pernyataan</p>
                    <p class="text-base font-medium text-gray-900 dark:text-white">{{ $formulir->suratPernyataan->tanggal_surat_pernyataan ? \Carbon\Carbon::parse($formulir->suratPernyataan->tanggal_surat_pernyataan)->translatedFormat('d F Y') : '-' }}</p>
                </div>
                
                {{-- LAMPIRAN SURAT PERNYATAAN --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 border-t border-blue-200 dark:border-blue-800 pt-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">TTD Ketua (Bermaterai)</p>
                        @if($formulir->suratPernyataan->file_ttd_ketua_materai)
                            @php $ttdKetuaUrl = url('/storage/private/private/ormas/pernyataan/' . $formulir->suratPernyataan->file_ttd_ketua_materai); @endphp
                            @if(Str::endsWith(strtolower($formulir->suratPernyataan->file_ttd_ketua_materai), '.pdf'))
                                <a href="{{ $ttdKetuaUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg> Lihat PDF
                                </a>
                            @else
                                <a href="{{ $ttdKetuaUrl }}" target="_blank"><img src="{{ $ttdKetuaUrl }}" class="h-24 object-contain rounded-lg border border-gray-200 shadow-sm bg-white p-2 hover:opacity-90"></a>
                            @endif
                        @else
                            <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-2">TTD Sekretaris</p>
                        @if($formulir->suratPernyataan->file_ttd_sekretaris)
                            @php $ttdSekUrl = url('/storage/private/private/ormas/pernyataan/' . $formulir->suratPernyataan->file_ttd_sekretaris); @endphp
                            @if(Str::endsWith(strtolower($formulir->suratPernyataan->file_ttd_sekretaris), '.pdf'))
                                <a href="{{ $ttdSekUrl }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 text-sm font-medium transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg> Lihat PDF
                                </a>
                            @else
                                <a href="{{ $ttdSekUrl }}" target="_blank"><img src="{{ $ttdSekUrl }}" class="h-24 object-contain rounded-lg border border-gray-200 shadow-sm bg-white p-2 hover:opacity-90"></a>
                            @endif
                        @else
                            <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 7. FORMULIR ISIAN ORGANISASI --}}
    @if($formulir->formulirIsian)
    <div>
        <h4 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4 flex items-center gap-2">
            <span class="flex items-center justify-center w-6 h-6 text-xs rounded-full bg-blue-100 text-blue-600">5</span>
            Formulir Isian Organisasi Lengkap
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 bg-gray-50/50 dark:bg-gray-800/50 p-5 rounded-lg border border-gray-100 dark:border-gray-700 text-sm">
            
            <div class="md:col-span-2 border-b border-gray-200 dark:border-gray-700 pb-3 mb-1">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Bidang & Ruang Lingkup</span>
                <span class="font-bold text-base text-gray-900 dark:text-white">{{ $formulir->formulirIsian->bidang_kegiatan }}</span> 
                <span class="text-gray-600 dark:text-gray-400"> (Lingkup: {{ $formulir->formulirIsian->ruang_lingkup }})</span>
            </div>

            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Tempat, Tanggal Pendirian</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->tempat_pendirian }}, {{ \Carbon\Carbon::parse($formulir->formulirIsian->tanggal_pendirian)->translatedFormat('d F Y') }}</span>
            </div>
            
            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Asas / Ciri Organisasi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->asas_ciri_organisasi }}</span>
            </div>

            <div class="md:col-span-2">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Tujuan Organisasi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->tujuan_organisasi }}</span>
            </div>

            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-3 mt-1">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Tokoh Organisasi (Pendiri, Pembina, Penasehat)</span>
                <ul class="list-disc list-inside font-medium text-gray-900 dark:text-white">
                    <li><span class="text-gray-600 dark:text-gray-400 font-normal">Pendiri:</span> {{ $formulir->formulirIsian->nama_pendiri }}</li>
                    <li><span class="text-gray-600 dark:text-gray-400 font-normal">Pembina:</span> {{ $formulir->formulirIsian->nama_pembina ?? '-' }}</li>
                    <li><span class="text-gray-600 dark:text-gray-400 font-normal">Penasehat:</span> {{ $formulir->formulirIsian->nama_penasehat ?? '-' }}</li>
                </ul>
            </div>

            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Masa Bhakti Kepengurusan</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->masa_bhakti_kepengurusan }}</span>
            </div>

            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Keputusan Tertinggi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->keputusan_tertinggi_organisasi }}</span>
            </div>

            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Unit Sayap Otonom</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->unit_sayap_otonom ?? '-' }}</span>
            </div>

            <div>
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Usaha Organisasi</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->usaha_organisasi ?? '-' }}</span>
            </div>

            <div class="md:col-span-2">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Sumber Keuangan</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $formulir->formulirIsian->sumber_keuangan }}</span>
            </div>

            {{-- LAMPIRAN LOGO & BENDERA --}}
            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Logo Organisasi</p>
                    @if($formulir->formulirIsian->file_logo_organisasi)
                        @php $logoUrl = url('/storage/private/private/ormas/logo/' . $formulir->formulirIsian->file_logo_organisasi); @endphp
                        <a href="{{ $logoUrl }}" target="_blank"><img src="{{ $logoUrl }}" class="h-28 object-contain rounded-lg border border-gray-200 shadow-sm hover:opacity-90"></a>
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Bendera Organisasi</p>
                    @if($formulir->formulirIsian->file_bendera_organisasi)
                        @php $benderaUrl = url('/storage/private/private/ormas/bendera/' . $formulir->formulirIsian->file_bendera_organisasi); @endphp
                        <a href="{{ $benderaUrl }}" target="_blank"><img src="{{ $benderaUrl }}" class="h-28 object-contain rounded-lg border border-gray-200 shadow-sm hover:opacity-90"></a>
                    @else
                        <span class="text-sm text-gray-400 italic">Tidak ada lampiran</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@else
<div class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300">
    <p class="text-gray-500 dark:text-gray-400 mb-2">Data detail permohonan tidak ditemukan atau masih berupa draft kosong.</p>
</div>
@endif