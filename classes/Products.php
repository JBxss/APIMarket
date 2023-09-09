<?php

require 'vendor/autoload.php';

class Products
{

    function crearProducto()
    {

        $db = Flight::db();
        $codigo = Flight::request()->data->codigo_producto;
        $nombre = Flight::request()->data->nombre_producto;
        $valor = Flight::request()->data->valor_producto;
        $query = $db->prepare("INSERT INTO tbl_productos (codigo_producto, nombre_producto, valor_producto) VALUES (:codigo, :nombre, :valor)");

        $array = [
            "error" => "Hubo un error al agregar los registros",
            "status" => "Error"
        ];

        if ($query->execute([":codigo" => $codigo, ":nombre" => $nombre, ":valor" => $valor])) {
            $array = [
                "Nuevo_Producto" => [
                    "Codigo" => $codigo,
                    "Nombre" => $nombre,
                    "Valor" => $valor
                ],
                "status" => "success"
            ];
        };

        Flight::json($array);
    }
}
