<?php
session_start();

// Comprobar si el usuario ya está conectado
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

// Incluir archivo de configuración
require_once "../db/config.php";

// Definir variables e inicializar con valores vacíos
$email = $password = "";
$email_err = $password_err = "";

// Procesar datos del formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Comprobar si el email está vacío
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese su email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Comprobar si la contraseña está vacía
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validar credenciales
    if (empty($email_err) && empty($password_err)) {
        // Preparar consulta select
        $sql = "SELECT id, nombre, email, password, email_verified FROM usuarios WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Vincular variables a la sentencia preparada como parámetros
            $stmt->bindParam(1, $param_email, PDO::PARAM_STR);
            
            // Establecer parámetros
            $param_email = $email;
            
            // Intentar ejecutar la sentencia preparada
            if ($stmt->execute()) {
                // Comprobar si existe el email, en caso afirmativo verificar la contraseña
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $nombre = $row["nombre"];
                        $email = $row["email"];
                        $hashed_password = $row["password"];
                        $email_verified = $row["email_verified"];
                        
                        // Verificar si el correo electrónico está verificado
                        if ($email_verified == 0) {
                            $error_msg = "Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico y sigue las instrucciones para verificar tu cuenta.";
                            header("location: ../login.php?error=" . urlencode($error_msg));
                            exit();
                        }
                        
                        // Verificar la contraseña
                        if (password_verify($password, $hashed_password)) {
                            // La contraseña es correcta, iniciar una nueva sesión
                            session_start();
                            
                            // Almacenar datos en variables de sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nombre"] = $nombre;
                            $_SESSION["email"] = $email;
                            
                            // Manejo de "Recordarme"
                            if (isset($_POST["remember"]) && $_POST["remember"] == "on") {
                                // Generar un token aleatorio y guardarlo en la base de datos
                                $token = bin2hex(random_bytes(32));
                                
                                $update_sql = "UPDATE usuarios SET remember_token = ? WHERE id = ?";
                                $update_stmt = $conn->prepare($update_sql);
                                $update_stmt->execute([$token, $id]);
                                
                                // Crear cookie que dura 30 días
                                setcookie("remember_token", $token, time() + (86400 * 30), "/");
                            }
                            
                            // Redirigir al usuario a la página principal
                            header("location: ../index.php");
                        } else {
                            // La contraseña no es válida
                            $password_err = "La contraseña ingresada no es válida.";
                            header("location: ../login.php?error=" . urlencode($password_err));
                            exit();
                        }
                    }
                } else {
                    // No existe el email
                    $email_err = "No existe una cuenta con ese correo electrónico.";
                    header("location: ../login.php?error=" . urlencode($email_err));
                    exit();
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }

            // Cerrar sentencia
            unset($stmt);
        }
    } else {
        // Si hay errores, redirigir con los mensajes de error
        $error_msg = "";
        if (!empty($email_err)) $error_msg .= $email_err . " ";
        if (!empty($password_err)) $error_msg .= $password_err;
        
        header("location: ../login.php?error=" . urlencode($error_msg));
        exit();
    }
    
    // Cerrar conexión
    unset($conn);
}
?>
