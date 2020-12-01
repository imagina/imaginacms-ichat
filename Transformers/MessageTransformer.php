<?php

namespace Modules\Ichat\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Iprofile\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;

class MessageTransformer extends JsonResource
{
  public function toArray($request)
  {
    $this->user;
    $data = [
      'id' => $this->id,
      'type' => $this->when( $this->type, $this->type ),
      'body' => $this->when( $this->body, $this->body ),
      'attached' => $this->when( $this->attached, $this->attached ),
      'conversationId' => $this->when( $this->conversation_id, $this->conversation_id ),
      'userId' => $this->when( $this->user_id, $this->user_id ),
      'user' => new UserTransformer ( $this->whenLoaded('user') ),
      'conversation' => new ConversationTransformer ( $this->whenLoaded('conversation') ),
      'createdAt' => $this->when( $this->created_at, $this->created_at ),
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
