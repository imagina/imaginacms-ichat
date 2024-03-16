<?php

namespace Modules\Ichat\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Ichat\Events\Handlers\RegisterIchatSidebar;
use Illuminate\Support\Arr;

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
     */
    public function register()
    {
        $this->registerBindings();
        //$this->app['events']->listen(BuildingSidebar::class, RegisterIchatSidebar::class);

        /*$this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('messages', Arr::dot(trans('ichat::messages')));
            // append translations

        });*/
    }

  public function boot()
  {
    $this->publishConfig('ichat', 'permissions');
    $this->publishConfig('ichat', 'config');
    $this->mergeConfigFrom($this->getModuleConfigFilePath('ichat', 'cmsPages'), "asgard.ichat.cmsPages");
    $this->mergeConfigFrom($this->getModuleConfigFilePath('ichat', 'cmsSidebar'), "asgard.ichat.cmsSidebar");
    $this->mergeConfigFrom($this->getModuleConfigFilePath('ichat', 'settings'), "asgard.ichat.settings");
    $this->mergeConfigFrom($this->getModuleConfigFilePath('ichat', 'settings-fields'), "asgard.ichat.settings-fields");

        //$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [];
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
        $this->app->bind(
            'Modules\Ichat\Repositories\ConversationRepository',
            function () {
                $repository = new \Modules\Ichat\Repositories\Eloquent\EloquentConversationRepository(new \Modules\Ichat\Entities\Conversation());
                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Ichat\Repositories\Cache\CacheConversationDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Ichat\Repositories\ConversationUserRepository',
            function () {
                $repository = new \Modules\Ichat\Repositories\Eloquent\EloquentConversationUserRepository(new \Modules\Ichat\Entities\ConversationUser());
                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Ichat\Repositories\Cache\CacheConversationUserDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Ichat\Repositories\UserRepository',
            function () {
                $repository = new \Modules\Ichat\Repositories\Eloquent\EloquentUserRepository(new \Modules\User\Entities\Sentinel\User());
                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Ichat\Repositories\Cache\CacheUserDecorator($repository);
            }
        );
        // add bindings
    }
}
