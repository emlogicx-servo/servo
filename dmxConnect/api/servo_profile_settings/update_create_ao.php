<?php
require('../../../dmxConnectLib/dmxConnect.php');


$app = new \lib\App();

$app->define(<<<'JSON'
{
  "meta": {
    "$_POST": [
      {
        "type": "number",
        "name": "profile_settings_id"
      },
      {
        "type": "text",
        "name": "create_ao"
      }
    ]
  },
  "exec": {
    "steps": {
      "name": "update_create_ao",
      "module": "dbupdater",
      "action": "update",
      "options": {
        "connection": "servodb",
        "sql": {
          "type": "update",
          "values": [
            {
              "table": "servo_profile_settings",
              "column": "profile_settings_id",
              "type": "number",
              "value": "{{$_POST.profile_settings_id}}"
            },
            {
              "table": "servo_profile_settings",
              "column": "create_ao",
              "type": "text",
              "value": "{{$_POST.create_ao}}"
            }
          ],
          "table": "servo_profile_settings",
          "wheres": {
            "condition": "AND",
            "rules": [
              {
                "id": "profile_settings_id",
                "type": "double",
                "operator": "equal",
                "value": "{{$_POST.profile_settings_id}}",
                "data": {
                  "column": "profile_settings_id"
                },
                "operation": "="
              }
            ]
          },
          "returning": "profile_settings_id",
          "query": "UPDATE servo_profile_settings\nSET profile_settings_id = :P1 /* {{$_POST.profile_settings_id}} */, create_ao = :P2 /* {{$_POST.create_ao}} */\nWHERE profile_settings_id = :P3 /* {{$_POST.profile_settings_id}} */",
          "params": [
            {
              "name": ":P1",
              "type": "expression",
              "value": "{{$_POST.profile_settings_id}}"
            },
            {
              "name": ":P2",
              "type": "expression",
              "value": "{{$_POST.create_ao}}"
            },
            {
              "operator": "equal",
              "type": "expression",
              "name": ":P3",
              "value": "{{$_POST.profile_settings_id}}"
            }
          ]
        }
      },
      "meta": [
        {
          "name": "affected",
          "type": "number"
        }
      ],
      "output": true
    }
  }
}
JSON
);
?>