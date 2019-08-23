<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/conversation-user'], function (Router $router) {

  $router->post('/', [
    'as' => 'api.ichat.conversation-user.create',
    'uses' => 'ConversationUserApiController@create',
    'middleware' => ['auth:api']
  ]);
  $router->get('/', [
    'as' => 'api.ichat.conversation-user.index',
    'uses' => 'ConversationUserApiController@index',
    'middleware' => ['auth:api']
  ]);
  $router->get('/{criteria}', [
    'as' => 'api.ichat.conversation-user.show',
    'uses' => 'ConversationUserApiController@show',
    'middleware' => ['auth:api']
  ]);
  $router->put('/{criteria}', [
    'as' => 'api.ichat.conversation-user.update',
    'uses' => 'ConversationUserApiController@update',
    'middleware' => ['auth:api']
  ]);
  $router->delete('/{criteria}', [
    'as' => 'api.ichat.conversation-user.delete',
    'uses' => 'ConversationUserApiController@delete',
    'middleware' => ['auth:api']
  ]);
});
