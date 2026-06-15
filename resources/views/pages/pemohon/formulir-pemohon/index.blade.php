@extends('layouts.main')

@section('title', 'Formulir Permohonan Pencatatan Ormas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
    <div id="step-1" class="step-content transition-opacity duration-300">
        <form id="form-pencatatan-ormas" 
            action="{{ route('pemohon.services.store') }}" 
            data-autosave-url="{{ route('pemohon.services.autosave') }}" 
            data-history-url="{{ route('pemohon.history.index') }}" 
            method="POST" 
            enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="tiket_uuid" id="tiket_uuid" value="{{ $tiketUuid ?? '' }}">

            <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Formulir Permohonan Pencatatan Ormas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi data surat, pemohon, organisasi, kepengurusan, dan unggah berkas terkait.</p>
                </div>

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">1</span>
                        Informasi Surat Permohonan
                    </h3>
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Surat</label>
                            <input type="text" name="nomor" value="{{ old('nomor', $payloadDraft['nomor'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 001/ORG/V/2026">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Perihal</label>
                            <input type="text" name="perihal" value="{{ old('perihal', $payloadDraft['perihal'] ?? 'Permohonan Pencatatan Ormas') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Permohonan</label>
                            <input type="date" name="tanggal_permohonan" value="{{ old('tanggal_permohonan', $payloadDraft['tanggal_permohonan'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">2</span>
                        Data Pemohon (Yang Bertanda Tangan)
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                            <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $payloadDraft['nama_pemohon'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $payloadDraft['tempat_lahir'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $payloadDraft['tanggal_lahir'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jabatan dalam Organisasi</label>
                            <input type="text" name="jabatan_pemohon" value="{{ old('jabatan_pemohon', $payloadDraft['jabatan_pemohon'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor KTP</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="16" minlength="16" name="nomor_ktp" value="{{ old('nomor_ktp', $payloadDraft['nomor_ktp'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Rumah</label>
                            <textarea name="alamat_rumah" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('alamat_rumah', $payloadDraft['alamat_rumah'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">3</span>
                        Profil Organisasi
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Organisasi</label>
                            <input type="text" name="nama_organisasi" value="{{ old('nama_organisasi', $payloadDraft['nama_organisasi'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sifat Kekhususan</label>
                            <input type="text" name="sifat_kekhususan" value="{{ old('sifat_kekhususan', $payloadDraft['sifat_kekhususan'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: LSM, OKP, Kegiatan, Agama, Fungsional" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Akte Pendirian dari Notaris Nomor</label>
                            <input type="text" name="nomor_akte_pendirian" value="{{ old('nomor_akte_pendirian', $payloadDraft['nomor_akte_pendirian'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 12/NOT/VIII/2023"   required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor NPWP Organisasi</label>
                            <input 
                                type="text" 
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                minlength="15" 
                                maxlength="16" 
                                name="nomor_npwp_organisasi" 
                                value="{{ old('nomor_npwp_organisasi', $payloadDraft['nomor_npwp_organisasi'] ?? '') }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                placeholder="Masukkan 15 atau 16 digit angka NPWP tanpa tanda baca"
                            >
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Organisasi</label>
                            <textarea name="alamat_organisasi" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('alamat_organisasi', $payloadDraft['alamat_organisasi'] ?? '') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Sekretariat</label>
                            <textarea name="alamat_sekretariat" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('alamat_sekretariat', $payloadDraft['alamat_sekretariat'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">4</span>
                        Nama Pengurus & Keanggotaan
                    </h3>
                    <div class="grid gap-6 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">1. Nama Ketua</label>
                            <input type="text" name="nama_ketua" value="{{ old('nama_ketua', $payloadDraft['nama_ketua'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">2. Nama Sekretaris</label>
                            <input type="text" name="nama_sekretaris" value="{{ old('nama_sekretaris', $payloadDraft['nama_sekretaris'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">3. Nama Bendahara</label>
                            <input type="text" name="nama_bendahara" value="{{ old('nama_bendahara', $payloadDraft['nama_bendahara'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">4. Jumlah Anggota</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="jumlah_anggota" value="{{ old('jumlah_anggota', $payloadDraft['jumlah_anggota'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banyaknya Cabang</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" name="jumlah_cabang" value="{{ old('jumlah_cabang', $payloadDraft['jumlah_cabang'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">5</span>
                        Lampiran Dokumen
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Kop Surat (PDF/JPG/PNG)</label>
                            <input type="file" name="file_kop_surat" accept=".pdf,.jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pastikan file jelas dan dapat dibaca.</p>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Tanda Tangan Pemohon (JPG/PNG)</label>
                            <input type="file" name="file_tanda_tangan_pemohon" accept=".jpg,.jpeg,.png,.webp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan format gambar berlatar putih atau transparan.</p>
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">6</span>
                        Bio Data Pengurus (Ketua, Sekretaris, & Bendahara)
                    </h3>

                    @php
                        $roles = ['Ketua', 'Sekretaris', 'Bendahara'];
                    @endphp

                    <div class="space-y-12">
                        @foreach($roles as $role)
                        @php $key = strtolower($role); @endphp
                        
                        <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                            <h4 class="text-md font-bold text-blue-700 dark:text-blue-400 mb-6 uppercase tracking-wider">Formulir Biodata {{ $role }}</h4>
                            
                            <input type="hidden" name="pengurus[{{ $key }}][jabatan]" value="{{ $role }}">

                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap (Jelas dengan gelar)</label>
                                    <input type="text" name="pengurus[{{ $key }}][nama_lengkap]" value="{{ old('pengurus.'.$key.'.nama_lengkap', $payloadDraft['pengurus'][$key]['nama_lengkap'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                                    <input type="text" name="pengurus[{{ $key }}][tempat_lahir]" value="{{ old('pengurus.'.$key.'.tempat_lahir', $payloadDraft['pengurus'][$key]['tempat_lahir'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                                    <input type="date" name="pengurus[{{ $key }}][tanggal_lahir]" value="{{ old('pengurus.'.$key.'.tanggal_lahir', $payloadDraft['pengurus'][$key]['tanggal_lahir'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                                    <select name="pengurus[{{ $key }}][jenis_kelamin]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                        <option value="" disabled {{ empty(old('pengurus.'.$key.'.jenis_kelamin', $payloadDraft['pengurus'][$key]['jenis_kelamin'] ?? '')) ? 'selected' : '' }}>Pilih...</option>
                                        <option value="Pria" {{ old('pengurus.'.$key.'.jenis_kelamin', $payloadDraft['pengurus'][$key]['jenis_kelamin'] ?? '') == 'Pria' ? 'selected' : '' }}>Pria</option>
                                        <option value="Wanita" {{ old('pengurus.'.$key.'.jenis_kelamin', $payloadDraft['pengurus'][$key]['jenis_kelamin'] ?? '') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                    <select name="pengurus[{{ $key }}][status_perkawinan]" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                        <option value="" disabled {{ empty(old('pengurus.'.$key.'.status_perkawinan', $payloadDraft['pengurus'][$key]['status_perkawinan'] ?? '')) ? 'selected' : '' }}>Pilih...</option>
                                        <option value="Kawin" {{ old('pengurus.'.$key.'.status_perkawinan', $payloadDraft['pengurus'][$key]['status_perkawinan'] ?? '') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                        <option value="Belum Kawin" {{ old('pengurus.'.$key.'.status_perkawinan', $payloadDraft['pengurus'][$key]['status_perkawinan'] ?? '') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                        <option value="Janda" {{ old('pengurus.'.$key.'.status_perkawinan', $payloadDraft['pengurus'][$key]['status_perkawinan'] ?? '') == 'Janda' ? 'selected' : '' }}>Janda</option>
                                        <option value="Duda" {{ old('pengurus.'.$key.'.status_perkawinan', $payloadDraft['pengurus'][$key]['status_perkawinan'] ?? '') == 'Duda' ? 'selected' : '' }}>Duda</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agama</label>
                                    <input type="text" name="pengurus[{{ $key }}][agama]" value="{{ old('pengurus.'.$key.'.agama', $payloadDraft['pengurus'][$key]['agama'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Utusan Organisasi</label>
                                    <input type="text" name="pengurus[{{ $key }}][utusan_organisasi]" value="{{ old('pengurus.'.$key.'.utusan_organisasi', $payloadDraft['pengurus'][$key]['utusan_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Organisasi</label>
                                    <textarea name="pengurus[{{ $key }}][alamat_organisasi]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('pengurus.'.$key.'.alamat_organisasi', $payloadDraft['pengurus'][$key]['alamat_organisasi'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telepon Organisasi</label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="pengurus[{{ $key }}][telepon_organisasi]" value="{{ old('pengurus.'.$key.'.telepon_organisasi', $payloadDraft['pengurus'][$key]['telepon_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pendidikan Terakhir</label>
                                    <input type="text" name="pengurus[{{ $key }}][pendidikan_terakhir]" value="{{ old('pengurus.'.$key.'.pendidikan_terakhir', $payloadDraft['pengurus'][$key]['pendidikan_terakhir'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Rumah</label>
                                    <textarea name="pengurus[{{ $key }}][alamat_rumah]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>{{ old('pengurus.'.$key.'.alamat_rumah', $payloadDraft['pengurus'][$key]['alamat_rumah'] ?? '') }}</textarea>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Telepon Rumah / HP</label>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" name="pengurus[{{ $key }}][telepon_rumah_hp]" value="{{ old('pengurus.'.$key.'.telepon_rumah_hp', $payloadDraft['pengurus'][$key]['telepon_rumah_hp'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hobby / Kegemaran</label>
                                    <input type="text" name="pengurus[{{ $key }}][hobi]" value="{{ old('pengurus.'.$key.'.hobi', $payloadDraft['pengurus'][$key]['hobi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Riwayat Organisasi</label>
                                    <p class="text-xs text-gray-500 mb-2 dark:text-gray-400">Isikan hingga 5 riwayat organisasi terakhir.</p>
                                    <div class="space-y-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-6">{{ $i }}.</span>
                                                <input type="text" name="pengurus[{{ $key }}][riwayat_organisasi][]" value="{{ old('pengurus.'.$key.'.riwayat_organisasi.'.($i-1), $payloadDraft['pengurus'][$key]['riwayat_organisasi'][$i-1] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengisian</label>
                                    <input type="date" name="pengurus[{{ $key }}][tanggal_pengisian]" value="{{ old('pengurus.'.$key.'.tanggal_pengisian', $payloadDraft['pengurus'][$key]['tanggal_pengisian'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="md:col-span-2 grid gap-6 md:grid-cols-2 mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto Resmi (3x4)</label>
                                        <input type="file" name="pengurus[{{ $key }}][foto_resmi]" accept="image/*" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600" required>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Tanda Tangan</label>
                                        <input type="file" name="pengurus[{{ $key }}][file_tanda_tangan]" accept="image/*" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600">
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">7</span> Surat Pernyataan Organisasi
                    </h3>
                    
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-6">
                            Lengkapi data di bawah ini untuk Surat Pernyataan yang ditandatangani oleh Ketua (bermaterai) dan Sekretaris.
                        </p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Ketua</label>
                                <input type="text" name="surat_pernyataan[nama_ketua]" value="{{ old('surat_pernyataan.nama_ketua', $payloadDraft['surat_pernyataan']['nama_ketua'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor KTP Ketua</label>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="16" minlength="16" name="surat_pernyataan[nomor_ktp_ketua]" value="{{ old('surat_pernyataan.nomor_ktp_ketua', $payloadDraft['surat_pernyataan']['nomor_ktp_ketua'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Sekretaris</label>
                                <input type="text" name="surat_pernyataan[nama_sekretaris]" value="{{ old('surat_pernyataan.nama_sekretaris', $payloadDraft['surat_pernyataan']['nama_sekretaris'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor KTP Sekretaris</label>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="16" minlength="16" name="surat_pernyataan[nomor_ktp_sekretaris]" value="{{ old('surat_pernyataan.nomor_ktp_sekretaris', $payloadDraft['surat_pernyataan']['nomor_ktp_sekretaris'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>

                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-600 pt-4 mt-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Surat Pernyataan</label>
                                <input type="date" name="surat_pernyataan[tanggal_surat_pernyataan]" value="{{ old('surat_pernyataan.tanggal_surat_pernyataan', $payloadDraft['surat_pernyataan']['tanggal_surat_pernyataan'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/2 p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload TTD Ketua (Bermaterai)</label>
                                <input type="file" name="surat_pernyataan[file_ttd_ketua_materai]" accept="image/*,.pdf" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format gambar atau PDF. Pastikan TTD mengenai materai.</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload TTD Sekretaris</label>
                                <input type="file" name="surat_pernyataan[file_ttd_sekretaris]" accept="image/*,.pdf" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format gambar atau PDF.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="h-px mt-10 mb-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mb-10">
                    <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                        <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">8</span> Formulir Isian Organisasi
                    </h3>
                    
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Lengkapi detail profil, sejarah, dan struktur organisasi secara lengkap sesuai dengan AD/ART.</p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Organisasi</label>
                                <input type="text" name="formulir_isian[nama_organisasi]" value="{{ old('formulir_isian.nama_organisasi', $payloadDraft['formulir_isian']['nama_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bidang Kegiatan</label>
                                <input type="text" name="formulir_isian[bidang_kegiatan]" value="{{ old('formulir_isian.bidang_kegiatan', $payloadDraft['formulir_isian']['bidang_kegiatan'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: Sosial, Pendidikan, Keagamaan" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ruang Lingkup</label>
                                <input type="text" name="formulir_isian[ruang_lingkup]" value="{{ old('formulir_isian.ruang_lingkup', $payloadDraft['formulir_isian']['ruang_lingkup'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: Kabupaten/Kota, Provinsi, Nasional" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Sekretariat</label>
                                <textarea name="formulir_isian[alamat_sekretariat]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>{{ old('formulir_isian.alamat_sekretariat', $payloadDraft['formulir_isian']['alamat_sekretariat'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Pendirian</label>
                                <input type="text" name="formulir_isian[tempat_pendirian]" value="{{ old('formulir_isian.tempat_pendirian', $payloadDraft['formulir_isian']['tempat_pendirian'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pendirian</label>
                                <input type="date" name="formulir_isian[tanggal_pendirian]" value="{{ old('formulir_isian.tanggal_pendirian', $payloadDraft['formulir_isian']['tanggal_pendirian'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asas / Ciri Organisasi</label>
                                <input type="text" name="formulir_isian[asas_ciri_organisasi]" value="{{ old('formulir_isian.asas_ciri_organisasi', $payloadDraft['formulir_isian']['asas_ciri_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: Pancasila dan UUD 1945" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan Organisasi</label>
                                <textarea name="formulir_isian[tujuan_organisasi]" rows="3" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>{{ old('formulir_isian.tujuan_organisasi', $payloadDraft['formulir_isian']['tujuan_organisasi'] ?? '') }}</textarea>
                            </div>

                            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-600 pt-4 mt-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pendiri Organisasi</label>
                                <textarea name="formulir_isian[nama_pendiri]" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Pisahkan dengan koma jika lebih dari satu" required>{{ old('formulir_isian.nama_pendiri', $payloadDraft['formulir_isian']['nama_pendiri'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pembina <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="text" name="formulir_isian[nama_pembina]" value="{{ old('formulir_isian.nama_pembina', $payloadDraft['formulir_isian']['nama_pembina'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Penasehat <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="text" name="formulir_isian[nama_penasehat]" value="{{ old('formulir_isian.nama_penasehat', $payloadDraft['formulir_isian']['nama_penasehat'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div class="md:col-span-2 grid gap-6 md:grid-cols-3">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Ketua</label>
                                    <input type="text" name="formulir_isian[nama_ketua]" value="{{ old('formulir_isian.nama_ketua', $payloadDraft['formulir_isian']['nama_ketua'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Sekretaris</label>
                                    <input type="text" name="formulir_isian[nama_sekretaris]" value="{{ old('formulir_isian.nama_sekretaris', $payloadDraft['formulir_isian']['nama_sekretaris'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Bendahara</label>
                                    <input type="text" name="formulir_isian[nama_bendahara]" value="{{ old('formulir_isian.nama_bendahara', $payloadDraft['formulir_isian']['nama_bendahara'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Masa Bhakti Kepengurusan</label>
                                <input type="text" name="formulir_isian[masa_bhakti_kepengurusan]" value="{{ old('formulir_isian.masa_bhakti_kepengurusan', $payloadDraft['formulir_isian']['masa_bhakti_kepengurusan'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: 2024 - 2029" required>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keputusan Tertinggi Organisasi</label>
                                <input type="text" name="formulir_isian[keputusan_tertinggi_organisasi]" value="{{ old('formulir_isian.keputusan_tertinggi_organisasi', $payloadDraft['formulir_isian']['keputusan_tertinggi_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: Musyawarah Nasional / Kongres" required>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Sayap Otonom <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="text" name="formulir_isian[unit_sayap_otonom]" value="{{ old('formulir_isian.unit_sayap_otonom', $payloadDraft['formulir_isian']['unit_sayap_otonom'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Usaha Organisasi <span class="text-xs text-gray-500">(Opsional)</span></label>
                                <input type="text" name="formulir_isian[usaha_organisasi]" value="{{ old('formulir_isian.usaha_organisasi', $payloadDraft['formulir_isian']['usaha_organisasi'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sumber Keuangan</label>
                                <input type="text" name="formulir_isian[sumber_keuangan]" value="{{ old('formulir_isian.sumber_keuangan', $payloadDraft['formulir_isian']['sumber_keuangan'] ?? '') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Contoh: Iuran Anggota, Sumbangan Tidak Mengikat" required>
                            </div>

                            <div class="md:col-span-2 grid gap-6 md:grid-cols-2 mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Logo Organisasi <span class="text-xs text-gray-500">(Opsional)</span></label>
                                    <input type="file" name="formulir_isian[file_logo_organisasi]" accept="image/*" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600">
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Bendera Organisasi <span class="text-xs text-gray-500">(Opsional)</span></label>
                                    <input type="file" name="formulir_isian[file_bendera_organisasi]" accept="image/*" class="block w-full text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none dark:bg-gray-800 dark:border-gray-600">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="flex justify-end items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                    <span id="save-status" class="text-sm text-gray-500 dark:text-gray-400 mr-4 font-medium"></span>

                    <button type="button" id="btn-autofill" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 mr-3 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                        <svg class="w-4 h-4 mr-2 inline-block -mt-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        Isi Dummy Data
                    </button>
                    
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Simpan Permohonan
                    </button>
                </div>
            </div>
        </form>       
    </div>
</div>

@push('scripts')
    @vite('resources/js/service-controller.js')
@endpush
@endsection