<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use Modules\Ichat\Presenters\ConversationPresenter;

class Conversation extends Model
{
  use PresentableTrait;

  protected $presenter = ConversationPresenter::class;

  protected $table = 'ichat__conversations';

  protected $fillable = [
    'private',
  ];

  public function messages()
  {
    return $this->hasMany('Modules\Ichat\Entities\Message');
  }

  public function users()
  {
    $entityPath = "Modules\\User\\Entities\\" . config('asgard.user.config.driver') . "\\User";
    return $this->belongsToMany($entityPath, 'ichat__conversation_user')->withTimestamps();
  }

  public function conversationUsers()
  {
    return $this->hasMany('Modules\Ichat\Entities\ConversationUser');
  }
}
