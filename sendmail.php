<?php

namespace App;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/dotenv.config.php';

use App\Services\Mail;

function sendMail ($id) {
  
  $uuid = date ('H:i:s'); # uuid ();

  $body = "Hello, World..! $id I am Sam, who are you???";

  Mail::AddToMailQueue ([
    'Subject' => 'Agostinho Lopes - '.date('YHis').' Envio de Email Test',
    'AltBody' => $body,
    'Body' => join (' ', [
      '<h1 style="border: 10px solid #daa540; padding: 70px; font-size: 80px; color:',
      '#ffffff; background-color: red;">'.$body.'</h1> <br>' . $uuid
    ]),

    'Addresses' => [
      ["agostinhosaml832@gmail.com", "Agostinho Lopes"]
    ]
  ]);
}

#$i = rand (1,);

$id = rand (1, 100) . time ();

$jobs = [
  'App\sendMail',
  'App\sendMail',
  'App\sendMail',
  'App\sendMail'
];

$i = 0;

while (true) {
  
  if (isset ($jobs [$i])) {
    call_user_func_array ($jobs[$i], [$id]);
  }
  
  $i++;
}

echo "\n\n\nEmail Sent!!!! :)\n\n\n\n";
