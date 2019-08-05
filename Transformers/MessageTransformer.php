<?php

namespace Modules\Ichat\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Iprofile\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class MessageTransformer extends Resource
{
  public function toArray($request)
  {
    $data = [
      'id' => $this->id,
      'message' => $this->when($this->message, $this->message),
      'senderId' => $this->when($this->sender_id, $this->sender_id),
      'senderName' => $this->when($this->sender_id, $this->checkUser($this->sender)),
      'receiverName' => $this->when($this->receiver_id, $this->checkUser($this->receiver)),
      'receiverId' => $this->when($this->receiver_id, $this->receiver_id),
      'attached' => $this->when($this->attached, $this->attached),
      'sender' => new UserTransformer ($this->whenLoaded('sender')),
      'receiver' => new UserTransformer ($this->whenLoaded('receiver')),
      'createdAt' => $this->when($this->created_at, $this->created_at),

    ];
    return $data;
  }

  public function checkUser ($user) {
    if (Auth::user()->id == $user->id) {
      return 'me';
    }
    return $user->first_name;
  }
}
