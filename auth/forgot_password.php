<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Calendario Digital</title>
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
                <h2>Recuperar Contraseña</h2>
                <p>Introduce tu dirección de correo electrónico para recibir un enlace de recuperación.</p>
            </div>
            
            <form action="reset_password_request.php" method="POST">
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                }
                if (isset($_GET['success'])) {
                    echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
                }
                ?>
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="login-btn">Enviar enlace de recuperación</button>
            </form>
            
            <div class="form-footer">
                <a href="../login.php" class="back-link"><i class="fas fa-arrow-left"></i> Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
