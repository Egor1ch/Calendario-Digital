<?php
require_once "../db/config.php";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y validar los datos del formulario
    $token = $_POST["token"];
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Las contraseñas no coinciden."));
        exit();
    }
    
    // Validar longitud mínima de contraseña
    if (strlen($password) < 6) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("La contraseña debe tener al menos 6 caracteres."));
        exit();
    }
    
    // Hash del token para verificar
    $token_hash = hash('sha256', $token);
    
    try {
        // Verificar primero si las columnas existen
        $columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
        if ($columnCheck->rowCount() == 0) {
            // Redirigir a una página para actualizar la base de datos
            header("Location: ../db/actualizar_bd.php");
            exit();
        }
        
        // Verificar si el token existe y no ha expirado
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND reset_token = ? AND token_expires_at > NOW()");
        $stmt->execute([$user_id, $token_hash]);
        
        if ($stmt->rowCount() == 0) {
            header("Location: ../login.php?error=" . urlencode("Enlace no válido o expirado."));
            exit();
        }
        
        // Actualizar la contraseña y eliminar el token de reseteo
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        
        $update_stmt = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, token_expires_at = NULL WHERE id = ?");
        $result = $update_stmt->execute([$hash_password, $user_id]);
        
        if ($result) {
            header("Location: ../login.php?success=" . urlencode("Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión."));
            exit();
        } else {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Error al actualizar la contraseña. Inténtalo de nuevo."));
            exit();
        }
    } catch (PDOException $e) {
        // Si hay un error relacionado con la columna no encontrada
        if (strpos($e->getMessage(), "Column not found") !== false || 
            strpos($e->getMessage(), "Unknown column") !== false) {
            header("Location: ../db/actualizar_bd.php");
            exit();
        } else {
            // Otro tipo de error
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Error en el sistema. Por favor, inténtalo más tarde."));
            exit();
        }
    }
} else {
    // Si no se ha enviado el formulario, redirigir a la página de login
    header("Location: ../login.php");
    exit();
}
?>
