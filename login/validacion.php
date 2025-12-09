<?php

require_once('../db/conexion.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_input = trim($_POST['user'] ?? '');
$password_input = $_POST['password'] ?? '';

if (empty($user_input) || empty($password_input)) {
    header("Location: login.php?message=error_empty_fields");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, usuario, contraseña, rol, cedula, correo FROM usuarios WHERE usuario = :user");
    $stmt->bindParam(':user', $user_input, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if (password_verify($password_input, $row['contraseña'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['cedula'] = $row['cedula'];
            $_SESSION['correo'] = $row['correo'];

            session_regenerate_id(true);

            if ($row['rol'] == 1 ) {
                header("Location: ../landing/landing.php");
            } elseif ($row['rol'] == 0){
                header("Location: ../includes/panel_productos.php");
            } else {
                header("Location: login.php?message=error_invalid_role");
            }
            exit();
        } else {
            header("Location: login.php?message=error_password");
            exit();
        }
    } else {
        header("Location: login.php?message=error_user");
        exit();
    }

} catch (PDOException $e) {
    header("Location: login.php?message=error_general");
    exit();
}
?>