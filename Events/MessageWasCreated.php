<?php

namespace Modules\Ichat\Events;

use Illuminate\Queue\SerializesModels;

class MessageWasCreated
{
  use SerializesModels;

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

   /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
      return [];
    }
}
