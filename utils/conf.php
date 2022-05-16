<?php

function arr ($data) {
  if (!(is_object ($data))) {
    return $data;
  }

  $data = ((array)($data));
  $newData = [];

  foreach ($data as $key => $value) {
    $newData [ $key ] = arr ($value);
  }

  return is_array ($newData) ? $newData : (
    ((array)( $newData ))
  );
}

function conf ($conf = null) {
  if (is_string ($conf)) {
    $confFilePath = join (DIRECTORY_SEPARATOR, [
      dirname (__DIR__), 'config', $conf
    ]);

    $confFileExtension = pathinfo ($confFilePath, PATHINFO_EXTENSION);

    if (is_file ($confFilePath) 
      && preg_match ('/^\.?json(np)?$/i', $confFileExtension)) {
      return arr (json_decode (file_get_contents ($confFilePath)));
    } elseif (is_file ($confFilePath) 
      && preg_match ('/^\.?php?$/i', $confFileExtension)) {
      $confData = @require ($confFilePath);

      if (is_array ($arr = arr ($confData))) {
        return $arr;
      }
    }
  }
}
