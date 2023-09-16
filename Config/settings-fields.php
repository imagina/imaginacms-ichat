<?php

return [
  'externalRoles' => [
    'name' => 'ichat::externalRoles',
    'value' => null,
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
];
