<?php

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
            $array = [
                "Nuevo_Cliente" => [
                    "Cedula" => $cedula,
                    "Nombre" => $nombre,
                    "Celular" => $celular,
                    "Correo" => $correo
                ],
                "status" => "success"
            ];
        };

        Flight::json($array);
    }
}
