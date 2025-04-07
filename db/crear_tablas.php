<?php
require_once "config.php";

try {
    // Crear tabla de eventos
    $sql = "CREATE TABLE IF NOT EXISTS eventos (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT(11) UNSIGNED NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        fecha DATE NOT NULL,
        hora TIME,
        categoria VARCHAR(50) NOT NULL,
        descripcion TEXT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "Tabla de eventos creada exitosamente<br>";

    // Crear tabla de categorías personalizadas
    $sql = "CREATE TABLE IF NOT EXISTS categorias (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT(11) UNSIGNED NOT NULL,
        nombre VARCHAR(50) NOT NULL,
        color VARCHAR(20) NOT NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "Tabla de categorías creada exitosamente<br>";

    // Modificar tabla de usuarios para añadir token de recuerdo, verificación y fecha de caducidad
    // Verificar si existe la columna remember_token
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'remember_token'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        $sql = "ALTER TABLE usuarios ADD remember_token VARCHAR(255) NULL";
        $conn->exec($sql);
        echo "Columna remember_token añadida a la tabla usuarios<br>";
    } else {
        echo "La columna remember_token ya existe en la tabla usuarios<br>";
    }
    
    // Verificar si existe la columna email_verified
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'email_verified'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        $sql = "ALTER TABLE usuarios ADD email_verified TINYINT(1) NOT NULL DEFAULT 0";
        $conn->exec($sql);
        echo "Columna email_verified añadida a la tabla usuarios<br>";
    } else {
        echo "La columna email_verified ya existe en la tabla usuarios<br>";
    }
    
    // Verificar si existe la columna verification_token
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'verification_token'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        $sql = "ALTER TABLE usuarios ADD verification_token VARCHAR(255) NULL";
        $conn->exec($sql);
        echo "Columna verification_token añadida a la tabla usuarios<br>";
    } else {
        echo "La columna verification_token ya existe en la tabla usuarios<br>";
    }
    
    // Verificar si existe la columna token_expires_at
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'token_expires_at'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        $sql = "ALTER TABLE usuarios ADD token_expires_at DATETIME NULL";
        $conn->exec($sql);
        echo "Columna token_expires_at añadida a la tabla usuarios<br>";
    } else {
        echo "La columna token_expires_at ya existe en la tabla usuarios<br>";
    }

    // Verificar si existe la columna reset_token
    $sql = "SHOW COLUMNS FROM usuarios LIKE 'reset_token'";
    $result = $conn->query($sql);

    if ($result->rowCount() == 0) {
        $sql = "ALTER TABLE usuarios ADD reset_token VARCHAR(255) NULL";
        $conn->exec($sql);
        echo "Columna reset_token añadida a la tabla usuarios<br>";
    } else {
        echo "La columna reset_token ya existe en la tabla usuarios<br>";
    }
    
    echo "Configuración de tablas completada.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
