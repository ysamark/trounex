<?php

namespace App\Utils;

use Symfony\Component\Dotenv\Dotenv;

final class Env {
  public static function Get ($envVariableName, $envVariableDefaultValue = null) {
    if (!isset ($_ENV ['PHP_ENV'])) {
      $dotEnvFilePath = dirname (dirname (__DIR__)) . '/.env';

      if (is_file ($dotEnvFilePath)) {
        call_user_func ([new Dotenv, 'load'], $dotEnvFilePath);
      }
    }    
    
    if (is_string ($envVariableName) && $envVariableName) {
      if (isset ($_ENV [$envVariableName])) {
        return $_ENV [$envVariableName];
      }
  
      return $envVariableDefaultValue;
    }
  }
}
