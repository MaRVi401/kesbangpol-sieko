<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\TiketApiController;

// Endpoint Publik (Tidak butuh token)
Route::post('/pemohon/register', [AuthController::class, 'register']);
Route::post('/pemohon/login', [AuthController::class, 'login']);

// Endpoint Terproteksi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pemohon/logout', [AuthController::class, 'logout']);

    // API Pengajuan (dari kode ServiceController Anda)
    Route::post('/pemohon/pengajuan/ormas', [ServiceApiController::class, 'store']);
    Route::post('/pemohon/pengajuan/autosave', [ServiceApiController::class, 'autosave']);
    
    // API Status Tiket
    Route::get('/pemohon/tiket', [TiketApiController::class, 'index']);
    Route::get('/pemohon/tiket/{uuid}', [TiketApiController::class, 'show']);

    
    Route::post('/pemohon/pengajuan/{tiket_uuid}/lampiran/legalitas', [App\Http\Controllers\Api\LampiranApiController::class, 'storeLegalitas']);
    Route::post('/pemohon/pengajuan/{tiket_uuid}/lampiran/domisili', [App\Http\Controllers\Api\LampiranApiController::class, 'storeDomisili']);
    Route::post('/pemohon/pengajuan/{tiket_uuid}/lampiran/ktp', [App\Http\Controllers\Api\LampiranApiController::class, 'storeKtp']);
    Route::post('/pemohon/pengajuan/{tiket_uuid}/lampiran/selesai', [App\Http\Controllers\Api\LampiranApiController::class, 'finalize']);
});