<?php

namespace Modules\Ichat\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Ichat\Events\Handlers\RegisterIchatSidebar;

class IchatServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIchatSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('messages', array_dot(trans('ichat::messages')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('ichat', 'permissions');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Ichat\Repositories\MessageRepository',
            function () {
                $repository = new \Modules\Ichat\Repositories\Eloquent\EloquentMessageRepository(new \Modules\Ichat\Entities\Message());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Ichat\Repositories\Cache\CacheMessageDecorator($repository);
            }
        );
// add bindings

    }
}
