<?php
// Iniciar la sesión si no está iniciada. Esto es crucial para poder acceder a las variables de sesión.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión.
$_SESSION = array();

// Si se desea destruir la cookie de sesión, también es necesario eliminarla.
// Nota: Esto destruirá la sesión, y no solo los datos de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir al usuario a la página de inicio de sesión o a la página principal.
// Asegúrate de que la ruta sea correcta para tu estructura de archivos.
header("Location: ../login/login.php"); // Redirige a la página de login
exit(); // Es importante usar exit() después de una redirección para asegurar que el script se detenga
?>
