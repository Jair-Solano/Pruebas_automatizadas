<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../db/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: metodo-pago.php?message=not_logged_in");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: metodo-pago.php?message=order_failed_missing_data");
    exit();
}

$id_usuario = $_SESSION['user_id'];
$correo_cliente = $_POST['correo_cliente'] ?? '';
$direccion_envio = $_POST['direccion_envio'] ?? '';
$metodo_pago = $_POST['metodo_pago'] ?? '';
$productos_json = $_POST['productos'] ?? '';
$subtotal_calculado = $_POST['subtotal_calculado'] ?? 0;
$envio = $_POST['envio'] ?? 0;
$itbms = $_POST['itbms'] ?? 0;
$total = $_POST['total'] ?? 0;
$quiere_factura_email = $_POST['send_email_invoice_hidden'] ?? 0;

$subtotal_calculado = (float)$subtotal_calculado;
$envio = (float)$envio;
$itbms = (float)$itbms;
$total = (float)$total;
$quiere_factura_email = (int)$quiere_factura_email;

if (empty($productos_json) || empty($metodo_pago) || empty($direccion_envio)) {
    header("Location: metodo-pago.php?message=order_failed_missing_data");
    exit();
}

$productos = json_decode($productos_json, true);

if (!is_array($productos) || empty($productos)) {
    header("Location: metodo-pago.php?message=order_failed_invalid_products");
    exit();
}

$pdo->beginTransaction();

try {
    $stmt_factura = $pdo->prepare("INSERT INTO facturas (id_usuario, fecha_factura, productos_json, subtotal, envio, itbms, total, metodo_pago, direccion_envio, enviar_factura_correo, correo_cliente) VALUES (:id_usuario, NOW(), :productos_json, :subtotal, :envio, :itbms, :total, :metodo_pago, :direccion_envio, :enviar_factura_correo, :correo_cliente)");

    $stmt_factura->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_factura->bindParam(':productos_json', $productos_json, PDO::PARAM_STR);
    $stmt_factura->bindParam(':subtotal', $subtotal_calculado, PDO::PARAM_STR);
    $stmt_factura->bindParam(':envio', $envio, PDO::PARAM_STR);
    $stmt_factura->bindParam(':itbms', $itbms, PDO::PARAM_STR);
    $stmt_factura->bindParam(':total', $total, PDO::PARAM_STR);
    $stmt_factura->bindParam(':metodo_pago', $metodo_pago, PDO::PARAM_STR);
    $stmt_factura->bindParam(':direccion_envio', $direccion_envio, PDO::PARAM_STR);
    $stmt_factura->bindParam(':enviar_factura_correo', $quiere_factura_email, PDO::PARAM_INT);
    $stmt_factura->bindParam(':correo_cliente', $correo_cliente, PDO::PARAM_STR);

    $stmt_factura->execute();

    $id_factura = $pdo->lastInsertId();

    $stmt_detalle = $pdo->prepare("INSERT INTO detalle_factura (id_factura, id_producto, nombre_producto, cantidad, precio_unitario, subtotal_linea) VALUES (:id_factura, :id_producto, :nombre_producto, :cantidad, :precio_unitario, :subtotal_linea)");

    foreach ($productos as $producto) {
        $prod_id = $producto['id'] ?? null;
        $prod_nombre = $producto['nombre'] ?? 'Producto Desconocido';
        $prod_cantidad = $producto['cantidad'] ?? 0;
        $prod_precio = $producto['precio'] ?? 0.00;
        $subtotal_linea = $prod_cantidad * $prod_precio;

        $stmt_detalle->bindParam(':id_factura', $id_factura, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':id_producto', $prod_id, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':nombre_producto', $prod_nombre, PDO::PARAM_STR);
        $stmt_detalle->bindParam(':cantidad', $prod_cantidad, PDO::PARAM_INT);
        $stmt_detalle->bindParam(':precio_unitario', $prod_precio, PDO::PARAM_STR);
        $stmt_detalle->bindParam(':subtotal_linea', $subtotal_linea, PDO::PARAM_STR);
        
        $stmt_detalle->execute();
    }

    $pdo->commit();

    echo '<script>localStorage.removeItem("carrito");</script>';
    ob_flush();
    flush();

    header("Location: factura.php?order_id=" . $id_factura .
               "&cliente=" . urlencode($correo_cliente) .
               "&direccion=" . urlencode($direccion_envio) .
               "&total=" . $total .
               "&itbms=" . $itbms .
               "&envio=" . $envio .
               "&productos_encoded=" . urlencode($productos_json));

    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    header("Location: metodo-pago.php?message=order_failed");
    exit();
}