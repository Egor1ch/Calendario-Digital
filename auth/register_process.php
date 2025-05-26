<?php
require_once "../db/config.php";
require_once "../utils/mail.php";

$nombre = $email = $password = $confirm_password = "";
$nombre_err = $email_err = $password_err = "";

function añadirFestivosANuevoUsuario($conn, $usuario_id, $año = null) {
    if ($año === null) {
        $año = date('Y');
    }
    
    $check_table = $conn->query("SHOW TABLES LIKE 'festivos_globales'");
    if ($check_table->rowCount() == 0) {
        return;
    }
    
    $festivos = $conn->query("SELECT * FROM festivos_globales")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($festivos)) {
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO eventos (usuario_id, titulo, fecha, categoria, descripcion) VALUES (?, ?, ?, 'party', ?)");
    
    foreach ($festivos as $festivo) {
        $fecha = sprintf('%04d-%02d-%02d', $año, $festivo['mes'], $festivo['dia']);
        
        try {
            $stmt->execute([
                $usuario_id,
                $festivo['titulo'],
                $fecha,
                $festivo['descripcion']
            ]);
        } catch(PDOException $e) {
            if ($e->getCode() != 23000) {
                error_log("Error al añadir festivo para nuevo usuario: " . $e->getMessage());
            }
        }
    }
}

function generarTokenVerificacion($data) {
    return hash('sha256', $data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar nombre
    if (empty(trim($_POST["nombre"]))) {
        $nombre_err = "Por favor, introduce tu nombre.";
    } else {
        $nombre = trim($_POST["nombre"]);
    }
    
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, introduce tu correo electrónico.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(1, $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "Este correo ya está registrado.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }
            unset($stmt);
        }
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, introduce una contraseña.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    if (empty(trim($_POST["confirm_password"]))) {
        $password_err = "Por favor, confirma la contraseña.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $password_err = "Las contraseñas no coinciden.";
        }
    }
    
    if (empty($nombre_err) && empty($email_err) && empty($password_err)) {
        
        $verificationToken = generarTokenVerificacion(md5($email . time()));
        
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $sql = "INSERT INTO usuarios (nombre, email, password, verification_token, token_expires_at, email_verified) 
                VALUES (?, ?, ?, ?, ?, 0)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bindParam(1, $param_nombre, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_email, PDO::PARAM_STR);
            $stmt->bindParam(3, $param_password, PDO::PARAM_STR);
            $stmt->bindParam(4, $param_token, PDO::PARAM_STR);
            $stmt->bindParam(5, $param_token_expiry, PDO::PARAM_STR);
            
            $param_nombre = $nombre;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de la contraseña
            $param_token = $verificationToken;
            $param_token_expiry = $tokenExpiry;
            if ($stmt->execute()) {
                $usuario_id = $conn->lastInsertId();
                
                añadirFestivosANuevoUsuario($conn, $usuario_id);
                
                $asunto = "Confirma tu cuenta - Calendario Digital";
                $mensaje = generarMensajeConfirmacion($nombre, $verificationToken);
                $mailEnviado = enviarCorreo($email, $asunto, $mensaje);
                
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
            

            unset($stmt);
        }
    }
    
    if (!empty($nombre_err) || !empty($email_err) || !empty($password_err)) {
        $error_msg = "";
        if (!empty($nombre_err)) $error_msg .= $nombre_err . " ";
        if (!empty($email_err)) $error_msg .= $email_err . " ";
        if (!empty($password_err)) $error_msg .= $password_err;
        
        header("location: ../login.php?error=" . urlencode($error_msg));
        exit();
    }
    
    unset($conn);
}
?>
