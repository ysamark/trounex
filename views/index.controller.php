<?php

namespace Views;

class Index {
  public $title = 'Welcome to my App';

  public function handler () {
    $this->title .= join (' - ', ["\nDate", date ('Y-m-d H:i:s')]);
  }
}

return new Index;
