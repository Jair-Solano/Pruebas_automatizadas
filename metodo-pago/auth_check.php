<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    
    header("Location: ../login/login.php?message=not_logged_in");
    exit(); 
}

?>