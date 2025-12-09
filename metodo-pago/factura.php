<?php

session_start();

require_once '../db/conexion.php';

$orderId = rand(1000000, 9999999);
$cliente_display = 'Cliente Desconocido';
$correo_cliente_display = 'N/A';
$direccion_display = 'N/A';
$total_display = 0.00;
$itbms_display = 0.00;
$envio_display = 0.00;
$subtotal_display = 0.00;
$productos_display = [];
$metodo_pago_display = 'No especificado';
$fecha_db_format = date('Y-m-d H:i:s');
$fecha_display_format = date('d M Y, h:i A');
$user_id_factura = $_SESSION['user_id'] ?? null;

$message = '';
$is_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id_factura = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $productos_json_raw = filter_input(INPUT_POST, 'productos_encoded', FILTER_UNSAFE_RAW);
        $envio_display = filter_input(INPUT_POST, 'envio', FILTER_VALIDATE_FLOAT);
        $itbms_display = filter_input(INPUT_POST, 'itbms', FILTER_VALIDATE_FLOAT);
        $total_display = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_FLOAT);
        $subtotal_display = filter_input(INPUT_POST, 'subtotal', FILTER_VALIDATE_FLOAT);
        $cliente_display = filter_input(INPUT_POST, 'cliente', FILTER_SANITIZE_STRING);
        $correo_cliente_display = filter_input(INPUT_POST, 'cliente_correo', FILTER_SANITIZE_EMAIL);
        $direccion_display = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
        $metodo_pago_display = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_STRING);

        if (!$user_id_factura || empty($productos_json_raw) || !$total_display) {
            throw new Exception("Datos de pedido incompletos o inválidos.");
        }

        $productos_display = json_decode($productos_json_raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al decodificar los productos JSON: " . json_last_error_msg());
        }
        if (empty($productos_display) || !is_array($productos_display)) {
            throw new Exception("El carrito de compras está vacío o es inválido.");
        }

        $pdo->beginTransaction();
        $stmt_check_stock = $pdo->prepare("SELECT stock FROM productos WHERE id = :id_producto FOR UPDATE");

        foreach ($productos_display as $prod) {
            $productId = $prod['id'] ?? null;
            $cantidadPedida = $prod['cantidad'] ?? 0;

            if (!$productId || $cantidadPedida <= 0) {
                if ($cantidadPedida == 0) continue; 
                throw new Exception("Producto inválido en el carrito: ID no encontrado o cantidad no válida.");
            }

            $stmt_check_stock->bindParam(':id_producto', $productId, PDO::PARAM_INT);
            $stmt_check_stock->execute();
            $currentStock = $stmt_check_stock->fetchColumn();

            if ($currentStock === false || $currentStock < $cantidadPedida) {
                throw new Exception("Stock insuficiente para el producto: " . htmlspecialchars($prod['nombre']) . ". Disponible: " . ($currentStock === false ? '0' : $currentStock) . ", Pedido: " . $cantidadPedida);
            }
        }

        $stmt_factura = $pdo->prepare(
            "INSERT INTO facturas (id_usuario, fecha_factura, subtotal, envio, itbms, total, metodo_pago, direccion_envio, correo_cliente)
             VALUES (:id_usuario, :fecha_factura, :subtotal, :envio, :itbms, :total, :metodo_pago, :direccion_envio, :correo_cliente)"
        );

        $stmt_factura->bindParam(':id_usuario', $user_id_factura, PDO::PARAM_INT);
        $stmt_factura->bindParam(':fecha_factura', $fecha_db_format, PDO::PARAM_STR);
        $stmt_factura->bindParam(':subtotal', $subtotal_display, PDO::PARAM_STR); 
        $stmt_factura->bindParam(':envio', $envio_display, PDO::PARAM_STR);
        $stmt_factura->bindParam(':itbms', $itbms_display, PDO::PARAM_STR);
        $stmt_factura->bindParam(':total', $total_display, PDO::PARAM_STR);
        $stmt_factura->bindParam(':metodo_pago', $metodo_pago_display, PDO::PARAM_STR);
        $stmt_factura->bindParam(':direccion_envio', $direccion_display, PDO::PARAM_STR);
        $stmt_factura->bindParam(':correo_cliente', $correo_cliente_display, PDO::PARAM_STR);

        $stmt_factura->execute();

        $orderId = $pdo->lastInsertId();

        $stmt_detalle = $pdo->prepare(
            "INSERT INTO detalle_factura (id_factura, id_producto, nombre_producto, cantidad, precio_unitario, subtotal_linea)
             VALUES (:id_factura, :id_producto, :nombre_producto, :cantidad, :precio_unitario, :subtotal_linea)"
        );

        
        $stmt_update_stock = $pdo->prepare(
            "UPDATE productos SET stock = stock - :cantidad_deduct WHERE ID = :id_producto AND stock >= :cantidad_check"
        );

        foreach ($productos_display as $prod) {
            $productId = $prod['id'] ?? null;
            $nombre_producto = $prod['nombre'] ?? 'Desconocido';
            $cantidad = intval($prod['cantidad'] ?? 0);
            $precio_unitario = floatval($prod['precio'] ?? 0.00);
            $subtotal_linea = $cantidad * $precio_unitario;

            if ($cantidad <= 0 || !$productId) continue;

            $stmt_detalle->bindParam(':id_factura', $orderId, PDO::PARAM_INT);
            $stmt_detalle->bindParam(':id_producto', $productId, PDO::PARAM_INT);
            $stmt_detalle->bindParam(':nombre_producto', $nombre_producto, PDO::PARAM_STR);
            $stmt_detalle->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt_detalle->bindParam(':precio_unitario', $precio_unitario, PDO::PARAM_STR);
            $stmt_detalle->bindParam(':subtotal_linea', $subtotal_linea, PDO::PARAM_STR);
            $stmt_detalle->execute();

          
            $stmt_update_stock->bindParam(':cantidad_deduct', $cantidad, PDO::PARAM_INT);
            $stmt_update_stock->bindParam(':id_producto', $productId, PDO::PARAM_INT);
            $stmt_update_stock->bindParam(':cantidad_check', $cantidad, PDO::PARAM_INT); // El segundo bind
            $stmt_update_stock->execute();

            if ($stmt_update_stock->rowCount() == 0) {
                throw new Exception("Error al actualizar stock para " . htmlspecialchars($nombre_producto) . ". No se pudo decrementar el stock o el ID no existe.");
            }
        }

        $pdo->commit();

        $message = "Pedido guardado exitosamente. Factura No: #" . $orderId;
        $is_success = true;

        echo "<script>localStorage.removeItem('carrito');</script>";

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack(); 
        }
        $message = "Hubo un error al procesar su pedido: " . htmlspecialchars($e->getMessage()) . ". Por favor, intente de nuevo más tarde.";
        $is_success = false;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack(); 
        }
        
        $message = "Hubo un error de base de datos al procesar su pedido: " . htmlspecialchars($e->getMessage());
        $is_success = false;
    }
} else {
    $message = "Acceso no válido. Por favor, realice su pedido desde la página de método de pago.";
    $productos_display = []; 
}

