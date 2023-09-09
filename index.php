<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';
require 'classes/Users.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

$users = new Users();

Flight::route('GET /clientes', [$users, 'selectAll']);
Flight::route('GET /clientes/@id', [$users, 'selectOne']);
Flight::route('POST /auth', [$users, 'auth']);
Flight::route('POST /clientes', [$users, 'update']);
Flight::route('DELETE /clientes', [$users, 'update']);

Flight::start();
