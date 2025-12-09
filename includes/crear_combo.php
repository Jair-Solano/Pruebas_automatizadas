<?php
include '../db/conexion.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = floatval($_POST['precio']);
    $categoria = $_POST['categoria'];
    $imagen = '';

    // Insertar producto vacío para obtener el ID
    $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, categoria) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $descripcion, $precio, $imagen, $categoria]);
    $nuevo_id = $pdo->lastInsertId();

    // Procesar imagen si se subió
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen']['tmp_name'];
        $original_name = basename($_FILES['imagen']['name']);
        $imagen = $original_name;
        $dest = __DIR__ . "/assets/imagenes/" . $imagen;
        move_uploaded_file($tmp_name, $dest);

        // Actualizar imagen en el producto
        $upd = $pdo->prepare("UPDATE productos SET imagen = ? WHERE id = ?");
        $upd->execute([$imagen, $nuevo_id]);

        $msg = "Producto creado con imagen.";
    } else {
        $msg = "Producto creado.";
    }

    header('Location: panel_productos.php?msg=creado');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form { max-width: 400px; margin: 40px auto; background: #fff; padding: 2em; border-radius: 10px; box-shadow: 0 2px 8px #0002; }
        .edit-form label { display: block; margin-top: 1em; }
        .edit-form input, .edit-form textarea, .edit-form select { width: 100%; padding: 0.5em; margin-top: 0.3em; }
        .edit-form button { margin-top: 1.5em; padding: 0.7em 1.5em; font-size: 1.1em; font-weight: bold; border: none; border-radius: 30px; cursor: pointer; transition: background 0.2s; }
        .edit-form .guardar-btn { background: #e2b100; color: #fff; margin-top: 0 !important; align-self: center; }
        .edit-form .guardar-btn:hover { background: #cfa000; }
        .edit-form .msg { color: green; margin-top: 1em; }
        .edit-form img { max-width: 100%; margin-top: 1em; border-radius: 8px; }
        .custom-file-input-wrapper { display: flex; gap: 1.5em; align-items: center; margin-top: 1.5em; justify-content: center; }
        .custom-file-input-wrapper button { margin-top: 0 !important; height: 60px; display: flex; align-items: center; justify-content: center; }
        .select-img-btn, .guardar-btn { min-width: 180px; height: 60px; font-size: 1.2em; font-weight: bold; border-radius: 30px; padding: 0; line-height: 1; }
        .select-img-btn { background: #a1001f; color: #fff; border: none; transition: background 0.2s; }
        .select-img-btn:hover { background: #7a0017; }
        .guardar-btn { background: #e2b100; color: #fff; border: none; transition: background 0.2s; }
        .guardar-btn:hover { background: #cfa000; }
        .volver-btn { display: block; text-align: center; margin: 2em auto 0 auto; background: #fff; color: #a1001f; border: 2px solid #a1001f; border-radius: 20px; padding: 0.7em 1.5em; font-weight: bold; text-decoration: none; font-size: 1.1em; width: fit-content; transition: background 0.2s, color 0.2s; }
        .volver-btn:hover { background: #a1001f; color: #fff; }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2>Crear Producto</h2>
        <?php if (isset($msg)) echo '<div class="msg">' . $msg . '</div>'; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Nombre:
                <input type="text" name="nombre" required>
            </label>
            <label>Descripción:
                <textarea name="descripcion" required></textarea>
            </label>
            <label>Precio:
                <input type="number" name="precio" step="0.01" required>
            </label>
            <label>Categoría:
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option value="combo">Combo</option>
                    <option value="batido">Batido</option>
                    <option value="refresco">Refresco</option>
                </select>
            </label>
            <div class="custom-file-input-wrapper">
                <button type="button" class="select-img-btn" onclick="document.getElementById('realFileInput').click();">Elegir imagen</button>
                <input type="file" name="imagen" id="realFileInput" accept="image/png,image/jpeg" style="display:none">
                <button type="submit" name="guardar" class="guardar-btn">Crear Producto</button>
            </div>
        </form>
        <a href="panel_productos.php" class="volver-btn">&larr; Volver al panel</a>
    </div>
</body>
</html>
