<?php
 

include '../db/conexion.php'; 
$stmtCarrusel = $pdo->query("SELECT * FROM productos WHERE en_carrusel=1 ORDER BY ID ASC");
$carrusel = $stmtCarrusel->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados de una vez

// Obtener todos los productos para la sección de productos
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

$sql = "SELECT * FROM productos WHERE 1";
$parametros_pdo = []; // Array para los parámetros de PDO
// $tipos ya no es necesario para PDO, PDO detecta los tipos automáticamente

if ($categoria_filtro && in_array($categoria_filtro, ['combo', 'batido', 'refresco'])) {
    $sql .= " AND categoria = :categoria"; // Usar marcador de posición con nombre para PDO
    $parametros_pdo[':categoria'] = $categoria_filtro;
}

if ($busqueda !== '') {
    $sql .= " AND (nombre LIKE :busqueda1 OR descripcion LIKE :busqueda2)"; // Marcadores de posición diferentes o usar el mismo si se vincula dos veces
    $like = '%' . $busqueda . '%';
    $parametros_pdo[':busqueda1'] = $like;
    $parametros_pdo[':busqueda2'] = $like;
}

$sql .= " ORDER BY ID DESC";

// Siempre preparar la consulta si hay parámetros, o si es una buena práctica.
// PDO maneja los parámetros de forma segura a través de execute.
$stmtProductos = $pdo->prepare($sql);
$stmtProductos->execute($parametros_pdo); // Pasar el array de parámetros directamente a execute()
$productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los resultados de una vez

