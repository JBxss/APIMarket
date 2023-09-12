<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';

class Clients
{

    function resgistrarCliente()
    {

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
            
            $now = strtotime("now");
            $key = 'example_key';
            $payload = [
                'exp' => $now + 86400, // Agrega 86400 segundos (1 día) al tiempo actual.
                'data' => $cedula
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');

            $array = [
                "Nuevo_Cliente" => [
                    "Cedula" => $cedula,
                    "Nombre" => $nombre,
                    "Celular" => $celular,
                    "Correo" => $correo
                ],
                "status" => "success",
                "token" => $jwt
            ];
        };

        Flight::json($array);
    }

    function loginCliente()
    {

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
    }
}
