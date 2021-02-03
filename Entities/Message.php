<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Ichat\Events\MessageWasSaved;

class Message extends Model
{
  protected $table = 'ichat__messages';

  protected $fillable = [
    'type',
    'body',
    'attached',
    'conversation_id',
    'user_id'
  ];

  //Events
  protected $dispatchesEvents = [
    'saved' => MessageWasSaved::class
  ];

  public function conversation()
  {
    return $this->belongsTo('Modules\Ichat\Entities\Conversation');
  }

  public function user()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User", 'user_id');
  }
}
