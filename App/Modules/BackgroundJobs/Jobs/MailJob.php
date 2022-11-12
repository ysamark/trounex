<?php

namespace App\Modules\BackgroundJobs\Jobs;

use App\Services\Mail;
use PHPMailer\PHPMailer\SMTP;

class MailJob {
  /**
   * @method Run
   */
  function run ($mailData) {
    #print ("\n\n\n\n\nSend Mail::MailJob - ".uniqid ()."\n\n\n");

    print ("\nSend Mail => \n");
    print_r ($mailData);
    #return true;

    # return Mail::SendMail ($mailData, SMTP::DEBUG_SERVER);
    
    $i = 0;

    while ($i++ < 2) {
      print ("\nlog -> " . uuid () . "\n");
    }

    print ("\nBye!! :)\n");

    return true;
  }
}
