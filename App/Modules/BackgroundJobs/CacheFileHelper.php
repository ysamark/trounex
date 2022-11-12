<?php

namespace App\Modules\BackgroundJobs;

trait CacheFileHelper {
  /**
   * @method void SaveDataInCacheFile
   */
  public static function SaveDataInCacheFile ($queueCacheFilePath, $QueueCacheData) {
    $queueFile = @fopen ($queueCacheFilePath, 'w');

    if (!($queueFile && flock ($queueFile, LOCK_EX))) {
      if (is_resource ($queueFile)) {
        fclose ($queueFile);
      }
      
      return forward_static_call_array ([self::class, 'SaveDataInCacheFile'], func_get_args ());
    }
    
    fwrite ($queueFile, json_encode ($QueueCacheData, JSON_PRETTY_PRINT));
    
    flock ($queueFile, LOCK_UN);
    fclose ($queueFile);
  }
}
