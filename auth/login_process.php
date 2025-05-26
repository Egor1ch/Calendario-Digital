<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}

require_once "../db/config.php";

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor ingrese su email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {

        $sql = "SELECT id, nombre, email, password, email_verified FROM usuarios WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {

            $stmt->bindParam(1, $param_email, PDO::PARAM_STR);
            
            $param_email = $email;
            
            if ($stmt->execute()) {

                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $nombre = $row["nombre"];
                        $email = $row["email"];
                        $hashed_password = $row["password"];
                        $email_verified = $row["email_verified"];
                        

                        if ($email_verified == 0) {
                            $error_msg = "Tu cuenta no ha sido verificada. Por favor, revisa tu correo electrónico y sigue las instrucciones para verificar tu cuenta.";
                            header("location: ../login.php?error=" . urlencode($error_msg));
                            exit();
                        }
                        

                        if (password_verify($password, $hashed_password)) {

                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nombre"] = $nombre;
                            $_SESSION["email"] = $email;
                            
                            if (isset($_POST["remember"]) && $_POST["remember"] == "on") {
                                $token = bin2hex(random_bytes(32));
                                
                                $update_sql = "UPDATE usuarios SET remember_token = ? WHERE id = ?";
                                $update_stmt = $conn->prepare($update_sql);
                                $update_stmt->execute([$token, $id]);
                                setcookie("remember_token", $token, time() + (86400 * 30), "/");
                            }

                            header("location: ../index.php");
                        } else {

                            $password_err = "La contraseña ingresada no es válida.";
                            header("location: ../login.php?error=" . urlencode($password_err));
                            exit();
                        }
                    }
                } else {
                    $email_err = "No existe una cuenta con ese correo electrónico.";
                    header("location: ../login.php?error=" . urlencode($email_err));
                    exit();
                }
            } else {
                echo "¡Ups! Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
            }
            unset($stmt);
        }
    } else {
        $error_msg = "";
        if (!empty($email_err)) $error_msg .= $email_err . " ";
        if (!empty($password_err)) $error_msg .= $password_err;
        
        header("location: ../login.php?error=" . urlencode($error_msg));
        exit();
    }
    
    unset($conn);
}
?>
