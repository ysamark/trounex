<?php

namespace Views;

class Home {
  function handler () {
    $names = [
      'Lopes',
      'Sam',
      'SA'
    ];

    array_splice ($names, 1, 1);

    echo '<pre>';
    print_r ($names);
  }
}

return new Home;
