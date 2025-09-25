<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\User;
use App\Models\Transaction;

class TransactionController extends Controller
{
  public function index(Request $request)
  {
    $user = $request->user();

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
}
