<?php

require 'vendor/autoload.php';

class Compras
{

    function nuevaCompra()
    {
        if (!validateToken()) {
            Flight::halt(403, json_encode(
                [
                    "error" => "unauthorized",
                    "status" => "error",
                    "code" => "403"
                ]
            ));
        }

        $db = Flight::db();
        $codigo = Flight::request()->data->codigo_producto;
        $cedula = Flight::request()->data->cedula_cliente;
        $fecha = Flight::request()->data->fecha_compra;

        // Realiza validaciones de los datos de la compra
        $errores = [];

        if (empty($codigo) || !is_numeric($codigo)) {
            $errores[] = "El codigo es invalido";
        }

        if (empty($cedula) || !is_numeric($cedula)) {
            $errores[] = "La cedula es invalida";
        }

        if (!strtotime($fecha)) {
            $errores[] = "La fecha de compra no es válida";
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

            $query = $db->prepare("INSERT INTO tbl_compra (codigo_producto, cedula_cliente, fecha_compra) VALUES (:codigo, :cedula, :fecha)");

            if ($query->execute([":codigo" => $codigo, ":cedula" => $cedula, ":fecha" => $fecha])) {
                $array = [
                    "Nuevo_Compra" => [
                        "Codigo" => $codigo,
                        "Cedula" => $cedula,
                        "Fecha" => $fecha
                    ],
                    "status" => "success"
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

    function calcularCompra()
    {
        if (!validateToken()) {
            Flight::halt(403, json_encode(
                [
                    "error" => "unauthorized",
                    "status" => "error",
                    "code" => "403"
                ]
            ));
        }

        $db = Flight::db();
        $cedula = Flight::request()->data->cedula_cliente;
        $fecha = Flight::request()->data->fecha_compra;
        $descuento = 0;
        $total = 0;

        if (empty($cedula) || empty($fecha)) {
            Flight::halt(400, json_encode([
                "error" => "La cedula y la fecha es obligatorio",
                "status" => "error",
                "code" => "400"
            ]));
        }

        // Convierte la fecha en un objeto de fecha
        $parseFecha = date_create_from_format('Y-m-d', $fecha);
        if (!$parseFecha) {
            Flight::halt(400, json_encode([
                "error" => "Fecha Invalida",
                "status" => "error",
                "code" => "400"
            ]));
        }

        // Obtiene el día actual del mes
        $diaFecha = date_format($parseFecha, 'j');

        if ($diaFecha == 15) {
            $descuento = 0.10;
        } elseif ($diaFecha == 30) {
            $descuento = 0.20;
        }

        $queryCliente = $db->prepare("SELECT * FROM tbl_clientes WHERE cedula_cliente = :cedula");
        $queryCliente->execute([":cedula" => $cedula]);
        $dataCliente = $queryCliente->fetch();

        if (!$dataCliente) {
            Flight::halt(404, json_encode([
                "error" => "Cliente no encontrado",
                "status" => "error",
                "code" => "404"
            ]));
        }

        $query = $db->prepare("SELECT * FROM tbl_compra WHERE cedula_cliente = :cedula AND fecha_compra = :fecha");
        $query->execute([":cedula" => $cedula, ":fecha" => $fecha]);
        $data = $query->fetchAll();

        if (empty($data)) {
            Flight::halt(404, json_encode([
                "error" => "Compra no encontrada",
                "status" => "error",
                "code" => "404"
            ]));
        }

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
