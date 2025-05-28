<?php
require_once "../db/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    if ($password !== $confirm_password) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Las contraseñas no coinciden."));
        exit();
    }
    
    if (strlen($password) < 6) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("La contraseña debe tener al menos 6 caracteres."));
        exit();
    }
    
    $token_hash = hash('sha256', $token);
    
    try {
        $columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
        if ($columnCheck->rowCount() == 0) {
            header("Location: ../db/actualizar_bd.php");
            exit();
        }
        
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND reset_token = ? AND token_expires_at > NOW()");
        $stmt->execute([$user_id, $token_hash]);
        
        if ($stmt->rowCount() == 0) {
            header("Location: ../login.php?error=" . urlencode("Enlace no válido o expirado."));
            exit();
        }
        
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
        if (strpos($e->getMessage(), "Column not found") !== false || 
            strpos($e->getMessage(), "Unknown column") !== false) {
            header("Location: ../db/actualizar_bd.php");
            exit();
        } else {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=" . urlencode("Error en el sistema. Por favor, inténtalo más tarde."));
            exit();
        }
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>
