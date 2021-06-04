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

    //Get users to notify message
    $usersToNotifyId = $message->conversation->conversationUsers->whereNotIn('user_id', [Auth::user()->id])
      ->pluck('user_id')->toArray();

    //Manage conversation users info
    $this->manageConversationUsersInfo($message, $usersToNotifyId);

    //Notify conversation users
    $this->notifyConversationUsers($message, $usersToNotifyId);
  }

  //Manage conversation users infor
  public function manageConversationUsersInfo($message, $usersToNotifyId)
  {
    //Update last message info to user who send message
    ConversationUser::where('conversation_id', $message->conversation_id)
      ->where('user_id', $message->user_id)->update(['last_message_readed' => $message->id, 'unread_messages_count' => 0]);

    //Update last message info to conversation users
    foreach ($usersToNotifyId as $userId) {
      ConversationUser::where('conversation_id', $message->conversation_id)
        ->where('user_id', $userId)->update(['unread_messages_count' => \DB::raw('(
            SELECT COUNT(*) FROM ichat__messages
            WHERE ichat__messages.conversation_id = ichat__conversation_user.conversation_id
            AND ichat__messages.id > ichat__conversation_user.last_message_readed 
        )')]);
    }
  }

  //Notify to conversations users
  public function notifyConversationUsers($message, $usersToNotifyId)
  {
    //Send notification
    $this->inotification->to(['broadcast' => $usersToNotifyId])->push([
      "title" => "New message",
      "message" => "You have a new message!",
      "link" => url(''),
      "isAction" => true,
      "frontEvent" => [
        "name" => "inotification.chat.message",
        "data" => new MessageTransformer($message)
      ],
      "setting" => ["saveInDatabase" => 1]
    ]);
  }
}
