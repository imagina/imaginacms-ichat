<?php

namespace Modules\Ichat\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Iprofile\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class ConversationTransformer extends Resource
{
  public function toArray($request)
  {
    $data = [
      'id' => $this->id,
      'private' => $this->private ? true : false,
      "lastMessageReaded" => $this->present()->lastMessageReaded,
      'users' => UserTransformer::collection( $this->whenLoaded('users') ),
      'messages' => MessageTransformer::collection($this->whenLoaded('messages')),
      'conversationUsers' => ConversationUserTransformer::collection($this->whenLoaded('conversationUsers')),
    ];
    return $data;
  }
}
