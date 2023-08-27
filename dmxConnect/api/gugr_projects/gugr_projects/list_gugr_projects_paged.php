<?php
require('../../../../dmxConnectLib/dmxConnect.php');


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
      },
      {
        "type": "text",
        "name": "gugr_project_filter"
      },
      {
        "type": "text",
        "name": "gugr_project_status"
      },
      {
        "type": "text",
        "name": "offset"
      },
      {
        "type": "text",
        "name": "limit"
      }
    ]
  },
  "exec": {
    "steps": [
      {
        "name": "query_stats",
        "module": "dbupdater",
        "action": "custom",
        "options": {
          "connection": "servodb",
          "sql": {
            "query": "SELECT\n\n(select count(*) from servo_projects where project_status = 'Active') as Active,\n\n(select count(*) from servo_projects where project_status = 'Pending') as Pending,\n\n(select count(*) from servo_projects where project_status = 'Completed') as Completed",
            "params": []
          }
        },
        "output": true,
        "meta": [
          {
            "name": "Active",
            "type": "text"
          },
          {
            "name": "Pending",
            "type": "text"
          },
          {
            "name": "Completed",
            "type": "text"
          }
        ]
      },
      {
        "name": "query_list_gugr_projects",
        "module": "dbconnector",
        "action": "paged",
        "options": {
          "connection": "servodb",
          "sql": {
            "type": "SELECT",
            "columns": [
              {
                "table": "servo_projects",
                "column": "project_id"
              },
              {
                "table": "servo_projects",
                "column": "project_user_created"
              },
              {
                "table": "userConcerned",
                "column": "user_profile"
              },
              {
                "table": "servo_projects",
                "column": "project_status"
              },
              {
                "table": "servo_projects",
                "column": "project_date_created"
              },
              {
                "table": "servo_projects",
                "column": "project_date_due"
              },
              {
                "table": "servo_projects",
                "column": "project_notes"
              },
              {
                "table": "servo_projects",
                "column": "project_code"
              },
              {
                "table": "userConcerned",
                "column": "user_fname",
                "alias": "userConcerned_fname"
              },
              {
                "table": "userConcerned",
                "column": "user_lname",
                "alias": "userConcerned_lname"
              },
              {
                "table": "userConcerned",
                "column": "user_username",
                "alias": "userConcerned_username"
              },
              {
                "table": "servo_user",
                "column": "user_fname"
              },
              {
                "table": "servo_user",
                "column": "user_lname"
              },
              {
                "table": "servo_user",
                "column": "user_username"
              }
            ],
            "table": {
              "name": "servo_projects"
            },
            "joins": [
              {
                "table": "servo_user",
                "column": "*",
                "type": "LEFT",
                "clauses": {
                  "condition": "AND",
                  "rules": [
                    {
                      "table": "servo_user",
                      "column": "user_id",
                      "operator": "equal",
                      "value": {
                        "table": "servo_projects",
                        "column": "project_user_created"
                      },
                      "operation": "="
                    }
                  ]
                },
                "primary": "user_id"
              },
              {
                "table": "servo_user",
                "column": "*",
                "alias": "userConcerned",
                "type": "LEFT",
                "clauses": {
                  "condition": "AND",
                  "rules": [
                    {
                      "table": "userConcerned",
                      "column": "user_id",
                      "operator": "equal",
                      "value": {
                        "table": "servo_projects",
                        "column": "project_user_concerned"
                      },
                      "operation": "="
                    }
                  ]
                },
                "primary": "user_id"
              }
            ],
            "query": "SELECT servo_projects.project_id, servo_projects.project_user_created, userConcerned.user_profile, servo_projects.project_status, servo_projects.project_date_created, servo_projects.project_date_due, servo_projects.project_notes, servo_projects.project_code, userConcerned.user_fname AS userConcerned_fname, userConcerned.user_lname AS userConcerned_lname, userConcerned.user_username AS userConcerned_username, servo_user.user_fname, servo_user.user_lname, servo_user.user_username\nFROM servo_projects\nLEFT JOIN servo_user ON servo_user.user_id = servo_projects.project_user_created LEFT JOIN servo_user AS userConcerned ON userConcerned.user_id = servo_projects.project_user_concerned\nWHERE servo_projects.project_status LIKE :P1 /* {{$_GET.gugr_project_status}} */ AND servo_projects.project_code LIKE :P2 /* {{$_GET.gugr_project_filter}} */\nORDER BY servo_projects.project_status DESC, servo_projects.project_date_created DESC, servo_projects.project_code DESC",
            "params": [
              {
                "operator": "contains",
                "type": "expression",
                "name": ":P1",
                "value": "{{$_GET.gugr_project_status}}",
                "test": "%"
              },
              {
                "operator": "contains",
                "type": "expression",
                "name": ":P2",
                "value": "{{$_GET.gugr_project_filter}}",
                "test": "%"
              }
            ],
            "orders": [
              {
                "table": "servo_projects",
                "column": "project_status",
                "direction": "DESC",
                "recid": 1
              },
              {
                "table": "servo_projects",
                "column": "project_date_created",
                "direction": "DESC",
                "recid": 2
              },
              {
                "table": "servo_projects",
                "column": "project_code",
                "direction": "DESC",
                "recid": 3
              }
            ],
            "primary": "project_id",
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "servo_projects.project_status",
                  "field": "servo_projects.project_status",
                  "type": "string",
                  "operator": "contains",
                  "value": "{{$_GET.gugr_project_status}}",
                  "data": {
                    "table": "servo_projects",
                    "column": "project_status",
                    "type": "text",
                    "columnObj": {
                      "type": "enum",
                      "enumValues": [
                        "Pending",
                        "Active",
                        "Completed",
                        "Suspended",
                        "Cancelled",
                        "Paused"
                      ],
                      "maxLength": 9,
                      "primary": false,
                      "nullable": true,
                      "name": "project_status"
                    }
                  },
                  "operation": "LIKE"
                },
                {
                  "id": "servo_projects.project_code",
                  "field": "servo_projects.project_code",
                  "type": "string",
                  "operator": "contains",
                  "value": "{{$_GET.gugr_project_filter}}",
                  "data": {
                    "table": "servo_projects",
                    "column": "project_code",
                    "type": "text",
                    "columnObj": {
                      "type": "string",
                      "maxLength": 128,
                      "primary": false,
                      "nullable": true,
                      "name": "project_code"
                    }
                  },
                  "operation": "LIKE"
                }
              ],
              "conditional": null,
              "valid": true
            }
          }
        },
        "meta": [
          {
            "name": "offset",
            "type": "number"
          },
          {
            "name": "limit",
            "type": "number"
          },
          {
            "name": "total",
            "type": "number"
          },
          {
            "name": "page",
            "type": "object",
            "sub": [
              {
                "name": "offset",
                "type": "object",
                "sub": [
                  {
                    "name": "first",
                    "type": "number"
                  },
                  {
                    "name": "prev",
                    "type": "number"
                  },
                  {
                    "name": "next",
                    "type": "number"
                  },
                  {
                    "name": "last",
                    "type": "number"
                  }
                ]
              },
              {
                "name": "current",
                "type": "number"
              },
              {
                "name": "total",
                "type": "number"
              }
            ]
          },
          {
            "name": "data",
            "type": "array",
            "sub": [
              {
                "type": "number",
                "name": "project_id"
              },
              {
                "type": "number",
                "name": "project_user_created"
              },
              {
                "type": "text",
                "name": "user_profile"
              },
              {
                "type": "text",
                "name": "project_status"
              },
              {
                "type": "datetime",
                "name": "project_date_created"
              },
              {
                "type": "datetime",
                "name": "project_date_due"
              },
              {
                "type": "text",
                "name": "project_notes"
              },
              {
                "type": "text",
                "name": "project_code"
              },
              {
                "type": "text",
                "name": "userConcerned_fname"
              },
              {
                "type": "text",
                "name": "userConcerned_lname"
              },
              {
                "type": "text",
                "name": "userConcerned_username"
              },
              {
                "type": "text",
                "name": "user_fname"
              },
              {
                "type": "text",
                "name": "user_lname"
              },
              {
                "type": "text",
                "name": "user_username"
              }
            ]
          }
        ],
        "outputType": "object",
        "output": true,
        "type": "dbconnector_paged_select"
      }
    ]
  }
}
JSON
);
?>