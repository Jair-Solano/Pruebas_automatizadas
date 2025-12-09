<?php

use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CarritoTest extends TestCase
{
    public function setUp(): void
    {
        if (!defined('TESTING')) {
            define('TESTING', true);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['carrito'] = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    private function callAgregar()
    {
        include __DIR__ . '/../carrito/agregar_carrito.php';
    }

    public function testAgregarProductoNuevo()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $this->assertCount(1, $_SESSION['carrito']);
        $this->assertEquals(1, $_SESSION['carrito'][0]['cantidad']);
    }

    public function testAgregarDosProductosDistintos()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $_POST['producto_id'] = 2;
        $this->callAgregar();

        $this->assertCount(2, $_SESSION['carrito']);
    }

    public function testAgregarMismoProductoSumaCantidad()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $this->assertEquals(2, $_SESSION['carrito'][0]['cantidad']);
    }

    public function testNoAgregarProductoInexistente()
    {
        $_POST['producto_id'] = 999;
        $this->callAgregar();

        $this->assertCount(0, $_SESSION['carrito']);
    }

    public function testDatosProductoAgregadoSonCorrectos()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $item = $_SESSION['carrito'][0];

        $this->assertEquals(1, $item['ID']);
        $this->assertEquals('Producto A', $item['nombre']);
        $this->assertEquals(10, $item['precio']);
        $this->assertEquals('img1.jpg', $item['imagen']);
    }

    public function testCantidadInicialEsUno()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $this->assertEquals(1, $_SESSION['carrito'][0]['cantidad']);
    }

    public function testIncrementarTresVecesMismoProducto()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();
        $this->callAgregar();
        $this->callAgregar();

        $this->assertEquals(3, $_SESSION['carrito'][0]['cantidad']);
    }

    public function testInicializarCarritoCuandoNoExiste()
    {
        session_destroy();
        session_start();

        $_SESSION = [];
        $_POST['producto_id'] = 1;

        $this->callAgregar();

        $this->assertArrayHasKey(0, $_SESSION['carrito']);
    }

    public function testAgregarDosProductosYRepetirUno()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $_POST['producto_id'] = 2;
        $this->callAgregar();

        $_POST['producto_id'] = 1;
        $this->callAgregar();

        $this->assertEquals(2, $_SESSION['carrito'][0]['cantidad']); // ID 1
        $this->assertEquals(1, $_SESSION['carrito'][1]['cantidad']); // ID 2
    }

    public function testNoDuplicaEntradaEnCarrito()
    {
        $_POST['producto_id'] = 1;
        $this->callAgregar();
        $this->callAgregar();
        $this->callAgregar();

        $this->assertCount(1, $_SESSION['carrito']);
    }
}
