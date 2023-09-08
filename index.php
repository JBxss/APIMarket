<?php

require 'vendor/autoload.php';
Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=APIMarket', 'root', ''));

Flight::route('GET /clientes', function () {
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

    $array[] = [
        "Cedula" => $data['cedula_cliente'],
        "Nombre" => $data['nombre_cliente'],
        "Celular" => $data['celular_cliente'],
        "Correo" => $data['correo_cliente']
    ];

    Flight::json($array);
});

Flight::start();
