<?php
require_once "../utils/mail.php";

// Esta página es solo para probar la configuración de correo
// Se debe eliminar o proteger en un entorno de producción

echo '<h1>Prueba de Envío de Correo</h1>';

if (isset($_POST['enviar'])) {
    $destinatario = $_POST['destinatario'];
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];
    
    $resultado = enviarCorreo($destinatario, $asunto, $mensaje);
    
    if ($resultado) {
        echo '<div style="color: green; padding: 10px; border: 1px solid green; margin: 10px 0;">El correo se ha enviado correctamente.</div>';
    } else {
        echo '<div style="color: red; padding: 10px; border: 1px solid red; margin: 10px 0;">Error al enviar el correo. Revisa la configuración del servidor Mercury.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Correo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            background-color: #f5f5f7;
            padding: 20px;
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            height: 150px;
        }
        button {
            background-color: #0071e3;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005bb5;
        }
        .info {
            background-color: #e6f7ff;
            border-left: 4px solid #1890ff;
            padding: 10px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="info">
        <p><strong>Importante:</strong> Esta herramienta es solo para probar la configuración de Mercury Mail en XAMPP.</p>
        <p>Para que funcione correctamente:</p>
        <ol>
            <li>Asegúrate que Mercury Mail está activo en XAMPP</li>
            <li>Verifica que el servicio de correo está correctamente configurado en php.ini</li>
            <li>El remitente será siempre info@localhost</li>
        </ol>
    </div>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="destinatario">Destinatario:</label>
            <input type="email" id="destinatario" name="destinatario" required>
        </div>
        
        <div class="form-group">
            <label for="asunto">Asunto:</label>
            <input type="text" id="asunto" name="asunto" required>
        </div>
        
        <div class="form-group">
            <label for="mensaje">Mensaje (HTML permitido):</label>
            <textarea id="mensaje" name="mensaje" required></textarea>
        </div>
        
        <button type="submit" name="enviar">Enviar correo de prueba</button>
    </form>
</body>
</html>
