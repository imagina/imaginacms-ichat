<?php

namespace Modules\Ichat\Traits;

use Illuminate\Support\Str;

trait Chateable
{
  public function createConversation($params = null)
  {
    $conversationRepository = app('Modules\Ichat\Repositories\ConversationRepository');
    $data = [
      'public' => $params['public'] ?? false,
      'provider_type' => $params['provider_type'] ?? null,
      'provider_id' => $params['provider_id'] ?? null,
      'entity_id' => $params['entity_id'] ?? $this->id ?? null,
      'entity_type' => $params['entity_type'] ?? $this->entity ?? null,
      'users' => $params['users'] ?? $this->user ?? null
    ];
    $conversationRepository->create($data);
  }

  public function chat($params = null)
  {
    return $this->morphOne('Modules\Ichat\Entities\Conversation', 'entity');
  }
}