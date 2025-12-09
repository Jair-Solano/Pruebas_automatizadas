<?php

// RESULTADO SIMULADO: datos que "vendrían" desde BD
class FakeResult {
    private $data;
    private $index = 0;

    public function __construct($row) {
        $this->data = $row ? [$row] : [];
    }

    public function fetch_assoc() {
        if ($this->index < count($this->data)) {
            return $this->data[$this->index++];
        }
        return null;
    }
}

class FakeStatement {
    private $id;
    private $row;

    public function bind_param($type, $id) {
        $this->id = $id;
    }

    public function execute() {
        // simulamos productos existentes
        if ($this->id == 1) {
            $this->row = [
                'ID' => 1,
                'nombre' => 'Producto A',
                'precio' => 10,
                'imagen' => 'img1.jpg'
            ];
        } elseif ($this->id == 2) {
            $this->row = [
                'ID' => 2,
                'nombre' => 'Producto B',
                'precio' => 5,
                'imagen' => 'img2.jpg'
            ];
        } else {
            $this->row = null;
        }
    }

    public function get_result() {
        return new FakeResult($this->row);
    }
}

class FakeConexion {
    public function prepare($query) {
        return new FakeStatement();
    }
}
