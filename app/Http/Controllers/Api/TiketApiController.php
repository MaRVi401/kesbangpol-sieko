<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketApiController extends Controller
{
    // Melihat daftar semua tiket milik pemohon
    public function index(Request $request)
    {
        $tiket = Tiket::with(['layanan', 'riwayatStatus' => function($q) {
                $q->latest();
            }])
            ->where('users_id', $request->user()->uuid)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $tiket
        ]);
    }

    // Melihat detail 1 tiket dan status revisinya
    public function show(Request $request, $uuid)
    {
        $tiket = Tiket::with(['layanan', 'riwayatStatus', 'komentar'])
            ->where('uuid', $uuid)
            ->where('users_id', $request->user()->uuid)
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $tiket
        ]);
    }
}