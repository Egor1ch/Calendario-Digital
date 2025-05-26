<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Calendario Digital</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
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
            
            <div class="form-tabs">
                <button class="tab-btn active" data-tab="login">Iniciar Sesión</button>
                <button class="tab-btn" data-tab="register">Registrarse</button>
            </div>
            
            <div class="tab-content active" id="login-tab">
                <form id="login-form" action="auth/login_process.php" method="POST">
                    <?php
                    if (isset($_GET['error'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                    if (isset($_GET['success'])) {
                        echo '<div class="success-message">' . $_GET['success'] . '</div>';
                    }
                    ?>
                    <div class="form-group">
                        <label for="login-email">Correo electrónico</label>
                        <input type="email" id="login-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Contraseña</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <div class="form-group remember-me">
                        <input type="checkbox" id="remember-me" name="remember">
                        <label for="remember-me">Recordarme</label>
                    </div>
                    <button type="submit" class="login-btn">Iniciar Sesión</button>
                </form>
                <div class="form-footer">
                    <a href="auth/forgot_password.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            
            <div class="tab-content" id="register-tab">
                <form id="register-form" action="auth/register_process.php" method="POST">
                    <div class="form-group">
                        <label for="register-name">Nombre</label>
                        <input type="text" id="register-name" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Correo electrónico</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Contraseña</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">Confirmar contraseña</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="register-btn">Registrarse</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                tabContents.forEach(content => content.classList.remove('active'));
                document.getElementById(`${tab}-tab`).classList.add('active');
            });
        });

        document.getElementById('register-form').addEventListener('submit', function(e) {
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
