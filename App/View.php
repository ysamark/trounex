<?php

namespace App;

use Closure;
use App\Controllers\BaseController;

class View {
  /**
   * Render the requested view according to the page route
   * 
   * @return void
   */
  public static function Render () {
    call_user_func_array (Server::lambda (function ($viewPath = null) {
      $vars = get_object_vars ($this);
      
      if (BaseController::isControllerInstance ($this)) {
        $vars = $this->getProps (); 
      }

      foreach ($vars as $key => $var) {
        $varName = is_array ($var) ? $var ['name'] : $key;
        $value = is_array ($var) ? $var ['value'] : $vars [$key];

        if (preg_match ('/^([a-zA-Z0-9_]+)$/', $varName)) {
          $$varName = $value;
        }
      }

      if (!(is_string ($viewPath) && is_file ($viewPath))) {
        $viewPath = Server::GetViewPath ();
      }

      $args = func_get_args ();
      
      include ($viewPath);
    }), func_get_args ());
  }

  /**
   * Yield
   */
  public static function Yields () {
    $backTrace = debug_backtrace ();

    foreach ($backTrace as $i => $trace) {
      if (is_array ($trace) 
        && isset ($trace ['function'])
        && !($trace ['function'] != 'App\{closure}')
        && isset ($trace ['class'])
        && !($trace ['class'] != BaseController::class)
        && isset ($trace ['args'])
        && is_array ($args = $trace ['args'])
        && $args [-1 + count ($args)] instanceof Closure) {
        return call_user_func_array (Server::lambda ($args [-1 + count ($args)]), []);
      }
    }
  }
}
