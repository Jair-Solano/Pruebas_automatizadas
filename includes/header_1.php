<?php
// Asegúrate de que la sesión esté iniciada para acceder a las variables $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function header_1(){
    $html = '
    <header>
        <a href="../landing/landing.php">
            <img src="../assets/imag/logo-blanco.png" alt="logo">
        </a>
        <nav aria-label="Menú principal">
            <ul>
                <li><a href="#sobre-nosotros">SOBRE NOSOTROS</a></li>
                <li><a href="../landing/Tienda2.php">MENÚ</a></li>
                <li><a href="https://maps.app.goo.gl/mQ2iikptHcSNzuQP9" target="_blank" rel="noopener noreferrer">UBICACIÓN</a></li>
                <li><a href="#footer"/contacto">CONTACTO</a></li>';

    // Condicionalmente agrega el botón "Panel de Control" si el rol del usuario es 0
    if (isset($_SESSION['rol'])) {
        if ($_SESSION['rol'] == 0) { // Si el rol es 0 (administrador)
            $html .= '<li><a href="../includes/panel_productos.php" class="panel-control-button">Panel de Control</a></li>';
        }
    }

    // Agrega el botón de Iniciar Sesión/Cerrar Sesión
    if (isset($_SESSION['user_id'])) { // Asumiendo que 'user_id' se establece al iniciar sesión
        $html .= '
                <li><a href="../includes/cerrar_sesion.php" class="cerrar-sesion-button">Cerrar Sesión</a></li>';
    } else {
        $html .= '
                <li><a href="../includes/cerrar_sesion.php" class="cerrar-sesion-button">Cerrar Sesión</a></li>';
    }

    $html .= '
            </ul>
        </nav>
    </header>';
    return $html;
}
?>
