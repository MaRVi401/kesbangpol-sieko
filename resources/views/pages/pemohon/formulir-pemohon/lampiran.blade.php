@extends('layouts.main')

@section('title', 'Unggah Lampiran Dokumen Ormas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-8">
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Langkah 2: Unggah Lampiran Persyaratan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Silakan unggah dokumen legalitas dan persyaratan administratif secara bertahap.</p>
        </div>

        {{-- FORM 1: LEGALITAS --}}
        <form class="lampiran-form mb-10 border border-gray-200 dark:border-gray-700 p-6 rounded-xl relative overflow-hidden" 
              action="{{ route('pemohon.services.lampiran.legalitas', $tiketUuid) }}" method="POST">
            @csrf
            <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">1</span>
                Dokumen Legalitas Utama
            </h3>
            
            <fieldset class="grid gap-6 md:grid-cols-2" @if($isLegalitasDone) disabled @endif>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Akte Pendirian dari Notaris (PDF)</label>
                    <input type="file" name="akta_pendirian" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SK Kemenkumham / SKT (PDF)</label>
                    <input type="file" name="sk_kemenkumham" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">AD / ART (PDF)</label>
                    <input type="file" name="file_ad_art" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Program Kerja (PDF)</label>
                    <input type="file" name="file_program_kerja" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
            </fieldset>
            
            <div class="mt-6 flex justify-end">
                @if($isLegalitasDone)
                    <span class="inline-flex items-center text-sm font-medium text-green-600 bg-green-100 px-4 py-2 rounded-lg"><svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tersimpan</span>
                @else
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Unggah Bagian 1</button>
                @endif
            </div>
        </form>

        {{-- FORM 2: IDENTITAS & DOMISILI --}}
        <form class="lampiran-form mb-10 border border-gray-200 dark:border-gray-700 p-6 rounded-xl relative overflow-hidden" 
              action="{{ route('pemohon.services.lampiran.domisili', $tiketUuid) }}" method="POST">
            @csrf
            <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">2</span>
                Identitas & Keterangan Domisili
            </h3>
            
            <fieldset class="grid gap-6 md:grid-cols-2" @if($isDomisiliDone) disabled @endif>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SK Kepengurusan (PDF)</label>
                    <input type="file" name="file_sk_kepengurusan" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Surat Mandat Pengurus (PDF)</label>
                    <input type="file" name="file_surat_mandat" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ket. Domisili Camat (PDF/JPG)</label>
                    <input type="file" name="surat_domisili" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto Kantor (JPG/PNG)</label>
                    <input type="file" name="file_foto_kantor" accept=".jpg,.jpeg,.png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NPWP Organisasi (PDF/JPG)</label>
                    <input type="file" name="file_npwp" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SK Terlapor Kesbangpol <span class="text-xs font-normal">(Opsional)</span></label>
                    <input type="file" name="file_sk_terlapor" accept=".pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                </div>
            </fieldset>

            <div class="mt-6 flex justify-end">
                @if($isDomisiliDone)
                    <span class="inline-flex items-center text-sm font-medium text-green-600 bg-green-100 px-4 py-2 rounded-lg"><svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tersimpan</span>
                @else
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Unggah Bagian 2</button>
                @endif
            </div>
        </form>

        {{-- FORM 3: KTP PENGURUS INTI --}}
        <form class="lampiran-form mb-10 border border-gray-200 dark:border-gray-700 p-6 rounded-xl relative overflow-hidden" 
              action="{{ route('pemohon.services.lampiran.ktp', $tiketUuid) }}" method="POST">
            @csrf
            <h3 class="flex items-center text-lg font-bold text-gray-900 dark:text-white mb-6">
                <span class="flex items-center justify-center w-6 h-6 mr-2 text-sm rounded-full bg-blue-100 text-blue-600">3</span>
                Fotokopi KTP Pengurus Inti
            </h3>
            
            <fieldset class="grid gap-6 md:grid-cols-3" @if($isKtpDone) disabled @endif>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">KTP Ketua</label>
                    <input type="file" name="ktp_ketua" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">KTP Sekretaris</label>
                    <input type="file" name="ktp_sekretaris" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">KTP Bendahara</label>
                    <input type="file" name="ktp_bendahara" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" required>
                </div>
            </fieldset>

            <div class="mt-6 flex justify-end">
                @if($isKtpDone)
                    <span class="inline-flex items-center text-sm font-medium text-green-600 bg-green-100 px-4 py-2 rounded-lg"><svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> Tersimpan</span>
                @else
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Unggah Bagian 3</button>
                @endif
            </div>
        </form>

        {{-- TOMBOL FINALISASI (Cek kelengkapan di JS/Backend) --}}
        <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('pemohon.history.show', $tiketUuid) }}" class="text-gray-600 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300">
                Kembali
            </a>
            
            <button type="button" id="btn-selesaikan-permohonan" data-url="{{ route('pemohon.services.lampiran.selesai', $tiketUuid) }}" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
                Selesaikan Permohonan
                <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/lampiran-controller.js')
@endpush