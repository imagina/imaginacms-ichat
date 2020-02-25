<?php

namespace Modules\Ichat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Modules\Ichat\Transformers\MessageTransformer;

class NewMessageInConversation implements ShouldBroadcastNow
{
  use InteractsWithSockets, SerializesModels;

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

  public function broadcastWith()
  {
    return [
      "data" => new MessageTransformer($this->message)
    ];
  }

  public function broadcastAs()
  {
    return 'conversation_'.$this->message['conversation_id'];
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
