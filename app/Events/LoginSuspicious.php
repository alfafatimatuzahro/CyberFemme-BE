<?php

namespace App\Events;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginSuspicious implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public LoginLog $loginLog,
        public User $user,
        public int $adminId
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin.' . $this->adminId)];
    }

    public function broadcastAs(): string
    {
        return 'login.suspicious';
    }

    public function broadcastWith(): array
    {
        return [
            'log_id' => $this->loginLog->id,
            'user' => $this->user->username,
            'lokasi' => $this->loginLog->lokasi,
            'ip' => $this->loginLog->ip_address,
            'waktu' => $this->loginLog->created_at->toISOString(),
        ];
    }
}
