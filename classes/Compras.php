<?php

require 'vendor/autoload.php';

class Compras
{

    function nuevaCompra()
    {

        $db = Flight::db();
        $codigo = Flight::request()->data->codigo_producto;
        $cedula = Flight::request()->data->cedula_cliente;
        $fecha = Flight::request()->data->fecha_compra;
        $query = $db->prepare("INSERT INTO tbl_compra (codigo_producto, cedula_cliente, fecha_compra) VALUES (:codigo, :cedula, :fecha)");

        $array = [
            "error" => "Hubo un error al agregar los registros",
            "status" => "Error"
        ];

        if ($query->execute([":codigo" => $codigo, ":cedula" => $cedula, ":fecha" => $fecha])) {
            $array = [
                "Nuevo_Compra" => [
                    "Codigo" => $codigo,
                    "Cedula" => $cedula,
                    "Fecha" => $fecha
                ],
                "status" => "success"
            ];
        };

        Flight::json($array);
    }

    function calcularCompra()
    {

        $db = Flight::db();
        $cedula = Flight::request()->data->cedula_cliente;
        $fecha = Flight::request()->data->fecha_compra;

        $query = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :cedula");
        $query->execute([":cedula" => $cedula]);
        $data = $query->fetch();

        $array = [
            "Cedula" => $data['cedula_cliente'],
            "Nombre" => $data['nombre_cliente'],
        ];

        Flight::json($array);
    }
}
