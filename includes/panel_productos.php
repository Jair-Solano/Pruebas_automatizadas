<?php
include '../db/conexion.php'; 

// Agregar columna si no existe
date_default_timezone_set('America/Bogota');
$pdo->exec("ALTER TABLE productos ADD COLUMN IF NOT EXISTS en_carrusel TINYINT(1) DEFAULT 0");

// Crear producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['precio'], $_POST['descripcion'])) {
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $descripcion = $_POST['descripcion'];
    $en_carrusel = isset($_POST['en_carrusel']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, descripcion, en_carrusel) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $precio, $descripcion, $en_carrusel]);
}

// Actualizar carrusel
if (isset($_POST['actualizar_carrusel'])) {
    $pdo->exec("UPDATE productos SET en_carrusel = 0");

    if (!empty($_POST['carrusel_ids'])) {
        $ids = array_map('intval', $_POST['carrusel_ids']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("UPDATE productos SET en_carrusel = 1 WHERE ID IN ($placeholders)");
        $stmt->execute($ids);
    }
}

// Botón ver catálogo
if (isset($_POST['catalogo'])) {
    header('Location: ../landing/Tienda2.php');
    exit;
}

// Obtener productos
$stmt = $pdo->query("SELECT * FROM productos ORDER BY ID DESC");
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Productos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .panel-container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(161,0,31,0.10); padding: 32px; }
        .panel-container h2 { color: #a1001f; margin-bottom: 24px; }
        .panel-form { display: flex; gap: 18px; flex-wrap: wrap; margin-bottom: 32px; }
        .panel-form input, .panel-form textarea { padding: 10px; border-radius: 8px; border: 1px solid #e2b100; font-size: 1rem; }
        .panel-form label { font-weight: 700; color: #a1001f; }
        .panel-form button { background: #e2b100; color: #fff; border: none; border-radius: 24px; padding: 10px 28px; font-size: 1rem; font-weight: bold; cursor: pointer; }
        .panel-table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .panel-table th, .panel-table td { padding: 10px 8px; border-bottom: 1px solid #eee; text-align: center; }
        .panel-table th { background: #a1001f; color: #fff; }
        .panel-table tr:hover { background: #fffbe7; }
        .carrusel-checkbox { width: 20px; height: 20px; }
        .panel-actions { margin-top: 18px; text-align: right; }
    </style>
</head>
<body>
<div class="panel-container">
    <h2>Panel de Productos</h2>
    <div style="display: flex;">
    <form  method="GET" action="crear_combo.php">
        <button type="submit" style="background:#e2b100;color:#fff;border:none;border-radius:24px;padding:10px 28px;font-size:1rem;font-weight:bold;cursor:pointer;align-self:end;" name="nuevo" value="1">Crear nuevo combo</button>
    </form>
    <button  style="margin-left: 5px; background:#e2b100;color:#fff;border:none;border-radius:24px;padding:10px 28px;font-size:1rem;font-weight:bold;cursor:pointer;align-self:end;" onclick="window.location='ver_pedidos.php' ">Ver pedidos</button>
    </div>
    <form method="POST">
        <table class="panel-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>En Carrusel</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($productos as $p): ?>
                <tr style="cursor:pointer" onclick="window.location='editar_combo.php?id=<?= $p['ID'] ?>'">
                    <td><?= $p['ID'] ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td>$<?= number_format($p['precio'], 2) ?></td>
                    <td><?= htmlspecialchars($p['descripcion']) ?></td>
                    <td><?= htmlspecialchars($p['categoria'] ?? '') ?></td>
                    <td><input type="checkbox" class="carrusel-checkbox" name="carrusel_ids[]" value="<?= $p['ID'] ?>" <?= $p['en_carrusel'] ? 'checked' : '' ?> onclick="event.stopPropagation();"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="panel-actions">
            <button type="submit" name="actualizar_carrusel" style="background:#0074D9;color:#fff;border:none;border-radius:24px;padding:10px 28px;font-size:1rem;font-weight:bold;cursor:pointer;">Actualizar carrusel</button>
            <button type="submit" name="catalogo" style="background:#0074D9;color:#fff;border:none;border-radius:24px;padding:10px 28px;font-size:1rem;font-weight:bold;cursor:pointer;">ver cambios en catalogo</button>
        </div>
    </form>
</div>
</body>
</html>
