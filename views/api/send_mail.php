<?php

namespace API;

use App\Services\Mail;

class Send_Mail {
  function handler ($request) {

    $uuid = uuid ();

    $body = 'Olá carinha, como vai??';

    Mail::AddToMailQueue ([
      'Subject' => 'Agostinho Lopes - Querendo Envio de Email ' . $uuid,
      'AltBody' => $body,
      'Body' => join (' ', [
        '<h1 style="border: 10px solid #daa540; padding: 70px; font-size: 80px; color:',
        '#ffffff; background-color: red;">'.$body.'</h1> <br>' . $uuid
      ]),

      'Addresses' => [
        ["agostinhosaml832@gmail.com", "Agostinho Lopes"],
        ["luiskapemba@gmail.com", "Hélder Luís Kapemba"]
      ]
    ]);
    
    # flash ('Email Enviado com sucesso!!!!');

    # redirect_back ();

    exit ('Email Sent...!');
  }
}
