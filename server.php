<?php

namespace App\Server;

function serve () {
  print ("Servidor rodando em: \n http://127.0.0.1:7777\n\n");
  @system ('php -S localhost:7777 index.php');
}

serve ();
