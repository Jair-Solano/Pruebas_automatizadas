<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

$usuario_logueado = $_SESSION['usuario'] ?? 'Invitado';
$cedula_logueada = $_SESSION['cedula'] ?? 'N/A';
$correo_logueado = $_SESSION['correo'] ?? 'N/A';
$user_id_logueado = $_SESSION['user_id'] ?? null;

$direccion_default = "Calle Avenida Norte, Natá";
if ($user_id_logueado) {
    try {
        $stmtUserAddr = $pdo->prepare("SELECT direccion FROM usuarios WHERE ID = :user_id");
        $stmtUserAddr->bindParam(':user_id', $user_id_logueado, PDO::PARAM_INT);
        $stmtUserAddr->execute();
        $userDataAddr = $stmtUserAddr->fetch(PDO::FETCH_ASSOC);
        if ($userDataAddr && !empty($userDataAddr['direccion'])) {
            $direccion_default = htmlspecialchars($userDataAddr['direccion']);
        }
    } catch (PDOException $e) {
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/header_1.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>El Callejon - Método de Pago</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            background: #fdfcfc;
            color: #333;
            min-height: 100vh;
        }
        .app-header {
            background: #a3080d;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .logo { height: 40px; margin-right: 10px; }
        .app-container {
            display: flex; flex-direction: column; align-items: center; min-height: 100vh; width: 100%; justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 97%; max-width: 1050px;
            margin: 40px auto;
            padding: 32px 36px;
        }
        .card-title { color: #a3080d; margin-bottom: 24px; font-size: 1.4rem; font-weight: bold; }
        .checkout-grid {
            display: flex; gap: 48px; justify-content: space-between; align-items: flex-start;
        }
        .form-section {
            flex: 1 1 320px; min-width: 260px; max-width: 350px;
            display: flex; flex-direction: column; gap: 16px;
        }
        .form-section label { font-weight: bold; margin-bottom: 4px; display: block; }
        .form-section input[type="text"],
        .form-section input[type="email"],
        .form-section select {
            margin-bottom: 8px; margin-top: 4px; width: 100%;
            padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px;
        }
        .campo-readonly {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }
        .payment-options {
            margin: 4px 0 8px 0; display: flex; flex-wrap: wrap; gap: 12px;
        }
        .payment-button {
            background: #fff; border: 2px solid #a3080d; color: #a3080d;
            border-radius: 8px; padding: 8px 24px; font-size: 1rem; cursor: pointer;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }
        .payment-button.selected, .payment-button:hover {
            background: #a3080d; color: #fff;
        }
        .resumen-section {
            flex: 1 1 420px; min-width: 340px; max-width: 540px;
            display: flex; flex-direction: column; gap: 8px;
            background: #faf7f7; border-radius: 10px;
            padding: 18px 20px 20px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            height: fit-content; align-self: flex-start;
        }
        .resumen-item {
            display: flex; justify-content: space-between; font-size: 1rem; margin-bottom: 7px;
        }
        .resumen-total {
            font-weight: bold; font-size: 1.1rem; border-top: 1px solid #d9d9d9; padding-top: 7px; margin-top: 10px;
        }
        .btn-pedir {
            margin-top: 24px; background: #a3080d; color: #fff; border: none;
            border-radius: 8px; padding: 10px 0; font-size: 1.1rem; cursor: pointer;
            transition: background 0.2s; width: 100%;
        }
        .btn-pedir:hover { background: #7e0509; }
        @media (max-width: 1100px) {
            .checkout-grid { gap: 24px; }
        }
        @media (max-width: 800px) {
            .checkout-grid { flex-direction: column; gap: 24px; }
            .resumen-section { align-self: stretch; width: 100%; min-width: unset; margin-top: 16px; }
            .form-section { width: 100%; min-width: unset; }
            .card { padding: 18px; }
        }
        @media (max-width: 500px) {
            .card { padding: 8px; }
        }

        .alert-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<header>
    <?php
        include '../includes/header_1.php';
        echo header_1();
    ?>
</header>
<body>
    <div class="titulos-flex" style="margin-left:-53vw; margin-top: 10vw; margin-bottom: -3vw;">
        <h1 style="font-size: 40px;  font-weight: lighter;">Método de Pago</h1>
    </div>
    <div class="app-container">
        <main class="card">
            <div id="stock-warning-message" class="alert-message" style="display:none;"></div>

            <div class="checkout-grid">
                <div class="form-section">
                    <div>
                        <label for="tipo-entrega">Tipo de entrega:</label>
                        <label id="tipo-entrega">Retiro</label>
                    </div>
                    <div>
                        <label for="usuario-nombre">Cliente:</label>
                        <input type="text" id="usuario-nombre" name="usuario-nombre" value="<?php echo htmlspecialchars($usuario_logueado); ?>" readonly class="campo-readonly">
                    </div>
                    <div>
                        <label for="usuario-correo">Correo:</label>
                        <input type="email" id="usuario-correo" name="usuario-correo" value="<?php echo htmlspecialchars($correo_logueado); ?>" readonly class="campo-readonly">
                    </div>
                    <div>
                        <label for="direccion">Ubicación:</label>
                        <input type="text" id="direccion" name="direccion" placeholder="Calle Avenida Norte, Natá" value="<?php echo htmlspecialchars($direccion_default); ?>">
                    </div>
                    <div>
                        <label>Método de pago</label>
                        <div class="payment-options">
                            <button type="button" class="payment-button" data-metodo="efectivo">Efectivo</button>
                            <button type="button" class="payment-button" data-metodo="yappy">Yappy</button>
                        </div>
                    </div>
                </div>
                <div class="resumen-section">
                    <div class="resumen-item" style="flex-direction:column;align-items:flex-start;gap:6px;">
                        <span style="font-weight:bold;">Productos:</span>
                        <table id="tabla-productos" style="width:100%;margin-bottom:10px;font-size:1rem;table-layout:fixed;">
                            <thead>
                                <tr style="color:#a3080d;background:#f3f3f3;">
                                    <th style="text-align:left;padding:4px 6px;width:40%;word-break:break-word;">Producto</th>
                                    <th style="text-align:right;padding:4px 6px;width:20%;">Cantidad</th>
                                    <th style="text-align:right;padding:4px 6px;width:20%;">Precio</th>
                                    <th style="text-align:right;padding:4px 6px;width:20%;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="productos-listado">
                                </tbody>
                        </table>
                    </div>
                    <div class="resumen-item">
                        <span>Envío:</span>
                        <span id="resumen-envio">$0.00</span>
                    </div>
                    <div class="resumen-item">
                        <span>ITBMS:</span>
                        <span id="resumen-itbms">$0.00</span>
                    </div>
                    <div class="resumen-item resumen-total">
                        <span>Total:</span>
                        <span id="resumen-total">$0.00</span>
                    </div>
                    <form id="form-factura" action="factura.php" method="post" style="width:100%;margin-top:10px;">
                        <input type="hidden" name="productos_encoded" id="input-productos-encoded">
                        <input type="hidden" name="envio" id="input-envio">
                        <input type="hidden" name="itbms" id="input-itbms">
                        <input type="hidden" name="total" id="input-total">
                        <input type="hidden" name="subtotal" id="input-subtotal">
                        <input type="hidden" name="cliente" id="input-cliente">
                        <input type="hidden" name="cliente_correo" id="input-cliente-correo">
                        <input type="hidden" name="direccion" id="input-direccion">
                        <input type="hidden" name="metodo_pago" id="input-metodo-pago">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id_logueado); ?>">
                        <button type="submit" class="btn-pedir" id="btn-pedir">Pedir</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="qr-generator.js"></script>
    <script>
        const buttons = document.querySelectorAll('.payment-button');
        let metodoSeleccionado = localStorage.getItem('metodoPago') || '';
        const inputMetodoPago = document.getElementById('input-metodo-pago');

        function marcarSeleccion() {
            buttons.forEach(btn => {
                if (btn.dataset.metodo === metodoSeleccionado) {
                    btn.classList.add('selected');
                } else {
                    btn.classList.remove('selected');
                }
            });
            inputMetodoPago.value = metodoSeleccionado;
        }

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                metodoSeleccionado = btn.dataset.metodo;
                localStorage.setItem('metodoPago', metodoSeleccionado);
                marcarSeleccion();

                switch (metodoSeleccionado) {
                    case 'efectivo':
                        console.log('Método de pago seleccionado: Efectivo');
                        if (window.hideYappyQRDialog) {
                            window.hideYappyQRDialog();
                        }
                        break;
                    case 'yappy':
                        console.log('Método de pago seleccionado: Yappy');
                        const totalFinal = parseFloat(document.getElementById('resumen-total').textContent.replace('$', ''));
                        if (window.showYappyQRDialog) {
                            window.showYappyQRDialog(`https://yappy.pagoseguro.com/ElCallejon?amount=${totalFinal.toFixed(2)}`);
                        } else {
                            alert('Yappy QR generado. (No se pudo mostrar la ventana emergente)');
                        }
                        break;
                    default:
                        console.log('Ningún método de pago seleccionado o desconocido.');
                        if (window.hideYappyQRDialog) {
                            window.hideYappyQRDialog();
                        }
                        break;
                }
            });
        });

        let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
        const stockWarningDiv = document.getElementById('stock-warning-message');
        const btnPedir = document.getElementById('btn-pedir');
        let hayStockInsuficiente = false;

        async function loadAndRenderCarrito() {
            if (carrito.length === 0) {
                document.getElementById('productos-listado').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:10px;">Tu carrito está vacío.</td></tr>';
                btnPedir.disabled = true;
                btnPedir.textContent = 'Carrito Vacío';
                stockWarningDiv.style.display = 'none';
                recalcularTotales([]);
                return;
            }

            hayStockInsuficiente = false;
            let productosAjustados = [];

            for (const prod of carrito) {
                const stockReal = prod.stockMaximo;
                if (prod.cantidad > stockReal) {
                    prod.cantidad = stockReal;
                    hayStockInsuficiente = true;
                    stockWarningDiv.textContent = `Hemos ajustado la cantidad de "${prod.nombre}" a ${stockReal} unidades. No hay suficiente stock disponible.`;
                    stockWarningDiv.style.display = 'block';
                    if(stockReal === 0){
                        stockWarningDiv.textContent = `El producto "${prod.nombre}" no tiene stock y se ha eliminado del carrito.`;
                        prod.cantidad = 0;
                    }
                }
                productosAjustados.push(prod);
            }

            productosAjustados = productosAjustados.filter(p => p.cantidad > 0);
            localStorage.setItem("carrito", JSON.stringify(productosAjustados));

            const tbody = document.getElementById('productos-listado');
            tbody.innerHTML = '';
            
            if (productosAjustados.length === 0) {
                document.getElementById('productos-listado').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:10px;">Tu carrito está vacío después de la verificación de stock.</td></tr>';
                btnPedir.disabled = true;
                btnPedir.textContent = 'Carrito Vacío';
                stockWarningDiv.style.display = 'none';
                recalcularTotales(productosAjustados);
                return;
            }

            productosAjustados.forEach(prod => {
                const subtotalItem = prod.precio * prod.cantidad;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td style="text-align:left;padding:4px 6px;word-break:break-word;">${prod.nombre}</td>
                    <td style="text-align:right;padding:4px 6px;">${prod.cantidad}</td>
                    <td style="text-align:right;padding:4px 6px;">$${prod.precio.toFixed(2)}</td>
                    <td style="text-align:right;padding:4px 6px;">$${subtotalItem.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });

            recalcularTotales(productosAjustados);
            btnPedir.disabled = hayStockInsuficiente;
            if (hayStockInsuficiente && stockWarningDiv.style.display !== 'block') {
                stockWarningDiv.textContent = "Algunos productos en tu carrito tienen stock limitado. Por favor, revisa las cantidades.";
                stockWarningDiv.style.display = 'block';
            } else if (!hayStockInsuficiente) {
                stockWarningDiv.style.display = 'none';
            }
        }

        function recalcularTotales(currentCarrito) {
            let subtotalProductos = 0;
            currentCarrito.forEach(prod => {
                subtotalProductos += prod.precio * prod.cantidad;
            });

            const envio = 0.00;
            const itbms = +(subtotalProductos * 0.07).toFixed(2);
            const total = +(subtotalProductos + envio + itbms).toFixed(2);

            document.getElementById('resumen-envio').textContent = `$${envio.toFixed(2)}`;
            document.getElementById('resumen-itbms').textContent = `$${itbms.toFixed(2)}`;
            document.getElementById('resumen-total').textContent = `$${total.toFixed(2)}`;

            document.getElementById('input-productos-encoded').value = JSON.stringify(currentCarrito);
            document.getElementById('input-envio').value = envio.toFixed(2);
            document.getElementById('input-itbms').value = itbms.toFixed(2);
            document.getElementById('input-total').value = total.toFixed(2);
            document.getElementById('input-subtotal').value = subtotalProductos.toFixed(2);

            if (metodoSeleccionado === 'yappy' && window.updateYappyQR) {
                window.updateYappyQR(`https://yappy.pagoseguro.com/ElCallejon?amount=${total.toFixed(2)}`);
            }
        }

        const formFactura = document.getElementById('form-factura');
        formFactura.addEventListener('submit', function(e) {
            if (!metodoSeleccionado) {
                e.preventDefault();
                alert('Por favor, selecciona un método de pago.');
                return false;
            }
            if (hayStockInsuficiente) {
                e.preventDefault();
                alert('No se puede finalizar el pedido debido a un stock insuficiente en algunos productos. Por favor, revisa tu carrito.');
                return false;
            }
            document.getElementById('input-cliente').value = document.getElementById('usuario-nombre').value;
            document.getElementById('input-cliente-correo').value = document.getElementById('usuario-correo').value;
            document.getElementById('input-direccion').value = document.getElementById('direccion').value;
            document.getElementById('input-metodo-pago').value = metodoSeleccionado;
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadAndRenderCarrito();
            marcarSeleccion();
        });
    </script>
    <?php
        include '../includes/footer_1.php';
        echo footer_1();
    ?>
</body>
</html>