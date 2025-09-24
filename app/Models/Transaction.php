<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
  protected $fillable = [
    'sender_id',
    'receiver_id',
    'amount',
    'commission_fee',
    'sender_balance_after',
    'receiver_balance_after',
  ];

  protected $casts = [
    'amount' => 'decimal:2',
    'commission_fee' => 'decimal:2',
    'sender_balance_after' => 'decimal:2',
    'receiver_balance_after' => 'decimal:2',
  ];

  public function sender()
  {
    return $this->belongsTo(\App\Models\User::class, 'sender_id');
  }

  public function receiver()
  {
    return $this->belongsTo(\App\Models\User::class, 'receiver_id');
  }

}
