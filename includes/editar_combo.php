<?php
include '../db/conexion.php'; 
if (!isset($_GET['id'])) {
    echo "ID de combo no especificado.";
    exit;
}
$id = intval($_GET['id']);

// Obtener datos del combo
$stmt = $pdo->prepare("SELECT * FROM productos WHERE ID = ?");
$stmt->execute([$id]);
$combo = $stmt->fetch();

if (!$combo) {
    echo "Combo no encontrado.";
    exit;
}

// Procesar edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoria = $_POST['categoria'];

    if (isset($_POST['eliminar'])) {
        // Eliminar combo
        $del = $pdo->prepare("DELETE FROM productos WHERE ID = ?");
        $del->execute([$id]);

        // Eliminar imagen asociada si existe
        $img_path = __DIR__ . "/assets/imagenes/combo$id.png";
        if (file_exists($img_path)) {
            unlink($img_path);
        }

        header('Location: panel_productos.php?msg=eliminado');
        exit;
    } else {
        // Editar combo
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = floatval($_POST['precio']);

        $upd = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ? WHERE ID = ?");
        $upd->execute([$nombre, $descripcion, $precio, $categoria, $id]);

        $combo['nombre'] = $nombre;
        $combo['descripcion'] = $descripcion;
        $combo['precio'] = $precio;
        $combo['categoria'] = $categoria;

        // Procesar imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['imagen']['tmp_name'];
            $original_name = basename($_FILES['imagen']['name']);
            $imagen = $original_name;
            $dest = __DIR__ . "/assets/imagenes/" . $imagen;
            move_uploaded_file($tmp_name, $dest);

            // Actualizar imagen en la base de datos
            $upd_img = $pdo->prepare("UPDATE productos SET imagen = ? WHERE ID = ?");
            $upd_img->execute([$imagen, $id]);
            $combo['imagen'] = $imagen;

            $msg = "Combo actualizado y imagen cambiada.";
        } else {
            $msg = "Combo actualizado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Combo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-form { max-width: 400px; margin: 40px auto; background: #fff; padding: 2em; border-radius: 10px; box-shadow: 0 2px 8px #0002; }
        .edit-form label { display: block; margin-top: 1em; }
        .edit-form input, .edit-form textarea, .edit-form select { width: 100%; padding: 0.5em; margin-top: 0.3em; }
        .edit-form button { margin-top: 1.5em; padding: 0.7em 1.5em; font-size: 1.1em; font-weight: bold; border: none; border-radius: 30px; cursor: pointer; transition: background 0.2s; }
        .edit-form .guardar-btn { background: #e2b100; color: #fff; margin-right: 1em; }
        .edit-form .guardar-btn:hover { background: #cfa000; }
        .edit-form .delete-btn { background: #e74c3c; color: #fff; margin-left: 0; }
        .edit-form .delete-btn:hover { background: #c0392b; }
        .edit-form .msg { color: green; margin-top: 1em; }
        .edit-form img { max-width: 100%; margin-top: 1em; border-radius: 8px; }
        .btn-row { display: flex; gap: 1em; justify-content: center; align-items: center; margin-top: 2em; }
        .custom-file-input-wrapper { display: flex; gap: 0.5em; align-items: center; margin-top: 0.5em; }
        .select-img-btn { background: #a1001f; color: #fff; border: none; border-radius: 20px; padding: 0.5em 1.2em; font-size: 1em; cursor: pointer; transition: background 0.2s; }
        .select-img-btn:hover { background: #7a0017; }
        .volver-btn { display: block; text-align: center; margin: 2em auto 0 auto; background: #fff; color: #a1001f; border: 2px solid #a1001f; border-radius: 20px; padding: 0.7em 1.5em; font-weight: bold; text-decoration: none; font-size: 1.1em; width: fit-content; transition: background 0.2s, color 0.2s; }
        .volver-btn:hover { background: #a1001f; color: #fff; }
    </style>
</head>
<body>
    <div class="edit-form">
        <h2>Editar Combo</h2>
        <?php if(isset($msg)) echo '<div class="msg">'.$msg.'</div>'; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Nombre:
                <input type="text" name="nombre" value="<?= htmlspecialchars($combo['nombre']) ?>" required>
            </label>
            <label>Descripción:
                <textarea name="descripcion" required><?= htmlspecialchars($combo['descripcion']) ?></textarea>
            </label>
            <label>Precio:
                <input type="number" name="precio" step="0.01" value="<?= htmlspecialchars($combo['precio']) ?>" required>
            </label>
            <label>Categoría:
                <select name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <option value="combo" <?= $combo['categoria'] === 'combo' ? 'selected' : '' ?>>Combo</option>
                    <option value="batido" <?= $combo['categoria'] === 'batido' ? 'selected' : '' ?>>Batido</option>
                    <option value="refresco" <?= $combo['categoria'] === 'refresco' ? 'selected' : '' ?>>Refresco</option>
                </select>
            </label>

            <label>Imagen actual:</label>
            <img src="assets/imagenes/<?= htmlspecialchars($combo['imagen']) ?>?<?= time() ?>" alt="Imagen del combo" onerror="this.style.display='none'">

            <label>Subir nueva imagen:</label>
            <div class="custom-file-input-wrapper">
                <button type="button" class="select-img-btn" onclick="document.getElementById('realFileInput').click();">Elegir imagen</button>
                <input type="file" name="imagen" id="realFileInput" accept="image/png,image/jpeg" style="display:none">
            </div>
            <div class="btn-row">
                <button type="submit" name="guardar" class="guardar-btn">Guardar Cambios</button>
                <button type="submit" name="eliminar" class="delete-btn" onclick="return confirm('¿Seguro que deseas eliminar este combo?')">Eliminar Combo</button>
            </div>
        </form>
        <a href="panel_productos.php" class="volver-btn">&larr; Volver al panel</a>
    </div>
</body>
</html>
