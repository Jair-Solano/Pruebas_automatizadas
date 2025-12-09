<?php
session_start();
include '../db/conexion.php';

// Clase base Producto
class Producto {
    protected $id;
    protected $nombre;
    protected $precio;
    protected $imagen;

    public function __construct($id, $nombre, $precio, $imagen) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->imagen = $imagen;
    }

    public function getID() {
        return $this->id;
    }

    public function toArray() {
        return [
            'ID' => $this->id,
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'imagen' => $this->imagen,
        ];
    }
}

// Decorador: añade cantidad al producto
class ProductoConCantidad {
    protected $producto;
    protected $cantidad;

    public function __construct(Producto $producto, $cantidad = 1) {
        $this->producto = $producto;
        $this->cantidad = $cantidad;
    }

    public function incrementarCantidad() {
        $this->cantidad++;
    }

    public function getID() {
        return $this->producto->getID();
    }

    public function toArray() {
        $data = $this->producto->toArray();
        $data['cantidad'] = $this->cantidad;
        return $data;
    }
}

// Procesamiento POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = intval($_POST['producto_id']);

    // Buscar en la BD (usando mysqli o PDO según lo que uses en ../db/conexion.php)
    $stmt = $conexion->prepare("SELECT ID, nombre, precio, imagen FROM productos WHERE ID = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($row = $resultado->fetch_assoc()) {
        $producto = new Producto($row['ID'], $row['nombre'], $row['precio'], $row['imagen']);
        $productoDecorado = new ProductoConCantidad($producto);

        // Inicializar carrito
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Verificar si ya existe
        $existe = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['ID'] == $productoDecorado->getID()) {
                $item['cantidad']++;
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $_SESSION['carrito'][] = $productoDecorado->toArray();
        }
    }
}

header('Location: ../landing/tienda2.php');
exit;
?>
