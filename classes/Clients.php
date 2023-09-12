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

        // Realiza validaciones
        $errores = [];

        if (empty($cedula) || !is_numeric($cedula)) {
            $errores[] = "La cedula es invalida";
        }

        if (empty($nombre)) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($celular) || !is_numeric($celular)) {
            $errores[] = "El numero de celular es invalido";
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electronico no es valido";
        }

        if (!empty($errores)) {
            Flight::halt(400, json_encode(
                [
                    "error" => $errores,
                    "status" => "Error",
                    "code" => "400"
                ]
            ));
        } else {

            $query = $db->prepare("INSERT INTO tbl_clientes (cedula_cliente, nombre_cliente, celular_cliente, correo_cliente) VALUES (:cedula, :nombre, :celular, :correo)");

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
            } else {
                Flight::halt(500, json_encode(
                    [
                        "error" => "Hubo un error al agregar los registros",
                        "status" => "Error",
                        "code" => "500"
                    ]
                ));
            }
        }

        Flight::json($array);
    }

    function loginCliente()
    {

        $db = Flight::db();
        $cedula = Flight::request()->data->cedula_cliente;

        if (empty($cedula) || !is_numeric($cedula)) {
            Flight::halt(400, json_encode(
                [
                    "error" => "La cedula es invalida",
                    "status" => "Error",
                    "code" => "400"
                ]
            ));
        }

        $query = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :cedula");

        if ($query->execute([":cedula" => $cedula])) {
            $user = $query->fetch();

            if ($user) {
                $now = strtotime("now");
                $key = 'example_key';
                $payload = [
                    'exp' => $now + 86400, // Agrega 86400 segundos (1 día) al tiempo actual.
                    'data' => $user['cedula_cliente']
                ];

                $jwt = JWT::encode($payload, $key, 'HS256');
                $array = ["token" => $jwt];
            } else {
                Flight::halt(404, json_encode(
                    [
                        "error" => "Cliente no encontrado",
                        "status" => "Error",
                        "code" => "404"
                    ]
                ));
            }
        } else {
            Flight::halt(500, json_encode(
                [
                    "error" => "Hubo un error al buscar al cliente",
                    "status" => "Error",
                    "code" => "500"
                ]
            ));
        }

        Flight::json($array);
    }
}
