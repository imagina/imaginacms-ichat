<?php

namespace Modules\Ichat\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Media\Support\Traits\MediaRelation;
use Modules\Core\Support\Traits\AuditTrait;
use Modules\Ichat\Entities\Status;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Str;
use Modules\Itenant\Entities\Organization;

class Message extends Model
{
    protected $table = 'ichat__messages';

    use MediaRelation, AuditTrait, belongsToTenant;

  protected $fillable = [
    'type',
    'body',
    'attached',
    'conversation_id',
    'user_id',
    'reply_to_id',
    'created_at',
    'options',
    'status',
    'external_id'
  ];

    protected $casts = [
        'options' => 'array',
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

    public function replyTo()
    {
        return $this->hasOne(Message::class, 'id', 'reply_to_id');
    }

    public function organization()
    {
      return $this->belongsTo(Organization::class);
    }

  /**
   * @return mixed
   */
  public function getAttachmentAttribute()
  {

    if(!empty($this->attached)){
      $thumbnail = $this->files()->where('zone', 'attachment')->first();

      $tenancyMode = config("tenancy.mode", null);
      $path = \URL::route('ichat.message.attachment', ["conversationId" => $this->conversation_id, "messageId" => $this->id, "attachmentId" => $this->attached]);

      if (!empty($tenancyMode) && $tenancyMode == "singleDatabase" && !is_null($this->organization_id)) {
        $path = tenant_route(
          Str::remove('https://', $this->organization->url),
          'ichat.message.attachment',
          ["conversationId" => $this->conversation_id, "messageId" => $this->id, "attachmentId" => $this->attached]
        );
      }

      return [
        'mimetype' => $thumbnail->mimetype ?? '',
        'path' => $path,
        'extension' => $thumbnail->extension ?? '',
        'filename' => $thumbnail->filename ?? '',
        'filesize'=>$thumbnail->filesize ?? ''
      ];
    }
    else
      return null;

  }

  public function getStatusNameAttribute()
  {
    $status = new Status();
    return $status->get($this->status);
  }
}
