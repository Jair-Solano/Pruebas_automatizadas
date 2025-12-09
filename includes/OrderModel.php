<?php
// models/OrderModel.php

require_once __DIR__ . '/../db/conexion.php';

class OrderModel {
    private $pdo;

    public function __construct() {
        global $pdo; // Use the global $pdo from your procedural connection

        if ($pdo === null) {
            throw new Exception("No se pudo establecer conexión con la base de datos.");
        }
        $this->pdo = $pdo;
    }

    /**
     * Fetches orders with pagination and filters.
     * @param array $filters Associative array of filters (search_query, start_date, end_date)
     * @param int $items_per_page Number of orders per page
     * @param int $offset Starting offset for pagination
     * @return array An array containing 'orders' and 'total_orders' count
     */
    public function getOrders(array $filters = [], int $items_per_page = 10, int $offset = 0): array {
        $sql_base = "SELECT
                        f.id AS factura_id,
                        f.fecha_factura,
                        f.subtotal,
                        f.envio,
                        f.itbms,
                        f.total,
                        f.metodo_pago,
                        f.direccion_envio,
                        f.correo_cliente,
                        COALESCE(u.usuario) AS cliente_usuario -- This is the user's username. NO COMMA HERE if it's the last selected column
                    FROM
                        facturas f
                    LEFT JOIN
                        usuarios u ON f.id_usuario = u.id"; // Ensure u.id is the correct column name in your 'usuarios' table for the user ID

        $conditions = [];
        $params = [];

        // Apply filters
        if (!empty($filters['search_query'])) {
            $search = '%' . $filters['search_query'] . '%';
            $conditions[] = "(
                f.correo_cliente LIKE :search_cliente OR
                f.direccion_envio LIKE :search_direccion OR
                COALESCE(u.usuario, '') LIKE :search_usuario OR -- Search by username
                f.id LIKE :search_id
            )";
            $params[':search_cliente'] = $search;
            $params[':search_direccion'] = $search;
            $params[':search_usuario'] = $search; // Corrected alias
            $params[':search_id'] = $search;
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = "DATE(f.fecha_factura) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = "DATE(f.fecha_factura) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $where_clause = '';
        if (!empty($conditions)) {
            $where_clause = " WHERE " . implode(' AND ', $conditions);
        }

        // Count total orders for pagination
        $count_sql = "SELECT COUNT(*) FROM facturas f LEFT JOIN usuarios u ON f.id_usuario = u.id" . $where_clause;
        $stmt_count = $this->pdo->prepare($count_sql);
        $stmt_count->execute($params);
        $total_orders = $stmt_count->fetchColumn();

        // Main query with pagination
        $sql = $sql_base . $where_clause . " ORDER BY f.fecha_factura DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters for the main query
        foreach ($params as $key => $val) {
            // Check if the parameter exists in the main query to avoid errors with parameters not bound to the LIMIT/OFFSET
            if (strpos($sql, $key) !== false) {
                 $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }
        $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch details for each order
        foreach ($orders as &$order) {
            $order['detalles'] = $this->getOrderDetails($order['factura_id']);
        }

        return ['orders' => $orders, 'total_orders' => $total_orders];
    }

    /**
     * Fetches details for a specific order.
     * @param int $factura_id The ID of the invoice
     * @return array An array of order detail items
     */
    public function getOrderDetails(int $factura_id): array {
        $stmt_detalle = $this->pdo->prepare("SELECT nombre_producto, cantidad, precio_unitario, subtotal_linea FROM detalle_factura WHERE id_factura = :id_factura");
        $stmt_detalle->bindParam(':id_factura', $factura_id, PDO::PARAM_INT);
        $stmt_detalle->execute();
        return $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
    }
}