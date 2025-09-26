<?php

namespace App\Http\Controllers\Api;

use App\Events\MoneyTransferred;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $token = $request->bearerToken();
    $sanctumToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);

    if (!$sanctumToken) {
      return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $user = $sanctumToken->tokenable;
    auth()->setUser($user);

    $transactions = Transaction::where('sender_id', $user->id)
      ->orWhere('receiver_id', $user->id)
      ->with(['sender:id,name', 'receiver:id,name'])
      ->orderBy('created_at', 'desc')
      ->paginate(25);

    return response()->json([
      'balance' => $user->balance,
      'transactions' => $transactions,
    ]);
  }

  public function store(StoreTransactionRequest $request)
  {
    $authUser = $request->user();
    $receiverId = (int) $request->input('receiver_id');

    $amount = number_format((float) $request->input('amount'), 2, '.', '');

    $commissionFee = number_format(round($amount * 0.015, 2), 2, '.', '');
    $totalDebit = number_format(round($amount + $commissionFee, 2), 2, '.', '');

    $tx = null;
    $senderAfter = null;
    $receiverAfter = null;

    try {
      DB::beginTransaction();

      $firstId = min($authUser->id, $receiverId);
      $secondId = max($authUser->id, $receiverId);

      $first = User::where('id', $firstId)->lockForUpdate()->first();
      $second = User::where('id', $secondId)->lockForUpdate()->first();

      if (!$first || !$second) {
        DB::rollBack();
        return response()->json(['message' => 'User not found.'], 404);
      }

      if ($first->id === $authUser->id) {
        $sender = $first;
        $receiver = $second;
      } else {
        $sender = $second;
        $receiver = $first;
      }

      if (bccomp($sender->balance, $totalDebit, 2) === -1) {
        DB::rollBack();
        return response()->json(['message' => 'Insufficient balance.'], 422);
      }

      $sender->balance = bcsub($sender->balance, $totalDebit, 2);
      $sender->save();

      $receiver->balance = bcadd($receiver->balance, $amount, 2);
      $receiver->save();

      $tx = Transaction::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'amount' => $amount,
        'commission_fee' => $commissionFee,
        'sender_balance_after' => $sender->balance,
        'receiver_balance_after' => $receiver->balance,
      ]);

      DB::commit();

      broadcast(new MoneyTransferred($tx, $sender, $receiver));

      return response()->json([
        'message' => 'Transfer successful.',
        'transaction' => $tx,
      ], 201);
    } catch (Throwable $e) {
      if (DB::transactionLevel() > 0) {
        DB::rollBack();
      }

      return response()->json([
        'message' => 'Transfer failed.',
        'error' => $e->getMessage(),
      ], 500);
    }
  }
}
