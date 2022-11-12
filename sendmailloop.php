<?php

namespace App;

$i = 0;
$seconds = 0;


while (true) {
  $i++;

  if ($i / 1000 === (int)($i / 1000)) {
    $seconds++;



    echo "\nDiff", $i / 1000, " === ", (int)($i / 1000), "\n", "Passaram => ", $seconds, "\n\n";
  }
}
