<?php

namespace Modules\Ichat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ConversationUserWasUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id, $message)
    {
      $this->user_id = $user_id;
      $this->message = $message;
    }

    public function broadcastAs()
    {
        return "conversationsUserUpdated$this->user_id";
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message
        ];
    }


  /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
      return new Channel('global');
    }
}
