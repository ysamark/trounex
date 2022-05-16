<?php

function asset () {
  $assetPath = join (DIRECTORY_SEPARATOR, [
    dirname (__DIR__),
    'assets',
    join (DIRECTORY_SEPARATOR, func_get_args ())
  ]);

  $assetRenderTagListByExtension = [
    'js' => ['<script type="text/javascript">', '</script>'],
    'css' => ['<style type="text/css">', '</style>']
  ];


  if (is_file ($assetPath . '.php')) {
    $assetFileExtension = pathinfo ($assetPath, PATHINFO_EXTENSION);

    if (isset ($assetRenderTagListByExtension [$assetFileExtension])) {
      $renderDatas = $assetRenderTagListByExtension [$assetFileExtension];

      echo $renderDatas [0];
      include $assetPath . '.php';
      echo $renderDatas [1];
    }
  }
  

  if (!empty ($assetPath) && is_file ($assetPath)) {
    $assetPath = realpath ($assetPath);

    $assetFileContent = file_get_contents ($assetPath);
    $assetFileExtension = pathinfo ($assetPath, PATHINFO_EXTENSION);

    if (isset ($assetRenderTagListByExtension [$assetFileExtension])) {
      $renderDatas = $assetRenderTagListByExtension [$assetFileExtension];

      return join ('', [
        $renderDatas [0],
        trim ($assetFileContent),
        // trim (preg_replace ('/\s{2,}/', ' ', $assetFileContent)),
        $renderDatas [1]
      ]);
    }
  }
}
