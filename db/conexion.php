
<?php

if (!isset($pdo) || !$pdo instanceof PDO) {
    $host = 'localhost'; 
    $db   = 'proyectoFinal'; 
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        $pdo = null; 
        
        echo "Error de conexión a la base de datos: " . $e->getMessage() . "<br>"; 
    }
}
?>
