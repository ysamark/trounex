<?php

namespace API;

use App\Modules\BackgroundJobs\BackgroundJob;

class Send_Message {
  function handler ($request) {

    BackgroundJob::QueueMessage([
      'title' => 'Hello, World..!',
      'content' => 'Hi man, :) How are u doing??'
    ]);

    exit ('Message Sent...!');
  }
}
