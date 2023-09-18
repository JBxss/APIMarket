# APIMarket - Supermercado en línea API

APIMarket es una API de un supermercado en línea desarrollada en PHP utilizando el framework FlightPHP. Esta API proporciona endpoints para la gestión de productos, clientes, compras y generación de informes. Además, cuenta con autenticación mediante JSON Web Tokens (JWT).

## Requisitos

Asegúrate de tener instalado PHP en tu servidor y las siguientes dependencias:

- FlightPHP
- Composer (para instalar dependencias)
- MySQL (o cualquier otro sistema de gestión de bases de datos que prefieras)

## Configuración

1. Clona este repositorio en tu servidor.
2. Instala las dependencias utilizando Composer:

   ```bash
   composer install
   ```

3. Configura la base de datos en `index.php`:

   ```php
   Flight::register('db', 'PDO', array('mysql:host=localhost;dbname=database', 'user', 'pass'));
   ```

4. Ejecuta el script SQL en `APIMarket.sql` para crear la estructura de la base de datos.

## Uso

### Endpoints

#### Crear Producto

- **URL:** `/api/productos`
- **Método:** POST
- **Parámetros de entrada:**
  - Nombre del producto
  - Código del producto
  - Valor del producto

#### Registrar Cliente

- **URL:** `/api/clientes`
- **Método:** POST
- **Parámetros de entrada:**
  - Nombre del cliente
  - Cédula del cliente
  - Celular
  - Correo

#### Comprar Producto

- **URL:** `/api/compras`
- **Método:** POST
- **Parámetros de entrada:**
  - Código del producto
  - Cédula del cliente
  - Fecha de Compra (formato: YYYY-MM-DD)

#### Obtener Informe

- **URL:** `/api/calcular`
- **Método:** POST
- **Parámetros de filtro:**
  - Cédula del cliente
  - Fecha de Compra

### Autenticación

Cada endpoint está protegido por autenticación JWT. Debes incluir un token JWT válido en la cabecera de la solicitud para acceder a los recursos protegidos.

#### Generar Token JWT

Puedes generar un token JWT utilizando la ruta `/api/login`. Debes proporcionar un cedula de usuario válidos en el cuerpo de la solicitud para obtener un token.

### Descuentos

- Los clientes que realicen compras el día 15 del mes obtienen un 10% de descuento en todos los productos.
- Los clientes que realicen compras el día 30 de cada mes obtienen un 20% de descuento.

## Ejemplos de solicitud

A continuación, se muestran ejemplos de cómo realizar solicitudes a los endpoints:

### Crear Producto

```http
POST /api/productos

{
  "nombre": "Producto 1",
  "codigo": "P123",
  "valor": 25.99
}
```

### Registrar Cliente

```http
POST /api/clientes

{
  "nombre": "Juan Pérez",
  "cedula": "1234567890",
  "celular": "123-456-7890",
  "correo": "juan@example.com"
}
```

### Comprar Producto

```http
POST /api/compras

{
  "codigo_producto": "P123",
  "cedula_cliente": "1234567890",
  "fecha_compra": "2023-09-15"
}
```

### Obtener Informe

```http
POST /api/calcular

{
  "cedula_cliente": "1234567890",
  "fecha_compra": "2023-09-15"
}
```

## Contribuciones

Si deseas contribuir a este proyecto, ¡no dudes en enviar un pull request!

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.

---
