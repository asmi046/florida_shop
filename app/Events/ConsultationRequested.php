<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ConsultationRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $name;
    public string $phone;
    public ?string $comment;
    public ?Product $product;

    public function __construct(string $name, string $phone, ?string $comment = null, ?Product $product = null)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->comment = $comment;
        $this->product = $product;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
