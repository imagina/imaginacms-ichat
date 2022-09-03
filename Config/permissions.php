<?php

return [
  'ichat.conversations' => [
    'manage' => 'ichat::messages.manage resource',
    'index' => 'ichat::messages.list resource',
    'index-all' => 'ichat::messages.index-all resource',
    'create' => 'ichat::messages.create resource',
    'edit' => 'ichat::messages.edit resource',
    'destroy' => 'ichat::messages.destroy resource',
  ],
    'ichat.messages' => [
        'manage' => 'ichat::messages.manage resource',
        'index' => 'ichat::messages.list resource',
        'index-all' => 'ichat::messages.index-all resource',
        'create' => 'ichat::messages.create resource',
        'edit' => 'ichat::messages.edit resource',
        'destroy' => 'ichat::messages.destroy resource',
    ],
    'ichat.providers' => [
        'manage' => 'ichat::providers.manage resource',
        'index' => 'ichat::providers.list resource',
        'create' => 'ichat::providers.create resource',
        'edit' => 'ichat::providers.edit resource',
        'destroy' => 'ichat::providers.destroy resource',
        'restore' => 'ichat::providers.restore resource',
    ],
// append


];
