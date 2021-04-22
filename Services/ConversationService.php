<?php


namespace Modules\Ichat\Services;


use Modules\Ichat\Repositories\ConversationRepository;

class ConversationService
{

    public function __construct(ConversationRepository $conversation)
    {
        $this->conversation = $conversation;
    }

    public function create($entity, $users){
        $entityNamespace = get_class($entity);
        $entityPath = explode('\\', $entityNamespace);
        $entityName = end($entityPath);
        $moduleName = strtolower($entityPath[1]);
        if(setting("{$moduleName}::enableChat") === '1') {
            $conversationData = [
               'users' => $users,
               'private' => 1,
               'entity_id' => $entity->id,
               'entity_type' => $entityNamespace,
            ];
            $this->conversation->create($conversationData);
        }
    }
}
