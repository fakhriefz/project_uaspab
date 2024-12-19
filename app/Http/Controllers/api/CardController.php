<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Terminal;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    public function create()
    {
        $id = DB::table('cards')->insertGetId([
            'card_balance' => 0,
            'expiry_date' => now()->addYears(5), // Kartu berlaku 5 tahun
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'id' => $id,
            'message' => 'Kartu berhasil dibuat'
        ], 200);
    }

    public function getBalance($id)
    {
        $card = Card::findOrFail($id);
        return response()->json([
            'message' => 'Berhasil',
            'balance' => $card->card_balance
        ], 200);
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Update saldo kartu
            $card = Card::findOrFail($request->card_id);
            $card->card_balance += $request->amount;
            $card->save();

            // Catat transaksi topup
            DB::table('topups')->insert([
                'card_id' => $request->card_id,
                'terminal_id' => auth()->id(),
                'amount' => $request->amount,
                'topup_date_time' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Top up berhasil',
                'new_balance' => $card->card_balance
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Terjadi kesalahan saat top up'], 500);
        }
    }

    public function payTicket(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:cards,id',
            'schedule_id' => 'required|exists:schedules,id',
            'amount_paid' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $card = Card::findOrFail($request->card_id);
            
            // Cek saldo mencukupi
            if ($card->card_balance < $request->amount_paid) {
                return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
            }

            // Kurangi saldo kartu
            $card->card_balance -= $request->amount_paid;
            $card->save();

            // Catat transaksi pembayaran
            DB::table('payments')->insert([
                'card_id' => $request->card_id,
                'terminal_id' => auth()->id(),
                'schedule_id' => $request->schedule_id,
                'amount_paid' => $request->amount_paid,
                'payment_date_time' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pembayaran berhasil',
                'remaining_balance' => $card->card_balance
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Terjadi kesalahan saat pembayaran'], 500);
        }
    }

    public function transactionHistory($card_id)
    {
        // Ambil data pembayaran
        $payments = DB::table('payments')
            ->where('card_id', $card_id)
            ->get();

        // Ambil data topup
        $topups = DB::table('topups')
            ->where('card_id', $card_id)
            ->get();

        return response()->json([
            'message' => 'Berhasil',
            'transactions' => [
                'payments' => $payments,
                'topups' => $topups
            ]
        ], 200);
    }
}