<?php
session_start();

require_once __DIR__ . '/../db/conexion.php'; // La ruta a tu clase DB
require_once __DIR__ . '/../includes/OrderModel.php'; // La ruta a tu nueva clase OrderModel

// Redirigir si el usuario no está logueado o no tiene permisos (ejemplo básico)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php'); // Redirige a tu página de login
    exit();
}

$orderModel = new OrderModel(); // Instantiate the OrderModel

$pedidos = [];
$message = '';
$search_query = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$filters = [
    'search_query' => $search_query,
    'start_date' => $start_date,
    'end_date' => $end_date,
];

try {
    $results = $orderModel->getOrders($filters, $items_per_page, $offset);
    $pedidos = $results['orders'];
    $total_orders = $results['total_orders'];
    $total_pages = ceil($total_orders / $items_per_page);

} catch (PDOException $e) {
    $message = "Error de base de datos: " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    $message = "Error: " . htmlspecialchars($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pedidos - El Callejon</title>
    <link rel="stylesheet" href="../assets/css/header_1.css">
    <link rel="stylesheet" href="../assets/css/footer_1.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #fdfcfc; margin: 0; padding-top: 60px; } /* Ajuste para header fijo */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
        }
        h1 {
            color: #a3080d;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
        }
        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .filter-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            justify-content: center;
        }
        .filter-form label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .filter-form input[type="text"],
        .filter-form input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 180px;
            box-sizing: border-box;
        }
        .filter-form button {
            background-color: #a3080d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        .filter-form button:hover {
            background-color: #8a060b;
        }
        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 25px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .order-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-card-header h2 {
            margin: 0;
            color: #a3080d;
            font-size: 1.5rem;
        }
        .order-info span {
            font-weight: bold;
            color: #333;
        }
        .order-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .order-details-table th, .order-details-table td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        .order-details-table th {
            background-color: #f5f5f5;
            color: #555;
        }
        .order-summary {
            text-align: right;
            margin-top: 15px;
            font-size: 1.1rem;
        }
        .order-summary div {
            margin-bottom: 5px;
        }
        .order-summary span {
            font-weight: bold;
            color: #a3080d;
        }
        .pagination {
            text-align: center;
            margin-top: 30px;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #a3080d;
            background-color: #fff;
        }
        .pagination a:hover {
            background-color: #f0f0f0;
        }
        .pagination .current-page {
            background-color: #a3080d;
            color: white;
            border-color: #a3080d;
            font-weight: bold;
        }
        .no-orders {
            text-align: center;
            color: #777;
            font-size: 1.2rem;
            padding: 30px;
            border: 1px dashed #ccc;
            border-radius: 8px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-form input[type="text"],
            .filter-form input[type="date"] {
                width: 100%;
            }
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
    <div class="container">
        <h1>Historial de Pedidos</h1>

        <?php if (!empty($message)): ?>
            <div class="message-box error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="filter-form">
            <form action="ver_pedidos.php" method="GET" style="display:contents;">
                <div class="filter-group">
                    <label for="search">Buscar:</label>
                    <input type="text" id="search" name="search" placeholder="ID, Cliente, Correo, Dirección" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="filter-group">
                    <label for="start_date">Fecha Inicio:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="filter-group">
                    <label for="end_date">Fecha Fin:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <button type="submit">Filtrar Pedidos</button>
                <?php if (!empty($search_query) || !empty($start_date) || !empty($end_date)): ?>
                    <button type="button" onclick="window.location.href='ver_pedidos.php'">Limpiar Filtros</button>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="no-orders">
                <p>No se encontraron pedidos con los criterios seleccionados.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pedidos as $factura): ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <h2>Pedido #<?php echo htmlspecialchars($factura['factura_id']); ?></h2>
                        <span>Fecha: <?php echo date('d M Y, h:i A', strtotime($factura['fecha_factura'])); ?></span>
                    </div>
                    <div class="order-info">
                        <p><span>Cliente:</span> <?php echo htmlspecialchars($factura['cliente_usuario']); ?></p>
                        <p><span>Email:</span> <?php echo htmlspecialchars($factura['correo_cliente']); ?></p>
                        <p><span>Dirección:</span> <?php echo htmlspecialchars($factura['direccion_envio']); ?></p>
                        <p><span>Método de Pago:</span> <?php echo htmlspecialchars(ucfirst($factura['metodo_pago'])); ?></p>
                    </div>

                    <h3 style="margin-top: 20px; color: #a3080d;">Detalle de Productos:</h3>
                    <table class="order-details-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($factura['detalles'])): ?>
                                <?php foreach ($factura['detalles'] as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                        <td>$. <?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                        <td>$. <?php echo number_format($detalle['subtotal_linea'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">No hay detalles de productos para este pedido.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="order-summary">
                        <div>Subtotal Productos: <span>$. <?php echo number_format($factura['subtotal'], 2); ?></span></div>
                        <div>Envío: <span>$. <?php echo number_format($factura['envio'], 2); ?></span></div>
                        <div>ITBMS: <span>$. <?php echo number_format($factura['itbms'], 2); ?></span></div>
                        <div style="font-size: 1.3rem; margin-top: 10px;">Total del Pedido: <span style="color: #1a9c3c;">$. <?php echo number_format($factura['total'], 2); ?></span></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&start_date=<?php echo urlencode($start_date); ?>&end_date=<?php echo urlencode($end_date); ?>"
                       class="<?php echo ($i === $current_page) ? 'current-page' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>