// NOTA: Para el loop en el HTML, necesitarás iterar sobre `$productos` directamente,
// ya que `$productos` ahora es un array de resultados, no un objeto de resultado MySQLi.
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo</title>
    <link rel="stylesheet" href="../assets/css/style1.css">
    <link rel="stylesheet" href="../assets/css/product-card.css">
    <link rel="stylesheet" href="../assets/css/carusel.css">
    <link rel="stylesheet" href="../assets/css/header_1.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <header >
        <?php 
            include '../includes/header_1.php'; 
            echo header_1();
            
        ?>
    </header>
    <section style="position: fixed; top: 1em; left: 50%; margin-top:5vw; transform: translateX(-50%); z-index: 1000;">
        <div style="background-color: #e7aa00ff; border-radius: 20px; display: flex; padding: 10px 20px;">
            
            <form method="GET" style="display: flex;  gap: 1em; align-items: center; justify-content: center; margin: 0;">
                <label for="categoria" style="font-weight: bold; color: white;">Categoría:</label>
                
                <select name="categoria" id="categoria" style="padding: 0.5em; border: 1px solid #ccc; color: #7c7c7cff; border-radius: 10px;">
                    <option value="">Todas</option>
                    <option value="combo" <?= $categoria_filtro === 'combo' ? 'selected' : '' ?>>Combo</option>
                    <option value="batido" <?= $categoria_filtro === 'batido' ? 'selected' : '' ?>>Batido</option>
                    <option value="refresco" <?= $categoria_filtro === 'refresco' ? 'selected' : '' ?>>Refresco</option>
                </select>

                <input type="text" name="buscar" placeholder="Buscar producto..." value="<?= htmlspecialchars($busqueda) ?>"
                    style="padding: 0.5em; border-radius: 10px; border: 1px solid #ccc; min-width: 200px;">

                <button type="submit"
                    style="padding: 0.5em 1.5em; border-radius: 10px; border: none; background: white; color: #e7aa00ff; font-weight: bold;">Buscar</button>

                <a href="../carrito/carrito de compras.php" style="color: white; background-color: #e7aa00ff; padding: 10px 24px; border-radius: 10px; font-weight: bold; text-decoration: none; display: flex; align-items: center; white-space: nowrap;">
                    <i class="bi bi-cart-fill" style="margin-right: 5px;"></i>
                    Ver carrito (<span id="carrito-cantidad">0</span>)
                </a>
            </form>

        </div>
    </section>






    <section class="hero">
        <div class="overlay"></div>
        <div class="combo-carousel" style=" margin-top:14vw;">
            <div class="carousel-track">
                <?php $i = 0;
                foreach ($carrusel as $combo): // Iterar directamente sobre el array $carrusel ?>
                    <div class="carousel-slide<?= $i === 0 ? ' active' : '' ?>">
                        <div class="carousel-content">
                            <div class="carousel-text">
                                <h2><?= htmlspecialchars($combo['nombre']) ?></h2>
                                <p><?= htmlspecialchars($combo['descripcion']) ?></p>
                                <span class="product-price">$<?= number_format($combo['precio'], 2) ?></span>
                                <button class="combo-btn">PEDIR COMBO</button>
                            </div>
                            <div class="carousel-img-container">
                                <img src="../assets/imagenes/<?= htmlspecialchars($combo['imagen']) ?>"
                                    alt="<?= htmlspecialchars($combo['nombre']) ?>" class="carousel-img" />
                            </div>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
            <div class="carousel-indicators">
                <?php for ($j = 0; $j < $i; $j++): ?>
                    <span class="indicator<?= $j === 0 ? ' active' : '' ?>"></span>
                <?php endfor; ?>
            </div>
            <button class="carousel-arrow left">&#10094;</button>
            <button class="carousel-arrow right">&#10095;</button>
        </div>
    </section>

    <main>
        <h2 class="section-title">Lo más vendido</h2>
        <section class="product-list">
            <?php if (empty($productos)): // Verificar si el array está vacío ?>
                <p style="text-align:center; margin-top: 20px; font-size: 1.2em; color: #a1001f;">No se encontraron productos.</p>
            <?php endif; ?>

            <?php foreach ($productos as $p): // Iterar directamente sobre el array $productos ?>
    <div class="product-card" data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
        data-precio="<?= number_format($p['precio'], 2) ?>" data-desc="<?= htmlspecialchars($p['descripcion']) ?>"
        data-img="../assets/imagenes/<?= htmlspecialchars($p['imagen']) ?>"
        data-rating="<?= isset($p['calificacion']) ? (int) $p['calificacion'] : 5 ?>"
        data-categoria="<?= htmlspecialchars($p['categoria']) ?>"
        data-id="<?= htmlspecialchars($p['ID']) ?>" data-stock="<?= htmlspecialchars($p['stock']) ?>"> <div class="product-card-img-container">
            <img src="../assets/imagenes/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"
                class="product-card-img" />
        </div>
        <div class="product-info">
            <h3 class="product-name"><?= htmlspecialchars($p['nombre']) ?></h3>
            <p class="product-category"><?= ucfirst(htmlspecialchars($p['categoria'])) ?></p>
            <span class="product-price">$<?= number_format($p['precio'], 2) ?></span>
            <div class="product-rating">
                <?php
                $rating = 5;
                // Si tu columna 'calificacion' existe y tiene datos, podrías usar:
                // $rating = isset($p['calificacion']) ? (int) $p['calificacion'] : 5;
                for ($k = 1; $k <= 5; $k++) {
                    $off = $k > $rating ? ' off' : '';
                    echo '<img src="../assets/imag/muslito.png" alt="Muslito" class="muslito' . $off . '" />';
                }
                ?>
            </div>
            <div class="product-actions">
                <button type="button" class="add-to-cart-btn"
                    onclick="agregarAlCarrito(<?= $p['ID'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= $p['precio'] ?>, '../assets/imagenes/<?= htmlspecialchars($p['imagen']) ?>', <?= $p['stock'] ?>)"> Agregar al carrito
                </button>
            </div>

        </div>
    </div>
