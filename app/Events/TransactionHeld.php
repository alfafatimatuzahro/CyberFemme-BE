<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionHeld implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Transaction $transaction) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin.' . $this->transaction->admin_id)];
    }

    public function broadcastAs(): string
    {
        return 'transaction.held';
    }

    public function broadcastWith(): array
    {
        return [
            'invoice_id' => $this->transaction->invoice_id,
            'total' => $this->transaction->total,
            'reason' => $this->transaction->fraud_reason,
            'kasir' => $this->transaction->kasir->username,
        ];
    }
}
