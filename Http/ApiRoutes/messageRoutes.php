<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/messages'], function (Router $router) {

  $router->post('/', [
    'as' => 'api.ichat.messages.create',
    'uses' => 'MessageApiController@create',
    //'middleware' => ['auth:api']
  ]);
  $router->get('/', [
    'as' => 'api.ichat.messages.index',
    'uses' => 'MessageApiController@index',
  ]);
  $router->get('/{criteria}', [
    'as' => 'api.ichat.messages.show',
    'uses' => 'MessageApiController@show',
  ]);
  $router->put('/{criteria}', [
    'as' => 'api.ichat.messages.update',
    'uses' => 'MessageApiController@update',
    //'middleware' => ['auth:api']
  ]);
  $router->delete('/{criteria}', [
    'as' => 'api.ichat.messages.delete',
    'uses' => 'MessageApiController@delete',
    //'middleware' => ['auth:api']
  ]);

});
