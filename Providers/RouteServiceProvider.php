<?php

namespace Modules\Ichat\Providers;

use Modules\Core\Providers\RoutingServiceProvider as CoreRoutingServiceProvider;

class RouteServiceProvider extends CoreRoutingServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     * @var string
     */
    protected $namespace = 'Modules\Ichat\Http\Controllers';

    /**
     * @return string
     */
    protected function getFrontendRoute()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getBackendRoute()
    {
        return false; //__DIR__ . '/../Http/backendRoutes.php';
    }

    /**
     * @return string
     */
    protected function getApiRoute()
    {
      return __DIR__ . '/../Http/apiRoutes.php';
    }
}
