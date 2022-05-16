<?php

namespace DotEnv\Config;

use Symfony\Component\Dotenv\Dotenv;
use App\Utils\Env;

if (is_file (__DIR__ . '/.env')) {
  call_user_func ([new Dotenv, 'load'], __DIR__ . '/.env');
}
