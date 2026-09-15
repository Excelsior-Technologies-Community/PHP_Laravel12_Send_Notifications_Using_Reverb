<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
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
        $this->notification = $notification->load(
            'post',
            'user'
        );
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'notification.'.$this->notification->user_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'created';
    }

    public function broadcastWith(): array
    {
        $unreadCount = Notification::where(
            'user_id',
            $this->notification->user_id
        )
            ->where('is_read', false)
            ->count();

        $data = [
            'id' => $this->notification->id,
            'post_id' => $this->notification->post_id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'is_read' => false,
            'created_at' => $this->notification->created_at
                ? $this->notification->created_at->format('d M Y, h:i A')
                : now()->format('d M Y, h:i A'),
            'unread_count' => $unreadCount,
            'type' => 'notification_created',
        ];

        if ($this->notification->post) {
            $data['post'] = [
                'id' => $this->notification->post->id,
                'title' => $this->notification->post->title,
                'category' => $this->notification->post->category,
                'image' => $this->notification->post->image,
            ];
        }

        if ($this->notification->user) {
            $data['user_name'] = $this->notification->user->name ?? 'Unknown';
        }

        return $data;
    }
}
