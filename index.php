<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';
require 'classes/Clients.php';
require 'classes/Products.php';
require 'classes/Compras.php';

Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

function getToken()
{
    $headers = apache_request_headers();
    if (!isset($headers["Authorization"])) {
        Flight::halt(403, json_encode(
            [
                "error" => "Unautheticated Request",
                "status" => "error"
            ]
        ));
    }

    $authorization = $headers["Authorization"];
    $authorizationArray = explode(" ", $authorization);
    $token = $authorizationArray[1];

    try {
        return JWT::decode($token, new Key('example_key', 'HS256'));
    } catch (\Throwable $th) {
        Flight::halt(403, json_encode(
            [
                "error" => $th->getMessage(),
                "status" => "error"
            ]
        ));
    }
}

function validateToken()
{
    $info = getToken();
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :cedula");
    $query->execute([":cedula" => $info->data]);
    $rows = $query->fetchColumn();
    return $rows;
}

$clients = new Clients();
$products = new Products();
$compras = new Compras();

Flight::route('POST /clientes', [$clients, 'resgistrarCliente']);
Flight::route('POST /login', [$clients, 'loginCliente']);
Flight::route('POST /productos', [$products, 'crearProducto']);
Flight::route('POST /compras', [$compras, 'nuevaCompra']);
Flight::route('POST /calcular', [$compras, 'calcularCompra']);

Flight::start();
