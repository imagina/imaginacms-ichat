<?php

namespace Modules\Ichat\Services;

use Modules\Ichat\Repositories\ConversationRepository;
use Modules\Ichat\Repositories\UserRepository;

class ConversationService
{
    public function __construct(ConversationRepository $conversation)
    {
        $this->conversation = $conversation;
    }

    public function create($data)
    {
        try {
            $conversation = $this->conversation->create($data);

            return $conversation;
        } catch (\Exception $e) {
        }

        return false;
    }

    public function update($conversationId, $data)
    {
        try {
            $this->conversation->updateBy($conversationId, $data);
        } catch (\Exception $e) {
        }
    }

    /**
     * Get emails and broadcast information
     */
    public function getEmailsAndBroadcast($conversation)
    {

      $emailTo = [];
      $broadcastTo = [];
      $createdByUser = $conversation->createdByUser->present()->fullname;

      //Case | User
      if($conversation->entity_type=="Modules\User\Entities\Sentinel\User"){

        $userLogged = \Auth::user();

        //Same User Logged with Conversation User Id
        if($userLogged->id==$conversation->entity_id){
          $usersToNotify = json_decode(setting("ichat::responsableUsers"));
          if(count($usersToNotify)>0){
            //Setting Infor
            $users = UserRepository->getItemsBy(['filter' => ['id' => $usersToNotify]]);
            $emailTo = array_merge($emailTo, $users->pluck('email')->toArray());
            $broadcastTo = $users->pluck('id')->toArray();

          }else{
            //A la Tienda que le estan escribiendo
            if(isset($conversation->organization) && !is_null($conversation->organization)){
              $userOrg = $conversation->organization->users->first();
              array_push($emailTo, $userOrg->email);
              array_push($broadcastTo, $userOrg->id);
            }
          }
        }else{
          //Notificar al Entity Id de Conversation
          array_push($emailTo, $conversation->entity->email);
          array_push($broadcastTo, $conversation->entity_id);
        }

      }

      // Data Notification
      $to["email"] = $emailTo;
      $to["broadcast"] = $broadcastTo;
      $to['createdByUser'] = $createdByUser;

      return $to;
    }

}