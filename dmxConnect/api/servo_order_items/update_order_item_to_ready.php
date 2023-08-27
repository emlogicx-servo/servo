<?php
require('../../../dmxConnectLib/dmxConnect.php');


$app = new \lib\App();

$app->define(<<<'JSON'
{
  "meta": {
    "$_GET": [
      {
        "type": "text",
        "name": "sort"
      },
      {
        "type": "text",
        "name": "dir"
      }
    ],
    "$_POST": [
      {
        "type": "text",
        "name": "order_item_status"
      },
      {
        "type": "number",
        "name": "order_item_id"
      },
      {
        "type": "datetime",
        "name": "order_time_ready"
      },
      {
        "type": "number",
        "name": "servo_user_user_prepared_id"
      }
    ]
  },
  "exec": {
    "steps": [
      {
        "name": "update",
        "module": "dbupdater",
        "action": "update",
        "options": {
          "connection": "servodb",
          "sql": {
            "type": "update",
            "values": [
              {
                "table": "servo_order_items",
                "column": "order_item_status",
                "type": "text",
                "value": "{{$_POST.order_item_status}}"
              },
              {
                "table": "servo_order_items",
                "column": "order_time_ready",
                "type": "datetime",
                "value": "{{$_POST.order_time_ready}}"
              }
            ],
            "table": "servo_order_items",
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "order_item_id",
                  "type": "double",
                  "operator": "equal",
                  "value": "{{$_POST.order_item_id}}",
                  "data": {
                    "column": "order_item_id"
                  },
                  "operation": "="
                }
              ]
            },
            "query": "UPDATE servo_order_items\nSET order_item_status = :P1 /* {{$_POST.order_item_status}} */, order_time_ready = :P2 /* {{$_POST.order_time_ready}} */\nWHERE order_item_id = :P3 /* {{$_POST.order_item_id}} */",
            "params": [
              {
                "name": ":P1",
                "type": "expression",
                "value": "{{$_POST.order_item_status}}"
              },
              {
                "name": ":P2",
                "type": "expression",
                "value": "{{$_POST.order_time_ready}}"
              },
              {
                "operator": "equal",
                "type": "expression",
                "name": ":P3",
                "value": "{{$_POST.order_item_id}}"
              }
            ]
          }
        },
        "meta": [
          {
            "name": "affected",
            "type": "number"
          }
        ]
      },
      {
        "name": "get_group_item_reference",
        "module": "dbconnector",
        "action": "single",
        "options": {
          "connection": "servodb",
          "sql": {
            "type": "select",
            "columns": [
              {
                "table": "servo_order_items",
                "column": "order_item_group_reference"
              }
            ],
            "params": [
              {
                "operator": "equal",
                "type": "expression",
                "name": ":P1",
                "value": "{{$_POST.order_item_id}}",
                "test": ""
              }
            ],
            "table": {
              "name": "servo_order_items"
            },
            "primary": "order_item_id",
            "joins": [],
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "servo_order_items.order_item_id",
                  "field": "servo_order_items.order_item_id",
                  "type": "double",
                  "operator": "equal",
                  "value": "{{$_POST.order_item_id}}",
                  "data": {
                    "table": "servo_order_items",
                    "column": "order_item_id",
                    "type": "number",
                    "columnObj": {
                      "type": "increments",
                      "primary": true,
                      "nullable": false,
                      "name": "order_item_id"
                    }
                  },
                  "operation": "="
                }
              ],
              "conditional": null,
              "valid": true
            },
            "query": "select `order_item_group_reference` from `servo_order_items` where `servo_order_items`.`order_item_id` = ?"
          }
        },
        "output": true,
        "meta": [
          {
            "type": "number",
            "name": "order_item_group_reference"
          }
        ],
        "type": "dbconnector_single",
        "outputType": "object"
      },
      {
        "name": "update_gorup_refernece_items_to_ready",
        "module": "dbupdater",
        "action": "update",
        "options": {
          "connection": "servodb",
          "sql": {
            "type": "update",
            "values": [
              {
                "table": "servo_order_items",
                "column": "order_time_ready",
                "type": "datetime",
                "value": "{{$_POST.order_time_ready}}"
              },
              {
                "table": "servo_order_items",
                "column": "order_item_status",
                "type": "text",
                "value": "Ready"
              },
              {
                "table": "servo_order_items",
                "column": "servo_user_user_prepared_id",
                "type": "number",
                "value": "{{$_POST.servo_user_user_prepared_id}}"
              }
            ],
            "table": "servo_order_items",
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "order_item_group_reference",
                  "field": "order_item_group_reference",
                  "type": "double",
                  "operator": "equal",
                  "value": "{{get_group_item_reference.order_item_group_reference}}",
                  "data": {
                    "column": "order_item_group_reference"
                  },
                  "operation": "="
                }
              ],
              "conditional": null,
              "valid": true
            },
            "returning": "order_item_id",
            "query": "update `servo_order_items` set `order_time_ready` = ?, `order_item_status` = ?, `servo_user_user_prepared_id` = ? where `order_item_group_reference` = ?",
            "params": [
              {
                "name": ":P1",
                "type": "expression",
                "value": "{{$_POST.order_time_ready}}",
                "test": ""
              },
              {
                "name": ":P2",
                "type": "expression",
                "value": "{{$_POST.servo_user_user_prepared_id}}",
                "test": ""
              },
              {
                "operator": "equal",
                "type": "expression",
                "name": ":P3",
                "value": "{{get_group_item_reference.order_item_group_reference}}",
                "test": ""
              }
            ]
          }
        },
        "meta": [
          {
            "name": "affected",
            "type": "number"
          }
        ]
      }
    ]
  }
}
JSON
);
?>