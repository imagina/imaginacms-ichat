<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/provider/messages'], function (Router $router) {
  $router->post('/', [
    'as' => 'api.ichat.external.message.create',
    'uses' => 'ProviderMessagesApiController@create',
    'middleware' => ['auth:api']
  ]);
});
