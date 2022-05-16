<?php

use App\Models\Token;

function validate_password_reset_token ($redirect = true) {
  $back = function () {
    flash ('You have to find your account first.<br>Type your email below', 'info');    
    redirect_to ('/login/identify');
  };

  $redirect = is_bool ($redirect) ? $redirect : true;

  if (!(isset ($_SESSION ['password_reset_token'])
    && is_string ($_SESSION ['password_reset_token'])
    && !empty ($_SESSION ['password_reset_token']))) {
    if ($redirect) { 
      call_user_func ($back); 
    } else {
      return false;
    }
  }

  $tokenBody = filter_var ($_SESSION ['password_reset_token'], FILTER_SANITIZE_STRING);

  $fetchToken = Token::where (['body' => $tokenBody]);

  if ($fetchToken->count () < 1) {
    if ($redirect) { 
      call_user_func ($back); 
    } else {
      return false;
    }
  }

  $token = $fetchToken->first ();
  $now = strtotime ('now');

  if (is_null ($token->expire_time)
    || (float)$token->expire_time < $now) {
    flash ('Password recovery link has been expired.<br>Re-Enter you email to send another.');
    redirect_to ('/login/identify');
  }

  return $token;
}
