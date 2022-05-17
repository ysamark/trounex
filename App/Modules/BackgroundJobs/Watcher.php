<?php

namespace App\Modules\BackgroundJobs;

use App\Services\Mail;
use PHPMailer\PHPMailer\SMTP;

class Watcher {
  /**
   * @var boolean $running
   */
  private static $running = true;

  /**
   * @method void Run
   */
  public static function Run () {
    $queueCacheDirectoryPath = join (DIRECTORY_SEPARATOR, [
      dirname (dirname (dirname (__DIR__))), 'db', 'caches', 'queue', 'mail', ''
    ]);

    $queueCacheDirectoryFileList = glob ($queueCacheDirectoryPath . '*.json');
    #self::$running = false;

    if (!(is_array ($queueCacheDirectoryFileList)
      && $queueCacheDirectoryFileList)) {
      return;
    }

    # [ 'data' => $mailDatasAsString ]

    foreach ($queueCacheDirectoryFileList as $queueCacheFile) {
      $queueCacheFileContent = file_get_contents ($queueCacheFile);
      $queueCacheFileData = (array)(json_decode ($queueCacheFileContent));

      $mailDatas = (array)(json_decode (base64_decode ($queueCacheFileData ['data'])));

      if (Mail::SendMail ($mailDatas, SMTP::DEBUG_SERVER)) {

        print ("\nEmail Sent!\n");

        print_r ($mailDatas);
        print ("\n\n\n");

        @unlink ($queueCacheFile);
      }
    }
  }

  /**
   * @method void Start
   */
  public static function Start () {
    $i = 0;
    
    while (self::Running ()) {
      if ($i++ >= 5 * 1000) {
        echo "\nAwaiting...\n";
      }  

      self::Run ();
    }
  }

  /**
   * @method void Stop
   */
  public static function Stop () {
    self::$running = false;
  }

  /**
   * @method void Running
   */
  public static function Running () {
    return self::$running;
  }
}
