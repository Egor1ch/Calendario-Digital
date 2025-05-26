<?php
require_once "../db/config.php";
require_once "../utils/mail.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    
    try {
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $columnCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
            if ($columnCheck->rowCount() == 0) {
                header("Location: ../db/actualizar_bd.php");
                exit();
            }
            
            $update_stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, token_expires_at = ? WHERE id = ?");
            $update_stmt->execute([$token_hash, $token_expiry, $usuario['id']]);

            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $dirPath = dirname($currentUrl);
            
            $resetUrl = $dirPath . "/reset_password.php?token=" . $token;
            
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
            header("Location: forgot_password.php?success=" . urlencode("Si tu correo está registrado, recibirás un enlace de recuperación."));
            exit();
        }
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), "Column not found") !== false || 
            strpos($e->getMessage(), "Unknown column") !== false) {
            header("Location: ../db/actualizar_bd.php");
            exit();
        } else {
            header("Location: forgot_password.php?error=" . urlencode("Error en el sistema: " . $e->getMessage()));
            exit();
        }
    }
} else {
    header("Location: forgot_password.php");
    exit();
}
?>
