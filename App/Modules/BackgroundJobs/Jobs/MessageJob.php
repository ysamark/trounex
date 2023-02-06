<?php

namespace App\Modules\BackgroundJobs\Jobs;

class MessageJob extends Job {
  /**
   * @method Run
   */
  function run ($messageData) {

    print_r ($messageData);

    print ("\n\n\n\n\nSend Message::MessageJon - ".uniqid ()."\n\n\n");


    return true;
  }
}