<?php endforeach; ?>
        </section>

        <div id="product-modal" class="product-modal-overlay" style="display:none;">
            <div class="product-modal">
                <button class="product-modal-close">&times;</button>
                <img src=" " alt="Imagen producto" class="product-modal-img" />
                <h3 class="product-modal-name"></h3>
                <span class="product-modal-price"></span>
                <p class="product-modal-category"></p>
                <p class="product-modal-desc"></p>
                <div class="product-modal-rating"></div>
            </div>
        </div>
    </main>

    <footer>
        </footer>

    <script>
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.indicator');
        const leftArrow = document.querySelector('.carousel-arrow.left');
        const rightArrow = document.querySelector('.carousel-arrow.right');
        let currentSlide = 0;
        function showSlide(idx) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === idx);
                if (indicators[i]) indicators[i].classList.toggle('active', i === idx);
            });
            currentSlide = idx;
        }
        function nextSlide() {
            let idx = (currentSlide + 1) % slides.length;
            showSlide(idx);
        }
        function prevSlide() {
            let idx = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(idx);
        }
        if (rightArrow) rightArrow.onclick = nextSlide;
        if (leftArrow) leftArrow.onclick = prevSlide;
        indicators.forEach((ind, i) => {
            ind.onclick = () => showSlide(i);
        });
    </script>

    <script>
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function () {
                const modal = document.getElementById('product-modal');
                modal.style.display = 'flex';
                modal.querySelector('.product-modal-img').src = this.dataset.img;
                modal.querySelector('.product-modal-name').textContent = this.dataset.nombre;
                modal.querySelector('.product-modal-price').textContent = '$' + this.dataset.precio;
                modal.querySelector('.product-modal-desc').textContent = this.dataset.desc;
                modal.querySelector('.product-modal-category').textContent =
                    this.dataset.categoria.charAt(0).toUpperCase() + this.dataset.categoria.slice(1);

                const rating = parseInt(this.dataset.rating || '5');
                let muslitos = '';
                for (let i = 1; i <= 5; i++) {
                    muslitos += `<img src="../assets/imag/muslito.png" alt="Muslito" style="width:28px;opacity:${i <= rating ? 1 : 0.3};margin:0 2px;">`;
                }
                modal.querySelector('.product-modal-rating').innerHTML = muslitos;
            });
        });
        document.querySelector('.product-modal-close').onclick = function () {
            document.getElementById('product-modal').style.display = 'none';
        };
        document.getElementById('product-modal').onclick = function (e) {
            if (e.target === this) this.style.display = 'none';
        };
    </script>

    <script>
        function agregarAlCarrito(id, nombre, precio, imagen, stockDisponible) { // Añadir stockDisponible
              const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
              const index = carrito.findIndex(p => p.id === id);

              let cantidadActualEnCarrito = 0;
              if (index !== -1) {
                  cantidadActualEnCarrito = carrito[index].cantidad;
              }

              if (cantidadActualEnCarrito + 1 > stockDisponible) { // Verificar si al añadir uno más se excede el stock
                  alert(`No hay suficiente stock de "${nombre}". Solo quedan ${stockDisponible} unidades.`);
                  return; // Detener la función si no hay stock
              }

              if (index !== -1) {
                  carrito[index].cantidad += 1;
              } else {
                  // También podrías almacenar el stock original aquí para referencia si lo necesitas
                  carrito.push({ id, nombre, precio, cantidad: 1, imagen, stockMaximo: stockDisponible });
              }

              localStorage.setItem("carrito", JSON.stringify(carrito));
              actualizarContadorCarrito();
              alert(`Producto "${nombre}" agregado al carrito.`);
          }

        function actualizarContadorCarrito() {
            const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
            const cantidadTotal = carrito.reduce((acc, item) => acc + item.cantidad, 0);
            const contador = document.getElementById("carrito-cantidad");
            if (contador) {
                contador.textContent = cantidadTotal;
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            actualizarContadorCarrito();
        });
    </script>


    <script>
        function actualizarContadorCarritoHeader() {
            const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
            const cantidadTotal = carrito.reduce((acc, item) => acc + item.cantidad, 0);
            const contador = document.getElementById("carrito-cantidad");
            if (contador) {
                contador.textContent = cantidadTotal;
            }
        }

        document.addEventListener("DOMContentLoaded", actualizarContadorCarritoHeader);

        window.addEventListener('storage', (event) => {
            if (event.key === 'carrito') {
                actualizarContadorCarritoHeader();
            }
        });

    </script>
        <?php 
            include '../includes/footer_1.php'; 
            echo footer_1();
        ?>
    </body>

</html>