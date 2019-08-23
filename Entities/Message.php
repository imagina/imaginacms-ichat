<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  protected $table = 'ichat__messages';

  protected $fillable = [
    'type',
    'body',
    'attached',
    'conversation_id',
    'user_id',
    'is_seen',
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

  public function reads()
  {
    return $this->hasMany('Modules\Ichat\Entities\Read');
  }
}
