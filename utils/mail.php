<?php
/*Enviar correo*/
function enviarCorreo($destinatario, $asunto, $mensaje) {
    $cabeceras = "MIME-Version: 1.0" . "\r\n";
    $cabeceras .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $cabeceras .= "From: Calendario Digital <info@localhost>" . "\r\n";
    
    return mail($destinatario, $asunto, $mensaje, $cabeceras);
}

function generarMensajeConfirmacion($nombre, $token) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $urlVerificacion = $baseUrl . "/auth/verify_email.php?token=" . $token;
    
    $mensaje = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirma tu correo</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .container {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
            }
            .header {
                text-align: center;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .content {
                padding: 20px 0;
            }
            .button {
                display: inline-block;
                background-color: #0071e3;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #eee;
                font-size: 12px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Confirma tu dirección de correo</h2>
            </div>
            <div class="content">
                <p>Hola, <strong>' . $nombre . '</strong>!</p>
                <p>Gracias por registrarte en Calendario Digital. Para activar tu cuenta, haz clic en el siguiente botón:</p>
                <p style="text-align: center;">
                    <a href="' . $urlVerificacion . '" class="button">Verificar mi correo</a>
                </p>
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p>' . $urlVerificacion . '</p>
                <p>Este enlace caducará en 24 horas.</p>
            </div>
            <div class="footer">
                <p>Este correo fue enviado automáticamente. Por favor, no respondas a este mensaje.</p>
                <p>&copy; ' . date('Y') . ' Calendario Digital. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $mensaje;
}

function generarMensajeBienvenida($nombre) {
    $mensaje = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenido a Calendario Digital</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .container {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
            }
            .header {
                text-align: center;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .content {
                padding: 20px 0;
            }
            .button {
                display: inline-block;
                background-color: #0071e3;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #eee;
                font-size: 12px;
                color: #777;
            }
            .feature {
                margin: 15px 0;
            }
            .feature-icon {
                font-weight: bold;
                color: #0071e3;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>¡Bienvenido a Calendario Digital!</h2>
            </div>
            <div class="content">
                <p>Hola, <strong>' . $nombre . '</strong>!</p>
                <p>Tu cuenta ha sido activada correctamente. Ahora puedes acceder a todas las funciones de nuestra aplicación.</p>
                <p>Algunas características que te ofrecemos:</p>
                <div class="feature">
                    <span class="feature-icon">✓</span> Organiza tus eventos diarios, semanales y mensuales
                </div>
                <div class="feature">
                    <span class="feature-icon">✓</span> Clasifica tus actividades por categorías personalizables
                </div>
                <div class="feature">
                    <span class="feature-icon">✓</span> Sincroniza tu calendario en múltiples dispositivos
                </div>
                <div class="feature">
                    <span class="feature-icon">✓</span> Recibe recordatorios y notificaciones
                </div>
                <p style="text-align: center; margin-top: 30px;">
                    <a href="http://localhost/calendario/" class="button">Ir a mi calendario</a>
                </p>
            </div>
            <div class="footer">
                <p>Este correo fue enviado automáticamente. Por favor, no respondas a este mensaje.</p>
                <p>&copy; ' . date('Y') . ' Calendario Digital. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $mensaje;
}


function generarMensajeRecuperacion($nombre, $resetUrl) {
    $mensaje = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperación de contraseña</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
            }
            .container {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
            }
            .header {
                text-align: center;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .content {
                padding: 20px 0;
            }
            .button {
                display: inline-block;
                background-color: #0071e3;
                color: white;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid #eee;
                font-size: 12px;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Recuperación de contraseña</h2>
            </div>
            <div class="content">
                <p>Hola, <strong>' . $nombre . '</strong>!</p>
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en Calendario Digital. Para continuar con el proceso, haz clic en el siguiente botón:</p>
                <p style="text-align: center;">
                    <a href="' . $resetUrl . '" class="button">Restablecer contraseña</a>
                </p>
                <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                <p>' . $resetUrl . '</p>
                <p>Este enlace caducará en 1 hora por motivos de seguridad.</p>
                <p>Si no has solicitado el cambio de contraseña, simplemente ignora este mensaje y tu contraseña permanecerá sin cambios.</p>
            </div>
            <div class="footer">
                <p>Este correo fue enviado automáticamente. Por favor, no respondas a este mensaje.</p>
                <p>&copy; ' . date('Y') . ' Calendario Digital. Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $mensaje;
}
?>
