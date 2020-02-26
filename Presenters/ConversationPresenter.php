<?php

namespace Modules\Ichat\Presenters;
use Laracasts\Presenter\Presenter;
use Illuminate\Support\Facades\Auth;

class ConversationPresenter extends Presenter
{

  public function lastMessageReaded()
  {
    $conversationUsers = $this->conversationUsers;
    if (count($conversationUsers) > 0){
      return $conversationUsers->where('user_id', Auth::id())->first()->last_message_readed;
    }
    return null;
  }

}
