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
        $descuento = 0;
        $total = 0;

        // Convierte la fecha en un objeto de fecha
        $parseFecha = date_create_from_format('Y-m-d', $fecha);

        // Obtiene el día actual del mes
        $diaFecha = date_format($parseFecha, 'j');

        if ($diaFecha == 15) {
            $descuento = 0.10;
        } elseif ($diaFecha == 30) {
            $descuento = 0.20;
        }

        $query = $db->prepare("SELECT * FROM tbl_compra WHERE cedula_cliente = :cedula AND fecha_compra = :fecha");
        $query->execute([":cedula" => $cedula, ":fecha" => $fecha]);
        $data = $query->fetchAll();

        $queryCliente = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :cedula");
        $queryCliente->execute([":cedula" => $cedula]);
        $dataCliente = $queryCliente->fetch();

        foreach ($data as $row) {

            $queryProducto = $db->prepare("SELECT * FROM tbl_productos WHERE codigo_producto = :codigo");
            $queryProducto->execute([":codigo" => $row['codigo_producto']]);
            $dataProducto = $queryProducto->fetch();

            $arrayProducto[] = [
                    "Codigo del Producto" => $row['codigo_producto'],
                    "Nombre del Producto" => $dataProducto['nombre_producto'],
                    "Valor" => $dataProducto['valor_producto'],
            ];

            $total += $dataProducto['valor_producto'];
        }

        $array = [
            "Nombre" => $dataCliente['nombre_cliente'],
            "Cedula" => $data[0]['cedula_cliente'],
            "Productos" => $arrayProducto,
            "Fecha" => $data[0]['fecha_compra'],
            "Total" => $total,
            "Descuento" => ($descuento * 100) . "%",
            "Valor Final a Pagar" => $total - ($total * $descuento)
        ];

        Flight::json($array);
    }
}
