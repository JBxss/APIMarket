<?php

require 'vendor/autoload.php';

class Products
{

    function crearProducto()
    {

        if (!validateToken()) {
            Flight::halt(403, json_encode(
                [
                    "error" => "unauthorized",
                    "status" => "error"
                ]
            ));
        }

        $db = Flight::db();
        $codigo = Flight::request()->data->codigo_producto;
        $nombre = Flight::request()->data->nombre_producto;
        $valor = Flight::request()->data->valor_producto;

        if (empty($codigo) || empty($nombre) || empty($valor)) {

            Flight::halt(400, json_encode(
                [
                    "error" => "Todos los campos son obligatorios",
                    "status" => "Error",
                    "code" => "400"
                ]
            ));

        } else {

            $query = $db->prepare("INSERT INTO tbl_productos (codigo_producto, nombre_producto, valor_producto) VALUES (:codigo, :nombre, :valor)");

            if ($query->execute([":codigo" => $codigo, ":nombre" => $nombre, ":valor" => $valor])) {
                $array = [
                    "Nuevo_Producto" => [
                        "Codigo" => $codigo,
                        "Nombre" => $nombre,
                        "Valor" => $valor
                    ],
                    "status" => "Success",
                    "code" => "200"
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
}
