<?php

namespace App\Router;

class Param {
  /**
   * A list of the router sent parameters
   * @var array
   */
  private static $paramMapList = [];

  /**
   * @method void __construct
   */
  public function __construct ($paramMapList = []) {
    self::MapList ($paramMapList);
  }

  /**
   * @method mixed __get
   */
  public function __get ($routeParamKey = null) {
    if (!(is_string ($routeParamKey) 
      && !empty ($routeParamKey)
      && isset (self::$paramMapList [$routeParamKey]))) {
      return null;
    }

    return self::$paramMapList [$routeParamKey];
  }

  public static function MapList ($paramMapList) {
    if (is_array ($paramMapList) && $paramMapList) {
      self::$paramMapList = array_merge (self::$paramMapList, $paramMapList);
    }
  }
}
