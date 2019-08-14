<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  protected $table = 'ichat__messages';

  protected $fillable = [
    'message',
    'sender_id',
    'receiver_id',
    'attached',
    'read'
  ];

  public function sender()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User", 'sender_id');
  }

  public function receiver()
  {
    $driver = config('asgard.user.config.driver');
    return $this->belongsTo("Modules\\User\\Entities\\{$driver}\\User", 'receiver_id');
  }
}
