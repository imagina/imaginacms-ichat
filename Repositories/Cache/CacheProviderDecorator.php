<?php

namespace Modules\Ichat\Repositories\Cache;

use Modules\Ichat\Repositories\ProviderRepository;
use Modules\Core\Icrud\Repositories\Cache\BaseCacheCrudDecorator;

class CacheProviderDecorator extends BaseCacheCrudDecorator implements ProviderRepository
{
    public function __construct(ProviderRepository $provider)
    {
        parent::__construct();
        $this->entityName = 'ichat.providers';
        $this->repository = $provider;
    }
}
