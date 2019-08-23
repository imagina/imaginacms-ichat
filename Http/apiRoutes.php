<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/ichat/v1'], function (Router $router) {

  // Messages
  require ('ApiRoutes/messageRoutes.php');

  // Conversation
  require ('ApiRoutes/conversationRoutes.php');

  // Conversation User
  require ('ApiRoutes/conversationUserRoutes.php');

  // User With Conversations
  require ('ApiRoutes/userRoutes.php');
});
