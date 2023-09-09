<?php

require 'vendor/autoload.php';
require 'classes/Clients.php';
require 'classes/Products.php';
require 'classes/Compras.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

$clients = new Clients();
$products = new Products();
$compras = new Compras();

Flight::route('POST /clientes', [$clients, 'resgistrarCliente']);
Flight::route('POST /productos', [$products, 'crearProducto']);
Flight::route('POST /compras', [$compras, 'nuevaCompra']);
Flight::route('POST /calcular', [$compras, 'calcularCompra']);

Flight::start();
