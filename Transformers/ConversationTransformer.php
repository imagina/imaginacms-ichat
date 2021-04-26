<?php

namespace Modules\Ichat\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Iprofile\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class ConversationTransformer extends JsonResource
{
  public function toArray($request)
  {
    $data = [
      'id' => $this->id,
      'private' => $this->private ? true : false,
      "lastMessageReaded" => $this->present()->lastMessageReaded,
      "unReadMessages" => $this->present()->unReadMessages,
      'users' => UserTransformer::collection($this->whenLoaded('users')),
      'messages' => MessageTransformer::collection($this->whenLoaded('messages')),
      'conversationUsers' => ConversationUserTransformer::collection($this->whenLoaded('conversationUsers')),
    ];

    return $data;
  }
}
