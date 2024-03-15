<?php

return [
  'externalRoles' => [
    'name' => 'ichat::externalRoles',
    'value' => [],
    'type' => 'treeSelect',
    'columns' => 'col-12 col-md-6',
    'loadOptions' => [
      'apiRoute' => 'apiRoutes.quser.roles',
      'select' => ['label' => 'name', 'id' => 'id'],
    ],
    'props' => [
      'label' => trans('ichat::common.settings.labelExternalRoles'),
      'clearable' => true,
      'multiple' => true,
      'value-consists-of' => 'BRANCH_PRIORITY',
      'sort-value-by' => 'ORDER_SELECTED'
    ],
  ],
  'responsibleUsers' => [
    'name' => 'ichat::responsableUsers',
    'value' => [],
    'type' => 'select',
    'columns' => 'col-12 col-md-6',
    'help' => [
      'description' => 'ichat::common.settingsHelp.responsibleUsers',
    ],
    'loadOptions' => [
      'apiRoute' => 'apiRoutes.quser.users',
      'select' => ['label' => 'fullName', 'id' => 'id'],
      'filterByQuery' => true
    ],
    'props' => [
      'label' => 'ichat::common.settings.responsibleUsers',
      'multiple' => true,
      'clearable' => true,
    ],
  ],
];
