<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreate implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, SerializesModels;

    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post->load('user');
    }

    /**
     * Broadcast on public posts channel.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    /**
     * Broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'create';
    }

    /**
     * Data sent to connected browsers.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->post->id,

            'title' => $this->post->title,

            'body' => $this->post->body,

            'user_id' => $this->post->user_id,

            'user_name' => $this->post->user?->name ?? 'Unknown User',

            'created_at' => $this->post->created_at
                ? $this->post->created_at->format('d M Y, h:i A')
                : now()->format('d M Y, h:i A'),

            'message' => "New post received: {$this->post->title}",

            'type' => 'post_created',
        ];
    }
}