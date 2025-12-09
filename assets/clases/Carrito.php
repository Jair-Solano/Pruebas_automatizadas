<?php
class Carrito {
    public function __construct() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    public function obtenerContenido() {
        return $_SESSION['carrito'];
    }

    public function total() {
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }

    public function eliminarPorIndice($index) {
        if (isset($_SESSION['carrito'][$index])) {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
    }

    public function vaciar() {
        $_SESSION['carrito'] = [];
    }
}
?>
