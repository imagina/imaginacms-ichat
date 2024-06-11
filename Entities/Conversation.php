<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Ichat\Presenters\ConversationPresenter;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Isite\Entities\Organization;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

use Modules\User\Entities\Sentinel\User;
use Modules\Notification\Traits\IsNotificable;

class Conversation extends Model
{
    use PresentableTrait, AuditTrait, BelongsToTenant, IsNotificable;

    //protected $presenter = ConversationPresenter::class;

    protected $table = 'ichat__conversations';

  protected $fillable = [
    'private',
    'status',
    'entity_type',
    'entity_id',
    'provider_type',
    'provider_id',
    'organization_id'
  ];

  protected $with = ['users.roles',"users","lastMessage","conversationUsers","organization.files"];

    public function entity()
    {
        return $this->belongsTo($this->entity_type, 'entity_id');
    }

    public function messages()
    {
        return $this->hasMany('Modules\Ichat\Entities\Message');
    }

    public function lastMessage()
    {
        return
            $this->hasOne('Modules\Ichat\Entities\Message')->orderBy('created_at', 'desc');
    }

    public function users()
    {
        $entityPath = 'Modules\\User\\Entities\\'.config('asgard.user.config.driver').'\\User';

        return $this->belongsToMany($entityPath, 'ichat__conversation_user')->withTimestamps();
    }

    public function conversationUsers()
    {
        return $this->hasMany('Modules\Ichat\Entities\ConversationUser');
    }

  public function organization()
  {
    return $this->belongsTo(Organization::class);
  }

  public function createdByUser(){
    return $this->belongsTo(User::class,'created_by');
  }


  /**
   * Make Notificable Params | to Trait
   * @param $event (created|updated|deleted)
   */
  public function isNotificableParams($event)
  {

    //Get Emails and Broadcast
    $conversationService = app("Modules\Ichat\Services\ConversationService");
    $result = $conversationService->getEmailsAndBroadcast($this);

    //Validation Event Created
    if($event=="created"){
      //Extra Validation
      if(empty($result['email']) && $result['broadcast']){
        return null;
      }
    }

    return [
      'created' => [
        "title" => trans("ichat::common.conversation.created.title"),
        "message" =>  trans("ichat::common.conversation.created.message",['user' => $result['createdByUser']]),
        "email" => $result['email'],
        "broadcast" => $result['broadcast']
      ],
    ];

  }
}