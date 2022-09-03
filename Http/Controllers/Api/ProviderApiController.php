<?php

namespace Modules\Ichat\Http\Controllers\Api;

use Modules\Core\Icrud\Controllers\BaseCrudController;
//Model
use Modules\Ichat\Entities\Provider;
use Modules\Ichat\Repositories\ProviderRepository;

class ProviderApiController extends BaseCrudController
{
  public $model;
  public $modelRepository;

  public function __construct(Provider $model, ProviderRepository $modelRepository)
  {
    $this->model = $model;
    $this->modelRepository = $modelRepository;
  }
}
