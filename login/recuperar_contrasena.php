<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../assets/imag/fondo.png'); 
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #333;
        }
        .recovery-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 350px;
            text-align: center;
        }
        .recovery-container h2 {
            margin-bottom: 25px;
            color: #b30c00ff;
        }
        .recovery-container p {
            margin-bottom: 20px;
            color: #555;
            font-size: 0.95em;
        }
        .recovery-container input[type="email"] {
            width: calc(100% - 22px);
            padding: 12px 10px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .recovery-container button {
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
        .recovery-container button:hover {
            background-color: #b30c00ff;
        }
        .link-text {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .link-text a {
            color: #b30c00ff;
            text-decoration: none;
            transition: text-decoration 0.3s ease;
        }
        .link-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="recovery-container">
        <h2>Recuperar Contraseña</h2>
        <p>Introduce tu correo electrónico para enviarte un enlace de recuperación.</p>
        <form action="recovery_process.php" method="POST">
            <input type="email" name="email" placeholder="Correo Electrónico" required>
            <button type="submit">Enviar Enlace</button>
        </form>
        <div class="link-text">
            <a href="login.php">Volver al Login</a>
        </div>
    </div>
</body>
</html>