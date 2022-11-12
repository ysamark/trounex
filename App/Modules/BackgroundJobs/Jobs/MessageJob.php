<?php

namespace App\Modules\BackgroundJobs\Jobs;

class MessageJob {
  /**
   * @method Run
   */
  function run ($messageDatas) {

    print_r ($messageDatas);

    print ("\n\n\n\n\nSend Message::MessageJon - ".uniqid ()."\n\n\n");


    return true;
  }
}
