<?php

namespace App\Events;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneyTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $sender;
    public $receiver;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction, User $sender, User $receiver)
    {
      $this->transaction = $transaction;
      $this->sender = $sender;
      $this->receiver = $receiver;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
      \Log::info('Broadcasting MoneyTransferred event', [
        'sender_id' => $this->sender->id,
        'receiver_id' => $this->receiver->id,
        'sender' => $this->sender,
        'receiver' => $this->receiver,
        'channels' => [
          'user.' . $this->sender->id,
          'user.' . $this->receiver->id
        ]
      ]);

      return [
        new PrivateChannel('user.' . $this->sender->id),
        new PrivateChannel('user.' . $this->receiver->id),
      ];
    }

    public function broadcastWith()
    {
      return [
        'transaction' => $this->transaction->toArray(),
        'sender' => [
          'id' => $this->sender->id,
          'balance' => $this->sender->balance,
          'name' => $this->sender->name
        ],
        'receiver' => [
          'id' => $this->receiver->id,
          'balance' => $this->receiver->balance,
          'name' => $this->receiver->name
        ],
      ];
    }

    public function broadcastAs()
    {
      return 'MoneyTransferred';
    }
}
