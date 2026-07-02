<?php

namespace App\Http\Controllers\Api;

use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pemohon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi persis seperti versi Web
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_wa' => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'alamat' => 'required|string',
            'nik' => 'required|string|size:16|unique:pemohon,nik_ketua',
            'nama_organisasi' => 'required|string|max:255',
            'kta' => 'required|image|mimes:jpg,png,jpeg|max:5120',
            'surat_rekomendasi' => 'required|mimes:pdf|max:2048',
        ]);

        $uploadedFiles = [];
        DB::beginTransaction();

        try {
            // 2. Buat User
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'nama' => $request->name, // Menyesuaikan kolom 'nama' di skema DB
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pemohon',
                'no_wa' => $request->no_wa,
                'alamat' => $request->alamat,
            ]);

            // 3. Proses Upload File persis seperti versi Web
            $fileKta = $request->file('kta');
            $fileNameKta = 'KTA_' . $request->nik . '_' . time() . '.webp';
            $pathKta = 'verifikasi/kta/' . $fileNameKta;

            $img = Image::read($fileKta)->scale(width: 1200)->encodeByExtension('webp', quality: 75);
            Storage::disk('local')->put($pathKta, (string) $img);
            $uploadedFiles[] = $pathKta;

            $pathSurat = $request->file('surat_rekomendasi')->store('verifikasi/rekomendasi', 'local');
            $uploadedFiles[] = $pathSurat;

            // 4. Buat Profil Pemohon
            Pemohon::create([
                'uuid' => (string) Str::uuid(),
                'users_id' => $user->uuid,
                'nik_ketua' => $request->nik,
                'nama_organisasi' => $request->nama_organisasi,
                'kta_path' => $pathKta,
                'surat_rekomendasi_path' => $pathSurat,
                'status_akun' => 'pending',
            ]);

            DB::commit();

            // 5. Generate Token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi pemohon berhasil. Akun berstatus pending.',
                'access_token' => $token,
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            foreach ($uploadedFiles as $file) {
                Storage::disk('local')->delete($file);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal registrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial tidak valid'
            ], 401);
        }

        // Pengecekan status pemohon (seperti di LoginController Web)
        if ($user->role === 'pemohon') {
            $pemohon = Pemohon::where('users_id', $user->uuid)->first();
            $status = $pemohon->status_akun ?? null;

            if ($status !== 'aktif') {
                return response()->json([
                    'status' => 'error',
                    'message' => $status === 'ditolak' ? 'Akun Anda ditolak.' : 'Akun Anda belum aktif. Menunggu verifikasi.'
                ], 403);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out']);
    }
}