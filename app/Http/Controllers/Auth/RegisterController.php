<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pemohon; // <-- DIUBAH: Menggunakan model Pemohon
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Definisi Aturan Validasi
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'no_wa' => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'alamat' => 'required|string',
            'nik' => 'required|string|size:16|unique:pemohon,nik_ketua',
            'nama_organisasi' => 'required|string|max:255',
            'kta' => 'required|image|mimes:jpg,png,jpeg|max:5120',
            'surat_rekomendasi' => 'required|mimes:pdf|max:2048',
        ];

        // Memanggil Pesan Custom
        $messages = $this->customMessages();

        // Eksekusi Validasi dengan Pesan Custom
        $request->validate($rules, $messages);

        $uploadedFiles = [];
        DB::beginTransaction();

        try {
            $user = User::create([
                'nama' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pemohon', // <-- DIUBAH: Role menjadi pemohon
                'no_wa' => $request->no_wa,
                'alamat' => $request->alamat,
            ]);

            // --- PROSES KOMPRESI GAMBAR KTA KE WEBP ---
            $fileKta = $request->file('kta');
            $fileNameKta = 'KTA_' . $request->nik . '_' . time() . '.webp';
            $pathKta = 'verifikasi/kta/' . $fileNameKta;

            $img = Image::read($fileKta)
                ->scale(width: 1200)
                ->encodeByExtension('webp', quality: 75);

            Storage::disk('local')->put($pathKta, (string) $img);
            $uploadedFiles[] = $pathKta;

            // --- PROSES SURAT REKOMENDASI ---
            $pathSurat = $request->file('surat_rekomendasi')->store('verifikasi/rekomendasi', 'local');
            $uploadedFiles[] = $pathSurat;

            // <-- DIUBAH: Insert data ke tabel pemohon
            Pemohon::create([
                'users_id' => $user->uuid,
                'nik_ketua' => $request->nik,
                'nama_organisasi' => $request->nama_organisasi,
                'kta_path' => $pathKta,
                'surat_rekomendasi_path' => $pathSurat,
                'status_akun' => 'pending',
            ]);

            DB::commit();
            return redirect('/login')->with('success', 'Registrasi berhasil.');
        } catch (\Exception $e) {
            DB::rollback();
            foreach ($uploadedFiles as $file) {
                Storage::disk('local')->delete($file);
            }
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Definisi Pesan Error Custom
     */
    private function customMessages()
    {
        return [
            'name.required'              => 'Nama lengkap wajib diisi.',
            'username.required'          => 'Username wajib diisi.',
            'username.unique'            => 'Username sudah digunakan oleh orang lain.',
            'email.required'             => 'Alamat email wajib diisi.',
            'email.email'                => 'Format email tidak valid.',
            'email.unique'               => 'Email sudah terdaftar di sistem.',
            'password.required'          => 'Password wajib diisi.',
            'password.min'               => 'Password minimal harus 8 karakter.',
            'password.confirmed'         => 'Konfirmasi password tidak cocok.',
            'no_wa.required'             => 'Nomor WhatsApp wajib diisi.',
            'no_wa.max'                  => 'Nomor WhatsApp maksimal 15 digit.',
            'no_wa.regex'                => 'Nomor WhatsApp hanya boleh berisi angka.',
            'no_wa.min'                  => 'Nomor WhatsApp minimal 10 digit.',
            'alamat.required'            => 'Alamat lengkap wajib diisi.',
            'nik.required'               => 'NIK wajib diisi.',
            'nik.size'                   => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'                 => 'NIK ini sudah terdaftar.',
            'nama_organisasi.required'   => 'Nama organisasi wajib diisi.',
            'nama_organisasi.max'        => 'Nama organisasi maksimal 255 karakter.',
            'kta.required'               => 'Foto KTA wajib diunggah.',
            'kta.image'                  => 'File KTA harus berupa gambar.',
            'kta.mimes'                  => 'Format KTA harus JPG, PNG, atau JPEG.',
            'kta.max'                    => 'Ukuran foto KTA maksimal 5MB.',
            'surat_rekomendasi.required' => 'Surat rekomendasi wajib diunggah.',
            'surat_rekomendasi.mimes'    => 'Surat rekomendasi harus berformat PDF.',
            'surat_rekomendasi.max'      => 'Ukuran file surat rekomendasi maksimal 2MB.',
        ];
    }
}
