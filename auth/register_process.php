<?php
require_once "../db/config.php";
require_once "../utils/mail.php";

// Inicializar variables
$nombre = $email = $password = $confirm_password = "";
$nombre_err = $email_err = $password_err = "";

// Función para generar token de verificación
function generarTokenVerificacion($data) {
    return hash('sha256', $data);
}

// Procesar datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar nombre
    if (empty(trim($_POST["nombre"]))) {
        $nombre_err = "Por favor, introduce tu nombre.";
    } else {
        $nombre = trim($_POST["nombre"]);
    }
    
    // Validar email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, introduce tu correo electrónico.";
    } else {
        // Preparar consulta
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(1, $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "Este correo ya está registrado.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }
            
            // Cerrar la declaración
            unset($stmt);
        }
    }
    
    // Validar contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, introduce una contraseña.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validar confirmación de contraseña
    if (empty(trim($_POST["confirm_password"]))) {
        $password_err = "Por favor, confirma la contraseña.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $password_err = "Las contraseñas no coinciden.";
        }
    }
    
    // Verificar errores antes de insertar en la base de datos
    if (empty($nombre_err) && empty($email_err) && empty($password_err)) {
        
        // Generar token de verificación para el correo
        $verificationToken = generarTokenVerificacion(md5($email . time()));
        
        // Calcular fecha de expiración del token (24 horas)
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Preparar la consulta de inserción con los nuevos campos
        $sql = "INSERT INTO usuarios (nombre, email, password, verification_token, token_expires_at, email_verified) 
                VALUES (?, ?, ?, ?, ?, 0)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(1, $param_nombre, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_email, PDO::PARAM_STR);
            $stmt->bindParam(3, $param_password, PDO::PARAM_STR);
            $stmt->bindParam(4, $param_token, PDO::PARAM_STR);
            $stmt->bindParam(5, $param_token_expiry, PDO::PARAM_STR);
            
            // Establecer parámetros
            $param_nombre = $nombre;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_token = $verificationToken;
            $param_token_expiry = $tokenExpiry;
            
            // Intento de ejecutar la consulta
            if ($stmt->execute()) {
                // Enviar correo de confirmación
                $asunto = "Confirma tu cuenta - Calendario Digital";
                $mensaje = generarMensajeConfirmacion($nombre, $verificationToken);
                $mailEnviado = enviarCorreo($email, $asunto, $mensaje);
                
                // Redirigir a la página de login con mensaje de éxito
                $successMsg = "Te has registrado correctamente. ";
                if ($mailEnviado) {
                    $successMsg .= "Hemos enviado un correo de confirmación a tu dirección de email. Por favor, verifica tu cuenta antes de iniciar sesión.";
                } else {
                    $successMsg .= "Sin embargo, no pudimos enviar el correo de confirmación. Contacta al administrador.";
                }
                
                header("location: ../login.php?success=" . urlencode($successMsg));
                exit();
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }
            
            // Cerrar la declaración
            unset($stmt);
        }
    }
    
    // Si llegamos aquí con errores, los pasamos a la página de login
    if (!empty($nombre_err) || !empty($email_err) || !empty($password_err)) {
        $error_msg = "";
        if (!empty($nombre_err)) $error_msg .= $nombre_err . " ";
        if (!empty($email_err)) $error_msg .= $email_err . " ";
        if (!empty($password_err)) $error_msg .= $password_err;
        
        header("location: ../login.php?error=" . urlencode($error_msg));
        exit();
    }
    
    // Cerrar la conexión
    unset($conn);
}
?>
