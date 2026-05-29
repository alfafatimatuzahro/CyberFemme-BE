<?php

namespace App\Events;

use App\Models\Transaction;
use App\Models\SecurityNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FraudDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public SecurityNotification $notification
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.' . $this->transaction->admin_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'fraud.detected';
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'type' => $this->notification->type,
                'judul' => $this->notification->judul,
                'pesan' => $this->notification->pesan,
                'created_at' => $this->notification->created_at->toISOString(),
            ],
            'transaction' => [
                'id' => $this->transaction->id,
                'invoice_id' => $this->transaction->invoice_id,
                'total' => $this->transaction->total,
                'fraud_reason' => $this->transaction->fraud_reason,
                'kasir' => $this->transaction->kasir->username,
            ],
        ];
    }
}
