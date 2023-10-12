<?php
//	This file declares a SavedSearch and SearchDisplay for managing Training Records for contacts.
return [
  [
    'name' => 'SavedSearch_Training_List',
    'entity' => 'SavedSearch',
    'update' => 'unmodified',
    'cleanup' => 'unused',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Training_List',
        'label' => 'Training List',
        'form_values' => null,
        'search_custom_id' => null,
        'api_entity' => 'TrainingRecord',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'type_id',
            'TrainingRecord_TrainingType_type_id_01.name',
            'label',
            'credits_awarded',
            'credits',
            'entry_date'
          ],
          'orderBy' => [],
          'where' => [],
          'groupBy' => [],
          'join' => [
            [
              'TrainingType AS TrainingRecord_TrainingType_type_id_01',
              'INNER',
              [
                'type_id',
                '=',
                'TrainingRecord_TrainingType_type_id_01.id'
              ]
            ]
          ],
          'having' => []
        ],
        'expires_date' => null,
        'description' => null,
        'mapping_id' => null
      ],
      'match' => [ 'name' ],
    ]
  ],
  [
    'name' => 'SavedSearch_Training_List_SearchDisplay_Training_Records',
    'entity' => 'SearchDisplay',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Training_Records',
        'label' => 'Training Records',
        'saved_search_id.name' => 'Training_List',
        'type' => 'table',
        'settings' => [
          'actions' => true,
          'limit' => 50,
          'classes' => [
            'table',
            'table-striped'
          ],
          'pager' => [
            'show_count' => true,
            'expose_limit' => true
          ],
          'sort' => [],
          'columns' => [
            [
              'type' => 'field',
              'key' => 'id',
              'dataType' => 'Integer',
              'label' => 'ID',
              'sortable' => false
            ],
            [
              'type' => 'field',
              'key' => 'TrainingRecord_TrainingType_type_id_01.name',
              'dataType' => 'String',
              'label' => 'Training Type',
              'sortable' => true
            ],
            [
              'type' => 'field',
              'key' => 'label',
              'dataType' => 'String',
              'label' => 'Training Name',
              'sortable' => true
            ],
            [
              'type' => 'field',
              'key' => 'credits_awarded',
              'dataType' => 'Boolean',
              'label' => 'Award Credits?',
              'sortable' => true
            ],
            [
              'type' => 'field',
              'key' => 'credits',
              'dataType' => 'Integer',
              'label' => 'Credits Awarded',
              'sortable' => true
            ],
            [
              'type' => 'field',
              'key' => 'entry_date',
              'dataType' => 'Date',
              'label' => 'Entry Date',
              'sortable' => true
            ],
            [
              'links' => [
                [
                  'path' => 'civicrm/trainings/record?id=[id]',
                  'icon' => 'fa-pencil',
                  'text' => 'Edit',
                  'style' => 'default',
                  'condition' => [],
                  'entity' => '',
                  'action' => '',
                  'join' => '',
                  'target' => 'crm-popup'
                ],
                [
                  'path' => 'civicrm/trainings/record?id=[id]&action=delete',
                  'icon' => 'fa-trash',
                  'text' => 'Delete',
                  'style' => 'danger',
                  'condition' => [],
                  'entity' => '',
                  'action' => '',
                  'join' => '',
                  'target' => 'crm-popup'
                ]
              ],
              'type' => 'links',
              'alignment' => 'text-right'
            ]
          ]
        ],
        'acl_bypass' => false
      ],
      'match' => [
        'name',
        'saved_search_id'
      ]
    ]
  ],
];