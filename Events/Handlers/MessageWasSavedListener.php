<?php

namespace Modules\Ichat\Events\Handlers;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Modules\Ichat\Entities\ConversationUser;
use Modules\Notification\Services\Inotification;
use Modules\Ichat\Transformers\MessageTransformer;

class MessageWasSavedListener
{
  private $conversationUser;
  private $inotification;

  public function __construct(ConversationUser $conversationUser, Inotification $inotification)
  {
    $this->conversationUser = $conversationUser;
    $this->inotification = $inotification;
  }

  /**
   * Handle the event.
   *
   * @param object $event
   * @return void
   */
  public function handle($event)
  {
    //Get message
    $message = $event->message;
    //update last message read
    ConversationUser::where('conversation_id', $message->conversation_id)
      ->where('user_id', $message->user_id)->update(['last_message_readed' => $message->id]);
    //Get users to notify message
    $conversationUsers = $message->conversation->conversationUsers;
    $usersId = $conversationUsers->whereNotIn('user_id', [Auth::user()->id])->pluck('user_id')->toArray();
    //Send notification
    $this->inotification->to(['broadcast' => $usersId])->push([
      "title" => "New message",
      "message" => "You have a new message!",
      "link" => url(''),
      "frontEvent" => [
        "name" => "inotification.chat.message",
        "data" => new MessageTransformer($message)
      ],
      "setting" => ["saveInDatabase" => 1]
    ]);
  }
}
