 
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calle-web</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/carrusel.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">
    <link rel="stylesheet" href="../assets/css/header_1.css">
</head>

<body>
    <header >
    <?php 
            include '../includes/header_1.php'; 
            echo header_1();
            
        ?>
  </header>
    <section class="screen screen1">
        <?php 
            include '../includes/carrusel.php'; 
            echo carrusel();
        ?>
        <img class="decorative-image slide-top" src="../assets/imag/papa.png" alt="papas">
    </section>
    <button type="button" onclick="window.location.href='Tienda2.php'"  class="button-pedir">VER MENÚ</button>
    <rb></rb>
    <rb></rb>
    <section class="screen screen2"> 
        <img class="decorative-image slide-top" src="../assets/imag/papa2.png" alt="Papas">   
        <div class="scale-up-center titulos-pequeños titulos">
            <h1>¿AÚN NO SABES </h1>
            <div class="titulos-flex">
                <h1>QUÉ</h1>
                <div class="cursiva">
                    <h2>Pedir?</h2>
                </div>
            </div>
             
        </div>
        <img style="margin-right: 138vw; margin-top: 5vw; width: 48%;" src="../assets/imag/carrusel.png" alt="celular">
    </section>

    <section class="screen screen3">
        <img class="decorative-cel slide-right" src="../assets/imag/cel.png" alt="celular">
        <div class=" titulos-pequeños titulos2 scale-down  ">
            <h1>DESCARGAR NUESTRA</h1>
            <div class="titulos-flex">
                <h1>CALLE-</h1>
                <div class="cursiva">
                    <h2>App</h2>
                </div>
            </div>
            <p class="p2">Ya podrás realizar tus pedidos en nuestra app utilizando tu dispositivo móvil android y ios desde donde este, recuerda activar las notificaciones para aprovechar de todas nuestras promociones.</p>
        </div>
    </section>
    <section class="screen screen4"  id="sobre-nosotros">
        <div class=" titulos-pequeños" style="margin-top: -12vw;">
            <div>
                <img style="width: 25vw; margin-left:-2vw;"  src="../assets/imag/logo-color.png" alt="celular">
                <div style=" padding: 140px 170px; margin-top:-5vw;  text-align: center; ">
                    <div class="titulos-flex">
                    <h1>Sobre</h1>
                    <div class="cursiva">
                        <h2>Nosotros</h2>
                    </div>
                </div>
                <p class="p2" style="font-size: 2vw; color:#a1001f ;text-align: center;">Somos tu destino principal para productos de calidad. Explora nuestro catálogo y encuentra lo que necesitas.</p>
            </div>
        </div>
    </section>
    <button type="button" onclick="window.location.href='Tienda2.php'"  style="margin-left: 0vw;"  class="button-descargar">VER CATÁLOGO</button>
    <rb></rb>
    <rb></rb>
    <div style="margin-top: -7vw;">
        <?php 
            include '../includes/footer_1.php'; 
            echo footer_1();
        ?>
        </div>
</body>
</html>
 