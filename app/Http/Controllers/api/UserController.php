<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // Registrasi Administrator
    public function register_admin(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => bcrypt($request->password),
            'remember_token' => Str::random(10),
            'role' => 'ADMINISTRATOR',
            'plain_token' => '',
        ]);

        return response()->json(['message' => 'Registrasi Administrator Berhasil'], 200);
    }

    // Registrasi Terminal
    public function register_terminal(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
        ]);

        $id = DB::table('users')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'TERMINAL',
            'plain_token' => '',
        ]);

        $user = User::find($id);
        $plain_token = $user->createToken('machine-to-machine-token')->plainTextToken;
        $user->plain_token = $plain_token;
        $user->save();

        return response()->json([
            'token' => $plain_token,
            'message' => 'Registrasi Terminal Berhasil',
        ], 200);
    }

    // Login Administrator
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'ADMINISTRATOR')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial Login Tidak Valid'], 401);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => 'Login Berhasil',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout Berhasil'], 200);
    }

    // Get Terminal Token
    public function terminal_token(Request $request)
    {
        $email = $request->get('email', '');
        \Log::info("Email yang diterima: {$email}");
        
        $user = User::where('email', $email)->where('role', 'TERMINAL')->first();
        
        if ($user == null) {
            \Log::info("User tidak ditemukan untuk email: {$email}");
            return response()->json([
                'message' => 'Data tidak ada',
                'email' => $email,
            ], 404);
        } else {
            return response()->json([
                'message' => 'Berhasil',
                'token' => $user->plain_token,
                'email' => $email,
            ], 200);
        }
    }

    // List Users
    public function list(Request $request)
    {
        $page = $request->input('page', 0);
        $page_size = $request->input('page_size', 10);
        
        return response()->json([
            'message' => 'Berhasil',
            'users' => User::skip($page * $page_size)->take($page_size)
                ->select('id', 'name', 'email', 'role')->get(),
        ], 200);
    }
}