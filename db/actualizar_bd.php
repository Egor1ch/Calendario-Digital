<?php
require_once "config.php";

try {
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
    
    echo "Actualización completada correctamente. Ahora puedes usar la función de recuperación de contraseña.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
