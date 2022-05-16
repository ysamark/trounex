<?php

namespace App;

class Router {

  public function __construct () {
  }

  /**
   * @method GetRoutesPath ($viewsPath)
   */
  public static function GetRoutesPath ($viewsPath) {
    $routesPath = [];

    if (!(is_string ($viewsPath) && is_dir ($viewsPath))) {
      return $routesPath;
    }

    $directoryFileList = self::getDirectoryFileList ($viewsPath);

    #echo '<pre>';
    #print_r ($directoryFileList);
    #exit (0);
    return $directoryFileList;
  }

  /**
   * @method EvaluateRouteParams
   */
  public static function EvaluateRouteParams ($routeParamKeys, $routeParamValues) {
    $routeParams = [];

    foreach ($routeParamKeys as $routeParamKeyIndex => $routeParamKey) {
      $routeParamKeyValue = !isset ($routeParamValues [$routeParamKeyIndex]) ? null : (
        $routeParamValues [$routeParamKeyIndex]
      );

      $routeParams [$routeParamKey] = $routeParamKeyValue;
    }

    Router\Param::MapList ($routeParams);

    return $routeParams;
  }

  /**
   * Get the list of whole the php files in a directory
   */
  protected static function getDirectoryFileList ($directoryPath) {

    $directoryPathRe = ($directoryPath);

    # echo ('Re => ' . $directoryPathRe . '<br /><br />');

    $fileList = [];
    $directoryFileList = self::readDir ($directoryPathRe);
    $routeParamRe = '/\\[([^\\]]+)\\]/';

    #echo "File => <span style=\"color: blue;\">", $directoryPathRe, "/*</span><BR />";
    #echo '<div style="background-color: blue; color: white"><pre>';
    #print_r ($directoryFileList);
    #echo '</div></pre><br />';

    foreach ($directoryFileList as $directoryFile) {
      if (is_dir ($directoryFile)) {
        $fileList = array_merge ($fileList, self::getDirectoryFileList ($directoryFile));     
      } elseif (in_array (pathinfo ($directoryFile, PATHINFO_EXTENSION), ['php']) &&
        preg_match_all ($routeParamRe, $directoryFile, $match)) {

        $directoryFile = realpath ($directoryFile);

        array_push ($fileList, [ 
          'originalFilePath' => $directoryFile, 
          'routeRe' => self::routePathRe ($directoryFile),
          'match' => $match 
        ]);
      }
    }

    return $fileList;
  }

  protected static function readDir ($dir) {
    $files = [];

    if (is_dir ($dir)) {
      if ($dh = opendir ($dir)) {
        while (($file = readdir ($dh)) !== false) {
          if (!in_array ($file, ['.', '..'])) {
            array_push ($files, realpath ($dir . '/' . $file));
          }
        }

        closedir($dh);
      }
    }

    return $files;
  }

  protected static function routePathRe ($directoryFile) {
    $routeParamRe = '/\\[([^\\]]+)\\]/';

    $re = preg_replace ($routeParamRe, '([^\/\\\\\\]+)', self::sanitizePathStr ($directoryFile));
    
    return "/^($re)$/i";
  }

  /**
   * Sanitize a given path string
   */
  protected static function sanitizePathStr ($pathStr) {
    return preg_replace_callback ('/[\/\\\\.]/', function ($match) {
      return '\\' . $match [0];
    }, $pathStr);
  }
}
