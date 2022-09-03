<?php

namespace Modules\Ichat\Entities;

use Modules\Core\Icrud\Entities\CrudModel;

class Provider extends CrudModel
{

  protected $table = 'ichat__providers';
  public $transformer = 'Modules\Ichat\Transformers\ProviderTransformer';
  public $requestValidation = [
    'create' => 'Modules\Ichat\Http\Requests\CreateProviderRequest',
    'update' => 'Modules\Ichat\Http\Requests\UpdateProviderRequest',
  ];
  //Instance external/internal events to dispatch with extraData
  public $dispatchesEventsWithBindings = [
    //eg. ['path' => 'path/module/event', 'extraData' => [/*...optional*/]]
    'created' => [],
    'creating' => [],
    'updated' => [],
    'updating' => [],
    'deleting' => [],
    'deleted' => []
  ];
  protected $fillable = [
    "name",
    "end_point",
    "token",
  ];
}
