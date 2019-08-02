<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => '/ichat/v1'], function (Router $router) {
  // Messages
  require('ApiRoutes/messageRoutes.php');
});
