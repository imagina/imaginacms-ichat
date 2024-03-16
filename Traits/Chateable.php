<?php

namespace Modules\Ichat\Traits;

use Illuminate\Support\Str;

trait Chateable
{
  public function createConversation($params = null)
  {
    $user = \Auth::user();
    $conversationRepository = app('Modules\Ichat\Repositories\ConversationRepository');
    $data = [
      'private' => $params['private'] ?? false,
      'provider_type' => $params['provider_type'] ?? null,
      'provider_id' => $params['provider_id'] ?? null,
      'entity_id' => $params['entity_id'] ?? $this->id ?? null,
      'entity_type' => $params['entity_type'] ?? $this->entity ?? null,
      'users' => $params['users'] ?? [$user->id] ?? []
    ];
    return $conversationRepository->create($data);
  }

  public function conversation($params = null)
  {
    return $this->morphOne('Modules\Ichat\Entities\Conversation', 'entity');
  }
}