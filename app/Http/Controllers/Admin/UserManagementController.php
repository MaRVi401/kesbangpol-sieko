<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin;
use App\Models\Pemohon;
use App\Models\PetugasVerifikasiData;
use App\Models\PetugasVerifikasiLapangan;
use App\Models\AnalisKebijakanAhliMuda;
use App\Models\KabidKesbak;
use App\Models\Sekban;
use App\Models\Kaban;
use App\Models\JejakAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    // Kumpulan Role Pejabat/ASN yang membutuhkan NIP
    private array $nipRoles = [
        'super_admin',
        'petugas_verifikasi_data',
        'petugas_verifikasi_lapangan',
        'analis_kebijakan_ahli_muda',
        'kabid_kesbak',
        'sekban',
        'kaban'
    ];

    /**
     * Display a listing of the users with Search & Pagination.
     */
    public function index(Request $request)
    {
        $query = User::query()->where('uuid', '!=', Auth::id());

        // Pastikan pemohon yang muncul HANYA yang sudah aktif
        $query->where(function ($q) {
            $q->where('role', '!=', 'pemohon')
                ->orWhereHas('pemohon', function ($subQuery) {
                    $subQuery->where('status_akun', 'aktif');
                });
        });

        // Filter logic
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search Logic
        if ($request->filled('search')) {
            // Bersihkan spasi berlebih di awal/akhir dan ubah ke huruf kecil
            $search = trim($request->search);
            $searchLower = strtolower($search);

            $query->where(function ($q) use ($search, $searchLower) {
                // Gunakan fungsi db LOWER() agar pencarian tidak peduli huruf besar/kecil
                $q->whereRaw('LOWER(nama) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(username) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"])
                    // Cari NIP di semua tabel relasi detail (Angka tidak perlu dilower)
                    ->orWhereHas('superAdmin', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('petugasVerifikasiData', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('petugasVerifikasiLapangan', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('analisKebijakanAhliMuda', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('kabidKesbak', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('sekban', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"))
                    ->orWhereHas('kaban', fn($sq) => $sq->where('nip', 'LIKE', "%{$search}%"));
            });
        }

        // Use paginate for Flowbite pagination support
        $users = $query->latest()->paginate(10);

        return view('pages.super-admin.user-management.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('pages.super-admin.user-management.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $rolesString = implode(',', $this->nipRoles);

        $rules = [
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'role'     => 'required|in:' . $rolesString,
            'nip'      => 'required_if:role,' . $rolesString . '|nullable|numeric|digits:18',
            'no_wa'    => 'nullable|numeric|digits_between:10,13',
            'password' => 'required|min:8|confirmed',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'alamat'   => 'nullable|string',
        ];

        $request->validate($rules, $this->customMessages());

        DB::beginTransaction();
        $avatarPath = null;
        try {
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = 'avatars/' . Str::random(40) . '.webp';
                $image = Image::read($file)->scale(width: 500)->encodeByExtension('webp', quality: 75);
                Storage::disk('local')->put($filename, (string) $image);
                $avatarPath = $filename;
            }

            $user = User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'alamat'   => $request->alamat,
                'no_wa'    => $request->no_wa,
                'avatar'   => $avatarPath,
            ]);

            $roleModel = $this->getRoleModel($request->role);

            $detailData = [
                'uuid'     => (string) Str::uuid(),
                'users_id' => $user->uuid,
            ];

            if (in_array($request->role, $this->nipRoles)) {
                $detailData['nip'] = $request->nip;
            }

            $roleModel::create($detailData);

            // Catat Jejak Audit
            JejakAudit::create([
                'users_id'   => Auth::id(),
                'aksi'       => 'create',
                'nama_tabel' => 'users',
                'record_id'  => $user->uuid,
                'data_baru'  => $user->toArray(),
                'ip_address' => $request->ip()
            ]);

            DB::commit();
            return redirect()->route('user-management.index')->with('success', 'User baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($avatarPath) {
                Storage::disk('local')->delete($avatarPath);
            }
            return back()->withErrors(['error' => 'Gagal sistem: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $nip = '';

        if (in_array($user->role, $this->nipRoles)) {
            // Mengubah format role (contoh: 'kabid_kesbak' menjadi 'kabidKesbak')
            $relationName = Str::camel($user->role);
            $nip = $user->{$relationName} ? $user->{$relationName}->nip : '';
        }

        return view('pages.super-admin.user-management.edit', compact('user', 'nip'));
    }

    /**
     * Update user data and sync Minio (Private Storage) storage.
     */
    public function update(Request $request, User $user)
    {
        $rolesString = implode(',', $this->nipRoles);

        $rules = [
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->uuid . ',uuid',
            'username' => 'required|string|unique:users,username,' . $user->uuid . ',uuid',
            'role'     => 'required|in:' . $rolesString,
            'nip'      => 'required_if:role,' . $rolesString . '|nullable|numeric|digits:18',
            'no_wa'    => 'nullable|numeric|digits_between:10,13',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ];
        $request->validate($rules, $this->customMessages());

        DB::beginTransaction();
        try {
            $oldRole = $user->getOriginal('role');
            $dataLama = $user->getRawOriginal();

            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->no_wa = $request->no_wa;
            $user->alamat = $request->alamat;
            $user->role = $request->role;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('local')->delete($user->avatar);
                }
                $filename = 'avatars/' . Str::random(40) . '.webp';
                $image = Image::read($request->file('avatar'))->scale(width: 500)->encodeByExtension('webp', quality: 75);
                Storage::disk('local')->put($filename, (string) $image);
                $user->avatar = $filename;
            }

            $user->save();

            // Sinkronisasi Tabel Role
            $roleModel = $this->getRoleModel($request->role);
            $oldRoleModel = $this->getRoleModel($oldRole);

            if ($oldRole !== $request->role) {
                $oldRoleModel::where('users_id', $user->uuid)->delete();

                $newData = [
                    'uuid' => (string) Str::uuid(),
                    'users_id' => $user->uuid,
                ];

                if (in_array($request->role, $this->nipRoles)) {
                    $newData['nip'] = $request->nip;
                }

                $roleModel::create($newData);
            } else {
                if (in_array($request->role, $this->nipRoles)) {
                    $roleModel::where('users_id', $user->uuid)->update([
                        'nip' => $request->nip
                    ]);
                }
            }

            JejakAudit::create([
                'users_id' => Auth::id(),
                'aksi' => 'update',
                'nama_tabel' => 'users',
                'record_id' => $user->uuid,
                'data_lama' => $dataLama,
                'data_baru' => $user->fresh()->toArray(),
                'ip_address' => request()->ip()
            ]);

            DB::commit();
            return redirect()->route('user-management.index')->with('success', 'Profil user diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove user and clean (Private Storage).
     */
    public function destroy(User $user)
    {
        // 1. Validasi: Jangan hapus diri sendiri
        if ($user->uuid === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // 2. Validasi Relasi: Cek apakah user ini masih dirujuk oleh tabel tiket
        if ($user->tiketDitangani()->exists() || $user->tiketDibuat()->exists()) {
            return back()->with('error', 'User ini masih memiliki riwayat tiket yang terdaftar.');
        }

        // 3. Proses hapus jika tidak ada relasi
        DB::beginTransaction();
        try {
            if ($user->avatar) {
                Storage::disk('local')->delete($user->avatar);
            }

            JejakAudit::create([
                'users_id' => Auth::id(),
                'aksi' => 'delete',
                'nama_tabel' => 'users',
                'record_id' => $user->uuid,
                'data_lama' => $user->toArray(),
                'ip_address' => request()->ip()
            ]);

            $user->delete();
            DB::commit();

            return redirect()->route('user-management.index')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function pendingPemohon()
    {
        $pendingUsers = User::where('role', 'pemohon')
            ->whereHas('pemohon', function ($query) {
                $query->where('status_akun', 'pending');
            })
            ->with('pemohon')
            ->latest()
            ->paginate(10);

        return view('pages.super-admin.user-management.pending', compact('pendingUsers'));
    }

    public function activate(Request $request, string $uuid)
    {
        $request->validate([
            'status' => 'required|in:aktif,ditolak'
        ]);

        DB::beginTransaction();
        try {
            $pemohon = Pemohon::where('users_id', $uuid)->firstOrFail();
            $statusLama = $pemohon->status_akun;

            $pemohon->update([
                'status_akun' => $request->status
            ]);

            JejakAudit::create([
                'users_id' => Auth::id(),
                'aksi' => 'update',
                'nama_tabel' => 'pemohon',
                'record_id' => $pemohon->uuid,
                'data_lama' => ['status_akun' => $statusLama],
                'data_baru' => ['status_akun' => $request->status],
                'ip_address' => request()->ip()
            ]);

            DB::commit();
            $msg = $request->status == 'aktif' ? 'Akun berhasil diaktifkan.' : 'Akun telah ditolak.';
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses aktivasi: ' . $e->getMessage());
        }
    }

    public function rejectedPemohon()
    {
        $rejectedUsers = User::where('role', 'pemohon')
            ->whereHas('pemohon', function ($query) {
                $query->where('status_akun', 'ditolak');
            })
            ->with('pemohon')
            ->latest()
            ->paginate(10);

        return view('pages.super-admin.user-management.rejected', compact('rejectedUsers'));
    }

    public function forceDeletePemohon(string $uuid)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($uuid);
            $pemohon = Pemohon::where('users_id', $uuid)->first();

            // Hapus file fisik jika ada
            if ($pemohon) {
                if ($pemohon->kta_path) Storage::disk('local')->delete($pemohon->kta_path);
                if ($pemohon->surat_rekomendasi_path) Storage::disk('local')->delete($pemohon->surat_rekomendasi_path);
            }

            // Hapus user (akan menghapus relasi pemohon karena cascadeOnDelete)
            $user->delete();

            DB::commit();
            return back()->with('success', 'User dan dokumen berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Helper to get Role Model class.
     */
    private function getRoleModel(string $role)
    {
        return [
            'super_admin'                 => SuperAdmin::class,
            'pemohon'                     => Pemohon::class,
            'petugas_verifikasi_data'     => PetugasVerifikasiData::class,
            'petugas_verifikasi_lapangan' => PetugasVerifikasiLapangan::class,
            'analis_kebijakan_ahli_muda'  => AnalisKebijakanAhliMuda::class,
            'kabid_kesbak'                => KabidKesbak::class,
            'sekban'                      => Sekban::class,
            'kaban'                       => Kaban::class,
        ][$role];
    }

    private function customMessages()
    {
        return [
            'nama.required'        => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email sudah terdaftar di sistem.',
            'username.required'    => 'Username wajib diisi.',
            'username.unique'      => 'Username sudah digunakan.',
            'nip.required_if'      => 'NIP wajib diisi jika Anda memilih role ASN/Pejabat.',
            'nip.numeric'          => 'NIP harus berupa angka.',
            'nip.digits'           => 'NIP harus berjumlah 18 digit.',
            'no_wa.numeric'        => 'Nomor WhatsApp harus berupa angka.',
            'no_wa.digits_between' => 'Nomor WhatsApp harus berjumlah antara 10 sampai 13 digit.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal harus 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'avatar.image'         => 'File yang diunggah harus berupa gambar.',
            'avatar.mimes'         => 'Format gambar harus JPG, JPEG, PNG, atau WebP.',
            'avatar.max'           => 'Ukuran foto terlalu besar, maksimal adalah 2MB.',
        ];
    }
}
