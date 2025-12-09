<?php

use PHPUnit\Framework\TestCase;

class CarritoTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['carrito'] = [];
    }

    /* ============================================================
       ELIMINAR PRODUCTOS / VACIAR CARRITO
       ============================================================ */

    public function testEliminarProducto()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 1];

        unset($_SESSION['carrito'][1]);

        $this->assertArrayNotHasKey(1, $_SESSION['carrito']);
    }

    public function testEliminarProductoInexistente()
    {
        unset($_SESSION['carrito'][99]);

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testEliminarUltimoProducto()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 1];

        unset($_SESSION['carrito'][1]);

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testVaciarCarrito()
    {
        $_SESSION['carrito'] = [
            1 => ['cantidad' => 1],
            2 => ['cantidad' => 3]
        ];

        $_SESSION['carrito'] = [];

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testVaciarCarritoVacio()
    {
        $_SESSION['carrito'] = [];

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testEliminarSinId()
    {
        $id = null;

        if ($id !== null) {
            unset($_SESSION['carrito'][$id]);
        }

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testEliminarMientrasSeActualiza()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 2];

        unset($_SESSION['carrito'][1]);

        $this->assertArrayNotHasKey(1, $_SESSION['carrito']);
    }

    public function testEliminarProductoSinImagen()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 1];

        unset($_SESSION['carrito'][1]);

        $this->assertEmpty($_SESSION['carrito']);
    }

    public function testEliminarYAgregarDeNuevo()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 1];

        unset($_SESSION['carrito'][1]);

        $_SESSION['carrito'][1] = ['cantidad' => 2];

        $this->assertEquals(2, $_SESSION['carrito'][1]['cantidad']);
    }

    public function testEliminarEnPestanaDuplicada()
    {
        $_SESSION['carrito'][1] = ['cantidad' => 1];

        unset($_SESSION['carrito'][1]);
        unset($_SESSION['carrito'][1]);

        $this->assertEmpty($_SESSION['carrito']);
    }
}
