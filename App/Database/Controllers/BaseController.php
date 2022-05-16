<?php

namespace App\Controllers;
 
class BaseController {
  /**
   * controller property list
   * @var array
   */
  private $props = [];

  /**
   * setter
   */
  function __set ($propertyName, $propertyValue) {
    $this->props [ strtolower ($propertyName) ] = [
      'name' => $propertyName,
      'value' => $propertyValue
    ];
  }

  /**
   * getter
   */
  function __get ($propertyName) {
    $propertyName = strtolower ($propertyName);

    if (isset ($this->props [$propertyName])
      && is_array ($prop = $this->props [$propertyName])
      && isset ($prop ['value'])) {
      return $prop ['value'];
    }
  }

  function getProps () {
    return $this->props;
  }
}
