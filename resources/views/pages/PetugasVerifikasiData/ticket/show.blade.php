@extends('layouts.main')

@section('title', 'Proses Verifikasi Tiket')

@section('content')
<div class="p-4 mt-14">
    <div class="max-w-5xl mx-auto">
        
        <div class="mb-6 flex justify-between items-center bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Verifikasi Tiket #{{ $ticket->no_tiket }}</h2>
                <p class="text-sm text-gray-500">Pemohon: {{ $ticket->user->nama ?? '-' }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if($ticket->status === 'review_berita_acara')
                    <a href="{{ route('verif_data.ticket.preview-pdf', $ticket->uuid) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Unduh Berita Acara
                    </a>
                    
                    <button type="button" data-modal-target="modal-kirim-analis" data-modal-toggle="modal-kirim-analis" class="bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 shadow-sm">
                        Kirim ke Analis
                    </button>
                @else
                    <button type="button" data-modal-target="modal-update-status" data-modal-toggle="modal-update-status" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 shadow-sm">
                        Update Status Tiket
                    </button>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 p-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Dokumen & Data Pemohon</h3>
            </div>
            
            <div class="p-6">
                @include('pages.pemohon.DetailSuratPermohonan.partials._detail_ormas')
            </div>
        </div>

    </div>
</div>

{{-- Render modal secara dinamis sesuai status tiket --}}
@if($ticket->status === 'review_berita_acara')
    @include('pages.PetugasVerifikasiData.ticket.partials.modal_kirim_analis') 
@else
    @include('pages.PetugasVerifikasiData.ticket.partials.modal_update_status') 
@endif

@push('scripts')
    @vite('resources/js/ticket-action.js')
@endpush

@endsection