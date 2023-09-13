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

        // Realiza validaciones
        $errores = [];

        if (empty($codigo) || !is_numeric($codigo)) {
            $errores[] = "El codigo es invalido";
        }

        if (empty($nombre)) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($valor) || !is_numeric($valor)) {
            $errores[] = "El valor es invalido";
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
