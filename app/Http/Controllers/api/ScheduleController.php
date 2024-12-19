<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'train_title' => ['required', 'string', 'max:255'],
            'train_category' => ['required', 'string', 'max:255'],
            'start_station' => ['required', 'string', 'max:255'],
            'end_station' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
        ]);

        DB::table('schedules')->insert([
            'train_title' => $request->train_title,
            'train_category' => $request->train_category,
            'start_station' => $request->start_station,
            'end_station' => $request->end_station,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Jadwal kereta berhasil dibuat'], 200);
    }

    public function list(Request $request)
    {
        $page = $request->input('page', 0);
        $page_size = $request->input('page_size', 10);

        $schedules = Schedule::skip($page * $page_size)
            ->take($page_size)
            ->get();

        return response()->json([
            'message' => 'Berhasil',
            'schedules' => $schedules
        ], 200);
    }

    public function detail($id)
    {
        $schedule = Schedule::findOrFail($id);
        
        return response()->json([
            'message' => 'Berhasil',
            'schedule' => $schedule
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'train_title' => ['required', 'string', 'max:255'],
            'train_category' => ['required', 'string', 'max:255'],
            'start_station' => ['required', 'string', 'max:255'],
            'end_station' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->all());

        return response()->json(['message' => 'Jadwal kereta berhasil diperbarui'], 200);
    }

    public function delete($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Jadwal kereta berhasil dihapus'], 200);
    }
}