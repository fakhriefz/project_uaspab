<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TerminalController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users'],
            'location_branch' => ['required', 'string', 'max:255'],
            'payment_enabled' => ['required', 'boolean'],
            'topup_enabled' => ['required', 'boolean'],
        ]);

        // Minimal satu fungsi harus aktif
        if (!$request->payment_enabled && !$request->topup_enabled) {
            return response()->json([
                'message' => 'Terminal harus memiliki minimal satu fungsi aktif'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $terminal = Terminal::where('user_id', $user->id)->first();

        if ($terminal != null) {
            return response()->json([
                'message' => 'Email sudah dipakai di terminal lain'
            ], 422);
        }

        DB::table('terminals')->insert([
            'user_id' => $user->id,
            'location_branch' => $request->location_branch,
            'payment_enabled' => $request->payment_enabled,
            'topup_enabled' => $request->topup_enabled,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Terminal berhasil dibuat'], 200);
    }
    public function list(Request $request)
    {
        $page = $request->input('page', 0);
        $page_size = $request->input('page_size', 10);
        
        $terminals = Terminal::skip($page * $page_size)
            ->take($page_size)
            ->get();

        return response()->json([
            'message' => 'Berhasil',
            'terminals' => $terminals
        ], 200);
    }
}