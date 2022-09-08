<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/provider/conversations'], function (Router $router) {
  $router->post('/', [
    'as' => 'api.ichat.external.message.create',
    'uses' => 'ProviderConversationApiController@create',
    'middleware' => ['auth:api']
  ]);
});
