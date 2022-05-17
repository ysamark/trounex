<?php

namespace App\Utils\Commands;

use App\Models\User;
use App\Modules\BackgroundJobs\Watcher;

class Jobs {
  /**
   * Run jobs
   */
  public static function Handle () {
    self::SetUp ();

    print ("\nMail Queue Started...!!\n");

    Watcher::Start ();

    print ("\nMail Queue Ended...!!\n\tBye...! :)");

    exit (0);
  }

  /**
   * Pre Jobs
   */
  public static function SetUp () {
    $root = dirname (dirname (dirname (__DIR__)));
    
    require_once $root . '/dotenv.config.php';
  }
}
