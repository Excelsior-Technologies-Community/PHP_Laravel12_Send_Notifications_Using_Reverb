<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification->load('post', 'user');
    }

    /**
     * Broadcast notification to the posts channel.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('posts');
    }

    /**
     * Event name.
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * Broadcast data.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,

            'post_id' => $this->notification->post_id,

            'title' => $this->notification->title,

            'message' => $this->notification->message,

            'is_read' => $this->notification->is_read,

            'created_at' => $this->notification->created_at
                ? $this->notification->created_at->format('d M Y, h:i A')
                : now()->format('d M Y, h:i A'),

            'unread_count' => Notification::where('user_id', $this->notification->user_id)
                ->where('is_read', false)
                ->count(),
        ];
    }
}