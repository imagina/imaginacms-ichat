<?php


namespace Modules\Ichat\Services;


use Modules\Ichat\Repositories\ConversationRepository;

class ConversationService
{

    public function __construct(ConversationRepository $conversation)
    {
        $this->conversation = $conversation;
    }

    public function create($data){
        $this->conversation->create($data);
    }
}
