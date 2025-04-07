<?php
session_start();

// Eliminar todas las variables de sesión
$_SESSION = array();

// Destruir la sesión
session_destroy();

// Eliminar cookie de "recordarme" si existe
if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

// Redirigir a la página de inicio de sesión
header("Location: ../login.php");
exit();
?>
