<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../carrito/logica_producto.php';

class CarritoTest extends TestCase
{
    /**
     * ===========================================
     *   PRUEBAS DE VALOR LÍMITE (3)
     * ===========================================
     */

    // CE1
    public function testCE_CarritoVacioProductoNuevo()
    {
        $carrito = [];
        $producto = ['ID' => 1, 'nombre' => 'X', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertCount(1, $resultado);
        $this->assertEquals(1, $resultado[0]['cantidad']);
    }


    public function testCE_IncrementoCantidad()
    {
        $carrito = [];
        $producto = ['ID' => 1, 'nombre' => 'X', 'precio' => 1, 'imagen' => '', 'cantidad' => 0];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        //  NO debe agregarse un producto con cantidad 0
        $this->assertCount(0, $resultado);
    }


    // CE3: Cantidad máxima válida (5)
    public function testCE_CarritoGrandeCantidad()
    {
        $carrito = [
            ['ID' => 1, 'nombre' => 'X', 'precio' => 1, 'imagen' => '', 'cantidad' => 4]
        ];

        $producto = ['ID' => 1, 'nombre' => 'X', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertEquals(5, $resultado[0]['cantidad']);
    }


    /**
     * ===========================================
     *   PRUEBAS DE TABLA DE DECISIÓN (3)
     * ===========================================
     */

    // TD1: Producto NO existe → debe agregarse
    public function testTD_ProductoNoExisteDebeAgregarse()
    {
        $carrito = [
            ['ID' => 1, 'nombre' => 'A', 'precio' => 1, 'imagen' => '', 'cantidad' => 1]
        ];

        $producto = ['ID' => 2, 'nombre' => 'B', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertCount(2, $resultado);
    }

    // TD2: Producto existe (<5) → debe incrementar
    public function testTD_IncrementoCantidad()
    {
        $carrito = [
            ['ID' => 5, 'nombre' => 'Test', 'precio' => 1, 'imagen' => '', 'cantidad' => 3]
        ];

        $producto = ['ID' => 5, 'nombre' => 'Test', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertEquals(4, $resultado[0]['cantidad']);
    }

    // TD3 (FALLA INTENCIONAL): Producto existe pero ya está en máximo (5)
    // Se espera incorrectamente que suba a 6
    public function testTD_ProductoExistePeroNoDebeCambiar()
    {
        $carrito = [
            ['ID' => 5, 'nombre' => 'Test', 'precio' => 1, 'imagen' => '', 'cantidad' => 5]
        ];

        $producto = ['ID' => 5, 'nombre' => 'Test', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        // Fallo intencionado → esperamos incorrectamente 6
        $this->assertEquals(6, $resultado[0]['cantidad']);
    }


    /**
     * ===========================================
     *   PRUEBAS DE COMBINACIÓN (2)
     * ===========================================
     */

    // C1: Carrito vacío + producto nuevo
    public function testCOMB_CarritoVacioProductoNuevo()
    {
        $carrito = [];
        $producto = ['ID' => 10, 'nombre' => 'A', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertCount(1, $resultado);
    }

    // C2: Carrito con muchos + producto existente
    public function testCOMB_CarritoConMuchosProductoExistente()
    {
        $carrito = [];
        for ($i = 1; $i <= 20; $i++) {
            $carrito[] = [
                'ID' => $i,
                'nombre' => "Producto $i",
                'precio' => rand(1, 10),
                'imagen' => '',
                'cantidad' => rand(1, 5)
            ];
        }

        $producto = ['ID' => 7, 'nombre' => 'Producto 7', 'precio' => 1, 'imagen' => '', 'cantidad' => 1];

        $resultado = agregarProductoAlCarrito($carrito, $producto);

        $this->assertGreaterThanOrEqual(2, $resultado[6]['cantidad']);
    }
}
