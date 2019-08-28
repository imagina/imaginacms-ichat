<?php

namespace Modules\Ichat\Events\Handlers;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\Ichat\Entities\Message;
use Modules\Ichat\Entities\ConversationUser;

class MessageWasCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
      $message = Message::find($event->message->id);
      $conversationUsers = $message->conversation->conversationUsers->pluck('id');
      $conversationUsersUpdated =  ConversationUser::whereIn('id', $conversationUsers)->where(function ($query) use ($message){
        $query->where('user_id', '!=',$message->user_id)
              ->where('last_message_readed', null);
      })->update([
        'last_message_readed' => $message->id,
      ]);
    }
}
