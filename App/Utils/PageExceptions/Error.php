<?php

namespace App\Utils\PageExceptions;

class Error {
  /**
   * Throw any thing
   */
  public static function __callStatic ($method, $arguments) {
    exit ($method);
  }
}
