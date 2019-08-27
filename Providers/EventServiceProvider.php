<?php

namespace Modules\Ichat\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// Events And Handlers
use Modules\Ichat\Events\MessageWasCreated;
use Modules\Ichat\Events\Handlers\MessageWasCreatedListener;

class EventServiceProvider extends ServiceProvider
{
  protected $listen = [
    MessageWasCreated::class => [
      MessageWasCreatedListener::class,
    ],
  ];
}
