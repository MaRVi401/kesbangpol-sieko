<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevTemplateController;

use App\Http\Controllers\Admin\SiemController;
use App\Http\Controllers\Kabid\PersetujuanKabidController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileController;

use App\Http\Controllers\Pemohon\DashboardController as PemohonDashboardController;
use App\Http\Controllers\Pemohon\ServiceController;
use App\Http\Controllers\Pemohon\ServiceHistoryTicketController;



use App\Http\Controllers\Pemohon\LampiranController;


/*
|--------------------------------------------------------------------------
| Test Error Routes
|--------------------------------------------------------------------------
*/

Route::get('/error/{code}', function ($code) {
    abort($code);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Halaman Register
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Proses Register
    Route::post('/register', [RegisterController::class, 'register']);

    // Halaman Login
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Proses Login
    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.post');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard All Roles
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Rute untuk akses file di storage private
    Route::get('/storage/private/{path}', [FileController::class, 'show'])
        ->where('path', '.*')
        ->middleware('auth')
        ->name('file.show');

    // Edit profile

    // Route untuk halaman profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    /**
     * Route khusus untuk menampilkan avatar dari storage private
     * Menggunakan parameter {filename} untuk mencari file di storage/app/avatars/
     */
    Route::get('/user/avatar/{filename}', function ($filename) {
        $path = 'avatars/' . $filename;

        // Pastikan file ada di storage/app/avatars
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // Mengembalikan file sebagai response gambar
        return Storage::disk('local')->response($path);
    })->name('avatar.display')->middleware('auth');

    // Proses Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    /*
    |----------------------------------------------------------------------
    | Khusus Super Admin
    |----------------------------------------------------------------------
    */
    Route::middleware('can:super-admin-only')->group(function () {
        Route::get('user-management/pending-pemohon', [UserManagementController::class, 'pendingPemohon'])
            ->name('user-management.pending');
        Route::post('user-management/activate/{uuid}', [UserManagementController::class, 'activate'])
            ->name('user-management.activate');
        Route::get('user-management/rejected-pemohon', [UserManagementController::class, 'rejectedPemohon'])
            ->name('user-management.rejected');
        Route::delete('user-management/force-delete/{uuid}', [UserManagementController::class, 'forceDeletePemohon'])
            ->name('user-management.forceDelete');


        Route::resource('user-management', UserManagementController::class)
            ->names('user-management')
            ->parameters(['user-management' => 'user']);

        Route::prefix('super-admin/siem')->name('siem.')->controller(SiemController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/security-logs', 'securityLogs')->name('security-logs');
            Route::get('/audit-trails', 'auditTrails')->name('audit-trails');
        });
    });

    // 2. Pemohon
    Route::middleware('can:pemohon')->prefix('pemohon')->name('pemohon.')->group(function () {
        Route::get('/', [PemohonDashboardController::class, 'index'])->name('index');

        Route::resource('services', ServiceController::class)->only(['index', 'store']);
        Route::post('services/autosave', [ServiceController::class, 'autosave'])->name('services.autosave');
        Route::get('/history', [ServiceHistoryTicketController::class, 'index'])->name('history.index');
        Route::get('/history/{uuid}', [ServiceHistoryTicketController::class, 'show'])->name('history.show');
        
        Route::get('services/{tiket_uuid}/lampiran', [LampiranController::class, 'create'])->name('services.lampiran');
        
        Route::post('services/{tiket_uuid}/lampiran/legalitas', [LampiranController::class, 'storeLegalitas'])->name('services.lampiran.legalitas');
        Route::post('services/{tiket_uuid}/lampiran/domisili', [LampiranController::class, 'storeDomisili'])->name('services.lampiran.domisili');
        Route::post('services/{tiket_uuid}/lampiran/ktp', [LampiranController::class, 'storeKtp'])->name('services.lampiran.ktp');
        
        Route::post('services/{tiket_uuid}/lampiran/selesai', [LampiranController::class, 'finalize'])->name('services.lampiran.selesai');

        Route::delete('/history/{uuid}', [ServiceHistoryTicketController::class, 'destroy'])->name('history.destroy');
    });

    // 3. Petugas Verifikasi Data
    Route::middleware('can:petugas_verifikasi_data')->prefix('verifikator-data')->name('verif_data.')->group(function () {
        
            Route::post('/ticket/{uuid}/kirim-analis', [\App\Http\Controllers\PetugasVerifikasiData\TicketController::class, 'kirimKeAnalis'])->name('ticket.kirim-analis');
            Route::controller(\App\Http\Controllers\PetugasVerifikasiData\TicketController::class)->name('ticket.')->group(function () {
            Route::get('/tiket-masuk', 'index')->name('index');
            Route::post('/tiket/{uuid}/handle', 'handle')->name('handle');

            Route::get('/meja-kerja', 'workDesk')->name('workdesk');
            Route::put('/tiket/{uuid}', 'update')->name('update');
            Route::get('/tiket/{uuid}/detail', 'show')->name('show');

            Route::get('/riwayat', 'history')->name('history');


            Route::get('/revisi', 'revisi')->name('revisi');

            


            Route::get('/tiket/{uuid}/preview-pdf', 'previewPdf')->name('preview-pdf');
            Route::get('/tiket/{uuid}/download-docx', 'unduhSurat')->name('download-docx');
        });
    });

    // 4. Petugas Verifikasi LapanRoute::get('/', function () { return 'Ini halaman Kaban'; })->name('index');gan
    // 4. Petugas Verifikasi Lapangan
    Route::middleware('can:petugas_verifikasi_lapangan')->prefix('verifikator-lapangan')->name('verif_lapangan.')->group(function () {
        Route::controller(\App\Http\Controllers\PetugasVerifikasiLapangan\TicketController::class)->name('ticket.')->group(function () {
            
            Route::get('/antrean', 'index')->name('index');
            Route::get('/meja-kerja', 'workdesk')->name('workdesk');
            Route::get('/riwayat', 'history')->name('history');
            
            Route::get('/tiket/{uuid}/mulai', 'mulaiVerifikasi')->name('mulai');
            Route::get('/tiket/{uuid}/berita-acara', 'lihatBeritaAcara')->name('berita-acara');
            Route::get('/tiket/{uuid}/show', 'show')->name('show');
            
            // --- Manajemen Berita Acara Lapangan ---
            
            // Simpan Berita Acara Baru
            Route::post('/tiket/{uuid}/simpan-berita', 'simpanBeritaAcara')->name('simpan_berita');
            
            // Update Berita Acara 
            Route::put('/tiket/{uuid}/update-berita', 'updateBeritaAcara')->name('update_berita'); 
            
            // Generate & Download PDF
            Route::get('/tiket/{uuid}/generate-pdf-berita-acara', 'generatePdfBeritaAcaraLapangan')->name('generate_pdf');
            
            // Upload Fisik Scan Berita Acara
            Route::post('/tiket/{uuid}/upload-scan', 'uploadScanBeritaAcara')->name('upload_scan');
            
        });
    });
    // 5. Analis Kebijakan Ahli Muda
    Route::middleware('can:analis_kebijakan_ahli_muda')->prefix('analis')->name('analis.')->group(function () {
        Route::get('/dashboard/unduh-surat/{tiket:uuid}', [\App\Http\Controllers\Analis\DashboardController::class, 'unduhSuratRegistrasi'])->name('unduh.surat');

        Route::post('/dashboard/unggah-ttd-basah', [\App\Http\Controllers\Analis\DashboardController::class, 'unggahTtdBasah'])->name('unggah.ttd_basah');

    });

    // 6. Kabid Kesbak
    Route::middleware('can:kabid_kesbak')->prefix('kabid')->name('kabid.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Kabid\PersetujuanKabidController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/proses/{uuid}', [App\Http\Controllers\Kabid\PersetujuanKabidController::class, 'proses'])->name('tiket.proses');
    });

    // 7. Sekban
    Route::middleware('can:sekban')->prefix('sekban')->name('sekban.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Sekban\DashboardController::class, 'index'])->name('dashboard');
    
        Route::post('/dashboard/proses/{uuid}', [App\Http\Controllers\Sekban\DashboardController::class, 'proses'])->name('tiket.proses');
    });


    // 8. Kaban
    Route::middleware('can:kaban')->prefix('kaban')->name('kaban.')->group(function () {});
});
