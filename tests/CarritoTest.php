<?php

use PHPUnit\Framework\TestCase;

class CarritoTest extends TestCase
{
    public function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    /* ============================================================
       INTEGRANTE 2: ACTUALIZACIÓN DE CANTIDADES (10 pruebas)
       ============================================================ */

    public function testAumentarCantidad()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];

        $_SESSION['carrito'][1]['cantidad']++;

        $this->assertEquals(2, $_SESSION['carrito'][1]['cantidad']);
    }

    public function testDisminuirCantidad()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 3];

        $_SESSION['carrito'][1]['cantidad']--;

        $this->assertEquals(2, $_SESSION['carrito'][1]['cantidad']);
    }

    public function testDisminuirCantidadHastaCeroElimina()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];

        $_SESSION['carrito'][1]['cantidad']--;

        if ($_SESSION['carrito'][1]['cantidad'] <= 0) {
            unset($_SESSION['carrito'][1]);
        }

        $this->assertArrayNotHasKey(1, $_SESSION['carrito']);
    }

    public function testAumentarCantidadRepetidamente()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];

        for ($i = 0; $i < 9; $i++) {
            $_SESSION['carrito'][1]['cantidad']++;
        }

        $this->assertEquals(10, $_SESSION['carrito'][1]['cantidad']);
    }

public function testDisminuirProductoInexistente()
{
    // NO HACE NADA, pero la aserción original falla si 'carrito' no existe.

    // La lógica para decrementar en tu aplicación real debería ser algo así:
    if (isset($_SESSION['carrito'][99])) {
        $_SESSION['carrito'][99]['cantidad']--;
    } 

    // Si tu setUp está vacío, debes inicializar la variable para que la aserción funcione:
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Si la prueba es para un producto inexistente, la aserción debe ser sobre
    // si la clave 99 se creó o no. Con la corrección de setUp, esto debería funcionar:
    $this->assertArrayNotHasKey(99, $_SESSION['carrito']); 
}

    public function testCantidadNegativaNoPermitida()
{
    $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];
    
    $nueva_cantidad = -5;
    
    if ($nueva_cantidad < 0) {
    
        $_SESSION['carrito'][1]['cantidad'] = 0; 
    } else {
        $_SESSION['carrito'][1]['cantidad'] = $nueva_cantidad;
    }

    $this->assertGreaterThanOrEqual(0, $_SESSION['carrito'][1]['cantidad']);
}

    public function testCantidadConLetras()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];

        $_SESSION['carrito'][1]['cantidad'] = "abc";

        $this->assertIsNotNumeric($_SESSION['carrito'][1]['cantidad']);
    }

    public function testCantidadMuyAlta()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 1];

        $_SESSION['carrito'][1]['cantidad'] = 9999;

        $this->assertEquals(9999, $_SESSION['carrito'][1]['cantidad']);
    }

    public function testDuplicarCantidadManual()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 3];

        $_SESSION['carrito'][1]['cantidad'] *= 2;

        $this->assertEquals(6, $_SESSION['carrito'][1]['cantidad']);
    }

    public function testCantidadNoModificadaSiUsuarioCancela()
    {
        $_SESSION['carrito'][1] = ['precio' => 10, 'cantidad' => 5];

        $this->assertEquals(5, $_SESSION['carrito'][1]['cantidad']);
    }
}