$fecha = $fecha_display_format; 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/header_1.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Factura - El Callejon</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fdfcfc; margin: 0; }
        .factura-container {
            background: #fff; border-radius: 16px; max-width: 700px; margin: 40px auto; box-shadow: 0 8px 32px rgba(0,0,0,0.10); padding: 38px 44px;
        }
        .factura-header { color: #a3080d; font-weight: bold; font-size: 2rem; margin-bottom: 6px; }
        .factura-sub { color: #333; margin-bottom: 24px; font-size: 1.1rem; }
        .factura-info { margin-bottom: 18px; }
        .factura-info span { display: inline-block; min-width: 120px; font-weight: bold; }
        .factura-table { width: 100%; border-collapse: collapse; margin: 24px 0 18px 0; }
        .factura-table th, .factura-table td { border: 1px solid #e0e0e0; padding: 10px 8px; text-align: left; }
        .factura-table th { background: #f3f3f3; color: #a3080d; }
        .factura-table td { background: #fff; }
        .factura-total-row td { font-weight: bold; color: #a3080d; }
        .factura-total { font-size: 1.2rem; color: #1a9c3c; font-weight: bold; text-align: right; }
        .factura-label { color: #888; font-size: 0.96rem; }
        .factura-footer { margin-top: 32px; text-align: center; color: #666; font-size: 0.95rem; }
        @media (max-width: 600px) {
            .factura-container { padding: 14px; }
            .factura-header { font-size: 1.3rem; }
        }
        .app-header {
            background: #a3080d;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .logo { height: 40px; margin-right: 10px; }
        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
    <div class="titulos-flex" style="margin-left:-41vw; margin-top: 10vw; ">
        <h1 style="font-size: 40px; font-weight: lighter;">Factura</h1>
    </div>
    <div class="factura-container" style="margin-top: 2vw;">

        <div class="factura-header">El Callejon</div>
        <div class="factura-sub">Gracias por tu pedido. Aquí tienes tu factura:</div>
        <div class="factura-info">
            <div><span>Factura No:</span> #<?php echo htmlspecialchars($orderId); ?></div>
            <div><span>Fecha:</span> <?php echo htmlspecialchars($fecha); ?></div>
            <div><span>Cliente:</span> <?php echo htmlspecialchars($cliente_display); ?></div>
            <div><span>Dirección:</span> <?php echo htmlspecialchars($direccion_display); ?></div>
            <div><span>Método de Pago:</span> <?php echo htmlspecialchars(ucfirst($metodo_pago_display)); ?></div>
        </div>
        <table class="factura-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($productos_display) && is_array($productos_display)):
                    foreach ($productos_display as $prod):
                        if (($prod['cantidad'] ?? 0) > 0 && ($prod['id'] ?? null) !== null): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prod['nombre'] ?? 'N/A'); ?></td>
                                <td><?php echo intval($prod['cantidad'] ?? 0); ?></td>
                                <td>$. <?php echo number_format($prod['precio'] ?? 0.00, 2); ?></td>
                                <td>$. <?php echo number_format(($prod['precio'] ?? 0.00) * ($prod['cantidad'] ?? 0), 2); ?></td>
                            </tr>
                        <?php
                        endif;
                    endforeach;
                else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay productos en esta factura.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="factura-label">Envío</td>
                    <td>$. <?php echo number_format($envio_display, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="factura-label">ITBMS</td>
                    <td>$. <?php echo number_format($itbms_display, 2); ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="factura-label">Subtotal Productos</td>
                    <td>$. <?php echo number_format($subtotal_display, 2); ?></td>
                </tr>
                <tr class="factura-total-row">
                    <td colspan="3">Total</td>
                    <td>$. <?php echo number_format($total_display, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class="factura-footer">¡Gracias por comprar en El Callejon!</div>
        <div style="text-align:center;margin-top:24px;">
            <a href="../landing/landing.php" style="display:inline-block;background:#a3080d;color:#fff;padding:12px 36px;border-radius:8px;font-size:1.08rem;text-decoration:none;font-weight:bold;box-shadow:0 2px 8px rgba(0,0,0,0.07);transition:background 0.2s;">Regresar al Menú Principal</a>
        </div>
    </div>
</body>
</html>