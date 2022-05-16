<?php

use App\Utils\Env;

return [
  "default" => [
    "driver" => Env::Get ('DATABASE_DRIVER'),
    "host" => Env::Get ('DATABASE_HOST'),
    "username" => Env::Get ('DATABASE_USER'),
    "password" => Env::Get ('DATABASE_PASS'),
    "charset" => Env::Get ('DATABASE_CHARSET'),
    "collation" => Env::Get ('DATABASE_COLLATION'),
    "prefix" => Env::Get ('DATABASE_PREFIX'),
    "engine" => Env::Get ('DATABASE_ENGINE')
  ],

  "development" => [
    "database" => "app_development_database"
  ],

  "test" => [
    "database" => "app_test_database"
  ],

  "production" => [
    "database" => Env::Get ('DATABASE_NAME')
  ]
];
