<?php
require_once "../db/config.php";
require_once "../utils/mail.php";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    try {
        // Comprobar si el correo existe en la base de datos
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generar token único de reseteo
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            
            // Calcular tiempo de expiración (1 hora)
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Primero verificar si las columnas existen
            $columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
            if ($columnCheck->rowCount() == 0) {
                // Redirigir a una página para actualizar la base de datos
                header("Location: ../db/actualizar_bd.php");
                exit();
            }
            
            // Guardar token en la base de datos
            $update_stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, token_expires_at = ? WHERE id = ?");
            $update_stmt->execute([$token_hash, $token_expiry, $usuario['id']]);
            
            // Construir URL para resetear la contraseña
            // Obtener la URL del directorio actual, excluyendo el nombre del script
            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $dirPath = dirname($currentUrl); // Obtener el directorio del script actual
            
            // Construir la URL de reseteo relativa al directorio actual
            $resetUrl = $dirPath . "/reset_password.php?token=" . $token;
            
            // Enviar correo electrónico con el enlace
            $nombre = $usuario['nombre'];
            $asunto = "Recuperación de contraseña - Calendario Digital";
            $mensaje = generarMensajeRecuperacion($nombre, $resetUrl);
            
            if (enviarCorreo($email, $asunto, $mensaje)) {
                header("Location: forgot_password.php?success=" . urlencode("Se ha enviado un enlace de recuperación a tu correo electrónico."));
                exit();
            } else {
                header("Location: forgot_password.php?error=" . urlencode("No se pudo enviar el correo. Por favor, inténtalo de nuevo más tarde."));
                exit();
            }
        } else {
            // Si el correo no existe, mostrar mensaje de éxito igualmente (por seguridad)
            header("Location: forgot_password.php?success=" . urlencode("Si tu correo está registrado, recibirás un enlace de recuperación."));
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
            header("Location: forgot_password.php?error=" . urlencode("Error en el sistema: " . $e->getMessage()));
            exit();
        }
    }
} else {
    // Si no se ha enviado el formulario, redirigir a la página de recuperación
    header("Location: forgot_password.php");
    exit();
}
?>
