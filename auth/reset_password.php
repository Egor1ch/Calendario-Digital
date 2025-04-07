<?php
require_once "../db/config.php";

// Comprobar si se proporciona un token
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: ../login.php?error=" . urlencode("Enlace no válido o expirado."));
    exit();
}

$token = $_GET['token'];
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
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE reset_token = ? AND token_expires_at > NOW()");
    $stmt->execute([$token_hash]);
    
    if ($stmt->rowCount() == 0) {
        header("Location: ../login.php?error=" . urlencode("Enlace no válido o expirado."));
        exit();
    }
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si hay un error relacionado con la columna no encontrada
    if (strpos($e->getMessage(), "Column not found") !== false || 
        strpos($e->getMessage(), "Unknown column") !== false) {
        header("Location: ../db/actualizar_bd.php");
        exit();
    } else {
        // Otro tipo de error
        header("Location: ../login.php?error=" . urlencode("Error en el sistema. Por favor, inténtalo más tarde."));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Calendario Digital</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-form-container">
            <div class="form-header">
                <i class="fas fa-calendar-alt"></i>
                <h1>Calendario Digital</h1>
            </div>
            
            <div class="form-title">
                <h2>Restablecer Contraseña</h2>
                <p>Hola, <?php echo htmlspecialchars($usuario['nombre']); ?>. Introduce tu nueva contraseña.</p>
            </div>
            
            <form action="update_password.php" method="POST" id="reset-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="hidden" name="user_id" value="<?php echo $usuario['id']; ?>">
                
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                ?>
                
                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                <button type="submit" class="login-btn">Restablecer Contraseña</button>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('reset-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
