<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../db/conexion.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : null);
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$message = '';
$message_type = '';

if ($id === null || $id <= 0) {
    $message = 'Enlace de recuperación inválido o caducado.';
    $message_type = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id !== null && $id > 0) {
    if (empty($new_password) || empty($confirm_password)) {
        $message = 'Por favor, ingrese y confirme la nueva contraseña.';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Las contraseñas no coinciden.';
        $message_type = 'error';
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $id]);

            if ($stmt->rowCount() > 0) {
                header("Location: login.php?message=success_password_change");
                exit();
            } else {
                $message = 'No se pudo actualizar la contraseña o el usuario no existe.';
                $message_type = 'error';
            }

        } catch (PDOException $e) {
            $message = 'Error en la base de datos al cambiar la contraseña.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../assets/imag/fondo.png');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }
        .change-pass-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .change-pass-container h2 {
            margin-bottom: 25px;
            color: #b30c00ff;
        }
        .change-pass-container p {
            margin-bottom: 20px;
            color: #555;
            font-size: 0.95em;
        }
        .change-pass-container input[type="password"] {
            width: calc(100% - 22px);
            padding: 12px 10px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .change-pass-container button {
            width: 100%;
            padding: 12px;
            background-color: #b30c00ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 17px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .change-pass-container button:hover {
            background-color: #b30c00ff;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.95em;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="change-pass-container">
        <h2>Establecer Nueva Contraseña</h2>
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($id !== null && $id > 0 && $message_type !== 'error'): ?>
        <form action="cambiar_contrasena.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <input type="password" name="new_password" placeholder="Nueva Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
            <button type="submit" style="color: #ffffffff;" >Cambiar Contraseña</button>
        </form>
        <?php else: ?>
            <p>No se puede procesar la solicitud de cambio de contraseña. Por favor, solicite un nuevo enlace de recuperación.</p>
            <a href="recuperar_contrasena.php">Volver a recuperar contraseña</a>
        <?php endif; ?>
    </div>
</body>
</html>