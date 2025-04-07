<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
    // Crear conexión
    $conn = new PDO("mysql:host=$servername", $username, $password);
    // Establecer el modo de error PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS calendario_digital";
    $conn->exec($sql);
    echo "Base de datos creada exitosamente<br>";

    // Seleccionar la base de datos
    $conn->exec("USE calendario_digital");

    // Crear tabla de usuarios
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Tabla de usuarios creada exitosamente<br>";


    echo "Configuración completada. Ahora puedes iniciar sesión con:<br>";
    echo "Email: test@example.com<br>";
    echo "Contraseña: 123456";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
