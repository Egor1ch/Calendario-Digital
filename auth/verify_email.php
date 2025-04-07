<?php
require_once "../db/config.php";
require_once "../utils/mail.php";

// Inicializar variables
$verification_message = "";
$message_type = "error";

// Comprobar si se ha proporcionado un token
if (isset($_GET["token"]) && !empty($_GET["token"])) {
    $token = trim($_GET["token"]);
    
    try {
        // Buscar el token en la base de datos
        $sql = "SELECT id, nombre, email, email_verified, token_expires_at FROM usuarios WHERE verification_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$token]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si la cuenta ya está verificada
            if ($user["email_verified"] == 1) {
                $verification_message = "Tu cuenta ya ha sido verificada. Puedes iniciar sesión.";
                $message_type = "success";
            } 
            // Verificar si el token ha expirado
            else if (strtotime($user["token_expires_at"]) < time()) {
                // Generar nuevo token
                $newToken = generarTokenVerificacion(md5($user["email"] . time()));
                $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Actualizar token en la base de datos
                $update_sql = "UPDATE usuarios SET verification_token = ?, token_expires_at = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->execute([$newToken, $tokenExpiry, $user["id"]]);
                
                // Enviar nuevo correo
                $asunto = "Confirma tu cuenta - Calendario Digital";
                $mensaje = generarMensajeConfirmacion($user["nombre"], $newToken);
                enviarCorreo($user["email"], $asunto, $mensaje);
                
                $verification_message = "El enlace de verificación ha expirado. Hemos enviado un nuevo enlace a tu correo electrónico.";
                $message_type = "error";
            } 
            // Verificar el correo
            else {
                // Actualizar el estado de verificación
                $update_sql = "UPDATE usuarios SET email_verified = 1, verification_token = NULL WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->execute([$user["id"]]);
                
                // Enviar correo de bienvenida
                $asunto = "¡Bienvenido a Calendario Digital!";
                $mensaje = generarMensajeBienvenida($user["nombre"]);
                enviarCorreo($user["email"], $asunto, $mensaje);
                
                $verification_message = "¡Tu cuenta ha sido verificada correctamente! Ahora puedes iniciar sesión.";
                $message_type = "success";
            }
        } else {
            $verification_message = "El token de verificación no es válido.";
            $message_type = "error";
        }
    } catch (PDOException $e) {
        $verification_message = "Error en la base de datos: " . $e->getMessage();
        $message_type = "error";
    }
} else {
    $verification_message = "Token de verificación no proporcionado.";
    $message_type = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo - Calendario Digital</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }
        .icon-container {
            margin: 20px 0;
            font-size: 60px;
        }
        .success .icon-container {
            color: #4CAF50;
        }
        .error .icon-container {
            color: #ff3b30;
        }
        .verification-message {
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.6;
        }
        .action-button {
            background-color: #0071e3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }
        .action-button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="verification-container <?php echo $message_type; ?>">
            <div class="form-header">
                <i class="fas fa-calendar-alt"></i>
                <h1>Calendario Digital</h1>
            </div>
            
            <div class="icon-container">
                <?php if ($message_type == "success"): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="verification-message">
                <?php echo $verification_message; ?>
            </div>
            
            <a href="../login.php" class="action-button">Ir a iniciar sesión</a>
        </div>
    </div>
</body>
</html>
