<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

require 'vendor/autoload.php';
Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

Flight::route('GET /clientes', function () {

    if (!validateToken()) {
        Flight::halt(403, json_encode(
            [
                "error" => "unauthorized",
                "status" => "error"
            ]
        ));
    }


    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM tbl_clientes");
    $query->execute();
    $data = $query->fetchAll();
    $array = [];

    foreach ($data as $row) {
        $array[] = [
            "Cedula" => $row['cedula_cliente'],
            "Nombre" => $row['nombre_cliente'],
            "Celular" => $row['celular_cliente'],
            "Correo" => $row['correo_cliente']
        ];
    }


    Flight::json([
        "total_rows" => $query->rowCount(),
        "rows" => $array
    ]);
});

Flight::route('GET /clientes/@id', function ($id) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :id");
    $query->execute([":id" => $id]);
    $data = $query->fetch();

    $array = [
        "Cedula" => $data['cedula_cliente'],
        "Nombre" => $data['nombre_cliente'],
        "Celular" => $data['celular_cliente'],
        "Correo" => $data['correo_cliente']
    ];

    Flight::json($array);
});

Flight::route('POST /auth/', function () {

    $db = Flight::db();
    $cedula = Flight::request()->data->cedula_cliente;
    $query = $db->prepare("SELECT * FROM tbl_clientes where cedula_cliente = :cedula");

    $array = [
        "error" => "Hubo un error al agregar los registros",
        "status" => "Error"
    ];

    if ($query->execute([":cedula" => $cedula])) {
        $user = $query->fetch();
        $now = strtotime("now");
        $key = 'example_key';
        $payload = [
            'exp' => $now + 86400, // Agrega 86400 segundos (1 día) al tiempo actual.
            'data' => $user['cedula_cliente']
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        $array = ["token" => $jwt];
    };

    Flight::json($array);
});

Flight::route('POST /clientes/', function () {

    if (!validateToken()) {
        Flight::halt(403, json_encode(
            [
                "error" => "unauthorized",
                "status" => "error"
            ]
        ));
    }

    $db = Flight::db();
    $cedula = Flight::request()->data->cedula_cliente;
    $nombre = Flight::request()->data->nombre_cliente;
    $celular = Flight::request()->data->celular_cliente;
    $correo = Flight::request()->data->correo_cliente;
    $query = $db->prepare("INSERT INTO tbl_clientes (cedula_cliente, nombre_cliente, celular_cliente, correo_cliente) VALUES (:cedula, :nombre, :celular, :correo)");

    $array = [
        "error" => "Hubo un error al agregar los registros",
        "status" => "Error"
    ];

    if ($query->execute([":cedula" => $cedula, ":nombre" => $nombre, ":celular" => $celular, ":correo" => $correo])) {
        $array = [
            "data" => [
                "Cedula" => $cedula,
                "Nombre" => $nombre,
                "Celular" => $celular,
                "Correo" => $correo
            ],
            "status" => "success"
        ];
    };

    Flight::json($array);
});

Flight::route('DELETE /clientes/', function () {

    if (!validateToken()) {
        Flight::halt(403, json_encode(
            [
                "error" => "unauthorized",
                "status" => "error"
            ]
        ));
    }

    $db = Flight::db();
    $cedula = Flight::request()->data->cedula_cliente;

    $query = $db->prepare("DELETE from tbl_clientes WHERE cedula_cliente = :cedula");

    $array = [
        "error" => "Hubo un error al agregar los registros",
        "status" => "Error"
    ];

    if ($query->execute([":cedula" => $cedula])) {
        $array = [
            "data" => [
                "Cedula" => $cedula
            ],
            "status" => "success"
        ];
    };

    Flight::json($array);
});

Flight::start();
