<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/ichat/v1'], function (Router $router) {
  // Conversation
  require('ApiRoutes/conversationsRoutes.php');

  // Messages
  require('ApiRoutes/messagesRoutes.php');

  // External provider
  require('ApiRoutes/externalProvidersRoutes.php');
});
