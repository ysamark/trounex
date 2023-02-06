<?php

namespace App\Modules\BackgroundJobs;

use App\Modules\BackgroundJobs\Jobs\MailJob;
use App\Utils\ErrorHandler;

class BackgroundJob {
  use CacheFileHelper;
  use BackgroundJob\CoreObject;
  /**
   * @method void Queue
   */
  public static function Queue (BackgroundJob $job, array $jobData) {
    $queueCacheFilePath = join (DIRECTORY_SEPARATOR, [
      dirname(dirname (dirname (__DIR__))), 
      'db', 
      'caches', 
      'queue', 
      $job->name,
      '__data__.cache.json'
    ]);

    if (!file_exists ($queueCacheFilePath)) {
      self::createFile ($queueCacheFilePath);
    }

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
        'JobHandler' => $job->className,
        "JobId" => uuid ()
      ]);

      return self::SaveDataInCacheFile ($queueCacheFilePath, $queueCache);
    }

    return 0;
  }

  public static function __callStatic ($methodName, array $arguments = []) {
    $queueHelperPrefixRe = '/^(Queue)/';

    if (preg_match ($queueHelperPrefixRe, $methodName)) {
      $jobName = preg_replace ($queueHelperPrefixRe, '', $methodName);
      $jobClassName = join ('', [$jobName, 'Job']);
      $jobClassRef = join ('\\', [
        'App', 
        'Modules', 
        'BackgroundJobs', 
        'Jobs',
        $jobClassName
      ]);

      if (!(class_exists ($jobClassRef) && in_array (Jobs\Job::class, class_parents ($jobClassRef)))) {
        exit($jobClassRef);
        return ErrorHandler::handle ('No job called ' . $jobName);
      }

      $jobData = isset ($arguments [0]) ? $arguments [0] : null;

      $job = new BackgroundJob ([
        'name' => $jobName,
        'className' => $jobClassRef,
        'callArgs' => $arguments,
        'data' => $jobData
      ]);

      return self::Queue ($job, $jobData);
    }

    ErrorHandler::handle ("Not defined method: $methodName for BackgroundJob class");
  }
}
