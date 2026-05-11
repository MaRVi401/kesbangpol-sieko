<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Mahasiswa\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevTemplateController;
use App\Http\Controllers\Operator\TicketController as OperatorTicketController;
use App\Http\Controllers\Admin\SiemController;
use App\Http\Controllers\Kabid\PersetujuanKabidController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Mahasiswa\DetailSuratIzinPermohonan;
use App\Http\Controllers\Mahasiswa\ServiceHistoryTicketController;


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

    });

    // 2. Pemohon
    Route::middleware('can:pemohon')->prefix('pemohon')->name('pemohon.')->group(function () {
        
        
    });

   // 3. Petugas Verifikasi Data
    Route::middleware('can:petugas_verifikasi_data')->prefix('verifikator-data')->name('verif_data.')->group(function () {
        
    });

    // 4. Petugas Verifikasi LapanRoute::get('/', function () { return 'Ini halaman Kaban'; })->name('index');gan
    Route::middleware('can:petugas_verifikasi_lapangan')->prefix('verifikator-lapangan')->name('verif_lapangan.')->group(function () {
        
    });

    // 5. Analis Kebijakan Ahli Muda
    Route::middleware('can:analis_kebijakan_ahli_muda')->prefix('analis')->name('analis.')->group(function () {
        
    });

    // 6. Kabid Kesbak
    Route::middleware('can:kabid_kesbak')->prefix('kabid')->name('kabid.')->group(function () {
       
    });

    // 7. Sekban
    Route::middleware('can:sekban')->prefix('sekban')->name('sekban.')->group(function () {
        
    });


    // 8. Kaban
    Route::middleware('can:kaban')->prefix('kaban')->name('kaban.')->group(function () {
        
    });
});
