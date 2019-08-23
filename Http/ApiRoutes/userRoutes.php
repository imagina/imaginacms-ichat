<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/users'], function (Router $router) {
  $router->get('/', [
    'as' => 'api.ichat.users.index',
    'uses' => 'UserApiController@index',
    'middleware' => ['auth:api']
  ]);
  $router->get('/{criteria}', [
    'as' => 'api.ichat.users.show',
    'uses' => 'UserApiController@show',
    'middleware' => ['auth:api']
  ]);
});
