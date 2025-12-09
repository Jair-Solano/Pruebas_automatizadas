<?php
function footer_1() {
    return '
    <footer id="footer" class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="../assets/imag/logo-blanco.png" style="width: 13vw; margin-left: -4vw;" alt="Logo Syry">
            </div>
            <div class="footer-section links">
                <h4>Enlacess Rápidos</h4>
                <ul>
                    <li><a href="catalogo.php">Inicio</a></li>
                    <li><a href="#productos">Productos</a></li>  
                </ul>
            </div>
            <div class="footer-section contact">
                <h4>Contáctanos</h4>
                <p><i class="fas fa-map-marker-alt"></i>Calle Avenida Norte, Natá</p>
                <p><i class="fas fa-phone"></i> +507 6XX-XXXX</p>
                <p><i class="fas fa-envelope"></i> calleweb@gmail.com</p>
            </div>
            <div class="footer-section social">
                <h4>Síguenos</h4>
                <div class="social-icons">
                    <a href="https://www.instagram.com/elcallejonnata?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener">Instagram</a>
                </div>
            </div>
        </div>
        <div class="footer-copy">
            &copy; '.date('Y').' CalleWeb. Todos los derechos reservados.
        </div>
    </footer>
    ';
}
?>