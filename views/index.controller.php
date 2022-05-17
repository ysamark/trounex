<?php

namespace Views;

class Index {
  public $title = 'Welcome to my App';

  public function handler () {
    $this->title .= join (' - ', ["\nDate", date ('Y-m-d H:i:s')]);

    echo 'APP_DEFAULT_RECIPIENT_EMAIL_ADDRESS => ', $_ENV ['APP_DEFAULT_RECIPIENT_EMAIL_ADDRESS'];
  }
}

return new Index;
