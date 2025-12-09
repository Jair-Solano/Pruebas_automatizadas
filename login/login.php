<?php

session_start(); 
session_regenerate_id(true);

$message = $_GET['message'] ?? '';
$displayMessage = '';

if (!empty($message)) {
    switch ($message) {
        case 'ok':
            $displayMessage = 'Por favor, revisa tu correo';
            break;
        case 'success_password':
            $displayMessage = 'Inicia sesión con tu nueva contraseña';
            break;
        case 'error':
        case 'invalid_data':
        case 'error_password':
            $displayMessage = 'Error de contraseña';
            break;
        case 'fallido':
            $displayMessage = 'Inicio de sesión fallido';
            break;
        case 'error_user_domain':
            $displayMessage = 'Inicio de sesión fallido: formato de correo electrónico inválido';
            break;
        case 'email_sent':
            $displayMessage = 'Si el correo electrónico está registrado en nuestro sistema, hemos enviado un enlace para restablecer tu contraseña. Por favor, revisa tu bandeja de entrada (y spam).';
            break;
        case 'email_sent_error':
            $displayMessage = 'Hubo un problema al intentar enviar el correo. Por favor, inténtalo de nuevo más tarde.';
            break;
        case 'success_password_change':
            $displayMessage = 'Contraseña cambiada exitosamente';
            break;
        case 'empty_email':
             $displayMessage = 'Por favor, ingresa tu correo electrónico.';
             break;
        case 'error_general':
            $displayMessage = 'Ocurrió un error inesperado. Por favor, inténtalo de nuevo.';
            break;
        case 'error_user':
            $displayMessage = 'Usuario no encontrado.';
            break;
        case 'not_logged_in': // Mensaje cuando se intenta acceder a una página protegida sin login
            $displayMessage = 'Debes iniciar sesión para acceder a esta página.';
            break;
        default:
            $displayMessage = 'Mensaje desconocido.';
            break;
    }

    
    if (in_array($message, ['success_password', 'error', 'invalid_data', 'error_password', 'fallido', 'error_user_domain', 'email_sent', 'email_sent_error', 'success_password_change', 'empty_email', 'error_general', 'error_user', 'not_logged_in'])) {
        echo "<script>alert('" . htmlspecialchars($displayMessage) . "');";
       
        if (in_array($message, ['error_user_domain', 'email_sent', 'email_sent_error', 'success_password_change'])) {
            echo "window.location.href = 'login.php';";
        }
        echo "</script>";
    } else {
        
        echo '<div class="login-alert" style="color: red; margin-top: 10px;">' . htmlspecialchars($displayMessage) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calle-Web - Iniciar Sesión</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">
</head>
<body>
    <div class="login-bg">
        <div class="login-card">
            <h1 class="login-title">Iniciar sesión en </h1>
            <h2 class="login-title-cursive">El Callejón</h2>
            <form method="POST" action="validacion.php" class="login-form">
                <div class="input-group">
                    <input type="text" id="user" name="user" placeholder="@nombre" required class="input-field">
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Contraseña" required class="input-field">
                </div>
                <button class="login-btn" type="submit">Iniciar sesión</button>
            </form>
            <div class="link-text" style="margin-top: 12px;">
            <a href="recuperar_contrasena.php" style="color:brown;" >¿Olvidaste tu contraseña?</a>
        </div>
        </div>
           <div class="login-logo">
                <img src="../assets/imag/logo-color.png" alt="Logo de El Callejón">
            </div>
    </div>
    <?php 
            include '../includes/footer_1.php'; 
            echo footer_1();
        ?>
</body>
</html>