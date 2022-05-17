<?php

namespace API;

use App\Services\Mail;

class Send_Mail {
  function handler () {
    Mail::AddToMailQueue ([
      'Subject' => 'Agostinho Lopes - Testando Envio de Email',
      'AltBody' => 'O meu email foi enviado com seucesso para voçê, se alegre!!! hehehhe.',
      'Body' => '<h1 style="border: 10px solid #daa540; padding: 70px; font-size: 80px; color: #ffffff; background-color: red;">O meu email foi enviado com seucesso para voçê, se alegre!!! hehehhe.</h1>',

      'Addresses' => [
        ["agostinhosaml832@gmail.com", "Agostinho Lopes"],
        ["luisikapemba@gmail.com", "Hélder Luís Kapemba"]
      ]
    ]);

    exit ('Email Sent...!');
  }
}
