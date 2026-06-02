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
            
            <button type="button" data-modal-target="modal-update-status" data-modal-toggle="modal-update-status" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm px-5 py-2.5">
                Update Status Tiket
            </button>
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

@include('pages.PetugasVerifikasiData.ticket.partials.modal_update_status') 

@endsection