<?php

require 'vendor/autoload.php';
require 'classes/Clients.php';
require 'classes/Products.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

$clients = new Clients();
$products = new Products();

Flight::route('POST /clientes', [$clients, 'resgistrarCliente']);
Flight::route('POST /productos', [$products, 'crearProducto']);

Flight::start();
