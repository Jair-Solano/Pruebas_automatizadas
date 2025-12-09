<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../db/conexion.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer(true);

$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    header("Location: login.php?message=empty_email");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, usuario, correo FROM usuarios WHERE correo = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        try {
            $mail->isSMTP();
            $mail->Host       = 'localhost';
            $mail->SMTPAuth   = false;
            $mail->Port       = 1025;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('noreply@elcallejon.local', 'El Callejón');

            $destinatario = $user['correo'];
            $nombreUsuario = $user['usuario'];
            $mail->addAddress($destinatario, $nombreUsuario);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperacion de Contrasena - El Callejón';

            $reset_link = "http://localhost:3000/login/cambiar_contrasena.php?id=" . urlencode($user['id']);

            $mail->Body = '
            <html>
              <body>
                <p>Saludos ' . htmlspecialchars($user['usuario']) . ',</p>
                <p><strong>Tu correo registrado:</strong> ' . htmlspecialchars($user['correo']) . '</p>
                <p>Has solicitado un restablecimiento de contraseña para tu cuenta de El Callejón.</p>
                <p>Por favor, haz clic en el siguiente enlace para crear tu nueva contraseña:</p>
                <p><a href="' . $reset_link . '">Restablecer tu Contraseña</a></p>
                <p>Si no solicitaste un restablecimiento de contraseña, por favor, ignora este correo.</p>
                <br><br><br><br>
              </body>
            </html>';

            $mail->send();

            header("Location: login.php?message=email_sent");
            exit();

        } catch (Exception $e) {
            header("Location: login.php?message=email_sent_error");
            exit();
        }
    } else {
        header("Location: login.php?message=email_sent");
        exit();
    }

} catch (PDOException $e) {
    header("Location: login.php?message=error_general");
    exit();
}
?>