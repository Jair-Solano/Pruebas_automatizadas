<?php
session_start(); // Iniciar la sesión al principio
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tu Carrito</title>
  <link rel="stylesheet" href="../assets/css/App_modificado.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/footer_1.css">
  <link rel="stylesheet" href="../assets/css/header_1.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .payment-method {
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .payment-method:hover {
      border-color: #0d6efd;
      background-color: #f8f9fa;
    }

    .payment-method.selected {
      border-color: #0d6efd;
      background-color: #e7f1ff;
    }

    .payment-icon {
      font-size: 1.5rem;
      margin-right: 10px;
    }
  </style>
</head>
<?php 
            include '../includes/header_1.php'; 
            echo header_1();
        ?>
<body>

  <div class="container py-5">
    <div class="titulos-flex" style="margin-left:-63vw; margin-top: 5vw;">
      <h1>Tu carrito</h1>
    </div>
    
    <div class="row">
      <div class="col-md-8">
        <!-- Carrito dinámico -->
        <div class="card mb-3">
          <div class="card-body" id="carrito-contenido">
            <p>Cargando productos...</p>
          </div>
        </div>
        <!-- Botón vaciar -->
        <div class="mb-3 text-end">
          <button class="btn btn-danger" onclick="vaciarCarrito()">
            <i class="bi bi-x-circle"></i> Vaciar carrito
          </button>
        </div>
      </div>

      <div class="col-md-4">
        <!-- Resumen del pedido -->
        <div class="card mb-3">
          <div class="card-body" id="resumen-carrito">
            <h5 class="card-title mb-3">Resumen del Pedido</h5>
            <p class="text-muted">Calculando total...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script Carrito -->
  <script>
    // Actualiza el contador visible del carrito (ejemplo: en el header)
    function actualizarContadorCarrito() {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      const cantidadTotal = carrito.reduce((acc, item) => acc + item.cantidad, 0);
      const contador = document.getElementById("carrito-cantidad");
      if (contador) {
        contador.textContent = cantidadTotal;
      }
    }

    function renderCarrito() {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      const contenedor = document.getElementById("carrito-contenido");

      if (carrito.length === 0) {
        contenedor.innerHTML = "<p class='text-muted'>Tu carrito está vacío.</p>";
        actualizarResumen(0);
        return;
      }

      let html = "";
      let total = 0;

      carrito.forEach((item, i) => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;

        html += `
          <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
            <div class="d-flex align-items-center">
              <button class="btn btn-outline-danger me-3" title="Eliminar producto" onclick="eliminarProducto(${i})">
                <i class="bi bi-trash"></i>
              </button>
              <img src="${item.imagen}" alt="Producto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
              <div>
                <h6 class="mb-1">${item.nombre}</h6>
                <div class="input-group" style="width: 120px;">
                  <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${i}, -1)">-</button>
                  <input type="text" class="form-control text-center" value="${item.cantidad}" disabled>
                  <button class="btn btn-outline-secondary" type="button" onclick="cambiarCantidad(${i}, 1)">+</button>
                </div>
              </div>
            </div>
            <div class="text-end">
              <div>$${item.precio.toFixed(2)}</div>
              <strong>$${(item.precio * item.cantidad).toFixed(2)}</strong>
            </div>
          </div>
        `;
      });

      contenedor.innerHTML = html;
      actualizarResumen(total);
    }

    function cambiarCantidad(index, delta) {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      carrito[index].cantidad += delta;
      if (carrito[index].cantidad < 1) carrito[index].cantidad = 1;
      localStorage.setItem("carrito", JSON.stringify(carrito));
      renderCarrito();
      actualizarContadorCarrito(); // Actualiza contador
    }

    function eliminarProducto(index) {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      if (index < 0 || index >= carrito.length) return;
      carrito.splice(index, 1);
      localStorage.setItem("carrito", JSON.stringify(carrito));
      renderCarrito();
      actualizarContadorCarrito(); // Actualiza contador
    }

    function vaciarCarrito() {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      if (carrito.length === 0) {
        alert("Tu carrito ya está vacío.");
        return;
      }

      if (confirm("¿Estás seguro de que quieres vaciar tu carrito?")) {
        localStorage.removeItem("carrito");
        renderCarrito();
        actualizarContadorCarrito(); // Actualiza contador
      }
    }

    function procesarPago() {
      const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
      if (carrito.length === 0) {
        alert("Tu carrito está vacío. Agrega productos antes de continuar.");
        return false; // Evita continuar
      } else {
        // Aquí podrías agregar lógica para procesar el pago
        actualizarContadorCarrito();
        alert("Gracias por tu compra. Redirigiendo al método de pago...");
        // Redirigir a método de pago
        window.location.href = "../metodo-pago/metodo-pago.php";
        return true;
      }
    }

    function actualizarResumen(total) {
      const resumen = document.getElementById("resumen-carrito");
      resumen.innerHTML = `
        <h5 class="card-title mb-3">Resumen del Pedido</h5>
        <div class="d-flex justify-content-between mb-2">
          <span>Subtotal</span>
          <span>$${total.toFixed(2)}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Envío</span>
          <span class="text-success">Gratis</span>
        </div>
        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
          <span>Total</span>
          <span>$${total.toFixed(2)}</span>
        </div>
        <button class="btn btn-primary w-100 py-2" onclick="procesarPago()">
          PROCESAR PAGO
        </button>
        <div class="mt-3 text-center">
          <small class="text-muted">Al proceder con el pago, aceptas nuestros <a href="#">Términos y Condiciones</a></small>
        </div>
      `;
    }

    // Ejecutar funciones al cargar la página
    document.addEventListener("DOMContentLoaded", () => {
      actualizarContadorCarrito();
      renderCarrito();
    });

    document.addEventListener("DOMContentLoaded", () => {
      const productos = JSON.parse(localStorage.getItem("carrito")) || [];
      renderProductosTabla(productos);
    });
  </script>
  <?php 
            include '../includes/footer_1.php'; 
            echo footer_1();
        ?>
</body>

</html>
