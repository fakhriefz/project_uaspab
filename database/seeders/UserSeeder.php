<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Hapus data yang ada sebelumnya
        DB::table('users')->truncate();
        DB::table('terminals')->truncate();

        // Buat Administrator Utama
        $admin_id = DB::table('users')->insertGetId([
            'name' => 'Administrator Sistem Kereta',
            'email' => 'admin@keretaapi.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'ADMINISTRATOR',
            'plain_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat Terminal Pertama
        $terminal_1_id = DB::table('users')->insertGetId([
            'name' => 'Terminal Stasiun Semarang',
            'email' => 'terminal1@keretaapi.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'TERMINAL',
            'plain_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('terminals')->insert([
            'user_id' => $terminal_1_id,
            'location_branch' => 'Stasiun Semarang',
            'payment_enabled' => true,
            'topup_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat Terminal Kedua
        $terminal_2_id = DB::table('users')->insertGetId([
            'name' => 'Terminal Stasiun Bandung',
            'email' => 'terminal2@keretaapi.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'role' => 'TERMINAL',
            'plain_token' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('terminals')->insert([
            'user_id' => $terminal_2_id,
            'location_branch' => 'Stasiun Bandung',
            'payment_enabled' => true,
            'topup_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}