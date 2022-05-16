<?php

function logged_in_user_authenticated () {
  $publicPagesWhenLoggedIn = [
    path ('/reg/account/confirm'),
    path ('/reg/account/confirm/checkout'),
    path ('/reg/account/change/email')
  ];

  if (!$user = logged_in_user ()) {
    return false;
  } elseif ($token = $user->getToken ()) {
    if (!$token->verified) {
      if (!in_array ($_SERVER ['REQUEST_URI'], $publicPagesWhenLoggedIn)) {
        redirect_to ('/reg/account/confirm');
      }
    } else {
      return true;
    }
  }

  return false;
}
