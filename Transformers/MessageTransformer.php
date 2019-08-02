<?php

namespace Modules\Ichat\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Iprofile\Transformers\UserTransformer;

class MessageTransformer extends Resource
{
  public function toArray($request)
  {
    $data = [
      'id' => $this->id,
      'message' => $this->when($this->message, $this->message),
      'senderId' => $this->when($this->sender_id, $this->sender_id),
      'receiverId' => $this->when($this->receiver_id, $this->receiver_id),
      'attached' => $this->when($this->attached, $this->attached),
      'sender' => new UserTransformer ($this->whenLoaded('sender')),
      'receiver' => new UserTransformer ($this->whenLoaded('receiver')),
    ];
    return $data;
  }
}
