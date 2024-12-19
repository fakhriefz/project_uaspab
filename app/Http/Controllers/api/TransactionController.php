<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Terminal;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function payTicket(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'schedule_id' => 'required|exists:schedules,id',
            'amount_paid' => 'required|numeric|min:1',
        ]);

        $terminal = Terminal::where('user_id', auth()->id())->first();
        
        if (!$terminal || !$terminal->payment_enabled) {
            return response()->json(['message' => 'Terminal tidak memiliki akses pembayaran'], 403);
        }

        $card = Card::findOrFail($request->card_id);
        
        if ($card->card_balance < $request->amount_paid) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }

        DB::transaction(function () use ($request, $terminal, $card) {
            // Mencatat transaksi pembayaran
            DB::table('payments')->insert([
                'terminal_id' => $terminal->id,
                'card_id' => $request->card_id,
                'schedule_id' => $request->schedule_id,
                'amount_paid' => $request->amount_paid,
                'payment_date_time' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mengurangi saldo kartu
            $card->decrement('card_balance', $request->amount_paid);
        });

        return response()->json(['message' => 'Pembayaran berhasil'], 200);
    }

    public function transactionHistory($cardId)
    {
        $payments = Payment::where('card_id', $cardId)
            ->with(['terminal', 'schedule'])
            ->get();

        $topups = DB::table('topups')
            ->where('card_id', $cardId)
            ->get();

        return response()->json([
            'message' => 'Berhasil',
            'payments' => $payments,
            'topups' => $topups
        ], 200);
    }
}