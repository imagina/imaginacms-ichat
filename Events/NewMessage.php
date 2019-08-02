<?php

namespace Modules\Ichat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewMessage implements ShouldBroadcastNow
{
  use InteractsWithSockets, SerializesModels;

  public $message;
  public $senderId;
  public $receiverId;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct($senderId, $receiverId, $message)
  {
    $this->message = $message;
    $this->senderId = $senderId;
    $this->receiverId = $receiverId;
  }

  public function broadcastWith()
  {
    // This must always be an array. Since it will be parsed with json_encode()
    return [
      "data" => $this->message
    ];
  }

  public function broadcastAs()
  {
    return 'notification'.$this->receiverId.'_'.$this->senderId;
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
