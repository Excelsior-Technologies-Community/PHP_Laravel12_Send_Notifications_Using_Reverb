<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreate implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, SerializesModels;

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    public function broadcastAs(): string
    {
        return 'create';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => "[{$this->post->created_at}] New Post Received with title '{$this->post->title}'."
        ];
    }
}
