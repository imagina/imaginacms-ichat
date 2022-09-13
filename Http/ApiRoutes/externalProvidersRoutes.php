<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/provider/conversations'], function (Router $router) {
  $router->post('/', [
    'as' => 'api.ichat.external.message.create',
    'uses' => 'ProviderConversationApiController@create',
    'middleware' => ['auth:api']
  ]);
  $router->get('/file/{fileId}', [
    'as' => 'api.ichat.external.file.get',
    'uses' => 'ProviderConversationApiController@getFile',
    //'middleware' => ['auth:api']
  ]);
});
