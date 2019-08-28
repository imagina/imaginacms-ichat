<?php

namespace Modules\Ichat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageWasCreated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
      $this->message = $message;
    }

  public function broadcastAs()
  {
    return 'conversationUpdate'. $this->message->conversation->id;
  }

  public function broadcastWith()
  {
    return [
      'message' => $this->message
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
