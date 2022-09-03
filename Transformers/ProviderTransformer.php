<?php

namespace Modules\Ichat\Transformers;

use Modules\Core\Icrud\Transformers\CrudResource;

class ProviderTransformer extends CrudResource
{
  /**
  * Method to merge values with response
  *
  * @return array
  */
  public function modelAttributes($request)
  {
    return [];
  }
}
