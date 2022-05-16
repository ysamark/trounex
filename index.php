<?php

namespace App;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/dotenv.config.php';

const SERVER_PATH_PREFIX = '/app/';

Server::PathPrefix (SERVER_PATH_PREFIX);

Server::Run ();
