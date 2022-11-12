<?php

namespace App\Modules\BackgroundJobs;

use App\Modules\BackgroundJobs\Jobs\MailJob;

class BackgroundJob {
  use CacheFileHelper;
  /**
   * @method void Queue
   */
  public static function Queue (array $jobData) {
    $queueCacheFilePath = join (DIRECTORY_SEPARATOR, [
      dirname(dirname (dirname (__DIR__))), 
      'db', 
      'caches', 
      'queue', 
      'mail',
      '_mail_queue.cache.json'
    ]);

    # $queueCacheFileHandler = fopen ($queueCacheFilePath, 'r');

    if (!!is_file ($queueCacheFilePath)/* $queueCacheFileHandler */) {
      #$queueCacheFileLines = [];
      /**
       * Fetch the file content
       */
      #while (!feof ($queueCacheFileHandler)) {
      #  @array_push ($queueCacheFileLines, fgets ($queueCacheFileHandler));
      #}

      #@fclose ($queueCacheFileHandler);

      $queueCacheFileContent = file_get_contents ($queueCacheFilePath); # join ('', $queueCacheFileLines);

      $queueCache = (array)(json_decode ($queueCacheFileContent));

      array_push ($queueCache, [
        'JobProps' => $jobData,
        'JobHandler' => MailJob::class,
        "JobId" => uuid ()
      ]);

      return self::SaveDataInCacheFile ($queueCacheFilePath, $queueCache);
    }

    return 0;
  }
}
